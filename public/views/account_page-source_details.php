<?php if (!empty($data['fields_data'])):?>
	<h4>
	<?php echo 'Referral #' . $referral_id;?>
	<?php if (!empty($data['fields_data']['order_amount'])):?>
		<?php echo ', ' . __('Amount:', 'uap') . ' ' . $data['fields_data']['order_amount'];?> 
	<?php endif;?> 
	</h4>
	<?php foreach ($data['all_fields'] as $key=>$label):?>
		<?php if (isset($data['fields_data'][$key]) && $data['fields_data'][$key]!=''):?>
			<div><b><?php echo $label;?>: </b><?php echo $data['fields_data'][$key];?></div>
		<?php endif;?>
	<?php endforeach;?>
	
<?php endif;?>
