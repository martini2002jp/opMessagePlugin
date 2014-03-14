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

<div class="row">
  <div id="loading-more" class="center" style="display: none;">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
  <div id="more" class="btn span12" style="display: none;"><?php echo __('More') ?></div>
</div>

<div id="message-wrapper-parent">
  <p id="no-message" style="display: none;"><?php echo __('There are no messages') ?></p>
  <div id="first-loading" class="center">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
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
