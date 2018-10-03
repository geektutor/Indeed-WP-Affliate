<div>
	<h2>Indeed Ultimate Affiliate Pro</h2>
	<label class="uap-edit-wp-user-label"><?php _e('Become Affiliate', 'uap');?></label>
	<div class="uap-edit-wp-user-status">
		<?php if ($data['is_affiliate']): ?>
			<?php _e('Already registered as Affiliate.', 'uap');?>
			<div style="margin-top: 10px;">
				<button type="button" class="button button-secondary" onclick="uap_make_affiliate_simple_user(<?php echo $data['id'];?>);"><?php _e('Remove from Affiliates list', 'uap');?></button>				
			</div>				
		<?php else:?>
			<button type="button" class="button button-secondary" onclick="uap_make_user_affiliate(<?php echo $data['id'];?>);"><?php _e('Make This User Affiliate', 'uap');?></button>	
		<?php endif?>
	</div>
</div>