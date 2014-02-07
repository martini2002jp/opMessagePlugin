<?php use_helper('opAsset') ?>
<?php op_smt_use_stylesheet('/opMessagePlugin/css/smt-message.css', 'last') ?>
<?php op_smt_use_javascript('/opMessagePlugin/js/jquery.timeago.js', 'last') ?>
<?php op_smt_use_javascript('/opMessagePlugin/js/smt-message.js', 'last') ?>
<?php if (count($messageLists)): ?>
<div class="row">
  <div class="gadget_header span12"><?php echo __('Read messages') ?></div>
</div>
<?php foreach ($messageLists as $messageList): ?>
<?php $member = $messageList->getPartnerMember() ?>
<?php $message = $messageList->getSendMessageData() ?>
<div class="message-wrapper row<?php if ($messageList->hasUnreadMessage()): ?> message-unread<?php endif ?>">
  <div class="span2">
    <?php echo link_to(op_image_tag_sf_image($member->getImageFileName(), array('size' => '48x48')), '@obj_member_profile?id='.$member->getId()) ?>
  </div>
  <div class="span7">
    <p><?php echo link_to($member->getName(), '@obj_member_profile?id='.$member->getId()) ?></p>
    <p><?php echo link_to($message->getSubject(), '@messageChain?id='.$member->getId().'#submit-wrapper') ?></p>
  </div>
  <div class="span3">
    <p class="timeago" title="<?php echo $message->getCreatedAt() ?>"></p>
  </div>
</div>
<hr class="toumei" />
<?php endforeach ?>
<?php else: ?>
  <?php echo __('There are no messages') ?>
<?php endif ?>
