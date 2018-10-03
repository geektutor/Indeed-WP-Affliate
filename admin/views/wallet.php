<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Wallet', 'uap');?></h3>
		<div class="inside">
			<div class="row">
				<div class="col-xs-8">
					<h3><?php _e('Activate/Hold Wallet', 'uap');?></h3>
					<p><?php _e('Affiliates will have the option to spend their earnings directly in the website by using generated coupons with a specific flat discount.', 'uap');?></p>
					<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<?php $checked = ($data['metas']['uap_wallet_enable']) ? 'checked' : '';?>
						<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_wallet_enable');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="uap_wallet_enable" value="<?php echo $data['metas']['uap_wallet_enable'];?>" id="uap_wallet_enable" />
				</div>
			</div>
			<div class="row" style="margin-top: 30px;">
				<div class="col-xs-8">
					<p><?php _e('Establish a minimum amount required for an affiliate to be able to move his earnings from his account into his wallet. Only referrals that are verified but not yet paid can be available for converting into coupons in an affiliate’s “Wallet”.', 'uap');?></p>
				</div>
			</div>
			<div class="row" style="margin-bottom: 20px;">
				<div class="col-xs-4">
					<div class="input-group" style="margin-top: 10px;">
						<label class="input-group-addon"><?php _e('Minimum Amount', 'uap');?></label>
						<input type="number" class="form-control" step="0.01" name="uap_wallet_minimum_amount" value="<?php echo $data['metas']['uap_wallet_minimum_amount'];?>" />

					</div>
				</div>
			</div>

			<div class="row" style="margin-bottom: 20px;">
				<div class="col-xs-4">
					<div class="input-group" style="margin-top: 10px;">
						<label class="iump-labels-special"><?php _e('Excluded sources:', 'uap');?></label>
						<div>
								<?php
									if ($data['metas']['uap_wallet_exclude_sources']!='')
											$temp = explode(',', $data['metas']['uap_wallet_exclude_sources']);
									else
											$temp = array();
									$types = array(
														'ump' => 'Ultimate Membership Pro',
														'woo' => 'WooCommerce',
														'edd' => 'Easy Download Digital',
									);
									foreach ($types as $key=>$value):?>
									<div>
										<input type="checkbox" <?php if (in_array($key, $temp)) echo 'checked';?> onClick="uap_make_inputh_string(this, '<?php echo $key;?>', '#uap_wallet_exclude_sources');" /> <?php echo $value;?>
									</div>
								<?php endforeach;?>
						</div>
						<input type="hidden" name="uap_wallet_exclude_sources" value="<?php echo $data['metas']['uap_wallet_exclude_sources'];?>" id="uap_wallet_exclude_sources"/>
					</div>
				</div>
			</div>


			<div class="uap-submit-form">
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>
		</div>
	</div>
</form>
