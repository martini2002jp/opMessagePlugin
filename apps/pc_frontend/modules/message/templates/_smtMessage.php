<?php if (false !== $memberId): ?>
<hr class="toumei" />
<div class="row">
<div class="gadget_header span12"><?php echo __('Compose Message') ?></div>
</div>
<hr class="toumei" />
<?php echo link_to(__('Compose Message'), '@messageChain?id='.$memberId) ?>
<?php endif ?>
