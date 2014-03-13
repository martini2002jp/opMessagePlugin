<?php
use_helper('opMessage');
$data = array();

foreach ($pager->getResults() as $message)
{
  $message->readMessage();
  $sendMessageData = $message->getSendMessageData();
  $data[] = op_api_message($sendMessageData, $sendMessageData->getMember());
}

return array(
  'status' => 'success',
  'data' => $data,
);
