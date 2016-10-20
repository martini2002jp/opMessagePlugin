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
  public static function sendNotification($fromMember, $toMember, $message)
  {
    $memberRelationship = Doctrine_Core::getTable('MemberRelationship')
      ->retrieveByFromAndTo($fromMember->id, $toMember->id);

    $isFriend = $memberRelationship ? $memberRelationship->isFriend() : false;

    $notifyWeb = false;
    $notifyEmail = false;

    if ($toMember->getConfig('is_send_messageNew_web', '1') === '1')
    {
      $notifyWeb = true;
    }
    elseif ($isFriend && $toMember->getConfig('is_send_messageNewOnlyFriends_web', '1') === '1')
    {
      $notifyWeb = true;
    }

    if ($toMember->getConfig('is_send_pc_messageNew_mail', '1') === '1')
    {
      $notifyEmail = true;
    }
    elseif ($isFriend && $toMember->getConfig('is_send_pc_messageNewOnlyFriends_mail', '1') === '1')
    {
      $notifyEmail = true;
    }

    $messageUrl = sfContext::getInstance()->getConfiguration()
      ->generateAppUrl('pc_frontend', array('sf_route' => 'readReceiveMessage', 'id' => $message->id), true);

    if ($notifyWeb)
    {
      self::sendNotificationWeb($fromMember, $toMember, $message, $messageUrl);
    }
    if ($notifyEmail)
    {
      self::sendNotificationEmail($fromMember, $toMember, $message, $messageUrl);
    }
  }

  private static function sendNotificationWeb($fromMember, $toMember, $message, $messageUrl)
  {
    $body = '[Message] '.$message->subject;

    opNotificationCenter::notify($fromMember, $toMember, $body, array(
      'category' => 'message',
      'name' => 'message_'.$message->id,
      'url' => $messageUrl,
    ));
  }

  private static function sendNotificationEmail($fromMember, $toMember, $message, $messageUrl)
  {
    opMailSend::sendTemplateMailToMember('notifyNewMessage', $toMember, array(
      'member' => $fromMember,
      'message' => $message,
      'url' => $messageUrl,
    ));
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
