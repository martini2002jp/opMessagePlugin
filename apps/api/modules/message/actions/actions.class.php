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

    $body = $request['body'];
    $this->myMember = $this->member;
    $toMember = Doctrine::getTable('Member')->find($request['toMember']);
    $this->forward400Unless($toMember, 'invalid member');

    $relation = Doctrine_Core::getTable('MemberRelationship')->retrieveByFromAndTo($toMember->getId(), $this->member->getId());
    $this->forward400If($relation && $relation->getIsAccessBlock(), 'Cannot send the message.');

    $message = Doctrine::getTable('SendMessageData')->sendMessage($toMember, SendMessageData::SMARTPHONE_SUBJECT, $body, array());

    $file = $request->getFiles('message_image');
    try
    {
      $validator = new opValidatorImageFile(array('required' => false));
      $clean = $validator->clean($file);

      if (is_null($clean))
      {
        // if empty.
        return sfView::SUCCESS;
      }
    }
    catch (Exception $e)
    {
      $this->logMessage($e->getMessage());

      $this->forward400('This image file is invalid.');
    }

    $file = new File();
    $file->setFromValidatedFile($clean);
    $file->save();

    $messageFile = new MessageFile();
    $messageFile->setMessageId($message->getId());
    $messageFile->setFile($file);
    $messageFile->save();
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->forward400If('' === (string)$request['memberId'], 'memberId parameter is not specified.');
    $this->forward400If('' === (string)$request['maxId'], 'maxId parameter is not specified.');

    Doctrine_Core::getTable('MessageSendList')
     ->updateReadAllMessagesByMemberId($request['memberId'], $this->getUser()->getMemberId());

    $this->pager = Doctrine_Core::getTable('MessageSendList')->getMemberMessagesPager(
      $request['memberId'],
      $this->getUser()->getMemberId(),
      (bool) $request['isAddLow'],
      $request['maxId']
    );
  }

  public function executeRecentList(sfWebRequest $request)
  {
    $keyId = (int) $request->getParameter('keyId', 0);
    $page = (int) $request->getParameter('page', 1);
    $memberIds = opMessagePluginUtil::getMemberIdListFromString($request->getParameter('memberIds'));

    $this->pager = Doctrine_Core::getTable('MessageSendList')->getRecentMessagePager($memberIds, $this->getUser()->getMemberId(), $keyId, $page);
  }
}
