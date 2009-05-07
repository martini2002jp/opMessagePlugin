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
      $toMember        = MemberPeer::retrieveByPk($toMemberId);
      $fromMember      = sfContext::getInstance()->getUser()->getMember();
      $fromMemberName  = $fromMember->getName();

      $param = $arguments['actionInstance']->getRequest()->getParameter('friend_link');

      $sender = new opMessageSender();
      $sender->setToMember($toMember)
        ->setSubject('フレンドリンク申請メッセージ')
        ->setBodyFromGlobalTemplate('friendLinkMessage', array('fromMember' => $fromMember, 'message' => $friendLinkParam['message']))
        ->setMessageType('invite_friend')
        ->send();
    }
  }
}
