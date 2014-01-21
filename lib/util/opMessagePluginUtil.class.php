<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opMessagePluginUtil
 *
 * @package    opMessagePlugin
 * @author     tatsuya ichikawa <ichikawa@tejimaya.com>
 */
class opMessagePluginUtil
{
  public static function sendNotification($fromMember, $toMember, $messageId)
  {
    $rootPath = sfContext::getInstance()->getRequest()->getRelativeUrlRoot();
    $url = $rootPath.'/message/read/'.$messageId;

    $message = sfContext::getInstance()->getI18n()->__('There are new %d messages!', array('%d' => 1));

    opNotificationCenter::notify($fromMember, $toMember, $message, array('category' => 'message', 'url' => $url, 'icon_url' => null));
  }

  CONST SPLIT_KEY = ',';

  public static function getMemberIdListFromString($key)
  {
    if (!is_string($key))
    {
      return null;
    }

    $key = trim($key);

    if (!$key)
    {
      return null;
    }

    $memberIds = array();
    foreach (explode(self::SPLIT_KEY, $key) as $memberId)
    {
      if (is_numeric($memberId))
      {
        $memberIds[] = $memberId;
      }
    }

    return count($memberIds) ? $memberIds : null;
  }

  public static function getStringFromMemberIdList(array $memberIds)
  {
    $clean = array();
    foreach ($memberIds as $memberId)
    {
      if (is_numeric($memberId))
      {
        $clean[] = $memberId;
      }
    }

    return implode(self::SPLIT_KEY, $clean);
  }
}
