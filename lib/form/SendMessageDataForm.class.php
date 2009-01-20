<?php
/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * SendMessageData form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class SendMessageDataForm extends BaseSendMessageDataForm
{
  public function configure()
  {
    unset($this['created_at'], $this['updated_at'], $this['has_files']);
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
    if (sfConfig::get('app_message_is_upload_images', true))
    {
      if (!$this->isNew()) {
        sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
        $images = $this->getObject()->getMessageFiles();
      }

      $options = array(
        'file_src'     => '',
        'is_image'     => true,
        'with_delete'  => true,
      );

      $max = (int)sfConfig::get('app_message_max_image_file_num', 3);
      for ($i = 0; $i < $max; $i++) {
        if (!$this->isNew() && !empty($images[$i])) {
          $image = $images[$i];
          $options['edit_mode'] = true;
          $options['template'] = get_partial('message/formEditImage', array('image' => $image));
          $this->setValidator('image'.($i+1).'_delete', new sfValidatorBoolean(array('required' => false)));
        } else {
          $options['edit_mode'] = false;
        }
        $key = 'image'.($i+1);
        $this->setWidget($key, new sfWidgetFormInputFileEditable($options));
        $this->setValidator($key, new opValidatorImageFile(array('required' => false)));
      }
    }
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

  public function saveSendList(SendMessageData $message)
  {
    $send_member_id = $this->getValue('send_member_id');
    $send = MessageSendListPeer::getMessageByReferences($send_member_id, $message->getId());
    if (!$send) {
      $send = new MessageSendList();
      $send->setSendMessageData($message);
      $send->setMemberId($send_member_id);
    }
  }
  
  public function saveImageFiles(SendMessageData $message)
  {
    if (!$this->isNew()) {
      $images = $this->getObject()->getMessageFiles();
    }

    if (sfConfig::get('app_message_is_upload_images', true)) {
      $max = (int)sfConfig::get('app_message_max_image_file_num', 3);
      for ($i = 0; $i < $max; $i++) {
        if (!$this->isNew() && !empty($images[$i])) {
          if ($this->getValue('image'.($i+1).'_delete')) {
            $images[$i]->delete();
          }
        }
        $key = 'image'.($i+1);
        if ($this->getValue($key)) {
          if (!empty($images[$i]) && !$images[$i]->isDeleted()) {
            $images[$i]->delete();
          }

          $file = new File();
          $file->setFromValidatedFile($this->getValue($key));
          $file->setName('ms_'.$message->getId().'_'.$file->getName());

          $messageFile = new MessageFile();
          $messageFile->setSendMessageData($message);
          $messageFile->setFile($file);
          $messageFile->save();
        }
      }
    }
  }
}
