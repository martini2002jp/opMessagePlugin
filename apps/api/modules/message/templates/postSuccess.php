<?php
use_helper('opMessage');
$data = op_api_message($message, $myMember);

return array(
  'status' => 'success',
  'data' => $data,
);
