<?php use_helper('Date', 'Text'); ?>
<?php include_partial('message/sidemenu', array('list_type' => $sf_request->getParameter('type'))) ?>
<div class="dparts messageDetailBox">
<div class="parts">
<div class="partsHeading"><h3><?php echo __('Message') ?></h3></div>
<?php 
/* @todo 前後のメッセージ
({if $prev_c_message_id || $next_c_message_id})
<div class="block prevNextLinkLine">
({if $prev_c_message_id})<p class="prev"><a href="({t_url m=pc a=page_h_message})&amp;target_c_message_id=({$prev_c_message_id})&amp;({if $box == 'outbox'})box=({$box})({else})jyusin_c_message_id=({$prev_c_message_id})({/if})">«前を表示</a></p>({/if})
({if $next_c_message_id})<p class="next"><a href="({t_url m=pc a=page_h_message})&amp;target_c_message_id=({$next_c_message_id})&amp;({if $box == 'outbox'})box=({$box})({else})jyusin_c_message_id=({$next_c_message_id})({/if})">次を表示»</a></p>({/if})
</div>
({/if})
*/
?>
<table>
<tr>
<th>
<?php if ($message->getIsSender()): ?>
<?php echo __('To') ?>
<?php else: ?>
<?php echo __('From') ?>
<?php endif; ?></th>
<td>
<?php 
if ($message->getIsSender()):
    $sendLists = $message->getMessageSendLists();
    foreach ($sendLists as $sendTo): 
        echo link_to($sendTo->getMember()->getName(), '@member_profile?id='.$sendTo->getMemberId())."<br />";
    endforeach;
else:
    echo link_to($message->getMember()->getName(), '@member_profile?id='.$message->getMemberId());
endif;
?>
</td>
</tr>
<tr>
<th>日付</th>
<td><?php echo format_datetime($message->getCreatedAt(), 'f') ?></td>
</tr><tr>
<th>件名</th>
<td><?php echo $message->getSubject() ?></td>
</tr>
</table>
<div class="block">
<?php $images = $message->getMessageFiles() ?>
<?php if (count($images)): ?>
<?php foreach ($images as $image): ?>
<span class="photo">
<?php echo image_tag_sf_image($image->getFile(), array('size' => '120x120')) ?>
</span>
<?php endforeach; ?>
<?php endif; ?>
<p class="text">
<?php echo auto_link_text(nl2br($message->getBody()), 'urls', array('target' => '_blank'), true, 57) ?>
</p>
</div>

<?php /* @todo 添付ファイル
({if $c_message.filename && $smarty.const.OPENPNE_USE_FILEUPLOAD})
<div class="block attachFile"><ul>
<li><a href="({t_url m=pc a=do_h_message_file_download})&amp;target_c_message_id=({$c_message.c_message_id})&amp;sessid=({$PHPSESSID})">({$c_message.original_filename})</a></li>
</ul></div>
({/if})
*/ ?>
<div class="operation">
<ul class="moreInfo button">
<?php if ($sf_request->getParameter('type') == 'dust'): ?>
<li><?php echo button_to(__('Restore'), 'message/restore?id='.$deletedId)?></li>
<?php endif; ?>
<li><?php echo button_to(__('Delete'), $deleteButton) ?></li>
<?php if ($sf_request->getParameter('type') != 'dust' && !$message->getIsSender()): ?>
<li><?php echo button_to(__('Reply'), 'message/reply?id='.$message->getId()) ?></li>
</ul>
<?php else:?>
</ul>
<?php endif; ?>
</div>
</div>
</div>
