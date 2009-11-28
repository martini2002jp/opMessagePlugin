<?php op_mobile_page_title(__($title), __('Message')) ?>
<?php if ($message->getIsSender()): ?>
<?php echo __('To') ?>：
<?php foreach ($message->getMessageSendLists() as $sendTo): ?>
<?php if ($sendTo->getMember()): ?>
<?php echo link_to($sendTo->getMember()->getName(), 'member/profile?id='.$sendTo->getMemberId()) ?>
<?php endif; ?><br>
<?php endforeach; ?>
<?php else: ?>
<?php echo __('From') ?>：
<?php if ($message->getMember()): ?>
<?php echo link_to($message->getMember()->getName(), 'member/profile?id='.$message->getMemberId()) ?>
<?php endif; ?><br>
<?php endif; ?>

<?php echo __('Created At') ?>：
<?php echo op_format_date($message->getCreatedAt(), 'XDateTime'); ?><br>

<?php echo __('Subject') ?>：
<?php echo $message->getSubject() ?>

<hr>

<?php echo nl2br($message->getBody()) ?>

<hr>

<?php if ($messageType == 'dust'): ?>
<?php echo link_to(__('Restore'), 'message/restore?id='.$deletedId.'&_csrf_token='.$form->getCSRFToken()) ?><br>
<?php endif; ?>

<?php echo link_to(__('Delete'), $deleteButton.'&_csrf_token='.$form->getCSRFToken()) ?>

<?php if ($messageType != 'dust' && !$message->getIsSender()): ?>
<br><?php echo link_to(__('Reply'), 'message/reply?id='.$message->getId()) ?>
<?php endif; ?>

<hr>


<?php if ($messageType == 'receive'): ?>
<?php echo link_to(__('Inbox'), '@receiveList') ?>
<?php elseif ($messageType == 'send'): ?>
<?php echo link_to(__('Sent Messages'), '@sendList') ?>
<?php else : ?>
<?php echo link_to(__('Trash'), '@dustList') ?>
<?php endif; ?>
