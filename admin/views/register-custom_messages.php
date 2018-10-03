			<form method="post" action="">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Custom Messages', 'uap');?></h3>
					<div class="inside">	
						
						<div style="display:inline-block;width: 45%;">
							<div>
								<div class="uap-labels-special"><?php _e('Error - Username is taken:', 'uap');?></div>
								<textarea name="uap_register_username_taken_msg" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_username_taken_msg']);?></textarea>
							</div>
							
							<div>
								<div class="uap-labels-special"><?php _e('Error - Invalid Username:', 'uap');?></div>
								<textarea name="uap_register_error_username_msg" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_error_username_msg']);?></textarea>
							</div>		
										
							<div>
								<div class="uap-labels-special"><?php _e('Error - Email is taken:', 'uap');?></div>
								<textarea name="uap_register_email_is_taken_msg" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_email_is_taken_msg']);?></textarea>
							</div>
							
							<div>
								<div class="uap-labels-special"><?php _e('Error - Invalid Email Address:', 'uap');?></div>
								<textarea name="uap_register_invalid_email_msg" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_invalid_email_msg']);?></textarea>
							</div>
							
							<div>
								<div class="uap-labels-special"><?php _e('Error - Email Addresses did not Match:', 'uap');?></div>
								<textarea name="uap_register_emails_not_match_msg" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_emails_not_match_msg']);?></textarea>
							</div>
											
							<div>
								<div class="uap-labels-special"><?php _e('Error - Password did not match:', 'uap');?></div>
								<textarea name="uap_register_pass_not_match_msg" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_pass_not_match_msg']);?></textarea>
							</div>	
							
							<div>
								<div class="uap-labels-special"><?php _e('Error - Password Only Characters and Digits:', 'uap');?></div>
								<textarea name="uap_register_pass_letter_digits_msg" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_pass_letter_digits_msg']);?></textarea>
							</div>							
						</div>
						
						<div style="display:inline-block;width: 45%;vertical-align:top;">
							<div>
								<div class="uap-labels-special"><?php _e('Error - Password Min Length:', 'uap');?></div>
								<textarea name="uap_register_pass_min_char_msg" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_pass_min_char_msg']);?></textarea>
								<div class="uap-dashboard-mini-msg-alert"><?php _e('Where {X} will be the minimum length of password.', 'uap');?></div>
							</div>								
							
							<div>
								<div class="uap-labels-special"><?php _e('Error - Password Characters, Digits and minimum one uppercase letter:', 'uap');?></div>
								<textarea name="uap_register_pass_let_dig_up_let_msg" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_pass_let_dig_up_let_msg']);?></textarea>
							</div>	
											
							<div>
								<div class="uap-labels-special"><?php _e('Error - Pending User:', 'uap');?></div>
								<textarea name="uap_register_pending_user_msg" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_pending_user_msg']);?></textarea>
							</div>	
							
							<div>
								<div class="uap-labels-special"><?php _e('Error - Empty Required Fields:', 'uap');?></div>
								<textarea name="uap_register_err_req_fields" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_err_req_fields']);?></textarea>
							</div>
							
							<div>
								<div class="uap-labels-special"><?php _e('Error - ReCaptcha:', 'uap');?></div>
								<textarea name="uap_register_err_recaptcha" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_err_recaptcha']);?></textarea>
							</div>		
			
							<div>
								<div class="uap-labels-special"><?php _e('Error - TOS:', 'uap');?></div>
								<textarea name="uap_register_err_tos" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_err_tos']);?></textarea>
							</div>					
							
							<div>
								<div class="uap-labels-special"><?php _e('Success Message:', 'uap');?></div>
								<textarea name="uap_register_success_meg" class="uap-dashboard-textarea"><?php echo uap_correct_text($data['metas']['uap_register_success_meg']);?></textarea>
							</div>													
						</div>	
									
						<div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" onClick="" class="button button-primary button-large" />
						</div>					
					</div>
				</div>							
			</form>		
</div>