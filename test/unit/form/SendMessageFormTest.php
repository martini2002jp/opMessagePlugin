<?php

include dirname(__FILE__).'/../../bootstrap/unit.php';
include dirname(__FILE__).'/../../bootstrap/database.php';

$t = new lime_test(null, new lime_output_color());

$replyMessage = new SendMessageData();
$replyMessage->setReturnMessageId(5);

$form = new SendMessageForm($replyMessage);
$t->is($form->getDefault('subject'), 'Re:test subject');
$t->is($form->getDefault('body'), "> test body\n> test line");