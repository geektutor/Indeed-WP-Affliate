<?php
if (defined('WORDPRESS_SOCIAL_LOGIN_ABS_PATH')){
	$is_set = TRUE;
}

?>
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Wp Social Login Integration', 'uap');?></h3>
		
		<div class="inside">
			
			<?php if (empty($is_set)):?>
				<?php echo __("Wp Social Login it's not active on Your system. You can find ", 'uap') . '<a href="https://wordpress.org/plugins/wordpress-social-login/" target="_blank">' . __('here', 'uap') . '.</a>';?>
			<?php else:?>
			
				<div class="uap-form-line">
					<h2><?php _e('Activate/Hold', 'uap');?></h2>					
					<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<?php $checked = ($data['metas']['uap_wp_social_login_on']) ? 'checked' : '';?>
						<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_wp_social_login_on');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="uap_wp_social_login_on" value="<?php echo $data['metas']['uap_wp_social_login_on'];?>" id="uap_wp_social_login_on" /> 												
				</div>	
				
				<div class="uap-form-line">
					<h2><?php _e('Login/Register Redirect', 'uap');?></h2>
					<div class="uap-form-line">
						<select name="uap_wp_social_login_redirect_page">
							<?php foreach ($data['pages'] as $post_id=>$title):?>
								<?php $selected = ($data['metas']['uap_wp_social_login_redirect_page']==$post_id) ? 'selected' : '';?>
								<option value="<?php echo $post_id;?>" <?php echo $selected;?> ><?php echo $title;?></option>
							<?php endforeach;?>
						</select>
					</div>					
				</div>
				
				<div class="uap-form-line">				
					<h2><?php _e('WP Role', 'uap');?></h2>
					<div style="font-weight:bold"><?php _e('Predefined Wordpress Role Assign to new Users:', 'uap');?></div>
					<select name="uap_wp_social_login_default_role">
					<?php 
						if (empty($data['metas']['uap_wp_social_login_default_role'])){
							$data['metas']['uap_wp_social_login_default_role'] = get_option('uap_register_new_user_role');
						}
						$roles = uap_get_wp_roles_list();
						if ($roles){
							foreach ($roles as $k=>$v){
								$selected = ($data['metas']['uap_wp_social_login_default_role']==$k) ? 'selected' : '';
								?>
									<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
								<?php 
							}	
						}
					?>
					</select>							
				</div>	
	
				<div class="uap-form-line">
					<?php 
						if (empty($data['metas']['uap_wp_social_login_default_rank'])){
							$data['metas']['uap_wp_social_login_default_rank'] = get_option('uap_register_new_user_rank');										
						}
					?>					
					<div style="font-weight:bold"><?php _e('Rank assigned to new User', 'uap');?></div>
					<select name="uap_wp_social_login_default_rank">
						<option value="0" <?php if ($data['metas']['uap_wp_social_login_default_rank']==-1) echo 'selected';?> ><?php _e('None', 'uap');?></option>
						<?php 
							if ($data['ranks'] && count($data['ranks'])){
								foreach ($data['ranks'] as $key=>$object){
								?>
									<option value="<?php echo $object->id;?>" <?php if ($data['metas']['uap_wp_social_login_default_rank']==$object->id) echo 'selected';?> ><?php echo $object->label;?></option>
								<?php 
								}
							}
						?>
					</select>						
				</div>						

				
				<h4>Wordpress Social Login - Shortocode:</h4>
				<div class="uap-user-list-shortcode-wrapp">	
					<div class="content-shortcode" style="padding:15px; text-align:center;">						
						<span class="the-shortcode" style="font-size: 16px;">[wordpress_social_login]</span>
					</div>						
				</div>

				<div>
					<a href="<?php echo admin_url('options-general.php?page=wordpress-social-login');?>"><?php _e('Wordpress Social Login - Settings', 'uap');?></a>
				</div>
																													
				<div class="uap-submit-form" style="margin-top: 20px;"> 
					<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
				</div>		
					
			<?php endif;?>
							
		</div>		
	</div>
</form>

<?php


