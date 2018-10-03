			<form action="" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Cost Per Mile(CPM) Campaign', 'uap');?></h3>
					<div class="inside">
						<div class="row">
						<div class="col-xs-6">
							<h3><?php _e('Activate/Hold CPM Campaign', 'uap');?></h3>
							<p><?php _e('Affiliates will receive a CPM Referral with flat amount rewarded for 1000 impressions (displaying your banners 1000 times).', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_cpm_commission_enabled']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_cpm_commission_enabled');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_cpm_commission_enabled" value="<?php echo $data['metas']['uap_cpm_commission_enabled'];?>" id="uap_cpm_commission_enabled" />
                            <br/> <br/>
                            <p><?php _e('Once this module will be enabled Creatives (banners) listed on Account Page will have an additional code inside which will allow to track the Impressions. Affiliates will have to update their promotional banner codes to get this feature.', 'uap');?></p>

                        <p style="font-weight:bold;"><?php _e('Important: this feature may drain into your server over average traffic. Be sure that your Server Performance is good enough related to your number of affiliates otherwise disable the module to be avoided an overloading issue.', 'uap');?></p>
						</div>

						</div>

						<div class="uap-line-break"></div>

						<div class="row">
							<div class="col-xs-5">
							<?php if (!empty($data['rank_list'])) :?>
							<h3><?php _e('CPM Amount For Each Rank', 'uap');?></h3>
								<p><?php _e('Set a special CPM amount for each rank. This option will also become available in the “Rank Settings” page.', 'uap');?></p>
							<?php foreach ($data['rank_list'] as $id=>$label) :?>
									<div class="input-group" style="margin-bottom:20px;">
										<span class="input-group-addon" id="basic-addon1"><?php echo $label;?></span>
									 		<input type="number" class="form-control" min="0" step="0.01" class="uap-input-number" value="<?php echo $data['rank_value_array'][$id];?>" name="<?php echo "cpm_commission_value[$id]";?>" />
									 		<div class="input-group-addon"><?php echo $data['amount_types']['flat'];?></div>
										</div>
								<?php endforeach;?>
							<?php endif;?>
							</div>
						</div>

						<div class="uap-line-break"></div>

						<div class="uap-inside-item">
							<div class="row">
								<div class="col-xs-5">
									<h3><?php _e('Default Referral Status', 'uap');?></h3>
									<select name="uap_cpm_commission_default_referral_sts" class="form-control m-bot15"><?php
										foreach (array(1 => __('Unverified', 'uap'), 2 => __('Verified', 'uap')) as $k=>$v){
											$selected = ($data['metas']['uap_cpm_commission_default_referral_sts']==$k) ? 'selected' : '';
											?>
											<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
											<?php
										}
									?></select>
								</div>
							</div>
						</div>

						<div class="uap-submit-form" style="margin-top:40px;">
							<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>
					</div>
				</div>
			</form>
