<div class="uap-wrapper">
		<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Notifications', 'uap');?></span></div>
		<a href="<?php echo $data['url-add_edit'];?>" class="uap-add-new-like-wp"><i class="fa-uap fa-add-uap"></i><?php _e('Activate New Notification', 'uap');?></a>
		<a href="javascript:void(0)" class="button button-primary button-large" style="display:inline-block; float:right;" onClick="uap_check_email_server();"><?php _e('Check Mail Server', 'uap');?></a>
		<div class="uap-clear"></div>
		<div style="margin-top: 20px;">
		<?php if (!empty($data['listing_items'])) : ?>
		
			<form action="" method="post" id="form_notification">
				
					<table class="wp-list-table widefat fixed tags uap-admin-tables">
						<thead>
							<tr>
								<th><?php _e('Subject', 'uap');?></th>
								<th><?php _e('Action', 'uap');?></th>
								<th><?php _e('Goes to', 'uap');?></th>
								<th><?php _e('Target Ranks', 'uap');?></th>
								<?php if ($indeed_db->is_magic_feat_enable('pushover')):?>
								<th class="manage-column uap-text-center"><?php _e('Mobile Notifications', 'uap');?></th>
								<?php endif;?>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><?php _e('Subject', 'uap');?></th>
								<th><?php _e('Action', 'uap');?></th>
								<th><?php _e('Goes to', 'uap');?></th>
								<th><?php _e('Target Ranks', 'uap');?></th>
								<?php if ($indeed_db->is_magic_feat_enable('pushover')):?>
								<th class="manage-column uap-text-center"><?php _e('Mobile Notifications', 'uap');?></th>
								<?php endif;?>								
							</tr>
						</tfoot>
						<tbody class="ui-sortable uap-alternate">
							<?php
								$admin_notifications = array(
															'admin_user_register',										
															'admin_on_aff_change_rank',
															'admin_affiliate_update_profile',									
								);
							?>
							<?php foreach ($data['listing_items'] as $arr) : ?>
								<?php 
									if (empty($data['email_verification']) && ($arr->type=='email_check' || $arr->type=='email_check_success')){
										continue;
									}
								?>
								<tr onmouseover="uap_dh_selector('#notification_<?php echo $arr->id;?>', 1);" onmouseout="uap_dh_selector('#notification_<?php echo $arr->id;?>', 0);">
									<td>
										<?php echo $arr->subject;?>
										<div id="notification_<?php echo $arr->id;?>" style="visibility: hidden;">
											<a href="<?php echo $data['url-add_edit'] . '&id=' . $arr->id;?>"><?php _e('Edit', 'uap');?></a> 
											| 
											<a onclick="uap_delete_from_table(<?php echo $arr->id;?>, 'Notification', '#delete_notification_id', '#form_notification');" href="javascript:return false;" style="color: red;"><?php _e('Delete', 'uap');?></a>										
										</div>	
									</td>
									<td><div class="uap-list-affiliates-name-label" style="width:auto;"><?php if (!empty($data['actions_available'][$arr->type])) echo $data['actions_available'][$arr->type];?></div></td>
									<td><?php 
										if (in_array($arr->type, $admin_notifications)){
											echo 'Admin';
										} else {
											echo 'Affiliate';
										}
									?></td>
									<td><?php 
										if ($arr->rank_id==-1) _e("All", 'uap'); 
										else if (!empty($data['ranks'][$arr->rank_id])) echo $data['ranks'][$arr->rank_id];?>
									</td>
									<?php if ($indeed_db->is_magic_feat_enable('pushover')):?>
										<td class="uap-text-center">
											<?php if (!empty($arr->pushover_status)):?>
												<i class="fa-uap fa-pushover-on-uap"></i>	
											<?php endif;?>
										</td>	
									<?php endif;?>									
								</tr>
							<?php endforeach;?>
						</tbody>
					</table>
				
				<input type="hidden" name="delete_notification" value="" id="delete_notification_id" />
			</form>		
			
		<?php else :?>
		
			<h5><?php _e('No Notification Available!', 'uap');?></h5>
		
		<?php endif;?>
	</div>	
</div>


</div><!-- end of uap-dashboard-wrap -->


<?php
