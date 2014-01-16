<?php
use_helper('opMessage');
$data = array();

foreach ($messageLists as $messageList)
{
  $data[] = op_api_message($messageList, $messageList->getPartnerMember(), true);
}

return array(
  'status' => 'success',
  'data' => $data,
);
