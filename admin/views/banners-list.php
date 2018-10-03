<div class="uap-wrapper">
	<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Listing Banners', 'uap');?></span></div>
		<a href="<?php echo $data['url-add_edit'];?>" class="uap-add-new-like-wp"><i class="fa-uap fa-add-uap"></i><?php _e('Add New Banner', 'uap');?></a>
		<span class="uap-top-message"><?php _e('...create Banners for your Affiliates', 'uap');?></span>
		<?php if (!empty($data['listing_items'])) : ?>
			<form action="" method="post" id="form_banners" style="margin-top:30px;">
				<div class="uap-delete-wrapp">
					<input type="submit" value="<?php _e('Delete', 'uap');?>" name="delete" class="button button-primary button-large">
				</div>
					<table class="wp-list-table widefat fixed tags uap-admin-tables">
						<thead>
							<tr>
								<th style="width: 50px;"><input type="checkbox" onClick="uap_select_all_checkboxes( this, '.uap-delete-banner' );" /></th>
								<th><?php _e('Name', 'uap');?></th>
								<th><?php _e('Image', 'uap');?></th>
								<th><?php _e('URL', 'uap');?></th>
								<th><?php _e('Create Time', 'uap');?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>	
								<th style="width: 50px;"><input type="checkbox" onClick="uap_select_all_checkboxes( this, '.uap-delete-banner' );" /></th>
								<th><?php _e('Name', 'uap');?></th>
								<th><?php _e('Image', 'uap');?></th>
								<th><?php _e('URL', 'uap');?></th>
								<th><?php _e('Create Time', 'uap');?></th>
							</tr>
						</tfoot>
						<tbody class="ui-sortable uap-alternate">
							<?php foreach ($data['listing_items'] as $arr) : ?>
								<tr onmouseover="uap_dh_selector('#banner_<?php echo $arr->id;?>', 1);" onmouseout="uap_dh_selector('#banner_<?php echo $arr->id;?>', 0);">
									<th style="vertical-align: top;"><input type="checkbox" value="<?php echo $arr->id;?>" name="delete_banner[]" class="uap-delete-banner"/></th>
									<td>
										<div class="uap-list-affiliates-name-label"><?php echo $arr->name;?></div>
										<div id="banner_<?php echo $arr->id;?>" style="visibility: hidden;">
											<a href="<?php echo $data['url-add_edit'] . '&id=' . $arr->id;?>">Edit</a> 
											| 
											<a onclick="uap_delete_from_table(<?php echo $arr->id;?>, 'Banner', '#delete_banner_id', '#form_banners');" href="javascript:return false;" style="color: red;"><?php _e('Delete', 'uap');?></a>										
										</div>	
									</td>									
									<td><img src="<?php echo $arr->image;?>" class="uap-list-banner-img" /></td>
									<td><?php echo '<a href ="'.$arr->url.'" target="_blank">'.$arr->url.'</a>';?></td>
									<td style="color: #396;"><?php echo uap_convert_date_to_us_format($arr->DATE);?></td>
								</tr>
							<?php endforeach;?>
						</tbody>
					</table>
				<div class="uap-delete-wrapp">
					<input type="submit" value="<?php _e('Delete', 'uap');?>" name="delete" class="button button-primary button-large">
				</div>
				<input type="hidden" name="delete_banner[]" value="" id="delete_banner_id" />
			</form>
		<?php else : ?>
			<h5><?php _e('No Banners Available!', 'uap');?></h5>
		<?php endif;?>	
</div>
</div><!-- end of uap-dashboard-wrap -->