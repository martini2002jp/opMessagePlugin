<?php include_partial('message/sidemenu', array('list_type' => 'draft')) ?>
<?php include_partial('message/list', array('message_type' => 'draft', 'pager' => $pager, 'form' => $form)) ?>