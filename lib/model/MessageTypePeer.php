<?php

/**
 * Subclass for performing query and update operations on the 'message_type' table.
 *
 * 
 *
 * @package plugins.opMessagePlugin.lib.model
 */ 
class MessageTypePeer extends BaseMessageTypePeer
{
  /**
   * メッセージタイプの名前からIDを返す
   * @param  str: type_name
   * @return int : id
   */
  public static function getMessageTypeIdByName($type_name)
  {
    $c = new Criteria();
    $c->add(self::TYPE_NAME, $type_name);
    $c->add(self::IS_DELETED, 0);
    return self::doSelectOne($c);
  }
}
