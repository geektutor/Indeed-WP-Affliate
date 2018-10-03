<div class="uap-wrapper">
	<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Transactions', 'uap');?></span></div>

		<?php if (!empty($data['subtitle'])):?>
			<h4><?php echo $data['subtitle'];?></h4>
		<?php endif;?>
	<div class="uap-special-box" style="border: 1px solid #e8e8e8;">
		<div class="uap-general-date-filter-wrap" style="padding: 10px 0px;">
			<h3 style="margin-top:0px;"><?php _e('Generate CSV File', 'uap');?></h3>
			<div style="display:inline-block; padding:0 20px 0 0;">
			<!--label class="uap-label">Start:</label-->
			<input type="text" value="" class="uap-general-date-filter" placeholder="<?php _e('From - mm/dd/yyyy', 'uap');?>" id="csv_min_date" />
			<!--label class="uap-label">Until:</label-->-
			<input type="text" value="" class="uap-general-date-filter" placeholder="<?php _e('To - mm/dd/yyyy', 'uap');?>" id="csv_max_date" />
			</div>
			<div style=" display:inline-block; padding:0 20px;">
				<label><?php _e('Payment Type', 'uap');?></label>
				<select id="csv_payment_type">
					<option value=""><?php _e('All', 'uap');?></option>
					<option value="bank_transfer"><?php _e('Bank Transfer', 'uap');?></option>
					<option value="paypal"><?php _e('Paypal', 'uap');?></option>
					<option value="stripe"><?php _e('Stripe', 'uap');?></option>
					<option value="stripe_v2"><?php _e('Stripe v2', 'uap');?></option>
				</select>
			</div>
			<div style="display:inline-block; padding:0 20px;">
				<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
					<input type="checkbox" class="uap-switch" onclick="uap_check_and_h(this, '#csv_switch_status');" checked />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<label><?php _e("Switch Transactions to 'Complete' ", 'uap');?></label>
				<input type="hidden" id="csv_switch_status" value="1" />
			</div>
			<div style="display:inline-block; padding:0 20px;">
				<div class="uap-special-button" style="background-color:#38cbcb; float: none;" onclick="uap_generate_payments_csv();"><i class="fa-uap fa-export-csv"></i>Export CSV</div>
			</div>
			<div class="uap-clear"></div>
			<div class="uap-hidden-download-link" style="display: none;"><a href="" target="_blank"><?php _e("Click on this if download doesn't start automatically in 20 seconds!", 'uap');?></a></div>
		</div>
	</div>
	<?php if (!empty($data['listing_items'])) : ?>
	<div class="uap-special-box">
	<?php echo $data['filter'];?>
	</div>

	<?php if (isset($data['error_users'])):?>
		<div class="uap-wrapp-the-errors">
			<?php foreach ($data['error_users'] as $user):?>
				<div><?php echo __('The Payment cannot be proceed for affiliate ', 'uap') . $user . __(' because of the payment settings.', 'uap');?></div>
			<?php endforeach;?>
		</div>
	<?php endif;?>

	<div style="margin: 10px 0px; text-align: right;">
		<button class="button button-primary button-large" onClick="window.location.href='<?php echo $data['update_payments'];?>'"><?php _e("Check Payments Status", 'uap');?></button>
	</div>




	<form action="" method="post" id="form_payments">
					<table class="wp-list-table widefat fixed tags uap-admin-tables">
						<thead>
							<tr>
								<th><?php _e('Username', 'uap');?></th>
								<th><?php _e('Amount', 'uap');?></th>
								<th><?php _e('Payment Type', 'uap');?></th>
								<th><?php _e('Create Date', 'uap');?></th>
								<th><?php _e('Update Date', 'uap');?></th>
								<th><?php _e('Payment Service Status', 'uap');?></th>
								<th><?php _e('Status', 'uap');?></th>
								<th style="min-width:250px"><?php _e('Action', 'uap');?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><?php _e('Username', 'uap');?></th>
								<th><?php _e('Amount', 'uap');?></th>
								<th><?php _e('Payment Type', 'uap');?></th>
								<th><?php _e('Create Date', 'uap');?></th>
								<th><?php _e('Update Date', 'uap');?></th>
								<th><?php _e('Payment Service Status', 'uap');?></th>
								<th><?php _e('Status', 'uap');?></th>
								<th style="min-width:250px"><?php _e('Action', 'uap');?></th>
							</tr>
						</tfoot>
						<tbody class="ui-sortable uap-alternate">
							<?php foreach ($data['listing_items'] as $key => $array): ?>
							<tr>
								<td><div class="uap-list-affiliates-name-label"><?php
									if (empty($u_ids[$array['affiliate_id']])){
										$u_ids[$array['affiliate_id']] = $indeed_db->get_uid_by_affiliate_id($array['affiliate_id']);
									}
									echo $this->print_flag_for_affiliate($u_ids[$array['affiliate_id']]) . $array['username'];
								?></div></td>
								<td style="font-weight:bold;"><?php echo uap_format_price_and_currency($array['currency'], $array['amount']);?></td>
								<td><span class="uap-admin-aff-payment-type uap-payment-type-active-<?php echo $array['payment_type'];?>" style="font-size:12px;"><?php echo $array['payment_type'];?></span></td>
								<td><?php echo uap_convert_date_to_us_format($array['create_date']);?></td>
								<td><?php echo uap_convert_date_to_us_format($array['update_date']);?></td>
								<td><?php if ($array['payment_special_status']) {
									echo $array['payment_special_status'];
								} else {
									echo '-';
								}?></td>
								<td style="font-weight:bold; font-size: 12px;"><?php
									switch ($array['status']){
										case 0:
											?>
												<div><?php _e('Fail', 'uap');?></div>
											<?php
											break;
										case 1:
											?>
												<div><?php _e('Pending', 'uap');?>
													<?php if ($array['payment_type']=='paypal'):?>
															<div style="font-size: 9px;color: #c0c0c0;"><?php _e("Press the 'Check Payments Status' button to get the lattest status of transaction.", 'uap');?></div>
													<?php endif;?>
												</div>
											<?php
											break;
										case 2:
											?>
												<div><?php _e('Complete', 'uap');?></div>
											<?php
											break;
									}
								?></td>
								<td style="min-width:250px">
									<div class="referral-status-verified"><a style="color:#fff;" href="<?php echo $data['view_transaction_url'] . '&id=' . $array['id'];?>"><?php _e('View Details', 'uap');?></a></div>
									<div>

									<?php
										if ($array['status']==2){
											?>
											<span class="refferal-chang-status" onclick="jQuery('#transaction_id').val(<?php echo $array['id'];?>);jQuery('#new_status').val(1);jQuery('#form_payments').submit();"><?php _e('Mark as Pending', 'uap');?></span>
											<span>|</span>
											<?php
										} else if ($array['status']==1){
											?>
											<span class="refferal-chang-status" onclick="jQuery('#transaction_id').val(<?php echo $array['id'];?>);jQuery('#new_status').val(2);jQuery('#form_payments').submit();"><?php _e('Mark as Complete', 'uap');?></span><span>|</span> <?php
										}
									?>
									<span class="refferal-chang-status" onclick="jQuery('#delete_transaction').val(<?php echo $array['id'];?>);jQuery('#form_payments').submit();"><?php _e('Delete', 'uap');?></span>
									</div>
								</td>
							</tr>

							<?php endforeach;?>
						</tbody>
					</table>
			<input type="hidden" name="transaction_id" id="transaction_id" value="" />
			<input type="hidden" name="new_status" id="new_status" value="" />
			<input type="hidden" name="delete_transaction" id="delete_transaction" value="" />
	</form>

	<?php endif;?>
	<?php if (!empty($data['pagination'])) : ?>
		<?php echo $data['pagination'];?>
	<?php endif;?>
</div>
