<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * Message form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class MessageForm extends BaseMessageForm
{
  public function configure()
  {
    unset($this['created_at'], $this['updated_at']);
    unset($this->widgetSchema['member_id'],
          $this->widgetSchema['is_deleted'],
          $this->widgetSchema['is_send'],
          $this->widgetSchema['message_type_id']);
    $this->widgetSchema['subject'] = new sfWidgetFormInput();
    $this->widgetSchema['thread_message_id'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['return_message_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['subject']->setOption('trim', true);
    $this->validatorSchema['subject']->setOption('required', true);
    $this->validatorSchema['body']->setOption('trim', true);
    $this->validatorSchema['body']->setOption('required', true);
    $this->widgetSchema->setNameFormat('message[%s]');
    $this->mergeForm(new MessageFileForm());
  }
  
  public function save($con = null)
  {
    $message = parent::save($con);
    $this->saveImageFiles($message);
    return $message;
  }

  public function updateObject($values = null)
  {
    $object = parent::updateObject($values);
    if (sfContext::getInstance()->getRequest()->getParameter('is_draft')) {
      $object->setIsSend(0);
    } else {
      $object->setIsSend(1);
    }
    $object->setMemberId(sfContext::getInstance()->getUser()->getMemberId());
    $this->saveSendList($object);
    return $object;
  }

  public function saveSendList(Message $message)
  {
    $send_member_id = $this->getValue('send_member_id');
    $send = MessageSendListPeer::getMessageByReferences($send_member_id, $message->getId());
    if (!$send)
    {
      $send = new MessageSendList();
      $send->setMessage($message);
      $send->setMemberId($send_member_id);
    }
  }
  
  public function saveImageFiles(Message $message)
  {
    $imageKeys = array('image1', 'image2', 'image3');
    foreach ($imageKeys as $imageKey)
    {
      if ($this->getValue($imageKey))
      {
        $file = new File();
        $file->setFromValidatedFile($this->getValue($imageKey));
        $file->setName('ms_'.$message->getId().'_'.$file->getName());

        $messageFile = new MessageFile();
        $messageFile->setMessage($message);
        $messageFile->setFile($file);
        $messageFile->save();
      }
    }
  }
}
