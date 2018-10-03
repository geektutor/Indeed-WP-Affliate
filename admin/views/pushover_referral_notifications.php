<div class="uap-wrapper">
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('PushOver Referrals Notifications', 'uap');?></h3>
		<div class="inside">

			<div class="uap-form-line">
				<h2><?php _e('Activate/Hold PushOver Referrals Notifications', 'uap');?></h2>
				<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_pushover_referral_notifications_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_pushover_referral_notifications_enabled');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="uap_pushover_referral_notifications_enabled" value="<?php echo $data['metas']['uap_pushover_referral_notifications_enabled'];?>" id="uap_pushover_referral_notifications_enabled" /> 												
			</div>

			<div class="uap-submit-form" style="margin-top: 20px;">
				<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>

		</div>
	</div>


</form>

</div>
