<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * Subclass for performing query and update operations on the 'message_send_list' table.
 *
 * 
 *
 * @package plugins.opMessagePlugin.lib.model
 */ 
class MessageSendListPeer extends BaseMessageSendListPeer
{
  /**
   * add receive message criteria
   *
   * @param Criteria $criteria
   * @param integer  $memberId
   */
  public static function addReceiveMessageCriteria($criteria, $memberId = null)
  {
    if (is_null($memberId))
    {
      $memberId = sfContext::getInstance()->getUser()->getMemberId();
    }
    $criteria->addJoin(self::MESSAGE_ID, SendMessageDataPeer::ID);
    $criteria->add(self::MEMBER_ID, $memberId);
    $criteria->add(self::IS_DELETED, false);
    $criteria->add(SendMessageDataPeer::IS_SEND, true);
  }

  /**
   * 受信メッセージ一覧
   * @param $memberId
   * @param $page
   * @param $size
   * @return MessageSendList object（の配列）
   */
  public static function getReceiveMessagePager($memberId = null, $page = 1, $size = 20)
  {
    $c = new Criteria();
    self::addReceiveMessageCriteria($c, $memberId);
    $c->addDescendingOrderByColumn(self::CREATED_AT);

    $pager = new sfPropelPager('MessageSendList', $size);
    $pager->setCriteria($c);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }
  
  /**
   * 未読メッセージ数を返す
   * @param $member_id
   * @return int 
   */
  public static function countUnreadMessage($member_id)
  {
    $c = new Criteria();
    $c->add(SendMessageDataPeer::IS_SEND, 1);
    $c->add(self::MEMBER_ID, $member_id);
    $c->add(self::IS_DELETED, 0);
    $c->add(self::IS_READ, 0);
    return self::doCountJoinSendMessageData($c, $distinct = true);
  }
  
  /**
   * member_idとmessage_idから本人宛のメッセージであることを確認する
   * @param $member_id
   * @param $message_id
   * @return int 
   */
  public static function getMessageByReferences($member_id, $message_id)
  {
    $c = new Criteria();
    $c->add(self::MEMBER_ID, $member_id);
    $c->add(self::MESSAGE_ID, $message_id);
    $obj = self::doSelectOne($c);
    if (!$obj) {
      return null;
    }
    return $obj;
  }
}
