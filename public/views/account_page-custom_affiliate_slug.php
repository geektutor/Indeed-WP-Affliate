<div class="uap-ap-wrap">

<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>

	<form method="post" action="" id="uap_campaign_form">
		<div class="uap-ap-field">
			<label class="uap-ap-label uap-special-label"><?php _e('Slug:', 'uap');?></label>
			<input type="text" name="uap_affiliate_custom_slug" value="<?php echo $data['uap_affiliate_custom_slug'];?>" class="uap-public-form-control "/>
		</div>
		<div class="uap-ap-field">
			<input type="submit" name="save" value="<?php _e('Save', 'uap');?>" class="button button-primary button-large" />
		</div>	
		<?php if (isset($saved)):?>
			<?php if ($saved===FALSE):?>
				<div><?php _e('An error has occurred', 'uap');?></div>
			<?php else :?>
				<div><?php _e('Saved', 'uap');?></div>				
			<?php endif;?>
		<?php endif;?>
	</form>
</div>