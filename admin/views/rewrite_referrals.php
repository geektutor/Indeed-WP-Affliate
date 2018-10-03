			<form action="" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('ReWrite Referrals', 'uap');?></h3>
					<div class="inside">
					<div class="row">
						<div class="col-xs-8">
							<h3><?php _e('Activate/Hold ReWrite Referrals', 'uap');?></h3>
							<p><?php _e('Decides if a new customer is re-assigned to the first or last linked affiliate. If the same customer is referred to a different affiliate than the first one, you can decide if the reference will be changed or not.
Example: John is a customer that is linked to Smith (affiliate). John enters the website but doesn’t buy anything. Later, John enters the website through Bob’s link (affiliate) and makes a purchase. You can decide if John will be linked to the first affiliate (Smith), or the last one (Bob), therefore deciding which affiliate will receive referral.', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_rewrite_referrals_enable']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_rewrite_referrals_enable');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_rewrite_referrals_enable" value="<?php echo $data['metas']['uap_rewrite_referrals_enable'];?>" id="uap_rewrite_referrals_enable" /> 
						</div>
						</div>	
						<div class="uap-line-break"></div>				
						
						<div class="uap-submit-form" style="margin-top:40px;"> 
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>		
					</div>
				</div>
			</form>