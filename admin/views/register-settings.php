<div class="uap-page-title">
	Ultimate Affiliates Pro - <span class="second-text"><?php _e('Register Form', 'uap');?></span>
</div>
<div class="uap-stuffbox">
	<div class="uap-shortcode-display">
		[uap-register]
	</div>
</div>		
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Design', 'uap');?></h3>
		<div class="inside">
			<div class="uap-register-select-template">
				<?php 
					$templates = array(  
										'uap-register-9'=>'(#9) '.__('Radius Theme', 'uap'),
										'uap-register-14'=>'(#14) '.__('Ultimate Member', 'uap'),
										'uap-register-10'=>'(#10) '.__('BootStrap Theme', 'uap'),
										'uap-register-8'=>'(#8) '.__('Simple Border Theme', 'uap'),
										'uap-register-13'=>'(#13) '.__('Double BootStrap Theme', 'uap'), 
										'uap-register-11'=>'(#11) '.__('Double Simple Border Theme', 'uap'),
										'uap-register-12'=>'(#12) '.__('Dobule Radius Theme', 'uap'),  
										'uap-register-7'=>'(#7) '.__('BackBox Theme', 'uap'), 
										'uap-register-6'=>'(#6) '.__('Double Strong Theme', 'uap'), 
										'uap-register-5'=>'(#5) '.__('Strong Theme', 'uap'),
										'uap-register-4'=>'(#4) '.__('PlaceHolder Theme', 'uap'), 
										'uap-register-3'=>'(#3) '.__('Blue Box Theme', 'uap'), 
										'uap-register-2'=>'(#2) '.__('Basic Theme', 'uap'),
										'uap-register-1'=>'(#1) '.__('Standard Theme', 'uap')
					);
				?>
				<?php _e('Register Template:', 'uap');?>
				<select name="uap_register_template" id="uap_register_template" onChange="uap_register_preview();" style="min-width:400px">
					<?php 
						foreach ($templates as $k=>$v){
						?>
							<option value="<?php echo $k;?>" <?php if ($k==$data['metas']['uap_register_template']) echo 'selected';?> >
								<?php echo $v;?>
							</option>
						<?php 	
						}
						?>
				</select>						
			</div>
										
			<div style="padding: 5px;">
				<div id="register_preview"></div>
			</div>
						
			<div style="margin-top: 15px;">
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>	
									
		</div>
	</div>
						
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Settings', 'uap');?></h3>
		<div class="inside">
			<div class="uap-form-line">
				<h2><?php _e('Rank Settings', 'uap');?></h2>
				<div class="uap-form-line" id="rank_assign_to_user" style="padding:0;border-bottom: none; margin-top:10px;" >
					<div style="font-weight:bold"><?php _e('Default rank assigned to a new user', 'uap');?></div>
					<select name="uap_register_new_user_rank">
						<option value="0" <?php if($data['metas']['uap_register_new_user_rank']==0)echo 'selected';?> ><?php _e('None', 'uap');?></option>
					<?php 
					$ranks = $indeed_db->get_rank_list();
					if (!empty($ranks) && is_array($ranks)){
						foreach ($ranks as $id=>$v){
						?>
							<option value="<?php echo $id;?>" <?php if ($data['metas']['uap_register_new_user_rank']==$id) echo 'selected';?> ><?php echo $v;?></option>
						<?php 
						}
					}
					?>
					</select>						
				</div>			
		</div>
		<div class="uap-form-line">						
			<h2><?php _e('WP Role', 'uap');?></h2>
			<div style="font-weight:bold"><?php _e('Predefined Wordpress role assigned to new users:', 'uap');?></div>
				<select name="uap_register_new_user_role">
				<?php 
					$roles = uap_get_wp_roles_list();
					if ($roles){
						foreach ($roles as $k=>$v){
							$selected = ($data['metas']['uap_register_new_user_role']==$k) ? 'selected' : '';
							?>
							<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
							<?php 
						}	
					}
				?>
				</select>
			<p><?php _e('If the "Pending" Role is set the user cannot login until the Admin manually approves the user.', 'uap');?></p>													
		</div>
		
		<div class="uap-form-line">						
			<div style="font-weight:bold"><?php _e('After Approve Wordpress Role:', 'uap');?></div>
				<select name="uap_after_approve_role">
				<?php 
					$roles = uap_get_wp_roles_list();
					if ($roles){
						foreach ($roles as $k=>$v){
							$selected = ($data['metas']['uap_after_approve_role']==$k) ? 'selected' : '';
							?>
							<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
							<?php 
						}	
					}
				?>
				</select>											
		</div>		
													
		<div class="uap-form-line">
								
		<h2><?php _e('Form Settings', 'uap');?></h2>
		<div style="font-weight:bold"><?php _e('Password Minimum Length', 'uap');?></div>
		<input type="number" value="<?php echo $data['metas']['uap_register_pass_min_length'];?>" name="uap_register_pass_min_length" min="4"/>				
							
			<div style="margin-top:15px;">
				<div style="font-weight:bold"><?php _e('Password Strength Options', 'uap');?></div>
					<select name="uap_register_pass_options">
						<option value="1" <?php if ($data['metas']['uap_register_pass_options']==1)echo 'selected';?> ><?php _e('Standard', 'uap');?></option>
						<option value="2" <?php if ($data['metas']['uap_register_pass_options']==2)echo 'selected';?> ><?php _e('Characters and digits', 'uap');?></option>
						<option value="3" <?php if ($data['metas']['uap_register_pass_options']==3)echo 'selected';?> ><?php _e('Characters, digits, minimum one uppercase letter', 'uap');?></option>
					</select>
				</div>			
			</div>	
			<div class="uap-form-line">
				<h2><?php _e('Admin Notification', 'uap');?></h2>
					<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<?php $checked = ($data['metas']['uap_register_admin_notify']) ? 'checked' : '';?>
						<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_register_admin_notify');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="uap_register_admin_notify" value="<?php echo $data['metas']['uap_register_admin_notify'];?>" id="uap_register_admin_notify" /> 				
					<?php _e('Notify admin address on every new registration', 'uap');?>
					<p><?php _e('When a new user has registered, the WP Admin is notified using the default Email Admin address set into current WordPress Instance', 'uap');?></p>
			</div>	
									
			<div class="uap-form-line">	
				<h2><?php _e('Other Settings', 'uap');?></h2>
				<div style="margin-bottom: 15px;">							
					<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<?php $checked = ($data['metas']['uap_register_auto_login']) ? 'checked' : '';?>
						<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_register_auto_login');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
					</label>
						<input type="hidden" name="uap_register_auto_login" value="<?php echo $data['metas']['uap_register_auto_login'];?>" id="uap_register_auto_login" /> 	
						<?php _e('Auto Login after Registration', 'uap');?>							
				</div>
			</div>
																
			<div style="margin-top: 15px;">
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>			
		</div>
	</div>	
		
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Terms & Conditions (TOS) Label', 'uap');?></h3>
					<div class="inside">
					  <div  class="uap-form-line">
						<input type="text" name="uap_register_terms_c" value="<?php echo uap_correct_text($data['metas']['uap_register_terms_c']);?>" style="min-width:350px"/>
					  </div>	
						<div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" onClick="" class="button button-primary button-large" />
						</div>					
					</div>
				</div>
				
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Custom CSS:', 'uap');?></h3>
					<div class="inside">		
						<div>
							<textarea name="uap_register_custom_css" id="uap_register_custom_css" class="uap-dashboard-textarea" onBlur="uapRegisterLockerPreview();"><?php 
							echo stripslashes($data['metas']['uap_register_custom_css']);
							?></textarea>
						</div>
						<div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>					
					</div>
				</div>	
								
			</form>
			<script>
				jQuery(document).ready(function(){
					uap_register_preview();
				});
			</script>					
		