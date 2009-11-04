<?php echo $fromMember->getName() ?> さんからフレンドリンク申請メッセージが届いています。

<?php if ($message): ?>
メッセージ:
<?php echo $message ?>
<?php endif; ?>


この要請について承認待ちリストから認証また拒否を選択してください。
<?php echo url_for('@confirmation_list?category=friend_confirm', true) ?>
