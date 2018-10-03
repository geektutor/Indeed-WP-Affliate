<div class="uap-ap-wrap">

<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>




<div class="uap-row">

	<div class="uapcol-md-2 uap-account-wallet-tab1">
		<div class="uap-account-no-box uap-account-box-blue">
			<div class="uap-account-no-box-inside">
				<div class="uap-count"> <?php echo uap_format_price_and_currency($data['currency'], round(@$data['stats']['wallet'], 2) ); ?> </div>
				<div class="uap-detail"><?php echo __('Wallet available Credit', 'uap'); ?></div>
			</div>
		</div>
	</div>

	<div class="uapcol-md-2 uap-account-wallet-tab2">
		<div class="uap-account-no-box uap-account-box-green">
			<div class="uap-account-no-box-inside">
				<div class="uap-count"> <?php echo uap_format_price_and_currency($data['currency'], round($data['stats']['unpaid_payments_value'], 2));?> </div>
				<div class="uap-detail"><?php echo __('Available Deposit based on your Earnings', 'uap'); ?></div>
			</div>
		</div>
	</div>

</div>
<?php if ($data['stats']['unpaid_payments_value'] && $data['stats']['unpaid_payments_value']>=$settings['uap_wallet_minimum_amount']):?>
		<a href="<?php echo $data['add_new'];?>" class="uap-addd-to-wallet"><?php _e('Add New Wallet Credit', 'uap');?></a>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>
<?php if ($data['items']):?>
	<table class="uap-account-table">
		<thead>
			<tr>
				<th><?php _e('Coupon Code', 'uap');?></th>
				<th><?php _e('Type', 'uap');?></th>
				<th><?php _e('Amount', 'uap');?></th>
				<th><?php _e('Delete', 'uap');?></th>
			</tr>
		</thead>
	<?php foreach ($data['items'] as $k=>$v):?>
		<tr>
			<td style="font-size: 20px;"><?php echo $v['code'];?></td>
			<td><?php echo uap_service_type_code_to_title($v['type']);?></td>
			<td><?php echo uap_format_price_and_currency($data['currency'], $v['amount']);?></td>
			<td><i onClick="uap_remove_wallet_item('<?php echo $v['type']?>', '<?php echo $v['code']?>');" class="fa-uap fa-trash-uap"></i></td>
		</tr>
	<?php endforeach;?>
	</table>
<?php endif;?>
</div>

<script>
	var uap_current_url = '<?php echo $base_url;?>';
</script>
