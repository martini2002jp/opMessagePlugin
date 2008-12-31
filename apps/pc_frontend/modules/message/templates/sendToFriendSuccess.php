<?php 
if ($message->getIsSend() == 1) : 
  $title = 'メッセージを送る';
  $body = '送信完了しました。';
else:
  $title = 'メッセージを下書き保存';
  $body = '下書きメッセージを保存しました。';
endif;
?>
<?php include_box('formMessage', $title, $body) ?>
<?php if ($message->getIsSend() == 1) : ?>
<?php echo link_to('送信済みメッセージ一覧', 'message/sendList') ?>
<?php else : ?>
<?php echo link_to('下書きメッセージ一覧', 'message/draftList') ?>
<?php endif; ?>