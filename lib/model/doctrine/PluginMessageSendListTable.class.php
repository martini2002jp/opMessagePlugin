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
   * create left joined MessageData query
   *
   * @param string $localAlias
   * @param string $foreignAlias
   * @return Doctrine_Query
   */
  public function createLeftJoinMessageDataQuery($localAlias = 'm', $foreignAlias = 'm2')
  {
    return $this->createQuery($localAlias)
      ->leftJoin($localAlias.'.SendMessageData '.$foreignAlias);
  }

  /**
   * create send and receive query
   *
   * @param mixed $memberId (array|string|null)
   * @param mixed $myMemberId (string|null)
   * @param string $localAlias
   * @param string $foreignAlias
   * @return Doctrine_Query
   */
  public function createSendAndReceiveQuery($memberId = null, $myMemberId = null, $localAlias = 'm', $foreignAlias = 'm2')
  {
    if (is_null($myMemberId))
    {
      $myMemberId = sfContext::getInstance()->getUser()->getMemberId();
    }

    if (is_array($memberId))
    {
      $binder = implode(', ', array_fill(0, count($memberId), '?'));

      $where = sprintf('('.$foreignAlias.'.member_id IN (%s)'
               . ' AND '.$localAlias.'.member_id = ?'
               . ' AND '.$localAlias.'.is_deleted = ?)'
               . ' OR '
               . '('.$localAlias.'.member_id IN (%s)'
               . ' AND '.$foreignAlias.'.member_id = ?'
               . ' AND '.$foreignAlias.'.is_deleted = ?)',
               $binder,
               $binder
             );

      $params = array_merge($memberId, (array) $myMemberId, (array) false, $memberId, (array) $myMemberId, (array) false);
    }
    elseif (is_numeric($memberId))
    {
      $where = '('.$foreignAlias.'.member_id = ?'
             . ' AND '.$localAlias.'.member_id = ?'
             . ' AND '.$localAlias.'.is_deleted = ?)'
             . ' OR '
             . '('.$localAlias.'.member_id = ?'
             . ' AND '.$foreignAlias.'.member_id = ?'
             . ' AND '.$foreignAlias.'.is_deleted = ?)';

      $params = array($memberId, $myMemberId, false, $memberId, $myMemberId, false);
    }
    else
    {
      $where = '('.$localAlias.'.member_id = ?'
             . ' AND '.$localAlias.'.is_deleted = ?)'
             . ' OR '
             . '('.$foreignAlias.'.member_id = ?'
             . ' AND '.$foreignAlias.'.is_deleted = ?)';

      $params = array($myMemberId, false, $myMemberId, false);
    }

    return $this->createLeftJoinMessageDataQuery($localAlias, $foreignAlias)
      ->where($where, $params)
      ->andWhere($foreignAlias.'.is_send = ?', true);
  }

  /**
   * create receive query
   *
   * @param string $memberId
   * @param mixed $myMemberId (string|null)
   * @param string $localAlias
   * @param string $foreignAlias
   * @return Doctrine_Query
   */
  public function createReceiveQuery($memberId, $myMemberId = null, $localAlias = 'm', $foreignAlias = 'm2')
  {
    if (is_null($myMemberId))
    {
      $myMemberId = sfContext::getInstance()->getUser()->getMemberId();
    }

    return $this->createLeftJoinMessageDataQuery($localAlias, $foreignAlias)
      ->where($localAlias.'.member_id = ?', $myMemberId)
      ->andWhere($localAlias.'.is_deleted = ?', false)
      ->andWhere($foreignAlias.'.is_send = ?', true)
      ->andWhere($foreignAlias.'.member_id = ?', $memberId);
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
   * @param mixed $memberIds , Ex. array(1, 2, 3..) or null
   * @param mixed $myMemberId (string|null)
   * @param int   $keyId , 0 ~
   * @param int   $page , 1 ~
   * @param int   $size , default 25
   * @return sfReversibleDoctrinePager
   */
  public function getRecentMessagePager(array $memberIds = null, $myMemberId = null, $keyId = 0, $page = 1, $size = 25)
  {
    if (is_array($memberIds) && count($memberIds))
    {
      // If search by memberIds, page fixing.
      $page = 1;
    }
    else
    {
      $memberIds = null;
    }

    $results = $this->createSendAndReceiveQuery($memberIds, $myMemberId)
      ->select('m.member_id')
      ->addSelect('m2.member_id')
      ->addSelect('MAX(m.id)')
      ->addWhere('m2.id > ?', $keyId)
      ->groupBy('m.member_id, m2.member_id')
      ->execute(array(), Doctrine_Core::HYDRATE_NONE);

    $ids = array();
    foreach ($results as $result)
    {
      $receiveMemberId = $result[0];
      $sendMemberId = $result[1];
      $id = (int) $result[2];

      // Check more large MessageSendList.id. Use key that is partnerMemberId's memberId.
      $partnerMemberId = $receiveMemberId === $myMemberId ? $sendMemberId : $receiveMemberId;

      if (!isset($ids[$partnerMemberId]) || $ids[$partnerMemberId] < $id)
      {
        $ids[$partnerMemberId] = $id;
      }
    }

    $query = $this->createQuery()
      ->whereIn('id', $ids)
      ->orderBy('created_at, id DESC');

    $pager = new sfReversibleDoctrinePager('MessageSendList', $size);
    $pager->setSqlOrderColumn('created_at');
    $pager->setListOrder(sfReversibleDoctrinePager::ASC);
    $pager->setQuery($query);
    $pager->setPage($page);
    $pager->init();

    return $pager;
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
   * @param mixed $myMemberId (string|null)
   * @return bool
   */
  public function hasUnreadMessage($memberId, $myMemberId = null)
  {
    return (bool) $this
      ->createReceiveQuery($memberId, $myMemberId)
      ->andWhere('m.is_read = ?' ,false)
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
   * get member messages pager
   *
   * @param string $memberId
   * @param mixed $myMemberId (string|null)
   * @param bool $isAddLow
   * @param mixed $keyId (string|null)
   * @param integer $size
   * @return sfReversibleDoctrinePager
   */
  public function getMemberMessagesPager($memberId, $myMemberId = null, $isAddLow = true, $keyId = null, $size = 25)
  {
    $q = $this->createSendAndReceiveQuery($memberId, $myMemberId);

    $order =  sfReversibleDoctrinePager::ASC;
    if ($keyId)
    {
      if ($isAddLow)
      {
        $q->andWhere('m2.id > ?', $keyId);
      }
      else
      {
        $order = sfReversibleDoctrinePager::DESC;
        $q->andWhere('m2.id < ?', $keyId);
      }
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
   * @param mixed $myMemberId (string|null)
   */
  public function updateReadAllMessagesByMemberId($memberId, $myMemberId = null)
  {
    $results = $this->createReceiveQuery($memberId, $myMemberId)
      ->select('m.id')
      ->andWhere('m.is_read = ?', false)
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
