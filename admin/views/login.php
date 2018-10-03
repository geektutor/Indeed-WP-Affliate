		<div class="uap-page-title">Ultimate Affialiates Pro - 
			<span class="second-text"><?php _e('Login Form', 'uap');?></span>
		</div>
			<div class="uap-stuffbox">
				<div class="uap-shortcode-display">
					[uap-login-form]
				</div>
			</div>		
			<form action="" method="post" >		
				<div style="display: inline-block; width: 50%;">
					<div class="uap-stuffbox">
						<h3 class="uap-h3"><?php _e('Showcase Display', 'uap');?></h3>
						<div class="inside">				
						  <div class="uap-register-select-template">	
						  <?php _e('Login Template:', 'uap');?>
							<select name="uap_login_template" id="uap_login_template" onChange="uap_login_preview();"  style="min-width:400px">
							<?php
								foreach ($data['login_templates'] as $k=>$value){
									echo '<option value="uap-login-template-'.$k.'"'. ($data['metas']['uap_login_template']=='uap-login-template-'.$k ? 'selected': '') .'>'.$value.'</option>';
								}
							?>
							</select>
						 </div>
						 <div style="padding: 5px;">	
							<div id="uap-preview-login"></div>
						</div>
							<div class="uap-wrapp-submit-bttn">
								<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
							</div>					
						</div>			
					</div>				
					
					
				</div>
			   <div style="display: inline-block; width: 45%; vertical-align: top;">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Additional Display Options', 'uap');?></h3>
					<div class="inside">
							<div class="uap-form-line uap-no-border">
									<input type="checkbox" class="uap-checkbox" onClick="check_and_h(this, '#uap_login_remember_me');uap_login_preview();" <?php if($data['metas']['uap_login_remember_me']==1) echo 'checked';?>/>
									<input type="hidden" name="uap_login_remember_me" value="<?php echo $data['metas']['uap_login_remember_me'];?>" id="uap_login_remember_me"/>			
									<span style="color: #21759b; font-weight:bold;"><?php _e('Remember Me', 'uap');?></span>
							</div>
							<div class="uap-form-line uap-no-border">
									<input type="checkbox" class="uap-checkbox" onClick="check_and_h(this, '#uap_login_register');uap_login_preview();" <?php if($data['metas']['uap_login_register']==1) echo 'checked';?>/>
									<input type="hidden" name="uap_login_register" value="<?php echo $data['metas']['uap_login_register'];?>" id="uap_login_register"/>			
									<span style="color: #21759b; font-weight:bold;"><?php _e('Register Link', 'uap');?></span>
							</div>
							<div class="uap-form-line uap-no-border">
									<input type="checkbox" class="uap-checkbox" onClick="check_and_h(this, '#uap_login_pass_lost');uap_login_preview();" <?php if($data['metas']['uap_login_pass_lost']==1) echo 'checked';?>/>
									<span style="color: #21759b; font-weight:bold;"><?php _e('Lost your password', 'uap');?></span>
									<input type="hidden" name="uap_login_pass_lost" value="<?php echo $data['metas']['uap_login_pass_lost'];?>" id="uap_login_pass_lost"/>
							</div>	
							<div class="uap-form-line uap-no-border">
									<input type="checkbox" class="uap-checkbox" onClick="check_and_h(this, '#uap_login_show_recaptcha');uap_login_preview();" <?php if($data['metas']['uap_login_show_recaptcha']==1) echo 'checked';?>/>
									<span style="color: #21759b; font-weight:bold;"><?php _e('Show ReCaptcha', 'uap');?></span>
									<input type="hidden" name="uap_login_show_recaptcha" value="<?php echo $data['metas']['uap_login_show_recaptcha'];?>" id="uap_login_show_recaptcha"/>
							</div>						
							<div class="uap-wrapp-submit-bttn">
								<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
							</div>						
						</div>
				  </div>
				  <div class="uap-stuffbox">
						<h3 class="uap-h3"><?php _e('Custom CSS', 'uap');?></h3>
						<div class="inside">			
							<textarea id="uap_login_custom_css" name="uap_login_custom_css" onBlur="uap_login_preview();" class="uap-dashboard-textarea"><?php echo stripslashes($data['metas']['uap_login_custom_css']);?></textarea>
							<div class="uap-wrapp-submit-bttn">
								<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
							</div>	
						</div>
					</div>
				</div>			
			</form>		
<script>
	jQuery(document).ready(function(){
		uap_login_preview();
	});
</script>			