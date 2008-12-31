<?php

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
