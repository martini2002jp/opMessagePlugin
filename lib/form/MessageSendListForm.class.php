<?php

/**
 * MessageSendList form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class MessageSendListForm extends BaseMessageSendListForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'member_id'  => new sfWidgetFormInputHidden(),
    ));
    $this->setValidators(array(
      'member_id'  => new sfValidatorPropelChoice(array('model' => 'Member', 'column' => 'id', 'required' => false)),
    ));
    
  }
}
