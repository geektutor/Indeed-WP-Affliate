<p class="<?php echo $data['class'];?>">
	<?php if (self::$checkout_referrals_select_settings['uap_checkout_select_referral_label']):?>
		<label><?php echo self::$checkout_referrals_select_settings['uap_checkout_select_referral_label'] . ' ' . $data['require'];?></label>		
	<?php endif;?>
		<?php if (self::$checkout_referrals_select_settings['uap_checkout_select_referral_s_type']==1):?>
			<select name="uap_affiliate_username" class="<?php echo $data['select_class'];?>" <?php echo $data['require_on_input'];?> >
				<option value="">...</option>
				<?php 
				foreach ($data['affiliates'] as $id => $label):
					?>
					<option value="<?php echo $id;?>" ><?php echo $label;?></option>
					<?php
				endforeach;	
			?></select>
		<?php else :?>
			<input type="text" value="" name="uap_affiliate_username_text" class="<?php echo $data['input_class'];?>" <?php echo $data['require_on_input'];?> onBlur="uap_affiliate_username_test(this.value);" id="uap_affiliate_username_text"/>
		<?php endif;?>		
</p>