<?php

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
   * 受信メッセージ一覧
   * @param $member_id
   * @param $page
   * @param $size
   * @return MessageSendList object（の配列）
   */
  public static function getReceiveMessagePager($member_id, $page = 1, $size = 20)
  {
    $c = new Criteria();
    $c->addJoin(self::MESSAGE_ID, MessagePeer::ID);
    $c->add(self::MEMBER_ID, $member_id);
    $c->add(self::IS_DELETED, 0);
    $c->add(MessagePeer::IS_SEND, 1);
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
    $c->add(MessagePeer::IS_SEND, 1);
    $c->add(self::MEMBER_ID, $member_id);
    $c->add(self::IS_DELETED, 0);
    $c->add(self::IS_READ, 0);
    return self::doCountJoinMessage($c, $distinct = true);
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
