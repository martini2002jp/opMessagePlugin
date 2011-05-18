<?php echo op_format_date($message->getCreatedAt(), 'XDateTime') ?><br>
<?php echo sprintf('%s (%s)',
  ($message->getSendTo()->getId()) ? link_to(op_truncate($message->getSubject(), 28), 'message/edit?id='. $message->getId()) : op_truncate($message->getSubject(), 28),
  op_link_to_member($message->getSendTo())
);
