<?php
use_helper('opMessage');
$data = array();

foreach ($messageList as $message)
{
  $data[] = op_api_message($message, Doctrine::getTable('Member')->find($message->getMemberId()));
}

return array(
  'status' => 'success',
  'data' => $data,
);
