<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * Subclass for representing a row from the 'message_send_list' table.
 *
 * 
 *
 * @package plugins.opMessagePlugin.lib.model
 */ 
class MessageSendList extends BaseMessageSendList
{
  /**
   * 返信済みかどうか取得する
   * @return int
   */
  public function getIsHensin()
  {
    $reply = SendMessageDataPeer::getHensinMassage($this->getMemberId(), $this->getMessageId());
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
}
