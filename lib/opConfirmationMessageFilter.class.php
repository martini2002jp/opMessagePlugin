<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opConfirmationMessageFilter
 *
 * @package    opMessagePlugin
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class opConfirmationMessageFilter
{
  static public function filterFriendLink(sfEvent $event, $list)
  {
    if ('friend_confirm' !== $event['category'])
    {
      return $list;
    }

    foreach ($list as $k => $v)
    {
      $obj = Doctrine::getTable('SendMessageData')->getMessageByTypeAndIdentifier($v['id'], sfContext::getInstance()->getUser()->getMemberId(), 'friend_link');

      $list[$k]['list']['Message'] = array('text' => $obj->body);
    }

    return $list;
  }
}
