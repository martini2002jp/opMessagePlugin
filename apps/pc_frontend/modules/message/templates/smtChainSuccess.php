<?php use_helper('opAsset', 'Javascript') ?>
<?php op_smt_use_stylesheet('/opMessagePlugin/css/smt-message.css', 'last') ?>
<?php op_smt_use_javascript('/opMessagePlugin/js/jquery.timeago.js', 'last') ?>
<?php op_smt_use_javascript('/opMessagePlugin/js/smt-message.js', 'last') ?>
<?php echo javascript_tag('
var memberId = '.$member->getId().';
');
?>
<div class="row">
  <div class="gadget_header span12"><?php echo __('Read messages') ?></div>
</div>

<?php if (25 <= count($messageList)): ?>
<div class="row">
  <hr class="toumei" />
  <div id="loading-more" class="center">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
  <div id="more" class="btn span12"><?php echo __('More') ?></div>
</div>
<?php endif ?>

<div id="message-wrapper-parent">
<?php if (false === $messageList || 1 > count($messageList) || is_null($messageList)): ?>
  <p id="no-message"><?php echo __('There are no messages') ?></p>
<?php else: ?>
<?php for ($i = count($messageList) - 1; $i >= 0; $i--): ?>
<?php if ($messageList[$i]['member_id'] === $member->getId()): ?>
<?php $thisLoopMember = $member ?>
<?php else: ?>
<?php $thisLoopMember = $myMember ?>
<?php endif ?>
<div class="message-wrapper row" data-message-id="<?php echo $messageList[$i]['id'] ?>">
  <div class="span2">
    <?php echo link_to(op_image_tag_sf_image($thisLoopMember->getImageFileName(), array('size' => '48x48')), '@obj_member_profile?id='.$thisLoopMember->getId()) ?>
  </div>
  <div class="span7">
    <p><?php echo link_to($thisLoopMember->getName(), '@obj_member_profile?id='.$thisLoopMember->getId()) ?></p>
    <p><?php echo op_auto_link_text($messageList[$i]['body']) ?></p>
  </div>
  <div class="span3">
    <p class="timeago" title="<?php echo $messageList[$i]['created_at'] ?>"></p>
  </div>

  <?php $images = $messageList[$i]->getMessageFile() ?>
  <?php if (count($images)): ?>
  </div>
  <div class="row">
    <ul class="photo">
      <?php foreach ($images as $image): ?>
      <li><a href="<?php echo sf_image_path($image->getFile()) ?>" target="_blank">
      <?php echo image_tag_sf_image($image->getFile(), array('size' => '76x76')) ?></a></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
<hr class="toumei" />
<?php endfor ?>
<?php endif ?>
</div>

<hr class="toumei" />
<div class="row">
  <div id="loading" class="center">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
</div>

<div id="submit-wrapper" class="row">
  <div class="span9">
    <form>
      <input type="file" name="message_image" id="message_image" />
      <textarea name="body" id="submit-message"></textarea>
    </form>
  </div>
  <div class="span3">
    <button id="do-submit" class="btn btn-primary" to-member="<?php echo $member->getId() ?>"><?php echo __('Send') ?></button>
  </div>
</div>

<div id="message-template" class="message-wrapper" style="display: none;">
  <div class="row">
    <div class="span2">
      <a class="member-link">
        <img class="member-image" />
      </a>
    </div>
    <div class="span7">
      <p>
        <a class="member-link"><span class="member-name"></span></a>
      </p>
      <p class="message-body"></p>
    </div>
    <div class="span3">
      <p class="timeago message-created-at"></p>
    </div>
  </div>
  <div class="row">
    <ul class="photo">
    </ul>
  </div>
</div>
