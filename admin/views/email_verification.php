<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('E-mail Verification', 'uap');?></h3>
		<div class="inside">
												
			<div class="uap-form-line">	
				<h2><?php _e('Activate/Hold Email Verification', 'uap');?></h2>
				<p><?php _e('Requires the email address for new Affiliates to be verified before they will be able to login. If the email address is not confirmed, the user account may be automatically deleted after a certain time.', 'uap');?></p>
				<div style="margin-bottom: 15px;">							
					<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<?php $checked = ($data['metas']['uap_register_double_email_verification']) ? 'checked' : '';?>
						<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_register_double_email_verification');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="uap_register_double_email_verification" value="<?php echo $data['metas']['uap_register_double_email_verification'];?>" id="uap_register_double_email_verification" /> 	
					<p>
						<?php _e('Be sure that your notifications for “<strong>Double Email Verification</strong>” are properly set.', 'uap');?>							
					</p>					
				</div>
			</div>
						
		<div class="uap-inside-item">
			<div class="row">
				<div class="col-xs-4">			
					
					<div class="uap-form-line">
						<span class="uap-labels-special"><?php _e('Activation Link Expire Time:', 'uap');?></span>
							<select name="uap_double_email_expire_time">
								<?php 
									$arr = array(
															'-1' => 'Never',
															'900' => '15 Minutes',
															'3600' => '1 Hour',
															'43200' => '12 Hours',
															'86400' => '1 Day',
															);
									foreach ($arr as $k=>$v){
										?>
										<option value="<?php echo $k?>" <?php if ($k==$data['metas']['uap_double_email_expire_time']) echo 'selected';?> >
											<?php echo $v;?>
										</option>
										<?php 
									}
								?>
							</select>	
					</div>	
					
					<div class="uap-form-line">
						<span class="uap-labels-special"><?php _e('Success Redirect:', 'uap');?></span>
							<select name="uap_double_email_redirect_success">
								<option value="-1" <?php if($data['metas']['uap_double_email_redirect_success']==-1)echo 'selected';?> >...</option>
								<?php 
									if ($data['pages']){
										foreach ($data['pages'] as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_double_email_redirect_success']==$k) echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>
					</div>	
					
					<div class="uap-form-line">
						<span class="uap-labels-special"><?php _e('Error Redirect:', 'uap');?></span>
							<select name="uap_double_email_redirect_error">
								<option value="-1" <?php if($data['metas']['uap_double_email_redirect_error']==-1)echo 'selected';?> >...</option>
								<?php 
									if ($data['pages']){
										foreach ($data['pages'] as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_double_email_redirect_error']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>
					</div>	
					
					<div class="uap-form-line">
						<span class="uap-labels-special"><?php _e('Delete User if is not verified:', 'uap');?></span>
							<select name="uap_double_email_delete_user_not_verified">
								<?php 
									$arr = array(
															'-1' => 'Never',
															'1' => 'After 1 day',
															'7' => 'After 7 days',
															'14' => 'After 14 days',
															'30' => 'After 30 days',
															);
									foreach ($arr as $k=>$v){
										?>
										<option value="<?php echo $k?>" <?php if ($k==$data['metas']['uap_double_email_delete_user_not_verified']) echo 'selected';?> >
											<?php echo $v;?>
										</option>
										<?php 
									}
								?>
							</select>
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