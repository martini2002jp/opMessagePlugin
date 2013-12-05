<?php
use_helper('opMessage');
$data = array();

foreach ($pager->getResults() as $message)
{
  $message->readMessage();
  $data[] = op_api_message($message->getSendMessageData(), $message->getMember());
}

return array(
  'status' => 'success',
  'data' => $data,
);
