<?php echo sprintf('%s (%s)',
  link_to($message->getSubject(), '@readMessage?id='. $message->getId()),
  $message->getSendFrom()->getName()
);
