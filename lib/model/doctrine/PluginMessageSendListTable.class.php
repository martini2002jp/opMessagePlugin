<?php
class PluginMessageSendListTable extends Doctrine_Table
{
  public function addReceiveMessageQuery($memberId = null)
  {
    if (is_null($memberId))
    {
      $memberId = sfContext::getInstance()->getUser()->getMemberId();
    }

    $q = $this->createQuery()
      ->from('MessageSendList msl')
      ->leftJoin('msl.Member mem')
      ->leftJoin('msl.Message msd')
      ->where('msl.member_id = ?', $memberId)
      ->andwhere('msl.is_deleted = ?', false)
      ->andwhere('msd.is_send = ?', true);

    return $q;
  }

  /**
   * 受信メッセージ一覧
   * @param $memberId
   * @param $page
   * @param $size
   * @return MessageSendList object（の配列）
   */
  public function getReceiveMessagePager($memberId = null, $page = 1, $size = 20)
  {
    $q = $this->addReceiveMessageQuery($memberId);
    $q->orderBy('created_at DESC');

    $pager = new sfDoctrinePager('SendMessageData', $size);
    $pager->setQuery($q);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }

  /**
   * 未読メッセージ数を返す
   * @param $member_id
   * @return int 
   */
  public function countUnreadMessage($memberId)
  {
    $q = $this->createQuery()
      ->from('MessageSendList msl')
      ->leftJoin('msl.Message msd')
      ->where('msl.member_id = ?', $memberId)
      ->andwhere('msl.is_deleted = ?', false)
      ->andwhere('msl.is_read = ?', false)
      ->andwhere('msd.is_send = ?', true);

    return $q->count();
  }

  /**
   * member_idとmessage_idから本人宛のメッセージであることを確認する
   * @param $memberId
   * @param $messageId
   * @return int
   */
  public function getMessageByReferences($memberId, $messageId)
  {
    $q = $this->createQuery()
      ->from('MessageSendList')
      ->where('member_id = ?', $memberId)
      ->andwhere('message_id = ?', $messageId)
      ->fetchOne();

    if (!$q) return null;

    return $q;
  }

  /**
   * 宛先リストを取得する
   * @return array
   */
  public function getMessageSendList($messageId)
  {
    $q = $this->createQuery()
      ->from('MessageSendList ms')
      ->leftJoin('ms.Member mm')
      ->where('ms.message_id = ?', $messageId);

    return $q->execute();
  }

  /**
   * message_idからmessage_send_list_id を取得する
   * @param $messageId
   * @return int
   */
  public function getMessageSendListIdByMessageId($messageId)
  {
    $q = $this->createQuery()
      ->select('id')
      ->from('MessageSendList')
      ->andwhere('message_id = ?', $messageId)
      ->fetchOne();

    if (!$q) return null;

    return $q;
  }

  public function getMessageSendListQueryByMessageId($messageId)
  {
    $q = Doctrine_Query::create()
      ->from('MessageSendList')
      ->where('message_id = ?', $messageId)
      ->fetchOne();

    if (!$q) return null;

    return $q;
  }
}
