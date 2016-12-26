<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class updateOpMessagePlugin_2_0_0_1 extends opMigration
{
  public function up()
  {
    $sortOrder = 0;

    $naviTable = Doctrine_Core::getTable('Navigation');
    if ($naviTable->findOneByTypeAndUri('smartphone_default', '@receiveList'))
    {
      // smartphone message navi is exists.
      return;
    }

    $logoutNavi = $naviTable->findOneByTypeAndUri('smartphone_default', '@member_logout');
    if ($logoutNavi)
    {
      $sortOrder = $logoutNavi->getSortOrder();
      $naviTable->createQuery()
        ->update()
        ->set('sort_order', 'sort_order + 1')
        ->where('sort_order >= ?', $logoutNavi->getSortOrder())
        ->andWhere('type = ?', 'smartphone_default')
        ->execute();
    }
    else
    {
      $maxSortOrder = $naviTable->createQuery()
        ->select('MAX(sort_order)')
        ->where('type = ?', 'smartphone_default')
        ->fetchOne(array(), Doctrine_Core::HYDRATE_NONE);
      if ($maxSortOrder)
      {
        $sortOrder = $maxSortOrder[0] + 1;
      }
    }

    $record = $naviTable->create(array(
      'type' => 'smartphone_default',
      'uri' => '@receiveList',
      'sort_order' => $sortOrder,
    ));

    foreach (array('ja_JP' => 'メッセージ', 'en' => 'Message') as $lang => $caption)
    {
      $record->Translation[$lang]->caption = $caption;
    }

    $record->save();
  }
}
