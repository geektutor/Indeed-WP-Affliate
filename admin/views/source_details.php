<form action="" method="post">
	
	<?php $check = uap_get_active_services();?>
		
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Source Details', 'uap');?></h3>
		<div class="inside">
			<div class="row">
				<div class="col-xs-7">
					<h3><?php _e('Activate/Hold Source Details into Account Page', 'uap');?></h3>
					<label class="" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_source_details_enable']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_source_details_enable');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="uap_source_details_enable" value="<?php echo $data['metas']['uap_source_details_enable'];?>" id="uap_source_details_enable" /> 
				</div>
			</div>	
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>	
		</div>
	</div>
	
	<?php if (!empty($check['woo'])) :?>
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('WooCommerce Show Fields', 'uap');?></h3>
		<div class="inside">
			<div class="row">
				<div class="col-xs-7">
					<?php $temp = explode(',', $data['metas']['uap_source_details_woo_fields_list']);?>
					<?php foreach ($data['fields_available'] as $k=>$v):?>
						<?php $checked = (in_array($k, $temp)) ? 'checked' : '';?>
						<div><input type="checkbox" <?php echo $checked;?> value="<?php echo $k;?>" onClick="uap_make_inputh_string(this, this.value, '#uap_source_details_woo_fields_list');" /> <?php echo $v;?></div>
					<?php endforeach;?>
				</div>
			</div>	
			<input type="hidden" name="uap_source_details_woo_fields_list" id="uap_source_details_woo_fields_list" value="<?php echo $data['metas']['uap_source_details_woo_fields_list'];?>" />
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>	
		</div>
	</div>	
	<?php endif;?>
	
	<?php if (!empty($check['edd'])) :?>
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Easy Download Digital Show Fields', 'uap');?></h3>
		<div class="inside">
			<div class="row">
				<div class="col-xs-7">
					<?php $temp = explode(',', $data['metas']['uap_source_details_edd_fields_list']);?>
					<?php foreach ($data['fields_available'] as $k=>$v):?>
						<?php $checked = (in_array($k, $temp)) ? 'checked' : '';?>
						<div><input type="checkbox" <?php echo $checked;?> value="<?php echo $k;?>" onClick="uap_make_inputh_string(this, this.value, '#uap_source_details_edd_fields_list');" /> <?php echo $v;?></div>
					<?php endforeach;?>
				</div>
			</div>				
			<input type="hidden" name="uap_source_details_edd_fields_list" id="uap_source_details_edd_fields_list" value="<?php echo $data['metas']['uap_source_details_edd_fields_list'];?>" />
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>	
		</div>
	</div>	
	<?php endif;?>

	<?php if (!empty($check['ump'])):?>
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Ultimate Membership Pro Show Fields', 'uap');?></h3>
		<div class="inside">
			<div class="row">
				<div class="col-xs-7">
					<?php $temp = explode(',', $data['metas']['uap_source_details_ump_fields_list']);?>
					<?php foreach ($data['fields_available'] as $k=>$v):?>
						<?php $checked = (in_array($k, $temp)) ? 'checked' : '';?>
						<div><input type="checkbox" <?php echo $checked;?> value="<?php echo $k;?>" onClick="uap_make_inputh_string(this, this.value, '#uap_source_details_ump_fields_list');" /> <?php echo $v;?></div>
					<?php endforeach;?>
				</div>
			</div>				
			<input type="hidden" name="uap_source_details_ump_fields_list" id="uap_source_details_ump_fields_list" value="<?php echo $data['metas']['uap_source_details_ump_fields_list'];?>" />	
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>	
		</div>
	</div>	
	<?php endif;?>
	
	<?php if ($indeed_db->is_magic_feat_enable('sign_up_referrals')) :?>
	<?php
		unset($data['fields_available']['phone']);
		unset($data['fields_available']['cart_items']);
		unset($data['fields_available']['billing_address']);
		unset($data['fields_available']['shipping_address']);
		unset($data['fields_available']['order_amount']);
	?>
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('SignUp Show Fields', 'uap');?></h3>
		<div class="inside">
			<div class="row">
				<div class="col-xs-7">
					<?php $temp = explode(',', $data['metas']['uap_source_details_signup_fields_list']);?>
					<?php foreach ($data['fields_available'] as $k=>$v):?>
						<?php $checked = (in_array($k, $temp)) ? 'checked' : '';?>
						<div><input type="checkbox" <?php echo $checked;?> value="<?php echo $k;?>" onClick="uap_make_inputh_string(this, this.value, '#uap_source_details_signup_fields_list');" /> <?php echo $v;?></div>
					<?php endforeach;?>
				</div>
			</div>				
			<input type="hidden" name="uap_source_details_signup_fields_list" id="uap_source_details_signup_fields_list" value="<?php echo $data['metas']['uap_source_details_signup_fields_list'];?>" />	
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>	
		</div>
	</div>	
	<?php endif;?>	
				
</form>