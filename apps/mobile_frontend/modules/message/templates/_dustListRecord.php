<?php echo op_format_date($message->getCreatedAt(), 'XDateTime') ?><br>
<?php echo sprintf('%s (%s)',
  link_to(op_truncate($message->getSubject(), 28), '@readDustMessage?id='. $message->getViewMessageId()),
  ($message->getSendFromOrTo()->getId()) ? $message->getSendFromOrTo()->getName() : ''
);
