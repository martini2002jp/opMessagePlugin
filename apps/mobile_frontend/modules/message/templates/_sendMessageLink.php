<?php if ($id !== $sf_user->getMemberId()): ?>
<?php echo link_to(__('Send Message'), 'message/sendToFriend?id='.$id) ?><br>
<?php endif; ?>
