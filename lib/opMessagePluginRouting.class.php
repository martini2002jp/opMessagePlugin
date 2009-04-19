<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * Message routing.
 *
 * @package    OpenPNE
 * @author     Maki TAKAHASHI <maki@jobweb.co.jp>
 */
class opMessagePluginRouting
{
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $routing = $event->getSubject();
    $routing->prependRoute('receiveList',
      new sfRoute(
        '/message/receiveList',
        array('module' => 'message', 'action' => 'list', 'type' => 'receive')
      )
    );
    $routing->prependRoute('sendList',
      new sfRoute(
        '/message/sendList',
        array('module' => 'message', 'action' => 'list', 'type' => 'send')
      )
    );
    $routing->prependRoute('draftList',
      new sfRoute(
        '/message/draftList',
        array('module' => 'message', 'action' => 'list', 'type' => 'draft')
      )
    );
    $routing->prependRoute('dustList',
      new sfRoute(
        '/message/dustList',
        array('module' => 'message', 'action' => 'list', 'type' => 'dust')
      )
    );


    $routing->prependRoute('readMessage',
      new sfRoute(
        '/message/read/:id',
        array('module' => 'message', 'action' => 'show', 'type' => 'receive'),
        array('id' => '\d+')
      )
    );
    $routing->prependRoute('readSendMessage',
      new sfRoute(
        '/message/check/:id',
        array('module' => 'message', 'action' => 'show', 'type' => 'send'),
        array('id' => '\d+')
      )
    );
    $routing->prependRoute('readDeletedMessage',
      new sfRoute(
        '/message/checkDelete/:id',
        array('module' => 'message', 'action' => 'show', 'type' => 'dust'),
        array('id' => '\d+')
      )
    );
    $routing->prependRoute('deleteReceiveMessage',
      new sfRoute(
        '/message/deleteReceiveMessage/:id',
        array('module' => 'message', 'action' => 'delete', 'type' => 'receiveList'),
        array('id' => '\d+')
      )
    );
    $routing->prependRoute('deleteSendMessage',
      new sfRoute(
        '/message/deleteSendMessage/:id',
        array('module' => 'message', 'action' => 'delete', 'type' => 'sendList'),
        array('id' => '\d+')
      )
    );
    $routing->prependRoute('deleteDustMessage',
      new sfRoute(
        '/message/deleteComplete/:id',
        array('module' => 'message', 'action' => 'delete', 'type' => 'dustList'),
        array('id' => '\d+')
      )
    );
  }
}
