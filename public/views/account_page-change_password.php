<div class="uap-ap-wrap">
<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>

<form action="" method="post" class="uap-change-password-form">
	<div class="uap-change-password-field-wrap">
		<label class="uap-change-password-label"><?php _e("Old Password", 'uap');?></label>
		<input class="uap-change-password-field" type="password" value="" name="old_pass" />
	</div>
	<div class="uap-change-password-field-wrap">
		<label class="uap-change-password-label"><?php _e("New Password", 'uap');?></label>
		<input class="uap-change-password-field" type="password" value="" name="pass1" />
	</div>
	<div class="uap-change-password-field-wrap">
		<label class="uap-change-password-label"><?php _e("Repeat New Password", 'uap');?></label>
		<input class="uap-change-password-field" type="password" value="" name="pass2" />
	</div>
	<div class="uap-change-password-field-wrap">
		<input type="submit" value="<?php _e("Save", 'uap');?>" name="update_pass" class="button button-primary button-large" />
	</div>
	<?php if (!empty($data['error'])) : ?>
		<div><?php echo $data['error'];?></div>
	<?php elseif (!empty($data['success'])) : ?>
		<div><?php echo $data['success'];?></div>
	<?php endif; ?>
</form>
</div>