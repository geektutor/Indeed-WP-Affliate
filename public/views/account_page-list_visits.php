<div class="uap-ap-wrap">
<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>
	<div class="uap-row">
		<div class="uapcol-md-3 uap-account-visits-tab1">
			<div class="uap-account-no-box uap-account-box-blue"><div class="uap-account-no-box-inside"><div class="uap-count"><?php echo $data['stats']['visits'];?></div><div class="uap-detail"><?php _e('Total Visits', 'uap');?></div></div></div>
		</div>	
		<div class="uapcol-md-3 uap-account-visits-tab2">
			<div class="uap-account-no-box uap-account-box-blue"><div class="uap-account-no-box-inside"><div class="uap-count"><?php echo $data['stats']['conversions'];?></div><div class="uap-detail"><?php _e('Conversions', 'uap');?></div></div></div>
		</div>
		<div class="uapcol-md-3 uap-account-visits-tab3">
			<div class="uap-account-no-box uap-account-box-red"><div class="uap-account-no-box-inside"><div class="uap-count"><?php echo $data['stats']['success_rate'] . ' %';?></div><div class="uap-detail"><?php _e('Success Rate', 'uap');?></div></div></div>
		</div>
	</div>
	<?php echo $data['filter'];?>
	<?php if (!empty($data['items']) && is_array($data['items'])):?>
		<table class="uap-account-table">
			<thead>	
				<tr>
					<th><?php _e("Reffering URL", 'uap');?></th>
					<th><?php _e("Browser", 'uap');?></th>
					<th><?php _e("Device", 'uap');?></th>
					<th><?php _e("Date", 'uap');?></th>
					<th><?php _e("Converted", 'uap');?></th>
				</tr>
			</thead>
			<tfoot>	
				<tr>
					<th><?php _e("Reffering URL", 'uap');?></th>
					<th><?php _e("Browser", 'uap');?></th>
					<th><?php _e("Device", 'uap');?></th>
					<th><?php _e("Date", 'uap');?></th>
					<th><?php _e("Converted", 'uap');?></th>
				</tr>
			</tfoot>
			<tbody class="uap-alternate">	
			<?php foreach ($data['items'] as $array) : ?>
				<tr>
					<td><a href="<?php echo $array['url'];?>" target="_blank"><?php echo $array['url'];?></a></td>
					<td><?php echo $array['browser'];?></td>
					<td><?php echo $array['device'];?></td>
					<td><?php echo uap_convert_date_to_us_format($array['visit_date']);?></td>
					<td class="uap-special-label" style="text-align:center;"><?php 
						if ($array['referral_id']) echo 'Yes';
						else echo 'No';
					?></td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>
	<?php endif;?>
</div>

<?php if (!empty($data['pagination'])):?>
	<?php echo $data['pagination'];?>
<?php endif;?>