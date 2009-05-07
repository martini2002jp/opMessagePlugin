<?php
/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * Subclass for performing query and update operations on the 'message' table.
 *
 * 
 *
 * @package plugins.opMessagePlugin.lib.model
 */ 
class SendMessageDataPeer extends BaseSendMessageDataPeer
{
 /**
  * add send message criteria
  *
  * @param Criteria $criteria
  * @param integer  $memberId
  */
  public static function addSendMessageCriteria($criteria, $memberId = null)
  {
    if (is_null($memberId))
    {
      $memberId = sfContext::getInstance()->getUser()->getMemberId();
    }
    $criteria->add(self::MEMBER_ID, $memberId);
    $criteria->add(self::IS_DELETED, 0);
    $criteria->add(self::IS_SEND, true);
  }

  public static function getHensinMassage($member_id, $message_id)
  {
    $c = new Criteria();
    $c->add(self::MEMBER_ID, $member_id);
    $c->add(self::IS_SEND, 1);
    $c->add(self::RETURN_MESSAGE_ID, $message_id);
    $obj = self::doSelectOne($c);
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
  public static function getSendMessagePager($memberId = null, $page = 1, $size = 20)
  {
    $c = new Criteria();
    self::addSendMessageCriteria($c, $memberId);
    $c->addDescendingOrderByColumn(self::CREATED_AT);
    $pager = new sfPropelPager('SendMessageData', $size);
    $pager->setCriteria($c);
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
  public static function getDraftMessagePager($member_id, $page = 1, $size = 20)
  {
    $c = new Criteria();
    $c->add(self::MEMBER_ID, $member_id);
    $c->add(self::IS_DELETED, 0);
    $c->add(SendMessageDataPeer::IS_SEND, 0);
    $c->addDescendingOrderByColumn(self::CREATED_AT);

    $pager = new sfPropelPager('SendMessageData', $size);
    $pager->setCriteria($c);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }

 /**
  * send message
  *
  * Available options:
  *
  *  * type      : The message type   (default: 'message')
  *  * fromMember: The message sender (default: my member object)
  *
  * @param mixed   $toMembers  a Member instance or array of Member instance
  * @param string  $subject    a subject of the message
  * @param string  $body       a body of the message
  * @param array   $options    options
  * @return SendMessageData
  */
  public static function sendMessage($toMembers, $subject, $body, $options = array())
  {
    if ($toMembers instanceof Member)
    {
      $toMembers = array($toMembers);
    }
    elseif (!is_array($toMembers))
    {
      throw new InvalidArgumentException();
    }

    $sendMessageData = new SendMessageData();
    if (!isset($options['fromMember']))
    {
      $options['fromMember'] = sfContext::getInstance()->getUser()->getMember();;
    }
    $sendMessageData->setMember($options['fromMember']);
    $sendMessageData->setSubject($subject);
    $sendMessageData->setBody($body);
    if (!isset($options['type']))
    {
      $options['type'] = 'message';
    }
    $sendMessageData->setMessageType(MessageTypePeer::getMessageTypeIdByName($options['type']));
    $sendMessageData->setIsSend(1);

    foreach ($toMembers as $member)
    {
      $send = new MessageSendList();
      $send->setSendMessageData($sendMessageData);
      $send->setMember($member);
      $send->save();
    }

    return $sendMessageData;
  }
}
