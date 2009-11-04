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

  static public function filterCommunityJoiningRequest(sfEvent $event, $list)
  {
    if ('community_confirm' !== $event['category'])
    {
      return $list;
    }

    foreach ($list as $k => $v)
    {
      $communityMember = Doctrine::getTable('CommunityMember')->find($v['id']);
      $obj = Doctrine::getTable('SendMessageData')->getMessageByTypeAndIdentifier($communityMember->member_id, sfContext::getInstance()->getUser()->getMemberId(), 'community_joining_request', $v['id']);

      $list[$k]['list']['Message'] = array('text' => $obj->body);
    }

    return $list;
  }

  static public function filterCommunityTakingOver(sfEvent $event, $list)
  {
    if ('community_admin_request' !== $event['category'])
    {
      return $list;
    }

    foreach ($list as $k => $v)
    {
      $community = Doctrine::getTable('Community')->find($v['id']);
      $obj = Doctrine::getTable('SendMessageData')->getMessageByTypeAndIdentifier($community->getAdminMember()->id, sfContext::getInstance()->getUser()->getMemberId(), 'community_taking_over', $v['id']);

      $list[$k]['list']['Message'] = array('text' => $obj->body);
    }

    return $list;
  }
}
