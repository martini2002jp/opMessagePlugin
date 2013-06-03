<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * message actions.
 *
 * @package    OpenPNE
 * @subpackage message
 * @author     Maki TAKAHASHI
 */
class messageActions extends opMessagePluginMessageActions
{
 /**
  * set friend nav
  *
  * @param integer $memberId
  */
  protected function setFriendNav($memberId)
  {
    sfConfig::set('sf_nav_type', 'friend');
    sfConfig::set('sf_nav_id', $memberId);
  }

 /**
  * Executes send to frind action
  *
  * @param sfWebRequest A request object
  */
  public function executeSendToFriend(sfWebRequest $request)
  {
    $result = parent::executeSendToFriend($request);
    $this->setFriendNav($this->sendMember->getId());
    return $result;
  }

 /**
  * Executes edit message action
  *
  * @param sfWebRequest A request object
  */
  public function executeEdit(sfWebRequest $request)
  {
    $result = parent::executeEdit($request);
    $this->setFriendNav($this->sendMember->getId());
    return $result;
  }

 /**
  * Executes reply message action
  *
  * @param sfWebRequest A request object
  */
  public function executeReply(sfWebRequest $request)
  {
    $result = parent::executeReply($request);
    $this->setFriendNav($this->sendMember->getId());
    return $result;
  }

 /**
  * Executes reply message action
  M
  * @param sfWebRequest A request object
  */
  public function executeSmtChain(sfWebRequest $request)
  {
    $this->member = Doctrine::getTable('Member')->find($request['id']);
    $this->forward404Unless($this->member);

    $this->myMember = $this->getUser()->getMember();

    $this->messageList = Doctrine::getTable('SendMessageData')->getMemberMessages($request['id']);
    foreach ($this->messageList as $message)
    {
      $readMessage = Doctrine::getTable('MessageSendList')->getMessageByReferences(
        $this->getUser()->getMemberId(), $message->getId());
      if ($readMessage && 0 === (int)$readMessage->getIsRead()) {
        $readMessage->readMessage();
      }
    }

    $this->setLayout('smtLayoutHome');
  }

  public function executeSmtList(sfWebRequest $request)
  {
    $this->memberList = Doctrine::getTable('SendMessageData')->getSenderList($this->getUser()->getMemberId());
    $this->setLayout('smtLayoutHome');
  }
}
