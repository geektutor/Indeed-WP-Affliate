<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>

<div class="uap-row">
		<div class="uapcol-md-2 uap-account-payments-tab1">
			<div class="uap-account-no-box"><div class="uap-account-no-box-inside"><div class="uap-count"><?php echo uap_format_price_and_currency($data['currency'], $data['stats']['paid_payments_value']);?></div><div class="uap-detail"><?php _e('Total Transactions Amount', 'uap');?></div></div></div>
		</div>	
		<div class="uapcol-md-2 uap-account-payments-tab2">
			<div class="uap-account-no-box"><div class="uap-account-no-box-inside"><div class="uap-count"><?php echo $data['stats']['payments']?></div><div class="uap-detail"><?php _e('Total Transactions', 'uap');?></div></div></div>
		</div>
	</div>
<?php echo $data['filter'];?>
<div class="uap-wrapper">
	<?php if (!empty($data['listing_items'])) : ?>
		<table class="uap-account-table">
			<thead>
				<tr>
					<th><?php _e('Amount', 'uap');?></th>
					<th><?php _e('Payment Type', 'uap');?></th>
					<th><?php _e('Create Date', 'uap');?></th>
					<th><?php _e('Update Date', 'uap');?></th>
					<th><?php _e('Status', 'uap');?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>	
					<th><?php _e('Amount', 'uap');?></th>
					<th><?php _e('Payment Type', 'uap');?></th>
					<th><?php _e('Create Date', 'uap');?></th>
					<th><?php _e('Update Date', 'uap');?></th>
					<th><?php _e('Status', 'uap');?></th>
				</tr>
			</tfoot>
			<tbody class="uap-alternate">
				<?php foreach ($data['listing_items'] as $key => $array): ?>
				<tr>
					<td style="font-weight:bold;"><?php echo uap_format_price_and_currency($array['currency'], $array['amount']);?></td>
					<td><?php echo __($array['payment_type'], 'uap');?></td>
					<td><?php echo uap_convert_date_to_us_format($array['create_date']);?></td>
					<td><?php echo uap_convert_date_to_us_format($array['update_date']);?></td>
					<td class="uap-special-label"><?php
						switch ($array['status']){
							case 0:
								?>
									<div><?php _e('Fail', 'uap');?></div>
								<?php 								
								break;
							case 1:
								?>
									<div><?php _e('Pending', 'uap');?></div>
								<?php 
								break;
							case 2:
								?>
									<div><?php _e('Complete', 'uap');?></div>
								<?php 									
								break;
						} 
					?></td>
				</tr>				
				<?php endforeach;?>
			</tbody>
		</table>
	<?php endif;?>
	
	<?php if (!empty($data['pagination'])):?>
		<?php echo $data['pagination'];?>
	<?php endif;?>
</div>