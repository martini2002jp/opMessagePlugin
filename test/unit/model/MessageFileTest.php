
<?php

require_once __DIR__.'/../../bootstrap/unit.php';
require_once __DIR__.'/../../bootstrap/database.php';

$t = new lime_test(3);

$conn = opDoctrineQuery::getMasterConnectionDirect();

//------------------------------------------------------------
$t->diag('MessageFile: Cascading Delete');
$conn->beginTransaction();

$messageFile = Doctrine_Core::getTable('MessageFile')->find(1);
$fileId = $messageFile->file_id;

$messageFile->delete($conn);

$t->ok(!Doctrine_Core::getTable('MessageFile')->find($messageFile->id), 'message_file is deleted.');
$t->ok(!Doctrine_Core::getTable('File')->find($fileId), 'file is deleted.');
$t->ok(!Doctrine_Core::getTable('FileBin')->find($fileId), 'file_bin is deleted.');

$conn->rollback();
