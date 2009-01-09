<?php use_helper('Date', 'JavascriptBase'); ?>
<?php echo javascript_tag("
function checkAll() {
    var sm = document.delete_message;
    var len = sm.elements.length;
    for (var i = 0; i < len; i++) {
        sm.elements[i].checked = true;
    }
    return false;
}
function clearAll() {
    var sm = document.delete_message;
    var len = sm.elements.length;
    for (var i = 0; i < len; i++) {
        sm.elements[i].checked = false;
    }
    return false;
}
") ?>
<?php 
switch ($message_type):
  case 'receive':
    $title = __('Inbox');
    $page_url = "message/receiveList";
    $sender_title = __('From');
    break;
  case 'send':
    $title = __('Sent Message');
    $page_url = "message/sendList";
    $sender_title = __('To');
    break;
  case 'draft':
    $title = __('Drafts');
    $page_url = "message/draftList";
    $sender_title = __('To');
    break;
  case 'dust':
    $title = __('Trash');
    $page_url = "message/dustList";
    $sender_title = __('From/To');
    break;
endswitch;
?>
<div class="dparts searchResultList"><div class="parts">
<div class="partsHeading"><h3><?php echo $title ?></h3></div>
<?php if ($pager->getNbResults()): ?>
<div class="pagerRelativeMulti">
<?php if ($message_type == 'receive'): ?>
<p class="icons"> 
<span>
<?php echo image_tag('/plugins/opMessagePlugin/images/icon_mail_4.gif', array('alt' => __('Replied'))) ?>
<?php echo __('Replied') ?>
</span> 
</p>
<?php endif; ?>
</div>
<div class="pagerRelative">
<p class="number"><?php echo pager_navigation($pager, $page_url."?page=%d"); ?></p>
</div>

<form action="<?php echo url_for($page_url) ?>" method="post" name="delete_message">
<?php echo $form["object_name"] ?>
<?php echo $form["object_name"]->renderError() ?>
<table> 
<col class="status" /> 
<col class="delete" /> 
<col class="target" /> 
<col class="title" /> 
<col class="date" /> 
<tr> 
<th></th> 
<th class="delete"><?php echo __('Delete') ?></th> 
<th><?php echo $sender_title ?></th> 
<th><?php echo __('Subject') ?></th> 
<th><?php echo __('Created At') ?></th> 
</tr> 
<?php foreach ($pager->getResults() as $message): ?>
<?php 
switch ($message_type):
  case 'receive':
    $form_delete = $form["message_ids[".$message->getId()."]"];
    $form_delete_error = $form["message_ids[".$message->getId()."]"]->renderError();
    $sender = $message->getSendMessageData()->getMember()->getName();
    $detail_title = $message->getSendMessageData()->getSubject();
    $detail_url = '@readMessage?id='.$message->getMessageId();
    break;
  case 'send':
    $form_delete = $form["message_ids[".$message->getId()."]"];
    $form_delete_error = $form["message_ids[".$message->getId()."]"]->renderError();
    $sender = $message->getSendTo();
    $detail_title = $message->getSubject();
    $detail_url = '@readSendMessage?id='.$message->getId();
    break;
  case 'draft':
    $form_delete = $form["message_ids[".$message->getId()."]"];
    $form_delete_error = $form["message_ids[".$message->getId()."]"]->renderError();
    $sender = $message->getSendTo();
    $detail_title = $message->getSubject();
    $detail_url = 'message/edit?id='.$message->getId();
    break;
  case 'dust':
    $form_delete = $form["message_ids[".$message->getId()."]"];
    $form_delete_error = $form["message_ids[".$message->getId()."]"]->renderError();
    $sender = $message->getSender();
    $detail_title = $message->getSubject();
    $detail_url = '@readDeletedMessage?id='.$message->getViewMessageId();
    break;
endswitch;
?>
<tr <?php if ($message_type == 'receive' && $message->getIsRead() == 0): ?>class="unread"<?php endif; ?>> 
<td class="status"><span>
<?php if ($message_type == 'send'): ?>
<?php echo image_tag('/plugins/opMessagePlugin/images/icon_mail_3.gif') ?>
<?php elseif ($message_type == 'draft'): ?>
<?php echo image_tag('/plugins/opMessagePlugin/images/icon_mail_1.gif') ?>
<?php elseif ($message_type == 'dust'): ?>
  <?php if ($message->getIcon() && $message->getIconAlt()): ?>
  <?php echo image_tag('/plugins/opMessagePlugin/images/'.$message->getIcon(), array('alt' => $message->getIconAlt())) ?>
  <?php endif; ?>
<?php elseif ($message->getIsHensin() == 1): ?>
<?php echo image_tag('/plugins/opMessagePlugin/images/icon_mail_4.gif', array('alt' => __('Replied'))) ?>
<?php elseif ($message->getIsRead() == 1): ?>
<?php echo image_tag('/plugins/opMessagePlugin/images/icon_mail_2.gif', array('alt' => __('Open'))) ?>
<?php else: ?>
<?php echo image_tag('/plugins/opMessagePlugin/images/icon_mail_1.gif', array('alt' => __('Unopened'))) ?>
<?php endif; ?>
</span></td> 
<td><span>
<?php echo $form_delete ?>
<?php echo $form_delete_error ?>
</span></td> 
<td><span><?php echo $sender ?></span></td> 
<td><span><?php echo link_to($detail_title, $detail_url)?></span></td> 
<td><span><?php echo format_datetime($message->getCreatedAt(), 'f') ?></span></td> 
</tr> 
<?php endforeach; ?>
</table> 
<div class="pagerRelative">
<p class="number"><?php echo pager_navigation($pager, $page_url.'?page=%d'); ?></p>
</div>
<div class="operation"> 
<p>
<?php echo link_to_function('全てをチェック', "checkAll()", array('onkeypress' => 'checkAll();')) ?> / 
<?php echo link_to_function('全てのチェックをはずす', "clearAll()", array('onkeypress' => 'clearAll();')) ?>
</p> 
<ul class="moreInfo button"> 
<?php if ($message_type == 'dust'): ?>
<li>
<input type="submit" class="input_submit" name="restore" value="<?php echo __('Restore') ?>" />
</li>
<?php endif; ?>
<li>
<input type="submit" class="input_submit" value="<?php echo __('Delete') ?>" />
</li> 
</ul> 
</div> 
 
</form> 
<?php else: ?>
<div class="body">
<?php echo __('There are no messages') ?>
</div>
<?php endif; ?>
</div></div>

