<?php slot('op_sidemenu') ?>
<div class="parts pageNav">
<ul>
<?php if ($list_type == 'receive'): ?>
<li class="current"><?php echo __('Inbox') ?></li>
<?php else: ?>
<li><?php echo link_to(__('Inbox'), '@receiveList') ?></li>
<?php endif; ?>
<?php if ($list_type == 'send'): ?>
<li class="current"><?php echo __('Sent Message') ?></li>
<?php else: ?>
<li><?php echo link_to(__('Sent Message'), '@sendList') ?></li>
<?php endif; ?>
<?php if ($list_type == 'draft'): ?>
<li class="current"><?php echo __('Drafts') ?></li>
<?php else: ?>
<li><?php echo link_to(__('Drafts'), '@draftList') ?></li>
<?php endif; ?>
<?php if ($list_type == 'dust'): ?>
<li class="current"><?php echo __('Trash') ?></li>
<?php else: ?>
<li><?php echo link_to(__('Trash'), '@dustList') ?></li>
<?php endif; ?>
</ul>
</div>
<?php end_slot() ?>
