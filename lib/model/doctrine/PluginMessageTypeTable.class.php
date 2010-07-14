<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class PluginMessageTypeTable extends Doctrine_Table
{
  /**
   * メッセージタイプの名前からIDを返す
   * @param  str: type_name
   * @return int : id
   */
  public function getMessageTypeIdByName($type_name)
  {
    static $queryCacheHash;

    $q = $this->createQuery()
      ->where('type_name = ?', $type_name)
      ->andWhere('is_deleted = ?', false);

    if ($queryCacheHash)
    {
      $q->setCachedQueryCacheHash($queryCacheHash);

      return $q->fetchOne();
    }

    $result = $q->fetchOne();
    $queryCacheHash = $q->calculateQueryCacheHash();

    return $result;
  }
}
