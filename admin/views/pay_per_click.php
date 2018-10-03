			<form action="" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('PayPerClick Campaign', 'uap');?></h3>
					<div class="inside">
						<div class="row">
						<div class="col-xs-4">
							<h3><?php _e('Activate/Hold PayPerClick Campaign', 'uap');?></h3>
							<p><?php _e('Affiliates will receive a PPC Referral with flat amount each time a new referred user visit your website.', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_pay_per_click_enabled']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_pay_per_click_enabled');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_pay_per_click_enabled" value="<?php echo $data['metas']['uap_pay_per_click_enabled'];?>" id="uap_pay_per_click_enabled" />
						</div>
						</div>

						<div class="uap-line-break"></div>

						<div class="row">
							<div class="col-xs-5">
							<?php if (!empty($data['rank_list'])) :?>
							<h3><?php _e('PPC Amount For Each Rank', 'uap');?></h3>
								<p><?php _e('Set a special PPC amount for each rank. This option will also become available in the “Rank Settings” page.', 'uap');?></p>
							<?php foreach ($data['rank_list'] as $id=>$label) :?>
									<div class="input-group" style="margin-bottom:20px;">
										<span class="input-group-addon" id="basic-addon1"><?php echo $label;?></span>
									 		<input type="number" class="form-control" min="0" step="0.01" class="uap-input-number" value="<?php echo $data['rank_value_array'][$id];?>" name="<?php echo "pay_per_click_value[$id]";?>" />
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
									<select name="uap_pay_per_click_default_referral_sts" class="form-control m-bot15"><?php
										foreach (array(1 => __('Unverified', 'uap'), 2 => __('Verified', 'uap')) as $k=>$v){
											$selected = ($data['metas']['uap_pay_per_click_default_referral_sts']==$k) ? 'selected' : '';
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

<?php
