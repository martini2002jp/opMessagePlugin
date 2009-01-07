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
   * @param $member_id
   * @param $page
   * @param $size
   * @return Message object（の配列）
   */
  public static function getSendMessagePager($member_id, $page = 1, $size = 20)
  {
    $c = new Criteria();
    $c->add(self::MEMBER_ID, $member_id);
    $c->add(self::IS_DELETED, 0);
    $c->add(SendMessageDataPeer::IS_SEND, 1);
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
}
