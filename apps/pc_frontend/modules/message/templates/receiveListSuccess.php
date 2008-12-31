<?php include_partial('message/sidemenu', array('list_type' => 'receive')) ?>
<?php include_partial('message/list', array('message_type' => 'receive', 'pager' => $pager, 'form' => $form)) ?>