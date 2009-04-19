<?php op_mobile_page_title(__($title), 'Message List') ?>
<?php if ($pager->getNbResults()): ?>
<center><?php op_include_pager_total($pager) ?></center>
<?php if ($form->hasGlobalErrors()): ?>
<font color="#FF0000"><?php echo $form->renderGlobalErrors() ?></font>
<?php endif; ?>
<form action="<?php echo url_for($page_url) ?>" method="post">
<?php echo $form->renderHiddenFields(); ?>
<?php $_list = array() ?>
<?php foreach ($pager->getResults() as $message): ?>
<?php $_list[] = $form['message_ids['.$message->getId().']']->render().
op_format_date($message->getCreatedAt(), 'XDateTime')."<br>".
get_partial($message_type.'ListRecord', array('message' => $message)); ?>
<?php endforeach; ?>
<?php op_include_list('messageList', $_list, array()); ?>
</form>
<center><?php op_include_pager_navigation($pager, '@'.$message_type.'List?page=%d', array('is_total' => false)) ?></center>
<?php else: ?>
<?php echo __('There are no message.') ?><br><br>
<?php endif; ?>
<?php include_partial('message/menu', array('messageType' => $message_type)) ?>
