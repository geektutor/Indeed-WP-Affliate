<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Roles allowed to enter into WordPress Admin Dashboard:', 'uap');?></h3>
		<div class="inside">		
			<div class="uap-half-block">		
				<div class="uap-form-line uap-access-opacity">
					<span class="uap-access-label"><?php _e('Administrator', 'uap');?></span>
					<label class="uap_label_shiwtch uap-access-switch">
						<input type="checkbox" class="uap-switch" onClick="" checked disabled/>
						<div class="switch uap-inline-block"></div>
					</label>			
				</div>
				<?php 
					$roles = get_editable_roles();
					if (!empty($roles['administrator'])){
						unset($roles['administrator']);
					}
					if (!empty($roles['pending_user'])){
						unset($roles['pending_user']);	
					}							
					$count = count($roles) + 1;
					$break = ceil($count/2);
					$i = 1;
					foreach ($roles as $role=>$arr){
					?>
						<div class="uap-form-line">
							<span class="uap-access-label"><?php echo $arr['name'];?></span>
							<label class="uap_label_shiwtch uap-access-switch">
								<?php $checked = (in_array($role, $meta_values)) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uap_make_inputh_string(this, '<?php echo $role;?>', '#uap_dashboard_allowed_roles');" <?php echo $checked;?>/>
								<div class="switch uap-inline-block"></div>
							</label>			
						</div>							
					<?php 	
					$i++;
						if ($count>7 && $i==$break){
						?>
						</div>
						<div class="uap-half-block">	
						<?php 	
						}
					}///end of foreach
				?>
			</div>
			<input type="hidden" name="uap_dashboard_allowed_roles" id="uap_dashboard_allowed_roles" value="<?php echo $meta_value;?>" />
			<div class="uap-wrapp-submit-bttn iump-submit-form">
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>				
		</div>
	</div>
</div>