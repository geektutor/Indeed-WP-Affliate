<div class="uap-wrapper">
	<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('UnPaid Referrals', 'uap');?></span></div>
	
		<?php if (!empty($data['subtitle'])):?>
			<h4><?php echo $data['subtitle'];?></h4>
		<?php endif;?>	
	
	<?php if (!empty($data['listing_items'])) : ?>
	<div class="uap-special-box">
	<?php echo $data['filter'];?>
	</div>
	<form action="<?php echo $data['pay_link'];?>" method="post" id="form_payments">
				<div class="uap-delete-wrapp">
					<input type="submit" value="<?php _e('Pay selected Referrals', 'uap');?>" name="submit_select_pay" class="button button-primary button-large do-the-payment">
				</div>
					<table class="wp-list-table widefat fixed tags uap-admin-tables">
						<thead>
							<tr>
								<th style="width: 30px;"><input type="checkbox" onClick="uap_select_all_checkboxes( this, '.uap-referral' );" /></th>
								<th><?php _e('Affiliate', 'uap');?></th>
								<th><?php _e('Reference', 'uap');?></th>
								<th><?php _e('Amount', 'uap');?></th>
								<th><?php _e('Date', 'uap');?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>	
								<th style="width: 30px;"><input type="checkbox" onClick="uap_select_all_checkboxes( this, '.uap-referral' );" /></th>
								<th><?php _e('Affiliate', 'uap');?></th>
								<th><?php _e('Reference', 'uap');?></th>
								<th><?php _e('Amount', 'uap');?></th>
								<th><?php _e('Date', 'uap');?></th>
							</tr>
						</tfoot>
						<tbody class="ui-sortable uap-alternate">
							<?php foreach ($data['listing_items'] as $key => $array): ?>
							<tr>
								<th><input type="checkbox" value="<?php echo $array['id'];?>" name="referrals[]" class="uap-referral"/></th>
								<td>
									<div class="uap-list-affiliates-name-label"><?php 
										if (empty($u_ids[$array['affiliate_id']])){
											$u_ids[$array['affiliate_id']] = $indeed_db->get_uid_by_affiliate_id($array['affiliate_id']);
										}												
										echo $this->print_flag_for_affiliate($u_ids[$array['affiliate_id']]) . $array['username'];	
									?></div>
									<?php 
										if (!empty($data['payments_settings']) && !empty($data['payments_settings'][$array['affiliate_id']])):			
											echo " - ";
											$inside_array = $data['payments_settings'][$array['affiliate_id']];					
											switch ($inside_array['type']):											
												case 'paypal':
													$payment_class = ($inside_array['is_active']) ? 'uap-payment-type-active-paypal' : '';
													?>
													<span class="uap-admin-aff-payment-type <?php echo $payment_class;?>"><?php _e('PayPal', 'uap');?></span>
													<?php
													break;
												case 'bt':
													$payment_class = ($inside_array['is_active']) ? 'uap-payment-type-active-bt' : '';
													?>
													<span class="uap-admin-aff-payment-type <?php echo $payment_class;?>"><?php _e('Bank Transfer', 'uap');?></span>											
													<?php
													break;
												case 'stripe':
													$payment_class = ($inside_array['is_active']) ? 'uap-payment-type-active-stripe' : '';
													?>
													<span class="uap-admin-aff-payment-type <?php echo $payment_class;?>"><?php _e('Stripe', 'uap');?></span>
													<?php
													break;													
											endswitch;
										else :
											?>
											<?php							
										endif;
									?>		
								</td>
								<td><?php echo $array['reference'];?></td>
								<td><?php echo uap_format_price_and_currency($array['currency'], $array['amount']);?></td>
								<td><?php echo uap_convert_date_to_us_format($array['date']);?></td>
							</tr>
							
							<?php endforeach;?>
						</tbody>
					</table>
				<div class="uap-delete-wrapp">
					<input type="submit" value="<?php _e('Pay', 'uap');?>" name="submit_select_pay" class="button button-primary button-large do-the-payment">
				</div>					
	</form>
	<?php endif;?>
	<?php if (!empty($data['pagination'])) : ?>
		<?php echo $data['pagination'];?>
	<?php endif;?>
</div>
<script>
	jQuery(".do-the-payment").on('click', function(e){
		e.preventDefault();
    	jQuery('.uap-referral').each(function(i){
			if (jQuery(this).is(':checked')){
				jQuery("#form_payments").submit();
			}
		});
	});
</script>