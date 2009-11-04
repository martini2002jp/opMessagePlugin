<?php
$this->dispatcher->connect('routing.load_configuration', array('opMessagePluginRouting', 'listenToRoutingLoadConfigurationEvent'));

$this->dispatcher->connect('op_action.post_execute_friend_link', array('opRegisterMessage', 'listenToPostActionEventSendFriendLinkRequestMessage'));
$this->dispatcher->connect('op_action.post_execute_community_join', array('opRegisterMessage', 'listenToPostActionEventSendCommunityJoiningRequestMessage'));

$this->dispatcher->connect('op_confirmation.list_filter', array('opConfirmationMessageFilter', 'filterFriendLink'));
$this->dispatcher->connect('op_confirmation.list_filter', array('opConfirmationMessageFilter', 'filterCommunityJoiningRequest'));
