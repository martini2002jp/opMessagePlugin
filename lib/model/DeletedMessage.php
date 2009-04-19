<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class DeletedMessage extends BaseDeletedMessage
{
  private $message = null;
  /**
   * 宛先/送信先を取得する
   * @return Member
   */
  public function getSendFromOrTo()
  {
    if ($this->getMessageId()) {
      if (!$this->message) {
        $this->message = SendMessageDataPeer::retrieveByPK($this->getMessageId());
      }
      if ($this->message) {
        return $this->message->getSendTo();
      }
    } else if ($this->getMessageSendListId()) {
      if (!$this->message) {
        $this->message = MessageSendListPeer::retrieveByPK($this->getMessageSendListId());
      }
      if ($this->message) {
        return $this->message->getSendFrom();
      }
    }
    return null;
  }
  
  /**
   * 件名を取得する
   * @return str
   */
  public function getSubject()
  {
    if ($this->getMessageId()) {
      if (!$this->message) {
        $this->message = SendMessageDataPeer::retrieveByPK($this->getMessageId());
      }
      if ($this->message) {
        return $this->message->getSubject();
      }
    } else if ($this->getMessageSendListId()) {
      if (!$this->message) {
        $this->message = MessageSendListPeer::retrieveByPK($this->getMessageSendListId());
      }
      if ($this->message) {
        return $this->message->getSendMessageData()->getSubject();
      }
    }
    return null;
  }
  
  /**
   * アイコンを取得する
   * @return str
   */
  public function getIcon()
  {
    if ($this->getMessageId()) {
      if (!$this->message) {
        $this->message = SendMessageDataPeer::retrieveByPK($this->getMessageId());
      }
      if ($this->message) {
        if ($this->message->getIsSend() == 1) {
          return "icon_mail_3.gif";
        } else {
          return "icon_mail_1.gif";
        }
      }
    } else if ($this->getMessageSendListId()) {
      $this->message = MessageSendListPeer::retrieveByPK($this->getMessageSendListId());
      if ($this->message) {
        return "icon_mail_2.gif";
      }
    }
    return null;
  }
  
  /**
   * アイコン Altを取得する
   * @return str
   */
  public function getIconAlt()
  {
    if ($this->getMessageId()) {
      if (!$this->message) {
        $this->message = SendMessageDataPeer::retrieveByPK($this->getMessageId());
      }
      if ($this->message) {
        if ($this->message->getIsSend() == 1) {
          return __('Sent Message');
        } else {
          return __('Drafts');
        }
      }
    } else if ($this->getMessageSendListId()) {
      $this->message = MessageSendListPeer::retrieveByPK($this->getMessageSendListId());
      if ($this->message) {
        return __('Inbox');
      }
    }
    return null;
  }
  
  /**
   * メッセージID（表示用）を取得する
   * @return int
   */
  public function getViewMessageId()
  {
    if ($this->getMessageId()) {
      if (!$this->message) {
        $this->message = SendMessageDataPeer::retrieveByPK($this->getMessageId());
      }
    } else if ($this->getMessageSendListId()) {
      $this->message = MessageSendListPeer::retrieveByPK($this->getMessageSendListId());
    }
    if ($this->message) {
      return $this->message->getId();
    }
    return null;
  }
}
