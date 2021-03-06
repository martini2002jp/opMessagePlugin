<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * PluginMessageSendList
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    opMessagePlugin
 * @subpackage form
 */
abstract class PluginMessageSendList extends BaseMessageSendList
{
  /**
   * 返信済みかどうか取得する
   * @return int
   */
  public function getIsHensin()
  {
    $reply = Doctrine::getTable('SendMessageData')->getHensinMassage($this->getMemberId(), $this->getMessageId());
    if ($reply) {
      return 1;
    } else {
      return 0;
    }
  }
  
  /**
   * メッセージを既読状態にする
   * @return int
   */
  public function readMessage()
  { 
    $this->setIsRead(1);
    $this->save();
  }

  public function isSelf($memberId = null)
  {
    if (is_null($memberId))
    {
      $memberId = sfContext::getInstance()->getUser()->getMemberId();
    }

    return (int) $this->getMemberId() === (int) $memberId;
  }

  /**
   * get partner Member.
   *
   * @param string $memberId
   * @return Member
   */
  public function getPartnerMember($memberId = null)
  {
    if (!$this->isSelf($memberId))
    {
      return $this->getMember();
    }

    return $this->getSendFrom();
  }

  /**
   * get message send from
   *
   * @return Member
   */
  public function getSendFrom()
  {
    return $this->getSendMessageData()->getMember();
  }

 /**
  * get message subject
  *
  * @return string
  */
  public function getSubject()
  {
    return $this->getSendMessageData()->getSubject();
  }

  /**
   * has unread message.
   *
   * @param string $memberId
   * @return boolean
   */
  public function hasUnreadMessage($memberId = null)
  {
    return $this->getTable()
      ->hasUnreadMessage($this->getPartnerMember($memberId)->getId(), $memberId);
  }
}
