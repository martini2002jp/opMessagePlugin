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
        ->setSubject('フレンドリンク申請メッセージ')
        ->setBody($friendLinkParam['message'])
        ->setMessageType('friend_link')
        ->send();
    }
  }

  public function decorateFriendLinkBody(SendMessageData $message)
  {
    $params = array(
      'fromMember' => $message->getMember(),
      'message'    => $message->body,
    );

    return opMessageSender::decorateBySpecifiedTemplate('friendLinkMessage', $params);

    return $message->body;
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
