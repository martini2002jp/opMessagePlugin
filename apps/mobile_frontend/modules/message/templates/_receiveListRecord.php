<?php echo op_format_date($message->getCreatedAt(), 'XDateTime') ?> 
<?php if ($message->getIsHensin()): ?>
<font color="#0000FF">(<?php echo __('Replied') ?>)</font>
<?php elseif ($message->getIsRead()): ?>
(<?php echo __('Open') ?>)
<?php else: ?>
<font color="#FF0000">(<?php echo __('Unopened') ?>)</font>
<?php endif; ?><br>
<?php echo sprintf('%s (%s)',
  link_to(op_truncate($message->getSubject(), 28), '@readReceiveMessage?id='. $message->getId()),
  ($message->getSendFrom()->getId()) ? $message->getSendFrom()->getName() : ''
); ?>
