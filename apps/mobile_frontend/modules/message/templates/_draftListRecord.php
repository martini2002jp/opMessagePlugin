<?php echo sprintf('%s (%s)',
  link_to($message->getSubject(), 'message/edit?id='. $message->getId()),
  $message->getSendTo()->getName()
);
