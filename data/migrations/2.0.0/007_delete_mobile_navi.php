<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class updateOpMessagePlugin_2_0_0_2 extends opMigration
{
  public function up()
  {
    $nav = Doctrine_Core::getTable('Navigation')
      ->findOneByTypeAndUri('mobile_home_side', 'message/index');
    if (!$nav)
    {
      return;
    }

    $nav->delete();
  }
}
