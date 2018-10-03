<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Pushover Notifications', 'uap');?></h3>
		<div class="inside">
			
			<div class="uap-form-line">
				<h2><?php _e('Activate/Hold Pushover Notifications', 'uap');?></h2>
				<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_pushover_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_pushover_enabled');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="uap_pushover_enabled" value="<?php echo $data['metas']['uap_pushover_enabled'];?>" id="uap_pushover_enabled" /> 												
			</div>					
			<div class="uap-form-line">		
				<div class="row" style="margin-left:0px;">
					<div class="col-xs-4" style="margin-bottom: 10px;">
						<div class="input-group" style="margin:0px 0 15px 0;">
							<span class="input-group-addon" id="basic-addon1"><?php _e('App Token', 'uap');?></span>										
							<input type="text" class="uap-field-text-with-padding form-control" name="uap_pushover_app_token" value="<?php echo $data['metas']['uap_pushover_app_token'];?>" />
						</div>	
					</div>
				</div>
						
				<div class="row" style="margin-left:0px;">
					<div class="col-xs-4" style="margin-bottom: 10px;margin:0px 0 15px 0;">
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon1"><?php _e('Admin Personal User Token', 'uap');?></span>										
							<input type="text" class="uap-field-text-with-padding form-control" style="min-width:100px;" name="uap_pushover_admin_token" value="<?php echo $data['metas']['uap_pushover_admin_token'];?>" />
						</div>	
						<div style="font-size: 11px; color: #333; padding-left: 10px;">
							<?php _e("Use this to get 'Admin Notifications' on your own device.", 'uap');?>
						</div>			   		
					</div>
				</div>
									
				<div class="row" style="margin-left:0px;">
					<div class="col-xs-4" style="margin-bottom: 10px;">
						<div class="input-group" style="margin:0px 0 15px 0;">
							<span class="input-group-addon" id="basic-addon1"><?php _e('URL', 'uap');?></span>										
							<input type="text" class="uap-field-text-with-padding form-control" name="uap_pushover_url" value="<?php echo $data['metas']['uap_pushover_url'];?>" />
						</div>	
					</div>
				</div>
									
				<div class="row" style="margin-left:0px;">
					<div class="col-xs-4" style="margin-bottom: 10px;">
						<div class="input-group" style="margin:0px 0 15px 0;">
							<span class="input-group-addon" id="basic-addon1"><?php _e('URL Title', 'uap');?></span>										
							<input type="text" class="uap-field-text-with-padding form-control" name="uap_pushover_url_title" value="<?php echo $data['metas']['uap_pushover_url_title'];?>" />
						</div>	
					</div>
				</div>
				<div class="row" style="margin-left:0px;">
					<div style="font-size: 11px; color: #333; padding-left: 10px;">
						<ul class="uap-info-list">
							<li><?php echo __("1. Go to ", 'uap') . '<a href="https://pushover.net/" target="_blank">https://pushover.net/</a>' . __(" login with your credentials or sign up for a new account.", 'uap');?></li>
							<li><?php echo __("2. After that go to ", 'uap') . '<a href="https://pushover.net/apps/build" target="_blank">https://pushover.net/apps/build</a>' .  __(" and create new App.", 'uap');?></li>
							<li><?php _e("3. Set the type of App at 'Application'.", 'uap');?></li>
							<li><?php _e("4. Copy and paste API Token/Key.", 'uap');?></li>
						</ul>
					</div>
				</div>
			</div>																						
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="uap_save" class="button button-primary button-large" />
			</div>		
					
		</div>
	</div>
	
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Notification Sound', 'uap');?></h3>
		<div class="inside">	
			<div class="uap-form-line">
				<h4><?php _e('Default Sound for mobile notification', 'uap');?></h4>	
				<select name="uap_pushover_sound">
					<?php 
						$possible = array(
											'bike' => __('Bike', 'uap'),
											'bugle' => __('Bugle', 'uap'),
											'cash_register' => __('Cash Register', 'uap'),
											'classical' => __('Classical', 'uap'),
											'cosmic' => __('Cosmic', 'uap'),
											'falling' => __('Falling', 'uap'),
											'gamelan' => __('Gamelan', 'uap'),
											'incoming' => __('Incoming', 'uap'),
											'intermission' => __('Intermission', 'uap'),
											'magic' => __('Magic', 'uap'),
											'mechanical' => __('Mechanical', 'uap'),
											'piano_bar' => __('Piano Bar', 'uap'),
											'siren' => __('Siren', 'uap'),
											'space_alarm' => __('Space Alarm', 'uap'),
											'tug_boat' => __('Tug Boat', 'uap'),
						);
					?>
					<?php foreach ($possible as $k=>$v):?>
						<?php $selected = ($data['metas']['uap_pushover_sound']==$k) ? 'selected' : '';?>
						<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
					<?php endforeach;?>
 				</select>
			</div>				
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="uap_save" class="button button-primary button-large" />
			</div>							
		</div>
	</div>			

</form>