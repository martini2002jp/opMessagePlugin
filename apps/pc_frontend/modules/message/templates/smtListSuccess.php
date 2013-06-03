<?php use_helper('opAsset') ?>
<?php op_smt_use_stylesheet('/opMessagePlugin/css/smt-message.css', 'last') ?>
<?php op_smt_use_javascript('/opMessagePlugin/js/jquery.timeago.js', 'last') ?>
<?php op_smt_use_javascript('/opMessagePlugin/js/smt-message.js', 'last') ?>
<?php if (!isset($memberList) || is_null($memberList) || false === $memberList): ?>
  <?php echo __('There are no messages') ?>
<?php else: ?>

<div class="row">
  <div class="gadget_header span12"><?php echo __('Read messages') ?></div>
</div>
<?php foreach ($memberList as $member): ?>
<?php if ($member): ?>
<?php $message = Doctrine::getTable('SendMessageData')->getLatestMemberMessage($member->getId()) ?>
<?php if (!is_null($message) && 0 < count($message)): ?>
<?php if (Doctrine::getTable('MessageSendList')->checkUnreadMessage($member->getId())): ?>
<div class="message-wrapper row message-unread">
<?php else: ?>
<div class="message-wrapper row">
<?php endif ?>
  <div class="span2">
    <?php echo link_to(op_image_tag_sf_image($member->getImageFileName(), array('size' => '48x48')), '@obj_member_profile?id='.$member->getId()) ?>
  </div>
  <div class="span7">
    <p><?php echo link_to($member->getName(), '@obj_member_profile?id='.$member->getId()) ?></p>
    <p><?php echo link_to($message[0]['subject'], '@messageChain?id='.$member->getId().'#submit-wrapper') ?></p>
  </div>
  <div class="span3">
    <p class="timeago" title="<?php echo $message[0]['created_at'] ?>"></p>
  </div>
</div>
<hr class="toumei" />
<?php endif ?>
<?php endif ?>
<?php endforeach ?>
<?php endif ?>
