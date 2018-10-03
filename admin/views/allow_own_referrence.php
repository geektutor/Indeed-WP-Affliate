			<form action="" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Allow Own Referrence', 'uap');?></h3>
					<div class="inside">	
					<div class="row">
						<div class="col-xs-7">
							<h3><?php _e('Activate/Hold Allow Own Referrence', 'uap');?></h3>
							<p><?php _e('Affiliates will be able to earn a commission on their own purchases via their own referral links.', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_allow_own_referrence_enable']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_allow_own_referrence_enable');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_allow_own_referrence_enable" value="<?php echo $data['metas']['uap_allow_own_referrence_enable'];?>" id="uap_allow_own_referrence_enable" /> 
						</div>
						</div>	
						<div class="uap-line-break"></div>	
						
						<div class="uap-submit-form" style="margin-top:40px;"> 
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>		
					</div>
				</div>
			</form>