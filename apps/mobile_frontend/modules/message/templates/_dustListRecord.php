<?php echo sprintf('%s (%s)',
  link_to($message->getSubject(), '@readDeletedMessage?id='. $message->getMessageSendListId()),
  $message->getSendFromOrTo()->getName()
);
