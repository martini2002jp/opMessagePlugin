<?php use_helper('opAsset', 'Javascript', 'opMessage') ?>
<?php op_smt_use_stylesheet('/opMessagePlugin/css/smt-message.css', sfWebResponse::LAST) ?>
<?php op_smt_use_stylesheet('/opMessagePlugin/css/bootstrap-popover.css', sfWebResponse::LAST) ?>
<?php op_smt_use_javascript('/opMessagePlugin/js/jquery.timeago.js', sfWebResponse::LAST) ?>
<?php op_smt_use_javascript('/opMessagePlugin/js/smt-message.js', sfWebResponse::LAST) ?>
<?php op_smt_use_javascript('/opMessagePlugin/js/bootstrap.min.js', sfWebResponse::LAST) ?>
<input type="hidden" value="<?php echo $member->getId() ?>" name="toMember" id="messageToMember" />
<div class="row">
  <div class="gadget_header span12"><?php echo __('Read messages') ?></div>
</div>

<?php if ($pager->hasOlderPage()): ?>
<div class="row">
  <hr class="toumei" />
  <div id="loading-more" class="center" style="display: none;">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
  <div id="more" class="btn span12"><?php echo __('More') ?></div>
</div>
<?php endif ?>

<div id="message-wrapper-parent">
<?php if (!$pager->getNbResults()): ?>
  <p id="no-message"><?php echo __('There are no messages') ?></p>
<?php else: ?>
<?php foreach ($pager->getResults() as $messageList): ?>
<?php $message = $messageList->getSendMessageData() ?>
<div class="time-info-wrapper" data-created-at-date="<?php echo get_formatted_date($message->getCreatedAt()) ?>" style="display: none;">
  <p class="time-info"><i class="icon-time"></i><?php echo get_formatted_date($message->getCreatedAt()) ?></p>
</div>
<?php $thisLoopMember = $message->getMember() ?>
  <div class="timeago"><p class="message-created-at <?php echo $message->getIsSender($sf_user->getMemberId()) ? 'left' : 'right' ?>" title="<?php echo $message->getCreatedAt() ?>"></p></div>
  <div class="message-wrapper row popover <?php echo $message->getIsSender($sf_user->getMemberId()) ? 'left' : 'right' ?> show" data-message-id="<?php echo $messageList->getId() ?>">
    <div class="arrow"></div>
    <h3 class="popover-title"><?php echo $thisLoopMember->getName() ?></h3>
    <div class="popover-content">
      <div class="body">
      <p><?php echo op_auto_link_text($message->getBody()) ?></p>
      <?php $images = $message->getMessageFile() ?>
      <?php if (count($images)): ?>
      <ul class="photo">
        <?php foreach ($images as $image): ?>
        <li><a href="<?php echo sf_image_path($image->getFile()) ?>" target="_blank">
        <?php echo image_tag_sf_image($image->getFile(), array('size' => '76x76')) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
      </div>
    </div>
  </div>
<?php endforeach ?>
  <div class="clearfix"></div>
<?php endif ?>
</div>

<div class="row">
  <div id="loading" class="center" style="display: none;">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
</div>

<?php if (!$isBlocked): ?>
<div id="submit-wrapper" class="row">
  <div class="span9">
    <form enctype="multipart/form-data" method="post" id="send-message-form">
      <input type="file" name="message_image" id="message_image" />
      <textarea name="body" id="submit-message"></textarea>
    </form>
  </div>
  <div class="span3">
    <button id="do-submit" class="btn btn-primary"><?php echo __('Send') ?></button>
  </div>
</div>
<?php endif ?>

<div id="message-template" style="display: none;">
  <div class="time-info-wrapper" style="display: none;">
    <p class="time-info"><i class="icon-time"></i></p>
  </div>
  <div class="timeago"><p class="message-created-at"></p></div>
  <div class="message-wrapper row popover">
    <div class="arrow"></div>
    <h3 class="popover-title"></h3>
    <div class="popover-content">
      <div class="body">
        <p class="message-body"></p>
        <ul class="photo"></ul>
      </div>
    </div>
  </div>
</div>
