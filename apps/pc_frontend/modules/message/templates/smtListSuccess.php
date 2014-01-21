<?php use_helper('opAsset') ?>
<?php op_smt_use_stylesheet('/opMessagePlugin/css/smt-message.css', 'last') ?>
<?php op_smt_use_javascript('/opMessagePlugin/js/jquery.timeago.js', 'last') ?>
<?php op_smt_use_javascript('/opMessagePlugin/js/smt-message.js', 'last') ?>
<input type="hidden" value="0" name="messageKeyId" id="messageKeyId" />
<input type="hidden" value="" name="prevPage" id="prevPage" />
<input type="hidden" value="" name="nextPage" id="nextPage" />
<input type="hidden" value="" name="page" id="page" />
<input type="hidden" value="" name="memberIds" id="memberIds" />
<div class="row">
  <div class="gadget_header span12"><?php echo __('Read messages') ?></div>
</div>

<div id="message-wrapper-parent">
  <p id="no-message" style="display: none;"><?php echo __('There are no messages') ?></p>
  <div id="first-loading" class="center">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
</div>

<div id="message-template" style="display: none;">
  <div class="message-wrapper row">
    <div class="span2 memberIcon">
    </div>
    <div class="span7">
      <p class="memberProfile"></p>
      <p class="lastMessage"></p>
    </div>
    <div class="span3 timeago">
      <p class="message-created-at"></p>
    </div>
  </div>
</div>

<ul class="pager" style="display: none;">
  <li class="previous">
    <a href="javascript:void(0)" id="messagePrevLink" style="display: none;">&larr; <?php echo __('Prev') ?></a>
  </li>
  <li class="next">
    <a href="javascript:void(0)" id="messageNextLink" style="display: none;"><?php echo __('Next') ?> &rarr;</a>
  </li>
</ul>
