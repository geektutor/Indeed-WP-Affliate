<div class="uap-ap-wrap">
<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['content'])):?>
	<p><?php echo do_shortcode($data['content']);?></p>
<?php endif;?>

<form action="" method="post">
	<div class="uap-ap-field">
		<label class="uap-ap-label"><?php _e('Link', 'uap');?></label> 
		<input type="text" name="url" class="uap-public-form-control" />
	</div>
	<div class="uap-change-password-field-wrap">
		<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
	</div>	
	
	<?php if (!empty($data['err'])):?>
		<div class="uap-wrapp-the-errors">
			<div><?php echo $data['err'];?></div>
		</div>
	<?php endif;?>
	
</form>

<?php if (!empty($data['items'])):?>
<div class="uap-stuffbox">
	<table class="uap-account-table">
		<thead>
			<tr>
				<th><?php _e('Link', 'uap');?></th>
				<th><?php _e('Status', 'uap');?></th>
				<th><?php _e('Action', 'uap');?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><?php _e('Link', 'uap');?></th>
				<th><?php _e('Status', 'uap');?></th>
				<th><?php _e('Action', 'uap');?></th>
			</tr>
		</tfoot>
		<tbody class="uap-alternate">
			<?php foreach ($data['items'] as $item):?>
			<tr>
				<td><?php echo $item['url'];?></td>
				<td>
					<?php if ($item['status']):?>
						<?php _e('Active', 'uap');?>
					<?php else:?>
						<?php _e('Pending', 'uap');?>
					<?php endif;?>
				</td>
				<td>
					<a href="<?php echo add_query_arg('del', $item['id'], $data['url']);?>" style="color: red;"><?php _e('Delete', 'uap');?></a>
				</td>				
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>
<?php endif;?>
</div>
