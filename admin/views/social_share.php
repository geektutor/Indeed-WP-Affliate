<?php 
$enabled = uap_is_social_share_intalled_and_active();
?>
			<form action="" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Social Share', 'uap');?></h3>
					<div class="inside">	
					<div class="inside">
						<div class="row">
						<div class="col-xs-7">
							<h3><?php _e('Activate/Hold Social Share', 'uap');?></h3>
							<p><?php _e("This Feature will work only if You have '<strong>Social Share & Locker Pro Wordpress Plugin</strong>' installed and activated.", 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_social_share_enable']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_social_share_enable');" <?php echo $checked;?> <?php if (!$enabled) echo 'disabled';?>/>
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_social_share_enable" value="<?php echo $data['metas']['uap_social_share_enable'];?>" id="uap_social_share_enable" /> 
						</div>
						</div>
						<div class="uap-line-break"></div>
						<div class="row">
						<div class="col-xs-6">
							<h3><?php _e('Custom Share Message', 'uap');?></h3>	
							<p><?php _e('For a better share action, you may set a custom message that will be listed besides the affiliate links.', 'uap');?></p>
							
							<textarea name="uap_social_share_message" style="width: 80%; height: 150px;"><?php echo uap_correct_text($data['metas']['uap_social_share_message']);?></textarea>
						</div>
						</div>
						<div class="uap-line-break"></div>
						<div class="row">
						<div class="col-xs-6">
							<h3><?php _e('Shortcode', 'uap');?></h3>	
							<p><?php _e('You can generate the social share shortcode from the “Social Share & Locker” dashboard and paste it here.', 'uap');?></p>
							<p><?php 
								if ($enabled){
									echo '<a href="' . $data['social_share_page'] . '" target="_blank">' . __('Click here', 'uap') . '</a>' . __(' to grab a new shortcode.', 'uap');
								}
								?></p>
							<textarea name="uap_social_share_shortcode" style="width: 80%; height: 150px;"><?php echo uap_correct_text($data['metas']['uap_social_share_shortcode']);?></textarea>					
							</div>							
						</div>						
						
						<div class="uap-submit-form"> 
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>		
					</div>
				</div>
			</form>