<?php
$options = array('form' => array($form));
$title = __('Compose Message');
$options['url'] = 'message/sendToFriend';
$options['button'] = __('Send');
$options['isMultipart'] = true;
include_box('formMessage', $title, '', $options);
?>
