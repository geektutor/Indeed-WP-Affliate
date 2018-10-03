<?php 
$pages = uap_get_all_pages();
//getting pages
?>	
<div class="uap-wrapper">	
			<form action="" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Default Redirects', 'uap');?></h3>
					<div class="inside">							
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('After LogOut:', 'uap');?></span>
							<select name="uap_general_logout_redirect">
								<option value="-1" <?php if($data['metas']['uap_general_logout_redirect']==-1)echo 'selected';?> ><?php _e('Do Not Redirect', 'uap');?></option>
								<?php 
									$pages_arr = $pages + uap_get_redirect_links_as_arr_for_select();
									if ($pages_arr){
										foreach ($pages_arr as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_logout_redirect']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>	
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_logout_redirect']);?>						
						</div>	
						
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('After Registration:', 'uap');?></span>
							<select name="uap_general_register_redirect">
								<option value="-1" <?php if($data['metas']['uap_general_register_redirect']==-1)echo 'selected';?> ><?php _e('Do Not Redirect', 'uap');?></option>
								<?php 
									$pages_arr = $pages + uap_get_redirect_links_as_arr_for_select();
									if ($pages_arr){
										foreach ($pages_arr as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_register_redirect']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>		
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_register_redirect']);?>							
						</div>		
						
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('After Login:', 'uap');?></span>
							<select name="uap_general_login_redirect">
								<option value="-1" <?php if($data['metas']['uap_general_login_redirect']==-1)echo 'selected';?> ><?php _e('Do Not Redirect', 'uap');?></option>
								<?php 
									$pages_arr = $pages + uap_get_redirect_links_as_arr_for_select();
									if ($pages_arr){
										foreach ($pages_arr as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_login_redirect']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>	
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_login_redirect']);?>									
						</div>										
		
						<div class="uap-submit-form">
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>									
					</div>
				</div>	
				
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Extra Redirects', 'uap');?></h3>
					<div class="inside">	
						
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('Account Page - Non Logged Affiliates', 'uap');?></span>
							<select name="uap_general_account_page_no_logged_redirect">
								<option value="-1" <?php if($data['metas']['uap_general_account_page_no_logged_redirect']==-1)echo 'selected';?> ><?php _e('Do Not Redirect', 'uap');?></option>
								<?php 
									$pages_arr = $pages + uap_get_redirect_links_as_arr_for_select();
									if ($pages_arr){
										foreach ($pages_arr as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_account_page_no_logged_redirect']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>	
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_account_page_no_logged_redirect']);?>									
						</div>
						
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('Login Page - Affiliates Logged', 'uap');?></span>
							<select name="uap_general_login_page_logged_users_redirect">
								<option value="-1" <?php if($data['metas']['uap_general_login_page_logged_users_redirect']==-1)echo 'selected';?> ><?php _e('Do Not Redirect', 'uap');?></option>
								<?php 
									$pages_arr = $pages + uap_get_redirect_links_as_arr_for_select();
									if ($pages_arr){
										foreach ($pages_arr as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_login_page_logged_users_redirect']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>	
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_login_page_logged_users_redirect']);?>									
						</div>						

						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('Register Page - Affiliates Logged', 'uap');?></span>
							<select name="uap_general_register_page_logged_users_redirect">
								<option value="-1" <?php if($data['metas']['uap_general_register_page_logged_users_redirect']==-1)echo 'selected';?> ><?php _e('Do Not Redirect', 'uap');?></option>
								<?php 
									$pages_arr = $pages + uap_get_redirect_links_as_arr_for_select();
									if ($pages_arr){
										foreach ($pages_arr as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_register_page_logged_users_redirect']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>	
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_register_page_logged_users_redirect']);?>									
						</div>	

						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('LogOut Page - Non Logged Affiliates', 'uap');?></span>
							<select name="uap_general_logout_page_non_logged_users_redirect">
								<option value="-1" <?php if($data['metas']['uap_general_logout_page_non_logged_users_redirect']==-1)echo 'selected';?> ><?php _e('Do Not Redirect', 'uap');?></option>
								<?php 
									$pages_arr = $pages + uap_get_redirect_links_as_arr_for_select();
									if ($pages_arr){
										foreach ($pages_arr as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_logout_page_non_logged_users_redirect']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>	
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_logout_page_non_logged_users_redirect']);?>									
						</div>	

						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('Lost Password Page - Non Logged Affiliates', 'uap');?></span>
							<select name="uap_general_lost_pass_page_logged_users_redirect">
								<option value="-1" <?php if($data['metas']['uap_general_lost_pass_page_logged_users_redirect']==-1)echo 'selected';?> ><?php _e('Do Not Redirect', 'uap');?></option>
								<?php 
									$pages_arr = $pages + uap_get_redirect_links_as_arr_for_select();
									if ($pages_arr){
										foreach ($pages_arr as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_lost_pass_page_logged_users_redirect']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>	
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_lost_pass_page_logged_users_redirect']);?>									
						</div>	
																		
						<div class="uap-submit-form">
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>							
					</div>
				</div>			
				
			</form>
</div>			