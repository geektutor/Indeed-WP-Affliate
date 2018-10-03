<form action="" method="post" >	
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Messages:', 'uap');?></h3>
					<div class="inside">
						<div style="display: inline-block; width: 45%;">	
							<h2><?php _e('Login Messages', 'uap');?></h2>		
							<div>
								<div class="uap-labels-special"><?php _e('Successfully Message:', 'uap');?></div>
								<textarea name="uap_login_succes" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_login_succes']);?></textarea>
							</div>
							<div>
								<div class="uap-labels-special"><?php _e('Default message for pending users:', 'uap');?></div>
								<textarea name="uap_login_pending" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_login_pending']);?></textarea>
							</div>
							<div>
								<div class="uap-labels-special"><?php _e('Error Message:', 'uap');?></div>
								<textarea name="uap_login_error" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_login_error']);?></textarea>
							</div>
							<div>
								<div class="uap-labels-special"><?php _e('E-mail Pending:', 'uap');?></div>
								<textarea name="uap_login_error_email_pending" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_login_error_email_pending']);?></textarea>
							</div>							
						</div>
						
						<div style="display: inline-block; width: 45%;vertical-align: top;">
							<h2><?php _e('Reset Password Messages', 'uap');?></h2>	
							<div>
								<div class="uap-labels-special"><?php _e('Successfully Message:', 'uap');?></div>
								<textarea name="uap_reset_msg_pass_ok" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_reset_msg_pass_ok']);?></textarea>
							</div>
							
							<div>
								<div class="uap-labels-special"><?php _e('Error Message:', 'uap');?></div>
								<textarea name="uap_reset_msg_pass_err" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_reset_msg_pass_err']);?></textarea>
							</div>
							
							<div>
								<div class="uap-labels-special"><?php _e('Error Message:', 'uap');?></div>
								<textarea name="uap_login_error_on_captcha" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_login_error_on_captcha']);?></textarea>
							</div>
														
						</div>
										
						<div class="uap-wrapp-submit-bttn">
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>	
					</div>
				</div>	
</form>