<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class PluginSendMessageDataTable extends Doctrine_Table
{
 /**
  * add send message query
  *
  * @param Doctrine_Query $q
  * @param integer  $memberId
  */
  public function addSendMessageQuery($q, $memberId = null)
  {
    if (is_null($memberId))
    {
      $memberId = sfContext::getInstance()->getUser()->getMemberId();
    }
    $q = $q->where('member_id = ?', $memberId)
      ->andWhere('is_deleted = ?', false)
      ->andWhere('is_send = ?', true);
    return $q;
  }

  public function getHensinMassage($memberId, $messageId)
  {
    $obj = $this->createQuery()
      ->where('member_id = ?', $memberId)
      ->andWhere('is_send = ?', true)
      ->andWhere('return_message_id = ?', $messageId)
      ->fetchOne();
    if (!$obj) {
      return null;
    }
    return $obj;
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
    $q = $this->addSendMessageQuery($this->createQuery(), $memberId);
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
  public function getDraftMessagePager($member_id, $page = 1, $size = 20)
  {
    $q = $this->createQuery()
      ->andWhere('member_id = ?', $member_id)
      ->andWhere('is_deleted = ?', false)
      ->andWhere('is_send = ?', false)
      ->orderBy('created_at DESC');

    $pager = new sfDoctrinePager('SendMessageData', $size);
    $pager->setQuery($q);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }

  public function getPreviousSendMessageData(SendMessageData $message, $myMemberId)
  {
    $q = $this->addSendMessageQuery($this->createQuery(), $myMemberId);
    $q->andWhere('id < ?', $message->id)
      ->orderBy('id DESC');

    return $q->fetchOne();
  }

  public function getNextSendMessageData(SendMessageData $message, $myMemberId)
  {
    $q = $this->addSendMessageQuery($this->createQuery(), $myMemberId);
    $q->andWhere('id > ?', $message->id)
      ->orderBy('id ASC');

    return $q->fetchOne();
  }
}
