<?php

require_once __DIR__.'/../../bootstrap/unit.php';
require_once __DIR__.'/../../bootstrap/database.php';

// SendMessageData::postHydrate() で必要となる
sfContext::createInstance($configuration);

$t = new lime_test(4);

$conn = opDoctrineQuery::getMasterConnectionDirect();

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
