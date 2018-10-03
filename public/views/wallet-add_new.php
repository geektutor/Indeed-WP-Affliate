<div class="uap-ap-wrap">

<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>

	<?php if ($data['services']): ?>
	<form action="<?php echo $data['form_action'];?>" method="post">
		<input type="hidden" name="uapcheck" value="<?php echo $data['hash'];?>" />
		<div class="uap-ap-field">
			<label class="uap-ap-label uap-special-label"><?php _e('Service Type', 'uap');?></label>
			<select name="service_type" class="uap-public-form-control"><?php
					foreach ($data['services'] as $k=>$v):
						?>
						<option value="<?php echo $k;?>"><?php echo $v;?></option>
						<?php
					endforeach;
			?></select>
		</div>
			<label class="uap-ap-label uap-special-label"><?php _e('Referrals', 'uap');?></label>
			<?php if ($data['referrals']):?>
				<div class="uap-wallet-table-wrapp">
					<table class="uap-account-table">
						<thead>
							<tr>
								<th><?php _e('Select', 'uap');?></th>
								<th><?php _e("Amount", 'uap');?></th>
								<th><?php _e("From", 'uap');?></th>
								<th><?php _e("Description", 'uap');?></th>
							</tr>
						</thead>
						<tbody class="uap-alternate">
						<?php foreach ($data['referrals'] as $array) : ?>
							<tr>
								<td><input type="checkbox" onClick="uap_add_to_wallet(this, <?php echo $array['id'];?>, '#the_referrals_list');"/></td>
								<td style="font-weight:bold; color:#111;"><?php echo uap_format_price_and_currency($array['currency'], $array['amount']);?></td>
								<td><?php echo (empty($array['source'])) ? '' : uap_service_type_code_to_title($array['source']);?></td>
								<td><?php echo $array['description'];?></td>
							</tr>
						<?php endforeach;?>
						</tbody>
					</table>
				</div>

			<?php endif;?>

		<input type="hidden" value="" id="the_referrals_list" name="referrals" />
		<div style="margin: 20px 0;">
			<div style="float:left">
				<input type="submit" value="<?php _e('Add On Wallet', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>
			<div style="float:right;">
				<div class="uap-wallet-total"><?php _e('Total Credit: ', 'uap');?> <?php echo uap_format_price_and_currency($data['currency'], '<span id="uap_total_amount">0</span>');?></div>
			</div>
			<div class="uap-clear"></div>
	   </div>
	</form>

	<?php else : ?>
		<?php _e('No service available!', 'uap');?>
	<?php endif;?>

</div>
