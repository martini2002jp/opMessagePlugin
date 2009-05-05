<?php
$this->dispatcher->connect('routing.load_configuration', array('opMessagePluginRouting', 'listenToRoutingLoadConfigurationEvent'));

$this->dispatcher->connect('op_action.post_execute_friend_link', array('opRegisterMessage', 'listenToPostActionEventSendFriendLinkRequestMessage'));
