<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * MessageFile form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class MessageFileForm extends BaseMessageFileForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'image1' => new sfWidgetFormInputFile(),
      'image2' => new sfWidgetFormInputFile(),
      'image3' => new sfWidgetFormInputFile(),
    ));
    $this->setValidators(array(
      'image1' => new opValidatorImageFile(array('required' => false)),
      'image2' => new opValidatorImageFile(array('required' => false)),
      'image3' => new opValidatorImageFile(array('required' => false)),
    ));
  }
}
