<?php
/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * Subclass for representing a row from the 'message' table.
 *
 * 
 *
 * @package plugins.opMessagePlugin.lib.model
 */ 
class SendMessageData extends BaseSendMessageData
{
  /**
   * メッセージが本人送信のものかどうか確認する
   * @param  $member_id
   * @return int
   */
  public function getIsSender($member_id)
  { 
    if ($this->getMemberId() == $member_id) {
      return 1;
    } else {
      return 0;
    }
  }  
  
  /**
   * メッセージが本人宛かどうか確認する
   * @param  $member_id
   * @return int
   */
  public function getIsReceiver($member_id)
  { 
    $message = MessageSendListPeer::getMessageByReferences($member_id, $this->getId());
    if ($message) {
      return 1;
    } else {
      return 0;
    }
  }

  /**
   * 宛先リストを取得する
   * @return array
   */
  public function getSendList()
  {
    $c = new Criteria();
    $c->add(MessageSendListPeer::MESSAGE_ID, $this->getId());
    $objs = MessageSendListPeer::doSelectJoinMember($c);
    return $objs;
  }

  /**
   * 宛先を取得する
   * @return str
   */
  public function getSendTo()
  {
    $objs = $this->getSendList();
    if ($cnt = count($objs) == 0) {
      return null;
    }
    $first = $objs[0]->getMember()->getName();
    if ($cnt > 1) {
      $first = $first."...(".$cnt.")";
    } 
    return $first;
  }
  
  /**
   * 添付ファイルを取得する（idの昇順）
   * @return array
   */
  public function getMessageFiles($criteria = null, PropelPDO $con = null)
  {
    if (is_null($criteria))
    {
      $criteria = new Criteria();
      $criteria->addAscendingOrderByColumn(MessageFilePeer::ID);
    }
    $files = parent::getMessageFiles($criteria, $con);
    return $files;
  }
}
