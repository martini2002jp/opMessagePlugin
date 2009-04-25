<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * Send Message form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class SendMessageForm extends SendMessageDataForm
{
  public function configure()
  {
    parent::configure();
    $this->setWidget("send_member_id", new sfWidgetFormInputHidden());
    $this->setValidator("send_member_id", new sfValidatorInteger());
    $this->setDefault("send_member_id", $this->getOption('send_member_id'));
  }
  
  public function updateObject($values = null)
  {
    $object = parent::updateObject($values);
    $object->setMessageType(MessageTypePeer::getMessageTypeIdByName('message'));
    return $object;
  }
}
