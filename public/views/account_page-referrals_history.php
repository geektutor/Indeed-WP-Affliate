<div class="uap-ap-wrap">

<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>

<?php if (!empty($data['items']) && is_array($data['items'])):?>
	<div>

	<?php echo $data['filter'];?>
		<table class="uap-account-table">
			  <thead>	
				<tr>
					<th><?php _e("Campaign", 'uap');?></th>
					<th><?php _e("Amount", 'uap');?></th>					
					<th><?php _e("From", 'uap');?></th>
					<th><?php _e("Description", 'uap');?></th>
					<th><?php _e("Date", 'uap');?></th>
					<th><?php _e('Payment', 'uap');?></th>
					<th><?php _e("Status", 'uap');?></th>
				</tr>
			  </thead>
			  <tbody class="uap-alternate">	
			<?php foreach ($data['items'] as $array) : ?>
				<tr>
					<td><?php 
						if ($array['campaign']) {
							echo $array['campaign'];
						} else {
							echo '-';
						}
					?></td>
					<td style="font-weight:bold; color:#111;"><?php echo uap_format_price_and_currency($array['currency'], $array['amount']);?></td>
					<td><?php echo (empty($array['source'])) ? '' : uap_service_type_code_to_title($array['source']);?></td>
					<td><?php echo $array['description'];?></td>
					<td><?php echo uap_convert_date_to_us_format($array['date']);?></td>
					<td><?php 
						switch ($array['payment']){
							case 0:
								_e('UnPaid', 'uap');
								break;
							case 1:
								_e('Pending', 'uap');
								break;
							case 2: 
								_e('Paid', 'uap');
								break;	
						}						
					?></td>
					<td class="uap-special-label"><?php 
						if ($array['status']==0){
							_e('Refuse', 'uap');
						} else if ($array['status']==1){
							_e('Unverified', 'uap');
						} else if ($array['status']==2){
							_e('Verified', 'uap');
						}
					?></td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>
	</div>
<?php endif;?>

<?php if (!empty($data['pagination'])):?>
	<?php echo $data['pagination'];?>
<?php endif;?>
</div>