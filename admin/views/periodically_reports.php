<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Periodically Reports', 'uap');?></h3>
		<div class="inside">
			
			<div class="row">
				<div class="col-xs-7">
					<h3><?php _e('Activate/Hold Periodical Reports', 'uap');?></h3>
					<p><?php _e('If this module is activated, affiliates will receive periodical reports about their affiliate account and rewards. Each affiliate may decide the frequency of these reports (daily, weekly, monthly) from his “Account Page”.', 'uap');?></p>
					<label class="woo_account_page_enable" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_periodically_reports_enable']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_periodically_reports_enable');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="uap_periodically_reports_enable" value="<?php echo $data['metas']['uap_periodically_reports_enable'];?>" id="uap_periodically_reports_enable" /> 
				</div>
			</div>
			<div class="uap-line-break"></div>
			<div class="row">
				<div class="col-xs-6">
					<h3><?php _e('Report Subject', 'uap');?></h3>
					<input type="text" name="uap_periodically_reports_subject" value="<?php echo $data['metas']['uap_periodically_reports_subject'];?>" style="width: 100%;" />
				</div>
			</div>
							
			<div class="row">
				<div class="col-xs-12">
					<h3><?php _e('Report Content', 'uap');?></h3>
					<div class="uap-wp_editor" style="width:65%; display: inline-block; vertical-align: top;">
					<?php wp_editor(stripslashes($data['metas']['uap_periodically_reports_content']), 'uap_periodically_reports_content', array('textarea_name'=>'uap_periodically_reports_content', 'editor_height'=>500));?>
					</div>
					<div style="width: 33%; display: inline-block; vertical-align: top; padding-left:20px;">
						<?php echo "<h4>" . __('Referral Reports constants', 'uap') . "</h4>"; ?>
						<?php foreach ($data['reports_constants'] as $key=>$value) : ?>
							<div><?php echo '<span style="font-weight:bold; color:#33b5e5 ;">'.$value . '</span> : ' . $key;?></div>
						<?php endforeach; ?>
						<?php
						echo "<h4>" . __('Native Fields constants', 'uap') . "</h4>";
							$constants = array(	"{username}",
												"{first_name}",
												"{last_name}",
												"{user_id}",
												"{user_email}",
												"{account_page}",
												"{login_page}",
												"{blogname}",
												"{blogurl}",
												"{siteurl}",
												'{rank_id}',
												'{rank_name}',
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
				</div>
			</div>		
			<div class="uap-line-break"></div>
			<div class="row">
				<div class="col-xs-8">
					<h3><?php _e('Send Notification Time', 'uap');?></h3>
					<p><?php _e('Decide when it\’s the best time of day for your website to start managing email reports. The script runs daily on the back-end and checks the report period for each affiliate and how much time has passed since the last report. Based on this new reports are managed automatically by the system.', 'uap');?></p>
					<select name="uap_periodically_reports_cron_hour" style="min-width:100px; margin-bottom:10px;"><?php 
						for ($i=0; $i<24; $i++){
							$selected = ($data['metas']['uap_periodically_reports_cron_hour']==$i) ? 'selected' : '';
							?>
							<option value="<?php echo $i;?>" <?php echo $selected;?> ><?php 
								if ($i<10){
									echo 0;
								}	
								echo $i;
							?></option>
							<?php
						}	
					?></select>
					<p style="font-weight:bold;"><?php _e('Keep in mind that it may be necessary to send a big number of emails and your hosting provider may restrict this action. Be sure that you are able to manage email reports for all of your affiliates.', 'uap');?></p>
				</div>
			</div>				
			
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>	
							
		</div>
	</div>
</form>