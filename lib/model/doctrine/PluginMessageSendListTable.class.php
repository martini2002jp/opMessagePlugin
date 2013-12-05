<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class PluginMessageSendListTable extends Doctrine_Table
{
  /**
   * add receive message query
   *
   * @param Doctrine_Query $q
   * @param integer  $memberId
   */
  public function addReceiveMessageQuery(Doctrine_Query $q, $memberId = null)
  {
    if (is_null($memberId))
    {
      $memberId = sfContext::getInstance()->getUser()->getMemberId();
    }

    $q = $q->where('member_id = ?', $memberId)
      ->andWhere('is_deleted = ?', false)
      ->andWhere('message_id IN (SELECT m2.id FROM SendMessageData m2 WHERE m2.is_send = ?)', true);

    return $q;
  }

  /**
   * add receive message query
   *
   * @param string $memberId
   * @param string $localAlias
   * @param string $foreignAlias
   * @return Doctrine_Query
   */
  public function createLeftJoinMessageDataQuery($memberId = null, $localAlias = 'm', $foreignAlias = 'm2')
  {
    if (is_null($memberId))
    {
      $memberId = sfContext::getInstance()->getUser()->getMemberId();
    }

    return $this->createQuery($localAlias)
      ->leftJoin($localAlias.'.SendMessageData '.$foreignAlias)
      ->where($localAlias.'.member_id = ?', $memberId)
      ->andWhere($localAlias.'.is_deleted = ?', false)
      ->andWhere($foreignAlias.'.is_send = ?', true);
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
    $q = $this->addReceiveMessageQuery($this->createQuery(), $memberId);
    $q->orderBy('created_at DESC');

    $pager = new sfDoctrinePager('SendMessageData', $size);
    $pager->setQuery($q);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }

  /**
   * Newest Message List.
   *
   * @param $memberId
   * @return Doctrine_Collection
   */
  public function getRecentMessageList($memberId)
  {
    $results = $this->createQuery('m')
      ->leftJoin('m.SendMessageData m2')
      ->select('m.member_id')
      ->addSelect('m2.member_id')
      ->addSelect('MAX(m.id)')
      ->where('(m.member_id = ? OR m2.member_id = ?)', array($memberId, $memberId))
      ->andWhere('is_deleted = ?', false)
      ->andWhere('m2.is_send = ?', true)
      ->groupBy('m.member_id, m2.member_id')
      ->execute(array(), Doctrine_Core::HYDRATE_NONE);

    $messageIds = array();
    foreach ($results as $result)
    {
      $receiveMemberId = $result[0];
      $sendMemberId = $result[1];
      $id = (int) $result[2];

      $partnerMemberId = $receiveMemberId === $memberId ? $sendMemberId : $receiveMemberId;

      if (!isset($messageIds[$partnerMemberId]) || $messageIds[$partnerMemberId] < $id)
      {
        $messageIds[$partnerMemberId] = $id;
      }
    }

    return $this->createQuery()
      ->whereIn('id', $messageIds)
      ->orderBy('created_at DESC')
      ->execute();
  }

  /**
   * 未読メッセージ数を返す
   * @param $member_id
   * @return int 
   */
  public function countUnreadMessage($member_id)
  {
    $q = $this->createQuery()
      ->where('member_id = ?', $member_id)
      ->andWhere('is_deleted = ?', false)
      ->andWhere('is_read = ?', false)
      ->andWhere('message_id IN (SELECT m2.id FROM SendMessageData m2 WHERE m2.is_send = ?)', true);
    return $q->count();
  }

  /**
   * Has unread message.
   *
   * @param string $memberId
   * @param string $myMemberId
   * @return bool
   */
  public function hasUnreadMessage($memberId, $myMemberId = null)
  {
    return (bool) $this
      ->createLeftJoinMessageDataQuery($myMemberId)
      ->andWhere('m.is_read = ?' ,false)
      ->andWhere('m2.member_id = ?', $memberId)
      ->count();
  }

  /**
   * member_idとmessage_idから本人宛のメッセージであることを確認する
   * @param $memberId
   * @param $messageId
   * @return int
   */
  public function getMessageByReferences($memberId, $messageId)
  {
    $obj = $this->createQuery()
      ->where('member_id = ?', $memberId)
      ->andwhere('message_id = ?', $messageId)
      ->fetchOne();
    if (!$obj) {
      return null;
    }
    return $obj;
  }

  /**
   * 宛先リストを取得する
   * @return array
   */
  public function getMessageSendList($messageId)
  {
    $q = $this->createQuery()
      ->where('message_id = ?', $messageId);

    return $q->execute();
  }

  public function getPreviousSendMessageData(SendMessageData $message, $myMemberId)
  {
    $q = $this->addReceiveMessageQuery($this->createQuery(), $myMemberId);
    $q->andWhere('message_id < ?', $message->id)
      ->orderBy('message_id DESC');

    $list = $q->fetchOne();
    if ($list)
    {
      return $list->getSendMessageData();
    }

    return false;
  }

  public function getNextSendMessageData(SendMessageData $message, $myMemberId)
  {
    $q = $this->addReceiveMessageQuery($this->createQuery(), $myMemberId);
    $q->andWhere('message_id > ?', $message->id)
      ->orderBy('message_id ASC');

    $list = $q->fetchOne();
    if ($list)
    {
      return $list->getSendMessageData();
    }

    return false;
  }

  /**
   * get member messages
   *
   * @param string $memberId
   * @param integer $start
   * @return sfReversibleDoctrinePager
   */
  public function getMemberMessagesPager($memberId, $myMemberId = null, $order = sfReversibleDoctrinePager::ASC, $maxId = null, $size = 20)
  {
    if (is_null($myMemberId))
    {
      $myMemberId = sfContext::getInstance()->getUser()->getMemberId();
    }

    $q = $this->createQuery('m')
      ->leftJoin('m.SendMessageData m2')
      ->where('(m.member_id = ? OR (m.member_id = ? AND m.is_deleted = ?))', array($memberId, $myMemberId, false))
      ->andWhere('(m2.member_id = ? OR (m2.member_id = ? AND m2.is_deleted = ?))', array($memberId, $myMemberId, false))
      ->andWhere('m2.is_send = ?', true);

    if ($maxId)
    {
      $q->andWhere('m2.id < ?', $maxId);
    }

    $pager = new sfReversibleDoctrinePager('MessageSendList', $size);
    $pager->setQuery($q);
    $pager->setPage(1);
    $pager->setSqlOrderColumn('id');
    $pager->setSqlOrder(sfReversibleDoctrinePager::DESC);
    $pager->setListOrder($order);
    $pager->setMaxPerPage($size);
    $pager->init();

    return $pager;
  }

  /**
   * update read all messages by memberId
   *
   * @param string $memberId
   * @param string $myMemberId
   */
  public function updateReadAllMessagesByMemberId($memberId, $myMemberId = null)
  {
    $results = $this->createLeftJoinMessageDataQuery($myMemberId)
      ->select('m.id')
      ->andWhere('m.is_read = ?', false)
      ->andWhere('m2.member_id = ?', $memberId)
      ->execute(array(), Doctrine_Core::HYDRATE_NONE);

    if (!count($results))
    {
      return;
    }

    $ids = array();
    foreach ($results as $result)
    {
      $id = $result[0];
      $ids[] = $id;
    }

    $this->createQuery()->update()
      ->set('is_read', '?', true)
      ->whereIn('id', $ids)
      ->execute();
  }
}
