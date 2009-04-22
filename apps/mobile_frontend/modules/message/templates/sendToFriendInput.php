<?php op_mobile_page_title(__('Compose Message')) ?>
<?php if ($sendMember): ?>
<?php echo __('To') ?>:
<?php echo link_to($sendMember->getName(), 'member/profile?id='.$sendMember->getId()) ?>
<?php endif; ?>
<?php echo $form->renderFormTag(url_for('message/sendToFriend'), array('method' => 'POST')) ?>
<?php echo $form ?>
<input type="submit" value="<?php echo __('Send') ?>"><br>
<input type="submit" value="<?php echo __('Draft') ?>" name="is_draft">
</form>
