<?php

/**
 * message components.
 *
 * @package    OpenPNE
 * @subpackage message
 * @author     Maki Takahashi <maki@jobweb.co.jp>
 */
class opMessagePluginMessageComponents extends sfComponents
{
  public function executeUnreadMessage()
  {
    $this->unreadMessageCount = MessageSendListPeer::countUnreadMessage($this->getUser()->getMemberId());
  }
}
