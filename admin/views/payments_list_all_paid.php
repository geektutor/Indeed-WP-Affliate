<div class="uap-wrapper">
	<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Paid Referrals', 'uap');?></span></div>

		<?php if (!empty($data['subtitle'])):?>
			<h4><?php echo $data['subtitle'];?></h4>
		<?php endif;?>

	<?php if (!empty($data['listing_items'])) : ?>

		<?php
			if (!empty($data['filter'])):
				echo '<div class="uap-special-box">'.$data['filter'].'</div>';
			endif;
		?>

		<table class="wp-list-table widefat fixed tags uap-admin-tables">
			<thead>
				<tr>
					<th><?php _e('Affiliate', 'uap');?></th>
					<th><?php _e('Reference', 'uap');?></th>
					<th><?php _e('Amount', 'uap');?></th>
					<th><?php _e('Date', 'uap');?></th>
					<th><?php _e('Paid', 'uap');?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><?php _e('Affiliate', 'uap');?></th>
					<th><?php _e('Reference', 'uap');?></th>
					<th><?php _e('Amount', 'uap');?></th>
					<th><?php _e('Date', 'uap');?></th>
					<th><?php _e('Paid', 'uap');?></th>
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
					<td><?php echo $array['reference'];?></td>
					<td><?php echo uap_format_price_and_currency($array['currency'], $array['amount']);?></td>
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
								_e('Complete', 'uap');
								break;
						}
					?></td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	<?php endif;?>

	<?php if (!empty($data['payments_settings']['type'])):?>
		<h3><?php _e('Payment Details', 'ihc')?></h3>
		<?php if (!empty($data['payment_details_on_transaction'])):?>

			<?php if (!empty($data['payment_details_on_transaction']['payment_type'])):?>
				<div>
					<label><?php _e('Payment Type', 'uap');?>: </label><?php echo $data['payment_details_on_transaction']['payment_type'];?>
					<?php unset($data['payment_details_on_transaction']['payment_type']);?>
				</div>
			<?php endif;?>

			<?php foreach ($data['payment_details_on_transaction'] as $temp_array):?>
				<div>
					<label><?php echo $temp_array['label'];?>: </label><?php echo $temp_array['value'];?>
				</div>
			<?php endforeach;?>

		<?php endif;?>
	<?php endif;?>

	<?php if (!empty($data['pagination'])) : ?>
		<?php echo $data['pagination'];?>
	<?php endif;?>
</div>
