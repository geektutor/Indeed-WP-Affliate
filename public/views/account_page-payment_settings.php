<div class="uap-ap-wrap">
<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>	

<form action="" method="post" class="uap-change-password-form">
	<div class="uap-ap-field">
		<label class="uap-ap-label"><?php _e("Payment Type", 'uap');?></label>
		<select class="uap-public-form-control" onChange="uap_payment_type();" name="uap_affiliate_payment_type"><?php 
			foreach ($data['payment_types'] as $k=>$v):
				$selected = ($data['metas']['uap_affiliate_payment_type']==$k) ? 'selected' : '';
				?>
				<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
				<?php
			endforeach;	
		?></select>
	</div>	
	<div class="uap-ap-field" id="uap_payment_with_paypal" style="display: none;">
		<label class="uap-ap-label"><?php _e("PayPal E-mail Address", 'uap');?></label>
		<input class="uap-public-form-control" type="text" value="<?php echo $data['metas']['uap_affiliate_paypal_email'];?>" name="uap_affiliate_paypal_email" />
	</div>

	<div class="uap-ap-field" id="uap_payment_with_bt" style="display: none;">
		<label class="uap-ap-label"><?php _e("Bank Transfer Details", 'uap');?></label>
		<textarea style="min-height: 100px;" class="uap-public-form-control" name="uap_affiliate_bank_transfer_data"><?php echo $data['metas']['uap_affiliate_bank_transfer_data'];?></textarea>
	</div>
	
	<div class="uap-ap-field" id="uap_payment_with_stripe" style="display: none;">
		<div>
			<label class="uap-ap-label"><?php _e("Name on Card", 'uap');?></label>
			<input class="uap-public-form-control" type="text" value="<?php echo $data['metas']['uap_affiliate_stripe_name'];?>" name="uap_affiliate_stripe_name" />			
		</div>
		<div>
			<label class="uap-ap-label"><?php _e("Card Number", 'uap');?></label>
			<input class="uap-public-form-control" type="text" value="<?php echo $data['metas']['uap_affiliate_stripe_card_number'];?>" name="uap_affiliate_stripe_card_number" />			
		</div>
		<!-- div>
			<label class="uap-ap-label"><?php _e("CVC", 'uap');?></label>
			<input class="uap-public-form-control" type="password" value="<?php echo $data['metas']['uap_affiliate_stripe_cvc'];?>" name="uap_affiliate_stripe_cvc" />			
		</div -->
		<div>
			<label class="uap-ap-label"><?php _e("Expiration", 'uap');?></label>
			<div>
				<div style="display:inline-block;vertical-align: top">
					<select name="uap_affiliate_stripe_expiration_month"><?php
						for ($m=1; $m<13; $m++):
							$selected = ($m==$data['metas']['uap_affiliate_stripe_expiration_month']) ? 'selected' : '';
							?>
							<option value="<?php echo $m;?>" <?php echo $selected;?>><?php echo $m;?></option>
							<?php
						endfor;
					?></select>
				</div>
				<div style="display:inline-block;vertical-align: top">
					<select name="uap_affiliate_stripe_expiration_year"><?php
						$year = date('Y');
						for ($y=$year; $y<$year+10; $y++):
							$selected = ($y==$data['metas']['uap_affiliate_stripe_expiration_year']) ? 'selected' : '';
							?>
							<option value="<?php echo $y;?>" <?php echo $selected;?>><?php echo $y;?></option>
							<?php
						endfor;
					?></select>				
				</div>			
			</div>
		</div>
		<div>
			<label class="uap-ap-label"><?php _e("Type", 'uap');?></label>
			<div>
				<select name="uap_affiliate_stripe_card_type"><?php
					foreach ($data['stripe_card_types'] as $key=>$value):
						$selected = ($key==$data['metas']['uap_affiliate_stripe_card_type']) ? 'selected' : '';
						?>
						<option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
						<?php
					endforeach;
				?></select>				
			</div>			
		</div>	
		<!--div>
			<label class="uap-ap-label"><?php _e("Tax ID", 'uap');?></label>
			<input class="uap-public-form-control" type="text" value="<?php echo $data['metas']['uap_affiliate_stripe_tax_id'];?>" name="uap_affiliate_stripe_tax_id" />			
		</div-->			
	</div>

	<div class="uap-ap-field" id="uap_payment_with_stripe_v2" style="display: none;">
		
		<div>
			<label class="uap-ap-label"><?php _e("Type", 'uap');?></label>
			<div>
				<?php $user_type_arr = array('company' => __('Company', 'uap'), 'individual' => __('Individual', 'uap'));?>
				<select name="stripe_v2_meta_data[user_type]" class="stripe_v2_meta_data_user_type uap-public-form-control" onChange="uap_stripe_v2_update_fields();"><?php
					foreach ($user_type_arr as $key=>$value):
						$selected = ($key==$data['stripe_v2']['user_type']) ? 'selected' : '';
						?>
						<option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
						<?php
					endforeach;
				?></select>				
			</div>			
		</div>

		<div>
			<label class="uap-ap-label"><?php _e("Country", 'uap');?></label>
			<div>
				<select name="stripe_v2_meta_data[country]" class="stripe_v2_meta_data_country uap-public-form-control" onChange="uap_stripe_v2_update_fields();"><?php
					$countries = array(
										'gb' => 'UK', 
										'us' => 'US', 
										'at' => 'Austria',
			 							'be' => 'Belgium',
										'dk' => 'Denmark',
			 							'fr' => 'France',
			 							'fi' => 'Finland',
			 							'de' => 'Germany',	
			 							'ie' => 'Ireland',
			 							'it' => 'Italy',
			 							'lu' => 'Luxembourg',
			 							'nl' => 'Netherlands',
			 							'no' => 'Norway',
			 							'pt' => 'Portugal',
			 							'se' => 'Sweden',
			 							'es' => 'Spain',
			 							'ch' => 'Switzerland',								
					);
					foreach ($countries as $key=>$value):
						$selected = ($key==$data['stripe_v2']['country']) ? 'selected' : '';
						?>
						<option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
						<?php
					endforeach;
				?></select>				
			</div>			
		</div>		
		
		<div class="uap-stripe-v2-field" data-country="us" data-type="all">
			<label class="uap-ap-label"><?php _e("State", 'uap');?></label>
			<input type="text" class="uap-public-form-control" name="stripe_v2_meta_data[state]" value="<?php echo $data['stripe_v2']['state'];?>" />
		</div>		
		
		<div class="uap-stripe-v2-field" data-country="all" data-type="all">
			<label class="uap-ap-label"><?php _e("City", 'uap');?></label>
			<input type="text" class="uap-public-form-control" name="stripe_v2_meta_data[city]" value="<?php echo $data['stripe_v2']['city'];?>" />
		</div>

		<div class="uap-stripe-v2-field" data-country="non_us" data-type="company">
			<label class="uap-ap-label"><?php _e("Additional owners", 'uap');?></label>
			<input type="text" class="uap-public-form-control" name="stripe_v2_meta_data[additional_owners]" value="<?php echo @$data['stripe_v2']['additional_owners'];?>" />
		</div>				
	
		<div class="uap-stripe-v2-field" data-country="all" data-type="all">
			<label class="uap-ap-label"><?php _e("Routing Number", 'uap');?></label>
			<input type="text" class="uap-public-form-control" name="stripe_v2_meta_data[routing_number]" value="<?php echo $data['stripe_v2']['routing_number'];?>" />
		</div>
			
		<div class="uap-stripe-v2-field" data-country="all" data-type="all">
			<label class="uap-ap-label"><?php _e("Account Number", 'uap');?></label>
			<input type="text" class="uap-public-form-control" name="stripe_v2_meta_data[account_number]" value="<?php echo $data['stripe_v2']['account_number'];?>" />
		</div>
		
		<div class="uap-stripe-v2-field" data-country="all" data-type="all">
			<label class="uap-ap-label"><?php _e("Birthday day", 'uap');?></label>
			<input type="number" class="uap-public-form-control" name="stripe_v2_meta_data[day]" min="1" max="31" value="<?php echo $data['stripe_v2']['day'];?>" />
		</div>		
		
		<div class="uap-stripe-v2-field" data-country="all" data-type="all">
			<label class="uap-ap-label"><?php _e("Birthday month", 'uap');?></label>
			<input type="number" class="uap-public-form-control" name="stripe_v2_meta_data[month]" min="1" max="12" value="<?php echo $data['stripe_v2']['month'];?>" />
		</div>	
		
		<div class="uap-stripe-v2-field" data-country="all" data-type="all">
			<label class="uap-ap-label"><?php _e("Birthday year", 'uap');?></label>
			<input type="number" class="uap-public-form-control" name="stripe_v2_meta_data[year]" min="1900" max="" value="<?php echo $data['stripe_v2']['year'];?>" />
		</div>	
	
		<div class="uap-stripe-v2-field" data-country="all" data-type="all">
			<label class="uap-ap-label"><?php _e("First Name", 'uap');?></label>
			<input type="text" class="uap-public-form-control" name="stripe_v2_meta_data[first_name]" value="<?php echo $data['stripe_v2']['first_name'];?>" />
		</div>		
	
		<div class="uap-stripe-v2-field" data-country="all" data-type="all">
			<label class="uap-ap-label"><?php _e("Last Name", 'uap');?></label>
			<input type="text" class="uap-public-form-control" name="stripe_v2_meta_data[last_name]" value="<?php echo $data['stripe_v2']['last_name'];?>" />
		</div>				
	
		<div class="uap-stripe-v2-field" data-country="all" data-type="all">
			<label class="uap-ap-label"><?php _e("Address", 'uap');?></label>
			<textarea class="uap-public-form-control" name="stripe_v2_meta_data[line1]"><?php echo $data['stripe_v2']['line1'];?></textarea>
		</div>		
		
		<div class="uap-stripe-v2-field" data-country="all" data-type="all">
			<label class="uap-ap-label"><?php _e("Postal Code", 'uap');?></label>
			<input type="number" class="uap-public-form-control" name="stripe_v2_meta_data[postal_code]" min="0" max="" value="<?php echo $data['stripe_v2']['postal_code'];?>" />
		</div>	
		
		<div class="uap-stripe-v2-field" data-country="us" data-type="all">
			<label class="uap-ap-label"><?php _e("SSN Last 4", 'uap');?></label>
			<input type="number" class="uap-public-form-control" name="stripe_v2_meta_data[ssn_last_4]" min="0" max="" value="<?php echo $data['stripe_v2']['ssn_last_4'];?>" />
		</div>						
		
		<div class="uap-stripe-v2-field" data-country="us" data-type="all">
			<label class="uap-ap-label"><?php _e("Personal ID Number", 'uap');?></label>
			<input type="number" class="uap-public-form-control" name="stripe_v2_meta_data[personal_id_number]" min="0" max="" value="<?php echo $data['stripe_v2']['personal_id_number'];?>" />
		</div>			
		
		<div class="uap-stripe-v2-field" data-country="all" data-type="company">
			<label class="uap-ap-label"><?php _e("Business Name", 'uap');?></label>
			<input type="text" class="uap-public-form-control" name="stripe_v2_meta_data[business_name]" value="<?php echo $data['stripe_v2']['business_name'];?>" />
		</div>
				
		<div class="uap-stripe-v2-field" data-country="all" data-type="company">
			<label class="uap-ap-label"><?php _e("Business Tax ID", 'uap');?></label>
			<input type="text" class="uap-public-form-control" name="stripe_v2_meta_data[business_tax_id]" value="<?php echo $data['stripe_v2']['business_tax_id'];?>" />
		</div>
		
		<div class="uap-stripe-v2-field" data-country="non_us" data-type="company">
			<label class="uap-ap-label"><?php _e("Personal Address - City", 'uap');?></label>
			<input type="text" class="uap-public-form-control" name="stripe_v2_meta_data[personal_address.city]" value="<?php echo $data['stripe_v2']['personal_address.city'];?>" />
		</div>		
						
		<div class="uap-stripe-v2-field" data-country="non_us" data-type="company">
			<label class="uap-ap-label"><?php _e("Personal Address - Line 1", 'uap');?></label>
			<textarea class="uap-public-form-control" name="stripe_v2_meta_data[personal_address.line1]"><?php echo $data['stripe_v2']['personal_address.line1'];?></textarea>
		</div>		
		
		<div class="uap-stripe-v2-field" data-country="non_us" data-type="company">
			<label class="uap-ap-label"><?php _e("Personal Address Postal Code", 'uap');?></label>
			<input type="number" class="uap-public-form-control" name="stripe_v2_meta_data[personal_address.postal_code]" min="0" max="" value="<?php echo $data['stripe_v2']['personal_address.postal_code'];?>" />
		</div>		
		
		<div class="uap-stripe-v2-field" data-country="all" data-type="all">
			<label class="uap-ap-label"><?php _e("Verification Document", 'uap');?></label>
			<div>
				<?php echo uap_create_form_element(array('type' => 'file', 'name' => 'verification_document'));?>
			</div>
		</div>	
		
		<div class="uap-stripe-v2-field" data-country="all" data-type="all">
			<?php $checked = ($data['stripe_v2']['stripe_v2_tos']==1) ? 'checked' : '';?>
			<input type="checkbox" class="stripe_v2_tos" name="stripe_v2_meta_data[stripe_v2_tos]" value="1" disabled <?php echo $checked;?> /> 			
			<a href="#" onClick="jQuery('.stripe_v2_tos').removeAttr('disabled');window.open('https://stripe.com/us/connect-account/legal', '_blank');"><?php _e("Terms of service", 'uap');?></a>
		</div>
				
		<?php if (!empty($data['errors'])):?>
		<div>
			<?php echo $data['errors'];?>			
		</div>
		<?php endif;?>
														
	</div>	
	
	<div class="uap-change-password-field-wrap">
		<input type="submit" value="<?php _e("Save", 'uap');?>" name="save_settings" class="button button-primary button-large" />
	</div>
	<?php if (!empty($data['error'])) : ?>
		<div><?php echo $data['error'];?></div>
	<?php elseif (!empty($data['success'])) : ?>
		<div><?php echo $data['success'];?></div>
	<?php endif; ?>
</form>
</div>
<script>
	
	jQuery(document).ready(function(){
		uap_payment_type();
		uap_stripe_v2_update_fields();
	});
	
</script>