<?php
use_helper('opMessage');
$data = array();
$memberIds = array();

if ($pager->getNbResults())
{
  foreach ($pager->getResults() as $messageList)
  {
    $partnerMember = $messageList->getPartnerMember();
    $data[] = op_api_message($messageList, $partnerMember, true);
    $memberIds[] = $partnerMember->getId();
  }
}

$previousPage = 1 < $pager->getPage() ? $pager->getPreviousPage() : null;
$nextPage = $pager->getPage() < $pager->getNextPage() ? $pager->getNextPage() : null;

return array(
  'status' => 'success',
  'previousPage' => $previousPage,
  'nextPage' => $nextPage,
  'page' => $pager->getPage(),
  'memberIds' => opMessagePluginUtil::getStringFromMemberIdList($memberIds),
  'data' => $data,
);
