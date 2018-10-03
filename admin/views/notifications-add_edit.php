<?php if (empty($data['id'])):?>
	<script>
		jQuery(document).ready(function(){
			uap_return_notification();
		});
	</script>
<?php endif;?>

			<form action="<?php echo $data['form_action_url'];?>" method="post">
<div class="uap-wrapper">
		<div class="uap-stuffbox">
				<h3 class="uap-h3"><?php _e('Add/Edit Notification', 'uap');?></h3>
				<div class="inside">
					<div class="uap-form-line">
						<label class="uap-labels-special"><?php _e('Action:', 'uap');?></label>
						<select name="type" id="notf_type" onChange="uap_return_notification();">
						<?php foreach ($data['actions_available'] as $k=>$v):?>
							<?php 
								switch ($k){
									case 'admin_user_register':
										echo ' <optgroup label="' . __('Register Process', 'uap') . '">';
										break;	
									case 'affiliate_payment_fail':
										echo ' <optgroup label="' . __('Payments', 'uap') . '">';
										break;
									case 'reset_password_process':
										echo ' <optgroup label="' . __('Password', 'uap') . '">';
										break;	
									case 'affiliate_account_approve':
										echo ' <optgroup label="' . __('Profile Update', 'uap') . '">';
										break;	
									case 'admin_on_aff_change_rank':
										echo ' <optgroup label="' . __('Admin', 'uap') . '">';
										break;
									case 'email_check':
										echo ' <optgroup label="' . __('Double E-mail Verification', 'uap') . '">';
										break;							
								}
							?>
							<?php $selected = ($k==$data['type']) ? 'selected' : '';?>
							<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
							<?php 
								switch ($k){
									case 'register':
									case 'affiliate_payment_complete':
									case 'change_password':
									case 'rank_change':
									case 'admin_affiliate_update_profile':
									case 'email_check_success':
										echo '</optgroup>';
										break;	
								}
							?>							
						<?php endforeach;?>
						</select>						
					</div>
					<div class="uap-form-line">
						<label class="uap-labels-special"><?php _e('Target Rank:', 'uap')?></label>
						<select name="rank_id">
						<?php foreach ($data['ranks_available'] as $k=>$v):?>						
							<?php $selected = ($k==$data['rank_id']) ? 'selected' : '';?>
							<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
						<?php endforeach;?>			
						</select>
					</div>					
					<div class="uap-form-line">
						<label class="uap-labels-special"><?php _e('Subject:', 'uap');?></label>
						<input type="text" value="<?php echo $data['subject'];?>" name="subject" id="notf_subject" />
					</div>	
					<div class="uap-form-line">			
						<label  class="uap-labels-special" style="vertical-align: top;"><?php _e('Content:', 'uap');?></label>	
						<div style="padding-left: 5px; width: 70%;display:inline-block;">
							<?php wp_editor( $data['message'], 'notf_message', array('textarea_name'=>'message', 'quicktags'=>TRUE) );?>
						</div>	
						<div style="width: 20%; display: inline-block; vertical-align: top;margin-left: 10px; color: #333;">
						<?php 
							$constants = array(	"{username}",
												"{first_name}",
												"{last_name}",
												"{user_id}",
												'{affiliate_id}',
												"{user_email}",
												"{account_page}",
												"{login_page}",
												"{blogname}",
												"{blogurl}",
												"{siteurl}",
												'{rank_id}',
												'{rank_name}',
												'{NEW_PASSWORD}',
												'{password_reset_link}',
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
						
						<div class="uap-clear"></div>			
							
					<div class="uap-submit-form">
						<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large">
					</div>										
				</div>	
			</div>
		</div>			
						<!-- PUSHOVER -->
						<?php if ($indeed_db->is_magic_feat_enable('pushover')):?>
							<div class="uap-stuffbox">
							<h3 class="uap-h3"><?php _e('Pushover Notification', 'uap');?></h3>
								<div class="inside">							
									<div class="iump-form-line">
										<span class="uap-labels-special"><?php _e('Pushover Notification', 'uap');?></span>	
										<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
											<?php $checked = (empty($data['pushover_status'])) ? '' : 'checked';?>
											<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#pushover_status');" <?php echo $checked;?> />
											<div class="switch" style="display:inline-block;"></div>
										</label>
										<input type="hidden" name="pushover_status" value="<?php echo @$data['pushover_status'];?>" id="pushover_status" /> 				
									</div>				
									
									<div class="uap-form-line" style="padding: 10px 0px 0px 5px;">
										<label class="uap-labels-special"><?php _e('Pushover Message:', 'uap');?></label>
										<textarea name="pushover_message" style="width: 90%; height: 100px;" onBlur="uap_check_field_limit(1024, this);"><?php echo stripslashes(@$data['pushover_message']);?></textarea>
										<div style="color: #777; font-weight:bold;font-size: 11px; font-style: italic;"><?php _e('Only Plain Text and up to ', 'uap');?><span style="color:#000;">1024</span><?php _e(' characters are available!', 'uap');?></div>
									</div>	
									<div class="uap-submit-form">
										<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large">
									</div>											
								</div>
							</div>
						<?php else :?>
							<input type="hidden" name="pushover_message" value=""/>
							<input type="hidden" name="pushover_status" value=""/>										
						<?php endif;?>
						<!-- PUSHOVER -->					
				
				<input type="hidden" name="status" value="1" />
				<input type="hidden" name="id" value="<?php echo $data['id'];?>" />	

	</form>



</div><!-- end of uap-dashboard-wrap -->