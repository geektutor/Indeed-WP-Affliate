<div class="uap-wrapper">
	<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Referrals (rewards)', 'uap');?></span></div>

		<?php if (!empty($data['error'])):?>
			<div class="uap-wrapp-the-errors">
				<?php echo $data['error'];?>
			</div>
		<?php endif;?>

		<?php if (!empty($data['subtitle'])):?>
			<h4><?php echo $data['subtitle'];?></h4>
		<?php endif;?>

		<a href="<?php echo $data['url-add_edit'];?>" class="uap-add-new-like-wp"><i class="fa-uap fa-add-uap"></i><?php _e('Add New Referral', 'uap');?></a>
		<span class="uap-top-message"><?php _e('...add manual Referral (reward) for specific Affiliate', 'uap');?></span>

		<div class="uap-special-box" style="margin-top: 20px;">
		<?php echo $data['filter'];?>
		</div>

		<?php if (!empty($data['listing_items'])) : ?>
			<form action="" method="post" id="form_referrals">

				<div style="display: inline-block;float: left;margin: 10px 0px 10px 0px;">
					<select name="list_action"><?php
						foreach ($data['actions'] as $k=>$v):
							?>
							<option value="<?php echo $k;?>" <?php if ($data['current_actions']==$k) echo 'selected';?>><?php echo $v;?></option>
							<?php
						endforeach;
					?></select>
					<input type="submit" name="apply_bttn" value="<?php _e('Apply', 'uap');?>" class="button action" />
				</div>

				<div style="display: inline-block; float: right; margin: 10px 0px 10px 30px;">
					<strong><?php _e('Number of Referrals to Display:', 'uap');?></strong>
					<select name="uap_limit" onchange="window.location = '<?php echo $data['base_list_url'];?>&uap_limit='+this.value;">
						<?php
							foreach ($this->items_per_page as $value){
								$selected = ($value==$limit) ? 'selected' : '';
								?>
								<option value="<?php echo $value;?>" <?php echo $selected;?>><?php echo $value;?></option>
								<?php
							}
						?>
					</select>
				</div>
				<div style="float:right; display:inline-block">
					<?php
						if (!empty($data['pagination'])) :
							echo $data['pagination'];
						endif;
					?>
				</div>

					<table class="wp-list-table widefat fixed tags uap-admin-tables">
						<thead>
							<tr>
								<th style="width: 50px;"><input type="checkbox" onClick="uap_select_all_checkboxes( this, '.uap-delete-referral' );" /></th>
								<th style="width: 60px;"><?php _e('User ID', 'uap');?></th>
								<th><?php _e('Affiliate', 'uap');?></th>
								<th><?php _e('ID', 'uap');?></th>
								<th><?php _e('From', 'uap');?></th>
								<th><?php _e('Reference', 'uap');?></th>
								<th><?php _e('Description', 'uap');?></th>
								<th><?php _e('Amount', 'uap');?></th>
								<th><?php _e('Date', 'uap');?></th>
								<th><?php _e('Status', 'uap');?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th style="width: 50px;"><input type="checkbox" onClick="uap_select_all_checkboxes( this, '.uap-delete-referral' );" /></th>
								<th style="width: 60px;"><?php _e('User ID', 'uap');?></th>
								<th><?php _e('Affiliate', 'uap');?></th>
								<th><?php _e('ID', 'uap');?></th>
								<th><?php _e('From', 'uap');?></th>
								<th><?php _e('Referance', 'uap');?></th>
								<th><?php _e('Description', 'uap');?></th>
								<th><?php _e('Amount', 'uap');?></th>
								<th><?php _e('Date', 'uap');?></th>
								<th><?php _e('Status', 'uap');?></th>
							</tr>
						</tfoot>
						<tbody class="ui-sortable uap-alternate">
							<?php foreach ($data['listing_items'] as $array) : ?>
								<tr onmouseover="uap_dh_selector('.hidden-div-referral-<?php echo $array['id'];?>', 1);" onmouseout="uap_dh_selector('.hidden-div-referral-<?php echo $array['id'];?>', 0);">
									<th style="vertical-align: top;"><input type="checkbox" value="<?php echo $array['id'];?>" name="referral_list[]" class="uap-delete-referral"/></th>
									<?php $temp_uid = $indeed_db->get_uid_by_affiliate_id($array['affiliate_id']);?>
									<td><a href="<?php echo admin_url('user-edit.php?user_id=' . $temp_uid);?>" target="_blank"><?php echo $temp_uid;?></a></td>
									<td><?php
										echo '<div class="uap-list-affiliates-name-label">';
											if (!empty($array['username']))
												echo $array['username'];
											else _e('Unknown', 'uap');
										echo '</div>';
									?>
									<div id="referral_<?php echo $array['id'];?>" class="<?php echo 'hidden-div-referral-' . $array['id'];?>" style="visibility: hidden;">
											<a href="<?php echo $data['url-add_edit'] . '&id=' . $array['id'];?>"><?php _e('Edit', 'uap');?></a>
											|
											<a onclick="uap_delete_from_table(<?php echo $array['id'];?>, 'Refferal', '#delete_referral_id', '#form_referrals');" href="javascript:return false;" style="color: red;"><?php _e('Delete', 'uap');?></a>
										</div>
									</td>
									<td><?php echo $array['id'];?></td>
									<td><?php echo uap_service_type_code_to_title($array['source']);?></td>
									<td>
										<?php
											$link = '';
											if (!empty($array['reference'])){
												switch ($array['source']){
													case 'woo':
														if (!empty($data['woo_order_base_link'])){
															$link = $data['woo_order_base_link'] . $array['reference'] . '&action=edit';
														}
														break;
													case 'ulp':
														if (!empty($data['ulp_order_base_link'])){
															$link = $data['ulp_order_base_link'] . $array['reference'] . '&action=edit';
														}
														break;
													case 'edd':
														if (!empty($data['edd_order_base_link'])){
															$link = $data['edd_order_base_link'] . $array['reference'];
														}
														break;
													case 'ump':
														if (function_exists('ihc_get_payment_id_by_order_id')){
															$payment_id = ihc_get_payment_id_by_order_id($array['reference']);
															if ($payment_id){
																if (!empty($data['ump_order_base_link'])){
																	$link = $data['ump_order_base_link'] . $payment_id;
																}
															}
														}
														break;
													case 'mlm':
														$the_ref = $array['reference'];
														$the_ref = str_replace('mlm_', '', $the_ref);
														$link = $data['mlm_order_base_link'] . $the_ref;
														break;
													case 'User SignUp':
														if (!empty($array['reference']) && strpos($array['reference'], 'user_id_')!==FALSE){
															$uid_sign_up = str_replace('user_id_', '', $array['reference']);
															$link = $data['user_sign_up_link'] . $uid_sign_up;
														}
														break;
												}
											}
											if (!empty($link)){
												echo '<a href="' . $link . '" target="_blank">' . $array['reference'] . '</a>';
											} else {
												echo $array['reference'];
											}
										?>
									</td>
									<td><?php echo $array['description'];?></td>
									<td><?php echo '<b>' . uap_format_price_and_currency($array['currency'], $array['amount']) . '</b>';?></td>
									<td style="color: #396;"><?php echo uap_convert_date_to_us_format($array['date']);?></td>
									<td><?php
											/*
											 * 1 - UNVERIFIED
											 * 2 - VERIFIED
											 * 0 - REFUSE
											 */
										if (!$array['status']){
											?>
											<div class="referral-status-refuse"><?php _e('Refuse', 'uap');?></div>
											<?php
										} else if ($array['status']==1){
											?>
											<div class="referral-status-unverified"><?php _e('Unverified', 'uap');?></div>
											<?php
										} else if ($array['status']==2){
											?>
											<div class="referral-status-verified"><?php _e('Verified', 'uap');?></div>
											<?php
										}
									?><div>
											<?php
												$status_arr = array(0 => __('Refuse', 'uap'), 1 => __('Unverified', 'uap'), 2 => __('Verified', 'uap') );
												$i = 1;
												foreach ($status_arr as $k=>$v){
													if ($k!=$array['status']){
													 if($i != 1) echo " | ";
													  $i++;
													?>
													<span class="refferal-chang-status" onClick="jQuery('#change_status').val('<?php echo $array['id'] . '-' . $k;?>');jQuery('#form_referrals').submit();"><?php _e('Mark as ', 'uap');?><?php echo $v;?></span>
													<?php
													}
												}
											?>
									</div>
									</td>
								</tr>
							<?php endforeach;?>
						</tbody>
					</table>

				<div style="float:right; display:inline-block">
					<?php
						if (!empty($data['pagination'])) :
							echo $data['pagination'];
						endif;
					?>
				</div>
				<input type="hidden" name="change_status" value="" id="change_status" />
				<input type="hidden" name="delete_referral[]" value="" id="delete_referral_id" />

				<div style="float: left; display:inline-block; padding: 10px 0px">
					<select name="list_action_2"><?php
						foreach ($data['actions'] as $k=>$v):
							?>
							<option value="<?php echo $k;?>" <?php if ($data['current_actions']==$k) echo 'selected';?>><?php echo $v;?></option>
							<?php
						endforeach;
					?></select>
					<input type="submit" name="apply_bttn" value="<?php _e('Apply', 'uap');?>" class="button action" />
				</div>

			</form>
		<?php else : ?>
			<h4 style="margin-top:50px;"><?php _e('No Referrals Stored!', 'uap');?></h4>
		<?php endif;?>
</div>
</div><!-- end of uap-dashboard-wrap -->
