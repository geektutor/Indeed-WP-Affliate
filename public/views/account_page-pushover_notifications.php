<div class="uap-ap-wrap">
	<?php if (!empty($data['title'])):?>
		<h3><?php echo do_shortcode($data['title']);?></h3>
	<?php endif;?>
	<?php if (!empty($data['content'])):?>
		<p><?php echo do_shortcode($data['content']);?></p>
	<?php endif;?>
	<form method="post" action="">
		<div class="uap-form-line-register uap-form-text">
			<label style="font-weight: bold;"><?php _e('User Token', 'uap');?></label>
			<input type="text" name="uap_pushover_token" value="<?php echo $data['uap_pushover_token'];?>"/>
		</div>
		<div class="uap-submit-form">
			<input type="submit" value="<?php _e('Save', 'uap');?>" name="indeed_submit" class="uap-submit-bttn-fe" />
		</div>
	</form>
</div>
