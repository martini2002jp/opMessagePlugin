<?php echo $fromMember->getName() ?> さんから<?php echo $community->name ?><?php echo $op_term['community'] ?>への参加希望メッセージが届いています。

<?php if ($message): ?>
メッセージ:
<?php echo $message ?>
<?php endif; ?>


この要請について、承認待ちリストから承認または拒否を選択してください。
<?php echo url_for('@confirmation_list?category=community_confirm', true) ?>
