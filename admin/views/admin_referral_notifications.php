<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Referral Notifications', 'uap');?></h3>
		<div class="inside">
			
			<div class="row">
				<div class="col-xs-7">
					<h3><?php _e('Activate/Hold Referral Notifications', 'uap');?></h3>
					<p><?php _e('If this module is activated, admins have the option to receive instant notifications when an affiliate gets a new referral.', 'uap');?></p>
					<label class="uap_bp_account_page_enable" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_admin_referral_notifications_enable']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_admin_referral_notifications_enable');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="uap_admin_referral_notifications_enable" value="<?php echo $data['metas']['uap_admin_referral_notifications_enable'];?>" id="uap_admin_referral_notifications_enable" /> 
				</div>
			</div>
			<div class="uap-line-break"></div>
			<div class="row">
				<div class="col-xs-6">
					<h3><?php _e('Notification Subject', 'uap');?></h3>
					<input type="text" name="uap_admin_referral_notification_subject" value="<?php echo $data['metas']['uap_admin_referral_notification_subject'];?>" style="width: 100%;"/>
				</div>
			</div>
							
			<div class="row">
				<div class="col-xs-12">
					<h3><?php _e('Notification Content', 'uap');?></h3>
					<div class="uap-wp_editor" style="width:65%; display: inline-block; vertical-align: top;">
					<?php wp_editor(stripslashes($data['metas']['uap_admin_referral_notification_content']), 'uap_admin_referral_notification_content', array('textarea_name'=>'uap_admin_referral_notification_content', 'editor_height'=>400));?>
					</div>
					<div style="width: 33%; display: inline-block; vertical-align: top; padding-left:20px;">
						<?php echo "<h4>" . __('Referral details constants', 'uap') . "</h4>"; ?>
						<?php foreach ($data['notification_constants'] as $key=>$value) : ?>
							<div ><?php echo '<span style="font-weight:bold; color:#0bb586;">'.$value . '</span> : ' . $key;?></div>
						<?php endforeach; ?>
							<div ><?php echo '<span style="font-weight:bold; color:#0bb586;">' . __('WooCommerce Order Details', 'uap') . '</span> : {WOOCOMMERCE_ORDER_DETAILS}';?></div>
						<?php
						echo "<h4>" . __('Native Fields constants', 'uap') . "</h4>";
							$constants = array(	"{username}",
												"{first_name}",
												"{last_name}",
												"{user_id}",
												"{user_email}",
												"{account_page}",
												"{login_page}",
												"{blogname}",
												"{blogurl}",
												"{siteurl}",
												'{rank_id}',
												'{rank_name}',
							);
							$extra_constants = uap_get_custom_constant_fields();
							foreach ($constants as $v){
								?>
								<div><?php echo $v;?></div>
								<?php 	
							}
							echo "<h4>" . __('Custom Fields constants', 'uap') . "</h4>";
							foreach ($extra_constants as $k=>$v){
								?>
								<div><?php echo $k;?></div>
								<?php 	
							}
							
						?>	
					</div>					
				</div>
			</div>						
			
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>	
							
		</div>
	</div>
</form>