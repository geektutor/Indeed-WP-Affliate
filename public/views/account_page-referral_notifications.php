<div class="uap-ap-wrap">
	
	<?php if (!empty($data['title'])):?>
		<h3><?php echo $data['title'];?></h3>
	<?php endif;?>
	<?php if (!empty($data['message'])):?>
		<p><?php echo do_shortcode($data['message']);?></p>
	<?php endif;?>

<form action="" method="post" class="uap-change-password-form">
	<?php if (!empty($data['module_settings_notf']['uap_referral_notifications_enable'])) : ?>

	<div class="uap-single-notf-row-wrapper">

			<div class="uap-single-notf-row-top">
				<div class="uap-single-notf-col uap-single-notf-label"><?php _e("Referral Notification", 'uap');?></div>
				<div class="uap-single-notf-col uap-single-notf-checkbox"><?php _e("E-mail", 'uap');?></div>						
			</div>	
			<?php
				$posible_types = uap_get_possible_referral_types();
				$landing_commissions = $indeed_db->get_all_landing_commision_source_type();
				$posible_types = array_merge($posible_types, $landing_commissions);
				$items = array();
				if ($data['metas'] && isset($data['metas']['uap_notifications_on_every_referral_types'])){
					$items = explode(',', $data['metas']['uap_notifications_on_every_referral_types']);					
				}
				foreach ($posible_types as $k=>$v):
					$checked = (in_array($k, $items)) ? 'checked' : '';
					?>	
					<div class="uap-single-notf-row">
						<div class="uap-single-notf-col uap-single-notf-label"><?php echo $v['label'];?><span><?php echo (isset($v['sub_label']) ? $v['sub_label'] : '' );?></span></div>
						<div class="uap-single-notf-col uap-single-notf-checkbox"><input type="checkbox" onClick="uap_make_inputh_string(this, '<?php echo $k;?>', '#uap_types_in')" <?php echo $checked;?> /></div>						
					</div>	
					<?php		
				endforeach;
			?>					
			<input type="hidden" value="<?php echo $data['metas']['uap_notifications_on_every_referral_types'];?>" id="uap_types_in" name="uap_notifications_on_every_referral_types" />	
	</div>	
	<?php endif;?>
	<?php if (!empty($data['module_settings_reports']['uap_periodically_reports_enable'])) : ?>
	<div class="uap-periodically-reports-wrapper">
		<div class="uap-periodically-reports-title"><?php _e("Periodical Reports Interval", 'uap');?></div>
		<div>
			<select name="period"><?php
				foreach (array(0 => __('Never send reports', 'uap'), 1 => __('Daily Reports', 'uap'), 7 => __('Weekly Reports', 'uap'), 30 => __('Monthly Reports', 'uap')) as $k=>$v):
					$selected = ($k==$data['report_settings']['period']) ? 'selected' : '';
					?>
					<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
					<?php
				endforeach;
			?></select>			
		</div>		
	</div>	
	<?php endif;?>
	<div class="uap-change-password-field-wrap">
		<input type="submit" value="<?php _e("Save", 'uap');?>" name="save_settings" class="button button-primary button-large" />
	</div>
</form>

</div>
