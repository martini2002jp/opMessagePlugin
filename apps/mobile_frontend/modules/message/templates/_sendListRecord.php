<?php echo sprintf('%s (%s)',
  link_to($message->getSubject(), '@readSendMessage?id='. $message->getId()),
  $message->getSendTo()->getName()
);
