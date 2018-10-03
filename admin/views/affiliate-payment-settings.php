<div class="uap-wrapper">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Affiliate Payment Settings');?></h3>
		<div class="inside">
			<?php
			if (!empty($data['metas']['uap_affiliate_payment_type'])):
				$types = array('stripe'=>'Stripe', 'paypal'=>'PayPal', 'bt'=>'Bank Transfer', 'stripe_v2' => 'Stripe V2');
				echo "<div><label>" . __('Payment Type:', 'uap') . "</label> " . $types[$data['metas']['uap_affiliate_payment_type']] . "</div>";
				switch ($data['metas']['uap_affiliate_payment_type']){
					case 'stripe':
						?>
						<div><label><?php echo __("Name on Card:", 'uap');?></label> <?php echo $data['metas']['uap_affiliate_stripe_name'];?></div>
						<div><label><?php echo __("Card Number:", 'uap');?></label> <?php  echo $data['metas']['uap_affiliate_stripe_card_number'];?></div>
						<!-- div><label><?php echo __("CVC:", 'uap');?></label> <?php echo $data['metas']['uap_affiliate_stripe_cvc'];?></div -->
						<div><label><?php echo __("Expiration:", 'uap');?></label> <?php echo $data['metas']['uap_affiliate_stripe_expiration_month'] . '/'. $data['metas']['uap_affiliate_stripe_expiration_year'];?></div>
						<div><label><?php echo __("Type:", 'uap');?></label> <?php echo $data['metas']['uap_affiliate_stripe_card_type'];?></div>
						<?php	
						break;
					case 'bt':
						?>
						<div><label><?php echo __("Bank Transfer Details:", 'uap');?></label> <?php echo $data['metas']['uap_affiliate_bank_transfer_data'];?></div>
						<?php
						break;
					case 'paypal':
						?>
						<div><label><?php echo __("PayPal E-mail Address:", 'uap');?></label> <?php echo $data['metas']['uap_affiliate_paypal_email'];?></div>
						<?php
						break;
					case 'stripe_v2':
						$stripe_v2_data = $indeed_db->get_affiliate_stripe_v2_payment_settings(@$_GET['uid']);
						$possible = array(
											'first_name' => __('First Name', 'uap'),
											'last_name' => __('Last Name', 'uap'),
											'first_name' => __('First Name', 'uap'),
											'day' => __('Birth day', 'uap'),
											'month' => __('Month', 'uap'),
											'year' => __('Year', 'uap'),											
											'country' => __('Country', 'uap'),
											'state' => __('State', 'uap'),
											'city' => __('City', 'uap'),
											'line1' => __('Line1', 'uap'),
											'postal_code' => __('Postal Code', 'uap'),
											'user_type' => __('User Type', 'uap'),
											'routing_number' => __('Routing Number', 'uap'),
											'account_number' => __('Account Number', 'uap'),
											'ssn_last_4' => __('SSN last 4', 'uap'),
											'personal_id_number' => __('Personal id number', 'uap'),
											'business_name' => __('Business name', 'uap'),
											'business_tax_id' => __('Business tax id', 'uap'),
											'personal_address.city' => __('Personal Address City', 'uap'), 
											'personal_address.line1' => __('Personal Address Line1', 'uap'),
											'personal_address.postal_code' => __('Personal Address Postal Code', 'uap'),
						);					
						?>
						
						<?php foreach ($possible as $key=>$label):?>
							<?php if (isset($stripe_v2_data[$key])):?>
							<div><label><?php echo $label;?>:</label> <?php echo $stripe_v2_data[$key];?></div>	
							<?php endif;?>								
						<?php endforeach;?>					

						<?php
						break;
				}
			endif;
			?>			
		</div>
	</div>
</div>

