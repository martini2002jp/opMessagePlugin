<?php op_mobile_page_title(__($title), __('Message')) ?>
<?php if ($message->getIsSender()): ?>
<?php echo __('To') ?>：
<?php foreach ($message->getMessageSendLists() as $sendTo): ?>
<?php echo link_to($sendTo->getMember()->getName(), 'member/profile?id='.$sendTo->getMemberId()) ?><br>
<?php endforeach; ?>
<?php else: ?>
<?php echo __('From') ?>：
<?php echo link_to($message->getMember()->getName(), 'member/profile?id='.$message->getMemberId()) ?><br>
<?php endif; ?>

<?php echo __('Created At') ?>：
<?php echo op_format_date($message->getCreatedAt(), 'XDateTime'); ?><br>

<?php echo __('Subject') ?>：
<?php echo $message->getSubject() ?>

<hr>

<?php echo nl2br($message->getBody()) ?>

<hr>

<?php if ($messageType == 'dust'): ?>
<?php echo link_to(__('Restore'), 'message/restore?id='.$deletedId) ?><br>
<?php endif; ?>

<?php echo link_to(__('Delete'), $deleteButton) ?>

<?php if ($messageType != 'dust' && !$message->getIsSender()): ?>
<br><?php echo link_to(__('Reply'), 'message/reply?id='.$message->getId()) ?>
<?php endif; ?>

<hr>


<?php if ($messageType == 'receive'): ?>
<?php echo link_to(__('Inbox'), '@receiveList') ?>
<?php elseif ($messageType == 'send'): ?>
<?php echo link_to(__('Sent Message'), '@sendList') ?>
<?php else : ?>
<?php echo link_to(__('Trash'), '@dustList') ?>
<?php endif; ?>
