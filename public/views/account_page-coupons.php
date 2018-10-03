<div class="uap-banners-wrapp">

<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>
	<?php if (!empty($data['codes'])) : ?>
		<table class="uap-account-table">
			<thead>
				<tr>
					<th><?php _e('Code', 'uap');?></th>
					<th><?php _e('Source', 'uap');?></th>
					<th><?php _e('Amount', 'uap');?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><?php _e('Code', 'uap');?></th>
					<th><?php _e('Source', 'uap');?></th>
					<th><?php _e('Amount', 'uap');?></th>
				</tr>
			</tfoot>
			<tbody class="uap-alternate">
				<?php foreach ($data['codes'] as $arr) : ?>
					<tr>
						<td><?php echo $arr['code'];?></td>
						<td><?php echo uap_service_type_code_to_title($arr['type']);?></td>
						<td><?php
							$settings = unserialize($arr['settings']);
							if ($settings){
								if ($settings['amount_type']=='flat'){
									echo uap_format_price_and_currency($data['currency'], $settings['amount_value']);
								} else {
									echo $settings['amount_value'] . ' %';
								}
							}
						?></td>
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	<?php endif;?>
</div>
