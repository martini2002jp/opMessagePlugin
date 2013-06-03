<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * message api actions.
 *
 * @package    OpenPNE
 * @subpackage opMessagePlugin
 * @author     tatsuya ichikawa <ichikawa@tejimaya.com>
 */
class messageActions extends opJsonApiActions
{
  public function preExecute()
  {
    $this->member = $this->getUser()->getMember();
  }

  public function executePost(sfWebRequest $request)
  {
    $this->forward400If('' === (string)$request['body'], 'body parameter is not specified.');
    $this->forward400If('' === (string)$request['toMember'], 'toMember parameter is not specified.');
    // TODO 返信用IDとかを設定

    $body = $request['body'];
    $this->myMember = $this->member;
    $toMember = Doctrine::getTable('Member')->find($request['toMember']);
    $this->forward400Unless($toMember, 'invalid member');

    $this->message = Doctrine::getTable('SendMessageData')->sendMessage($toMember, mb_substr($body, 0, 25), $body, array());
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->forward400If('' === (string)$request['memberId'], 'memberId parameter is not specified.');
    $this->forward400If('' === (string)$request['maxId'], 'maxId parameter is not specified.');

    $this->messageList = Doctrine::getTable('SendMessageData')->getMemberMessages($request['memberId'], $request['maxId']);

    foreach ($this->messageList as $message)
    {
      $readMessage = Doctrine::getTable('MessageSendList')->getMessageByReferences(
        $this->getUser()->getMemberId(), $message->getId());
      if ($readMessage && 0 === (int)$readMessage->getIsRead()) {
        $readMessage->readMessage();
      }
    }
  }
}
