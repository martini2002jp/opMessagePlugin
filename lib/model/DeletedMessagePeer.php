<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class DeletedMessagePeer extends BaseDeletedMessagePeer
{
 /**
  * add delete message criteria
  *
  * @param Criteria $criteria
  * @param integer  $memberId
  */
  public static function addDeleteMessageCriteria($criteria, $memberId = null)
  {
    if (is_null($memberId))
    {
      $memberId = sfContext::getInstance()->getUser()->getMemberId();
    }
    $criteria->add(self::MEMBER_ID, $memberId);
    $criteria->add(self::IS_DELETED, false);
  }

  /**
   * 削除済みメッセージ一覧
   * @param integer $memberId
   * @param integer $page
   * @param integer $size
   * @return DeletedMessage object（の配列）
   */
  public static function getDeletedMessagePager($memberId = null, $page = 1, $size = 20)
  {
    $c = new Criteria();
    self::addDeleteMessageCriteria($c, $memberId);
    $c->addDescendingOrderByColumn(self::CREATED_AT);
    $pager = new sfPropelPager('DeletedMessage', $size);
    $pager->setCriteria($c);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }
 
  /**
   * message_idから削除済みメッセージを取得する
   * @param $member_id
   * @param $message_id
   * @return DeletedMessage 
   */
  public static function getDeletedMessageByMessageId($member_id, $message_id)
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
  
  /**
   * message_send_list_idから削除済みメッセージを取得する
   * @param $member_id
   * @param $message_send_list_id
   * @return DeletedMessage
   */
  public static function getDeletedMessageByMessageSendListId($member_id, $message_send_list_id)
  {
    $c = new Criteria();
    $c->add(self::MEMBER_ID, $member_id);
    $c->add(self::MESSAGE_SEND_LIST_ID, $message_send_list_id);
    $obj = self::doSelectOne($c);
    if (!$obj) {
      return null;
    }
    return $obj;
  }
  
  /**
   * delete message 
   * @param int $member_id
   * @param int $message_id
   * @param str $object_name
   * @return boolean 
   */
  public static function deleteMessage($member_id, $message_id, $object_name)
  {
    if ($object_name == 'MessageSendList') {
        $message = MessageSendListPeer::retrieveByPK($message_id);
        $deleted_message = DeletedMessagePeer::getDeletedMessageByMessageSendListId($member_id, $message_id);
        if (!$deleted_message) {
          $deleted_message = new DeletedMessage();
        }
        $deleted_message->setMessageSendListId($message_id);
      } else if ($object_name == 'Message') {
        $message = SendMessageDataPeer::retrieveByPK($message_id);
        $deleted_message = DeletedMessagePeer::getDeletedMessageByMessageId($member_id, $message_id);
        if (!$deleted_message) {
          $deleted_message = new DeletedMessage();
        }
        $deleted_message->setMessageId($message_id);
      } else if ($object_name == 'DeletedMessage') {
        $message = DeletedMessagePeer::retrieveByPK($message_id);
        $deleted_message = null;
      }
      if (!$message) {
        return false;
      }
      if ($deleted_message) {
        $deleted_message->setMemberId($member_id);
        $deleted_message->save();
      }
      $message->setIsDeleted(1);
      /* @todo 完全削除の場合ファイルも削除すべきかも */
      $message->save();
      return true;
  }
  
  /**
   * restore message 
   * @param int $message_id
   * @return boolean 
   */
  public static function restoreMessage($message_id)
  {
    $deleted_message = DeletedMessagePeer::retrieveByPK($message_id);
    if (!$deleted_message) {
      return false;
    }
    if ($deleted_message->getMessageSendListId() != null) {
        $message = MessageSendListPeer::retrieveByPK($deleted_message->getMessageSendListId());
    } else if ($deleted_message->getMessageId() != null) {
        $message = SendMessageDataPeer::retrieveByPK($deleted_message->getMessageId());
    }
    if (!$message) {
      return false;
    }
    $deleted_message->delete();
    $message->setIsDeleted(0);
    $message->save();
    return true;
  }
}
