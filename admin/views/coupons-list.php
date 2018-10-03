			<form action="" method="post" id="form_coupons">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Affiliates Coupons', 'uap');?></h3>
					<div class="inside">
						<div class="row">
							<div class="col-xs-7">
								<h3><?php _e('Activate/Hold Coupons option for your Affiliates', 'uap');?></h3>
								<p><?php _e('You can activate this option to take place in your affiliate system.', 'uap');?></p>
								<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
									<?php $checked = ($data['metas']['uap_coupons_enable']) ? 'checked' : '';?>
									<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_coupons_enable');" <?php echo $checked;?> />
									<div class="switch" style="display:inline-block;"></div>
								</label>
								<input type="hidden" name="uap_coupons_enable" value="<?php echo $data['metas']['uap_coupons_enable'];?>" id="uap_coupons_enable" /> 
							</div>
						</div>	
						<input type="hidden" name="delete_coupons" value="" id="delete_coupons" />
						<div class="uap-submit-form" style="margin-top:25px;"> 
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>		
					</div>
				</div>
			</form>
			
<div class="uap-wrapper">
	<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Listing Coupons', 'uap');?></span></div>
	<a href="<?php echo $data['url-add_edit'] . '&add_edit=0';?>" class="uap-add-new-like-wp"><i class="fa-uap fa-add-uap"></i><span><?php _e('Add new Coupon', 'uap');?></span></a>
	
	<div style="margin-top: 15px;"></div>	
	<?php if (!empty($data['listing_items'])):?>
		<table class="wp-list-table widefat fixed tags uap-admin-tables">
			<thead>
				<tr>
					<th><?php _e('Coupon', 'uap');?></th>
					<th><?php _e('Type', 'uap');?></th>
					<th><?php _e('Affiliate', 'uap');?></th>
					<th><?php _e('Status', 'uap');?></th>	
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><?php _e('Coupon', 'uap');?></th>
					<th><?php _e('Type', 'uap');?></th>
					<th><?php _e('Affiliate', 'uap');?></th>
					<th><?php _e('Status', 'uap');?></th>	
				</tr>
			</tfoot>			
		<?php $i = 0; foreach ($data['listing_items'] as $k=>$array):?>
			<tr onmouseover="uap_dh_selector('#aff_<?php echo $array['id'];?>', 1);" onmouseout="uap_dh_selector('#aff_<?php echo $array['id'];?>', 0);" class="<?php if ($i%2==0) echo 'alternate';?>">
				<th><?php 
					echo $array['code'];?>
					<div id="<?php echo 'aff_' . $array['id'];?>" style="visibility: hidden;">
						<a href="<?php echo $data['url-add_edit'] . '&add_edit=' . $array['code'];?>"><?php _e('Edit', 'uap');?></a> | <a onclick="uap_delete_from_table(<?php echo $array['id'];?>, 'Coupon', '#delete_coupons', '#form_coupons');" href="javascript:return false;" style="color: red;"><?php _e('Delete', 'uap');?></a>										
					</div>						
				</th>
				<th><?php echo $array['type'];?></th>
				<th><?php echo $indeed_db->get_wp_username_by_affiliate_id($array['affiliate_id']);?></th>
				<th><?php if ($array['status']) _e('Enabled');
				else _e('Disabled');?></th>	
			</tr>			
		<?php $i++; endforeach;?>
		</table>
	<?php endif;?>
</div>		
<?php
