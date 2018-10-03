<div class="uap-ap-wrap">

<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>	

<?php if (!empty($data['items']) && is_array($data['items'])):?>
	<div>
		<table class="uap-account-table">
		<thead>	
				<tr>
					<th><?php _e("Name", 'uap');?></th>
					<th align="center"><?php _e("Unique Visits", 'uap');?></th>
					<th align="center"><?php _e("Total Visits", 'uap');?></th>
					<th align="center"><?php _e("Referrals", 'uap');?></th>
				</tr>
			</thead>
			<tfoot>	
				<tr>
					<th><?php _e("Name", 'uap');?></th>
					<th align="center"><?php _e("Unique Visits", 'uap');?></th>
					<th align="center"><?php _e("Total Visits", 'uap');?></th>
					<th align="center"><?php _e("Referrals", 'uap');?></th>
				</tr>
			</tfoot>
			<tbody class="uap-alternate">
			<?php foreach ($data['items'] as $object) : ?>
				<tr>
					<td class="uap-special-label"><?php echo $object->name;?></td>
					<td align="center"><?php echo $object->unique_visits_count;?></td>
					<td align="center"><?php echo $object->visit_count;?></td>
					<td align="center"><?php echo $object->referrals;?></td>
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