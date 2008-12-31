<?php include_partial('message/sidemenu', array('list_type' => 'send')) ?>
<?php include_partial('message/list', array('message_type' => 'send', 'pager' => $pager, 'form' => $form)) ?>