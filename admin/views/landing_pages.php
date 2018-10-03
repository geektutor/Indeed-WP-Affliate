
<div class="uap-wrapper">
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Affiliate Landing Pages', 'uap');?></h3>
		<div class="inside">
        	<div class="uap-inside-item">
			<div class="row">
				<div class="col-xs-7">
				<h2><?php _e('Activate/Hold Landing Pages', 'uap');?></h2>
				<p><?php _e('An affiliate can be linked with a specific page from your website. Users will no longer avoid links that could benefit a certain affiliate because no affiliate link will be required on this case.', 'uap');?></p>
				<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_landing_pages_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_landing_pages_enabled');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="uap_landing_pages_enabled" value="<?php echo $data['metas']['uap_landing_pages_enabled'];?>" id="uap_landing_pages_enabled" />
				</div>
			</div>
            </div>
			<div class="uap-line-break"></div>

			<div class="uap-inside-item">
				<div class="row">
					<div class="col-xs-8" style="margin-bottom: 10px;">
						<h3><?php _e('How it Works', 'uap');?></h3>
						<p><?php _e('Once this Module is enabled, you will find on your editing page/post section an additional MetaBox dedicated for this purpose. There you can search for a specific <strong>Affiliate user</strong> and assiging him with current page.', 'uap');?></p>

					</div>
				</div>
			</div>

			<div class="uap-submit-form" style="margin-top: 20px;">
				<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>

		</div>
	</div>

</form>


<?php if (!empty($data['items'])):?>
	<div class="uap-stuffbox">
			<h3 class="uap-h3"><?php _e('Associated Landing pages with Affiliates', 'uap');?></h3>
			<div class="inside">


	<table class="wp-list-table widefat fixed tags uap-admin-tables" style="font-size: 11px;">
		<thead>
			<tr>
				<th><?php _e('Affiliate', 'uap');?></th>
				<th><?php _e('Landing Page', 'uap');?></th>
				<th><?php _e('Action', 'uap');?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><?php _e('Affiliate', 'uap');?></th>
				<th><?php _e('Landing Page', 'uap');?></th>
				<th><?php _e('Action', 'uap');?></th>
			</tr>
		</tfoot>
		<tbody>
			<?php $i = 1;
				foreach ($data['items'] as $item):?>
			<tr class="<?php if($i%2==0) echo 'alternate';?>">
				<td style="color: #21759b; font-weight:bold; width:120px;font-family: 'Oswald', arial, sans-serif !important;font-size: 14px;font-weight: 400;">
					<?php echo $item->user_login;?>
				</td>
				<td><a href="<?php echo $link = get_permalink( $item->ID );?>" target="_blank"><?php echo $item->post_title . " ( $link )";?></a></td>
				<td>
					<a href="<?php echo admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=landing_pages&delete=' . $item->post_meta_id);?>" style="color: red;"><?php _e('Delete', 'uap');?></a>
				</td>
			</tr>
			<?php $i++;
			endforeach;?>
		</tbody>
	</table>
	<?php if (!empty($data['pagination'])):?>
		<?php echo $data['pagination'];?>
	<?php endif;?>
</div>
</div>
<?php endif;?>



</div>
