<?php

require_once __DIR__.'/../../bootstrap/unit.php';
require_once __DIR__.'/../../bootstrap/database.php';

// SendMessageData::postHydrate() で必要となる
sfContext::createInstance($configuration);

$t = new lime_test(9);

$conn = opDoctrineQuery::getMasterConnectionDirect();
$messageTypeDefault = Doctrine_Core::getTable('MessageType')->findOneByTypeName('message');

//------------------------------------------------------------
$t->diag('SendMessageData: Cascading Delete');
$conn->beginTransaction();

$sendMessageData = Doctrine_Core::getTable('SendMessageData')->find(7);
$messageFile = $sendMessageData->MessageFile[0];
$fileId = $messageFile->file_id;

$sendMessageData->delete($conn);

$t->ok(!Doctrine_Core::getTable('SendMessageData')->find($sendMessageData->id), 'message is deleted.');
$t->ok(!Doctrine_Core::getTable('MessageFile')->find($messageFile->id), 'message_file is deleted.');
$t->ok(!Doctrine_Core::getTable('File')->find($fileId), 'file is deleted.');
$t->ok(!Doctrine_Core::getTable('FileBin')->find($fileId), 'file_bin is deleted.');

$conn->rollback();

//------------------------------------------------------------
$t->diag('SendMessageData::purgeIfOrphaned() [message.is_deleted != 1]');
$conn->beginTransaction();

$sendMessageData = Doctrine_Core::getTable('SendMessageData')->create(array(
  'member_id' => 1,
  'message_type_id' => $messageTypeDefault->id,
  'subject' => 'messageA',
  'body' => 'messageA body',
  'is_deleted' => false,
));
$sendMessageData->save($conn);

// message.is_deleted が 1 ではないので削除されない
$sendMessageData->purgeIfOrphaned($conn);

$t->ok(Doctrine_Core::getTable('SendMessageData')->find($sendMessageData->id));

$conn->rollback();

//------------------------------------------------------------
$t->diag('SendMessageData::purgeIfOrphaned() [message_send_list.is_deleted != 1]');
$conn->beginTransaction();

$sendMessageData = Doctrine_Core::getTable('SendMessageData')->create(array(
  'member_id' => 1,
  'message_type_id' => $messageTypeDefault->id,
  'subject' => 'messageA',
  'body' => 'messageA body',
  'is_deleted' => true, // 送信者側からは削除済み
));
$sendMessageData->save($conn);
$messageSendList = Doctrine_Core::getTable('MessageSendList')->create(array(
  'message_id' => $sendMessageData->id,
  'member_id' => 2,
  'is_deleted' => false,
));
$messageSendList->save($conn);

// message_send_list.is_deleted が 1 ではないので削除されない
$sendMessageData->purgeIfOrphaned($conn);

$t->ok(Doctrine_Core::getTable('SendMessageData')->find($sendMessageData->id));

$conn->rollback();

//------------------------------------------------------------
$t->diag('SendMessageData::purgeIfOrphaned() [deleted_message.is_deleted != 1 (sender)]');
$conn->beginTransaction();

$sendMessageData = Doctrine_Core::getTable('SendMessageData')->create(array(
  'member_id' => 1,
  'message_type_id' => $messageTypeDefault->id,
  'subject' => 'messageA',
  'body' => 'messageA body',
  'is_deleted' => true, // 送信者側からは削除済み
));
$sendMessageData->save($conn);
$messageSendList = Doctrine_Core::getTable('MessageSendList')->create(array(
  'message_id' => $sendMessageData->id,
  'member_id' => 2,
  'is_deleted' => true, // 受信者側からも削除済み
));
$messageSendList->save($conn);
$deletedMessage = Doctrine_Core::getTable('DeletedMessage')->create(array(
  'member_id' => 1,
  'message_id' => $sendMessageData->id,
  'message_send_list_id' => 0,
  'is_deleted' => false, // 送信者側のゴミ箱から未削除
));
$deletedMessage->save($conn);

// 送信者側の deleted_message.is_deleted が 1 ではないので削除されない
$sendMessageData->purgeIfOrphaned($conn);

$t->ok(Doctrine_Core::getTable('SendMessageData')->find($sendMessageData->id));

$conn->rollback();

//------------------------------------------------------------
$t->diag('SendMessageData::purgeIfOrphaned() [deleted_message.is_deleted != 1 (receiver)]');
$conn->beginTransaction();

$sendMessageData = Doctrine_Core::getTable('SendMessageData')->create(array(
  'member_id' => 1,
  'message_type_id' => $messageTypeDefault->id,
  'subject' => 'messageA',
  'body' => 'messageA body',
  'is_deleted' => true, // 送信者側からは削除済み
));
$sendMessageData->save($conn);
$messageSendList = Doctrine_Core::getTable('MessageSendList')->create(array(
  'message_id' => $sendMessageData->id,
  'member_id' => 2,
  'is_deleted' => true, // 受信者側からも削除済み
));
$messageSendList->save($conn);
$deletedMessage = Doctrine_Core::getTable('DeletedMessage')->create(array(
  'member_id' => 2,
  'message_id' => 0,
  'message_send_list_id' => $messageSendList->id,
  'is_deleted' => false, // 受信者側のゴミ箱から未削除
));
$deletedMessage->save($conn);

// 受信者側の deleted_message.is_deleted が 1 ではないので削除されない
$sendMessageData->purgeIfOrphaned($conn);

$t->ok(Doctrine_Core::getTable('SendMessageData')->find($sendMessageData->id));

$conn->rollback();

//------------------------------------------------------------
$t->diag('SendMessageData::purgeIfOrphaned() [orphaned message]');
$conn->beginTransaction();

$sendMessageData = Doctrine_Core::getTable('SendMessageData')->create(array(
  'member_id' => 1,
  'message_type_id' => $messageTypeDefault->id,
  'subject' => 'messageA',
  'body' => 'messageA body',
  'is_deleted' => true, // 送信者側からは削除済み
));
$sendMessageData->save($conn);
$messageSendList = Doctrine_Core::getTable('MessageSendList')->create(array(
  'message_id' => $sendMessageData->id,
  'member_id' => 2,
  'is_deleted' => true, // 受信者側からも削除済み
));
$messageSendList->save($conn);
$deletedMessage1 = Doctrine_Core::getTable('DeletedMessage')->create(array(
  'member_id' => 1,
  'message_id' => $sendMessageData->id,
  'message_send_list_id' => 0,
  'is_deleted' => true, // 送信者側のゴミ箱からも削除済み
));
$deletedMessage1->save($conn);
$deletedMessage2 = Doctrine_Core::getTable('DeletedMessage')->create(array(
  'member_id' => 2,
  'message_id' => 0,
  'message_send_list_id' => $messageSendList->id,
  'is_deleted' => true, // 受信者側のゴミ箱からも削除済み
));
$deletedMessage2->save($conn);

// 削除対象
$sendMessageData->purgeIfOrphaned($conn);

$t->ok(!Doctrine_Core::getTable('SendMessageData')->find($sendMessageData->id));

$conn->rollback();
