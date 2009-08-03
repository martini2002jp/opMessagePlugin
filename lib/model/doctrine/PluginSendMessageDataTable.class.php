<?php
/**
 */
class PluginSendMessageDataTable extends Doctrine_Table
{

 /**
  * @param integer  $memberId
  * @param bool     $isDraft
  */
  public function addSendMessageQuery($memberId = null, $isDraft = false)
  {
    if (is_null($memberId))
    {
      $memberId = sfContext::getInstance()->getUser()->getMemberId();
    }

    $isSend = true;
    if ($isDraft) $isSend = false;

    $q = $this->createQuery()
      ->from('SendMessageData')
      ->where('member_id = ?', $memberId)
      ->andwhere('is_deleted = ?', false)
      ->andwhere('is_send = ?', $isSend);

    return $q;
  }

  /**
   * 送信メッセージ一覧
   * @param $memberId
   * @param $page
   * @param $size
   * @return Message object（の配列）
   */
  public function getSendMessagePager($memberId = null, $page = 1, $size = 20)
  {
    $q = $this->addSendMessageQuery($memberId);
    $q->orderBy('created_at DESC');

    $pager = new sfDoctrinePager('SendMessageData', $size);
    $pager->setQuery($q);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }

  /**
   * 下書きメッセージ一覧
   * @param $member_id
   * @param $page
   * @param $size
   * @return Message object（の配列）
   */
  public function getDraftMessagePager($memberId, $page = 1, $size = 20)
  {
    $q = $this->addSendMessageQuery($memberId, true);
    $q->orderBy('created_at DESC');

    $pager = new sfDoctrinePager('SendMessageData', $size);
    $pager->setQuery($q);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }

  public function getHensinMassage($memberId, $messageId)
  {
    $q = $this->createQuery()
      ->from('SendMessageData')
      ->where('member_id = ?', $memberId)
      ->andwhere('is_send = ?', true)
      ->andwhere('return_message_id = ?', $messageId)
      ->fetchOne();

    if (!$q) return null;

    return $q;
  }

  public function getSendMessageData($messageId)
  {
    $q = $this->createQuery()
      ->from('SendMessageData sm')
      ->leftJoin('sm.Member mm')
      ->where('sm.id = ?', $messageId);

    return $q->execute();
  }

  public function getSendMassageDataQueryById($messageId)
  {
    $q = Doctrine_Query::create()
      ->from('SendMessageData')
      ->where('id = ?', $messageId)
      ->fetchOne();

    if (!$q) return null;

    return $q;
  }
}
