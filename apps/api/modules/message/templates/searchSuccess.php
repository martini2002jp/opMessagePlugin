<?php
use_helper('opMessage');
$data = array();

foreach ($pager->getResults() as $messageList)
{
  $data[] = op_api_message($messageList, $messageList->getSendFrom());
}

return array(
  'status' => 'success',
  'data' => $data,
  'has_more' => $pager->hasOlderPage(),
);
