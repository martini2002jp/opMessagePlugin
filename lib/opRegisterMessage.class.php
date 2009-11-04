<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opRegisterMessage
 *
 * @package    OpenPNE
 * @subpackage opMessagePlugin
 * @author     Shogo Kawahara <kawahara@tejimaya.net>
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class opRegisterMessage
{
  static public function listenToPostActionEventSendFriendLinkRequestMessage($arguments)
  {
    if ($arguments['result'] == sfView::SUCCESS)
    {
      $request         = $arguments['actionInstance']->getRequest();
      $friendLinkParam = $request->getParameter('friend_link');
      $toMemberId      = $request->getParameter('id');
      $toMember        = Doctrine::getTable('Member')->find($toMemberId);
      $fromMember      = sfContext::getInstance()->getUser()->getMember();
      $fromMemberName  = $fromMember->getName();

      $param = $arguments['actionInstance']->getRequest()->getParameter('friend_link');

      $sender = new opMessageSender();
      $sender->setToMember($toMember)
        ->setSubject(sfContext::getInstance()->getI18N()->__('%Friend% link request message'))
        ->setBody($friendLinkParam['message'])
        ->setMessageType('friend_link')
        ->send();
    }
  }

  static public function listenToPostActionEventSendCommunityJoiningRequestMessage($arguments)
  {
    if ($arguments['result'] == sfView::SUCCESS)
    {
      $community = $arguments['actionInstance']->community;
      if ('close' !== $community->getConfig('register_poricy'))
      {
        return false;
      }

      $request = $arguments['actionInstance']->getRequest();
      $param = $request->getParameter('community_join');

      $memberId = sfContext::getInstance()->getUser()->getMemberId();

      $communityMember = Doctrine::getTable('CommunityMember')->findOneByMemberIdAndCommunityId($memberId, $community->id);

      $sender = new opMessageSender();
      $sender->setToMember($community->getAdminMember())
        ->setSubject(sfContext::getInstance()->getI18N()->__('%Community% joining request message'))
        ->setBody($param['message'])
        ->setMessageType('community_joining_request')
        ->setIdentifier($communityMember->id)
        ->send();
    }
  }

  static public function listenToPostActionEventSendTakeOverCommunityRequestMessage($arguments)
  {
    if ($arguments['result'] == sfView::SUCCESS)
    {
      $community = $arguments['actionInstance']->community;
      $member = $arguments['actionInstance']->member;

      $request = $arguments['actionInstance']->getRequest();
      $param = $request->getParameter('admin_request');

      $sender = new opMessageSender();
      $sender->setToMember($member)
        ->setSubject(sfContext::getInstance()->getI18N()->__('%Community% taking over request message'))
        ->setBody($param['message'])
        ->setMessageType('community_taking_over')
        ->setIdentifier($community->id)
        ->send();
    }
  }

  public function decorateCommunityTakingOverBody(SendMessageData $message)
  {
    $id = $message->getForeignId();
    $community = Doctrine::getTable('Community')->find($id);
    if (!$community)
    {
      return $this->body;
    }

    $params = array(
      'fromMember' => $message->getMember(),
      'message'    => $message->body,
      'community'  => $community,
    );

    return opMessageSender::decorateBySpecifiedTemplate('communityTakingOverMessage', $params);
  }

  public function decorateCommunityJoiningRequestBody(SendMessageData $message)
  {
    $id = $message->getForeignId();
    $communityMember = Doctrine::getTable('CommunityMember')->find($id);
    if (!$communityMember)
    {
      return $message->body;
    }

    $params = array(
      'fromMember' => $message->getMember(),
      'message'    => $message->body,
      'community'  => $communityMember->getCommunity(),
    );

    return opMessageSender::decorateBySpecifiedTemplate('communityJoiningRequestMessage', $params);
  }

  public function decorateFriendLinkBody(SendMessageData $message)
  {
    $params = array(
      'fromMember' => $message->getMember(),
      'message'    => $message->body,
    );

    return opMessageSender::decorateBySpecifiedTemplate('friendLinkMessage', $params);
  }

  public function __call($method, $arguments)
  {
    if (substr($method, 0, strlen($prefix)) === $prefix
      && substr($method, -(strlen($suffix))) === $suffix)
    {
      $event = new sfEvent($this, 'op_message_plugin.decorate_body', array('method' => $method, 'arguments' => $arguments));
      $this->dispatcher->notifyUntil($event);
      if ($event->isProcessed())
      {
        return $event->getReturnValue();
      }
    }

    throw new sfException(sprintf('Call to undefined method %s::%s.', get_class($this), $method));
  }
}
