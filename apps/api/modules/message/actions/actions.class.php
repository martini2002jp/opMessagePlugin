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

    $this->message = Doctrine::getTable('SendMessageData')->sendMessage($toMember, mb_substr($body, 0, 25), $body, array());

    $filename = basename($_FILES['message_image']['name']);
    if (!is_null($filename) && '' !== $filename)
    {
      try
      {
        $validator = new opValidatorImageFile(array('required' => false));
        $validFile = $validator->clean($_FILES['message_image']);
      }
      catch (Exception $e)
      {
        $this->forward400($e->getMessage());
      }

      $f = new File();
      $f->setFromValidatedFile($validFile);
      $f->setName(hash('md5', uniqid(microtime()).$filename));
      if ($stream = fopen($_FILES['message_image']['tmp_name'], 'r'))
      {
        $bin = new FileBin();
        $bin->setBin(stream_get_contents($stream));
        $f->setFileBin($bin);
        $f->save();

        $di = new MessageFile();
        $di->setMessageId($this->message->getId());
        $di->setFileId($f->getId());
        $di->save();
      }
      else
      {
        $this->forward400(__('Failed to write file to disk.'));
      }
    }
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
