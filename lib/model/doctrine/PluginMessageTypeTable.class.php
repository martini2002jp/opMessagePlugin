<?php
/**
 */
class PluginMessageTypeTable extends Doctrine_Table
{
  /**
   * メッセージタイプの名前からIDを返す
   * @param  str: type_name
   * @return int : id
   */
  public function getMessageTypeIdByName($typeName)
  {
    $q = $this->createQuery()
      ->where('type_name = ?', $typeName)
      ->andwhere('is_deleted = ?', false);

    return $q->fetchOne();
  }
}
