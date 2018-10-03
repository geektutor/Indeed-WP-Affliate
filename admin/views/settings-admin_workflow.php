<div class="uap-wrapper">
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Admin Workflow Settings', 'uap');?></h3>
		<div class="inside">
					
			<div class="uap-inside-item">
				<div class="row">
					<div class="col-xs-4">
						<h3><?php _e('Updates', 'uap');?></h3>
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('Update Affiliates Rank:', 'uap');?></span>
							<select name="uap_update_ranks_interval" class="form-control m-bot15"><?php 
								$values = array(
													'hourly' => __('Hourly', 'uap'),
													'twicedaily' => __('At every 12hours', 'uap'),
													'daily' => __('Daily', 'uap'),
								);
								foreach ($values as $k=>$v){
									$selected = ($data['metas']['uap_update_ranks_interval']==$k) ? 'selected' : '';
									?>
									<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
									<?php 
								}
							?></select>
						</div>	
						
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('Update Payments Status:', 'uap');?></span>
							<select name="uap_update_payments_status" class="form-control m-bot15"><?php 
								$values = array(
													'hourly' => __('Hourly', 'uap'),
													'twicedaily' => __('At every 12hours', 'uap'),
													'daily' => __('Daily', 'uap'),
								);
								foreach ($values as $k=>$v){
									$selected = ($data['metas']['uap_update_payments_status']==$k) ? 'selected' : '';
									?>
									<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
									<?php 
								}
							?></select>
						</div>
					</div>		
				</div>
			</div>
					
			<div class="uap-line-break"></div>	
			
			<div class="uap-inside-item">
				<div class="row">
					<div class="col-xs-12">
						<h3><?php _e('Keep Referral Status as Unverified', 'uap');?></h3>
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e("Don't change the Referral Status to Verified", 'uap');?></span>
							<div class="uap-form-line">
								<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
									<?php $checked = ($data['metas']['uap_workflow_referral_status_dont_automatically_change']) ? 'checked' : '';?>
									<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_workflow_referral_status_dont_automatically_change');" <?php echo $checked;?> />
									<div class="switch" style="display:inline-block;"></div>
								</label>
								<input type="hidden" name="uap_workflow_referral_status_dont_automatically_change" value="<?php echo $data['metas']['uap_workflow_referral_status_dont_automatically_change'];?>" id="uap_workflow_referral_status_dont_automatically_change" /> 												
							</div>	
						</div>	
					</div>
				
					<div class="col-xs-12">
						<h3><?php _e('Show Dashboard Notifications', 'uap');?></h3>
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e("New Affiliates & Referrals", 'uap');?></span>
							<div class="uap-form-line">
								<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
									<?php $checked = ($data['metas']['uap_admin_workflow_dashboard_notifications']) ? 'checked' : '';?>
									<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_admin_workflow_dashboard_notifications');" <?php echo $checked;?> />
									<div class="switch" style="display:inline-block;"></div>
								</label>
								<input type="hidden" name="uap_admin_workflow_dashboard_notifications" value="<?php echo $data['metas']['uap_admin_workflow_dashboard_notifications'];?>" id="uap_admin_workflow_dashboard_notifications" /> 												
							</div>	
						</div>	
					</div>					
					
				</div>
			</div>															
																								
			<div class="uap-submit-form">
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>	
		
		</div>
	</div>
</form>
</div>

<?php


