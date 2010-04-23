<?php op_mobile_page_title(__($title), __('Message')) ?>
<?php if ($message->getIsSender()): ?>
<?php echo __('To') ?>：
<?php foreach ($message->getMessageSendLists() as $sendTo): ?>
<?php if ($sendTo->getMemberId()): ?>
<?php echo link_to($sendTo->getMember()->getName(), 'member/profile?id='.$sendTo->getMemberId()) ?>
<?php else: ?>
<?php $isDeletedMember = true; ?>
<?php endif; ?><br>
<?php endforeach; ?>
<?php else: ?>
<?php echo __('From') ?>：
<?php if ($message->getMemberId()): ?>
<?php echo link_to($message->getMember()->getName(), 'member/profile?id='.$message->getMemberId()) ?>
<?php else: ?>
<?php $isDeletedMember = true; ?>
<?php endif; ?><br>
<?php endif; ?>

<?php echo __('Created At') ?>：
<?php echo op_format_date($message->getCreatedAt(), 'XDateTime'); ?><br>

<?php echo __('Subject') ?>：
<?php echo $message->getSubject() ?>

<?php $images = $message->getMessageFile() ?>
<?php if (count($images)): ?>
<br>
<?php foreach ($images as $image): ?>
<?php echo link_to(__('View Image'), sf_image_path($image->getFile(), array('size' => '120x120', 'f' => 'jpg'))) ?><br>
<?php endforeach; ?>
<?php endif; ?>

<hr>

<?php echo nl2br($message->getBody()) ?>

<hr>

<?php if ($messageType == 'dust'): ?>
<?php echo $form->renderFormTag(url_for('message/restore?id='.$deletedId)); ?>
<?php echo $form ?>
<input type="submit" value="<?php echo __('Restore') ?>" />
</form>
<?php endif; ?>

<?php echo $form->renderFormTag(url_for($deleteButton)); ?>
<?php echo $form ?>
<input type="submit" value="<?php echo __('Delete') ?>"  />
</form>

<?php if ($messageType != 'dust' && !$message->getIsSender() && !$isDeletedMember): ?>
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
