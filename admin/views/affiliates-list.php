<?php wp_enqueue_script('uapAdminSendEmail', UAP_URL . 'assets/js/uapAdminSendEmail.js', array(), null ); ?>
<div class="uap-wrapper">
		<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Listing Affiliates', 'uap');?></span></div>
		<a href="<?php echo $data['url-add_edit'];?>" class="uap-add-new-like-wp"><i class="fa-uap fa-add-uap"></i><span><?php _e('Add new Affiliate', 'uap');?></span></a>

		<?php echo $data['errors'];?>

		<div class="uap-special-buttons-users">
			<div class="uap-special-button" onclick="jQuery('.uap-filters-wrapper').toggle();"><i class="fa-uap fa-search-uap"></i><?php _e('Add Filters', 'uap')?></div>
		</div>
		<?php
			$hidded = 'style="display:none;"';
			if (isset($_REQUEST['search_user'])|| isset($_REQUEST['ordertype_rank']) || isset($_REQUEST['orderby_user']) || isset($_REQUEST['ordertype_user']) ) $hidded ='';
		?>
		<div class="uap-filters-wrapper" <?php echo $hidded; ?>>
			<form method="get" action="<?php echo $data['base_list_url'];?>">
				<input type="hidden" name="page" value="ultimate_affiliates_pro" />
				<input type="hidden" name="tab" value="affiliates" />
				<div class="row-fluid">
					<div class="uap-span4">
						<div class="iump-form-line iump-no-border">
							<input name="search_t" type="text" value="<?php echo (isset($_REQUEST['search_t']) ? $_REQUEST['search_t'] : '') ?>" placeholder="<?php _e('Search by Name or Username', 'uap');?>..."/>
						</div>
					</div>
					<div class="uap-span2">
						<div class="iump-form-line iump-no-border">
							<select name="ordertype_rank">
								<?php
									$ranks = array(-1=>'...') + $indeed_db->get_rank_list();
									if ($ranks!==FALSE){
										foreach ($ranks as $k=>$v){
											$selected = (isset($_REQUEST['ordertype_rank']) && $_REQUEST['ordertype_rank']==$k) ? 'selected' : '';
											?>
											<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
											<?php
										}
									}
								?>
							</select>
						</div>
					</div>
					<div class="uap-span3">
						<div class="iump-form-line iump-no-border">
							<select name="orderby_user">
								<option value="display_name" <?php echo (isset($_REQUEST['orderby_user']) && $_REQUEST['orderby_user']=='display_name') ? 'selected' : ''; ?>><?php _e('Name', 'uap');?></option>
								<option value="user_login" <?php echo (isset($_REQUEST['orderby_user']) && $_REQUEST['orderby_user']=='user_login') ? 'selected' : ''; ?>><?php _e('Username', 'uap');?></option>
								<option value="user_email" <?php echo (isset($_REQUEST['orderby_user']) && $_REQUEST['orderby_user']=='user_email') ? 'selected' : ''; ?>><?php _e('Email', 'uap');?></option>
								<option value="ID" <?php echo (isset($_REQUEST['orderby_user']) && $_REQUEST['orderby_user']=='ID') ? 'selected' : ''; ?>><?php _e('ID', 'uap');?></option>
								<option value="user_registered" <?php echo (isset($_REQUEST['orderby_user']) && $_REQUEST['orderby_user']=='user_registered') ? 'selected' : ''; ?>><?php _e('Registered Time', 'uap');?></option>
							</select>
							<select name="ordertype_user">
								<option value="ASC" <?php echo (isset($_REQUEST['ordertype_user']) && $_REQUEST['ordertype_user']=='ASC') ? 'selected' : ''; ?>><?php _e('ASC', 'uap');?></option>
								<option value="DESC" <?php echo (isset($_REQUEST['ordertype_user']) && $_REQUEST['ordertype_user']=='DESC') ? 'selected' : ''; ?>><?php _e('DESC', 'uap');?></option>
							</select>
						</div>
					</div>
					<div class="uap-span1" style="padding:30px 10px 0 0;">
						<input type="submit" value="<?php _e('Search', 'uap');?>" name="search" class="button button-primary button-large">
					</div>
				</div>
			</form>
		</div>

		<?php if ($data['listing_affiliates']):?>
			<div style="display: inline-block;float: right;margin-right:10px;margin-top: 5px;">
				<strong><?php _e('Number of Users to Display:', 'uap');?></strong>
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

			<form action="" method="post" id="form_affiliates">

					<div style="display: inline-block;float: left;margin: 10px 0px 10px 0px;">
						<select name="do_action">
							<option value="0" selected="">...</option>
							<option value="delete"><?php _e('Delete', 'uap');?></option>
							<option value="update_ranks"><?php _e('Update Rank', 'uap');?></option>
						</select>
						<input type="submit" name="apply_bttn" value="Apply" class="button action" onClick="check_submit_affiliate_action();return false;"/>
					</div>

					<table class="wp-list-table widefat fixed tags uap-admin-tables" style="font-size: 11px;">
						<thead>
							<tr>
								<th style="width: 40px;"><input type="checkbox" onClick="uap_select_all_checkboxes( this, '.uap-delete-affiliates' );" /></th>
								<th style="width: 58px;"><?php _e('Affiliate ID', 'uap');?></th>
								<th style="width:13%;"><?php _e('UserName', 'uap');?></th>
								<th><?php _e('Name', 'uap');?></th>
								<th style="width:8%;"><?php _e('E-mail', 'uap');?></th>
								<th><?php _e('Rank', 'uap');?></th>
								<th><?php _e('Visits', 'uap');?></th>
								<!--th><?php _e('Converted', 'uap');?></th-->
								<th style="width: 7%;"><?php _e('Referrals', 'uap');?></th>
								<th><?php _e('Paid Amount', 'uap');?></th>
								<th><?php _e('UnPaid Amount', 'uap');?></th>
								<th style="width:200px;"><?php _e('Metrics', 'uap');?></th>
								<th><?php _e('Wp Role', 'uap');?></th>
								<?php if (!empty($data['email_verification'])):?>
								<th><?php _e('E-mail Status', 'uap');?></th>
								<?php endif;?>
								<th style="width:6%;"><?php _e('Affiliate Since', 'uap');?></th>
								<th><?php _e('Details', 'uap');?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th style="width: 40px;"><input type="checkbox" onClick="uap_select_all_checkboxes( this, '.uap-delete-affiliates' );" /></th>
								<th style="width: 58px;"><?php _e('Affiliate ID', 'uap');?></th>
								<th><?php _e('UserName', 'uap');?></th>
								<th><?php _e('Name', 'uap');?></th>
								<th><?php _e('E-mail', 'uap');?></th>
								<th><?php _e('Rank', 'uap');?></th>
								<th><?php _e('Visits', 'uap');?></th>
								<!--th><?php _e('Converted', 'uap');?></th-->
								<th><?php _e('Referrals', 'uap');?></th>
								<th><?php _e('Paid Amount', 'uap');?></th>
								<th><?php _e('UnPaid Amount', 'uap');?></th>
								<th style="width:180px;"><?php _e('Metrics', 'uap');?></th>
								<th><?php _e('Wp Role', 'uap');?></th>
								<?php if (!empty($data['email_verification'])):?>
								<th><?php _e('E-mail Status', 'uap');?></th>
								<?php endif;?>
								<th><?php _e('Affiliate Since', 'uap');?></th>
								<th><?php _e('Details', 'uap');?></th>
							</tr>
						</tfoot>
				<tbody class="ui-sortable">
					<?php $i = 1;
						foreach ($data['listing_affiliates'] as $id=>$arr):?>
						<tr onmouseover="uap_dh_selector('#aff_<?php echo $id;?>', 1);" onmouseout="uap_dh_selector('#aff_<?php echo $id;?>', 0);" class="<?php if($i%2==0) echo 'alternate';?>">
							<th><input type="checkbox" value="<?php echo $id;?>" name="affiliate_id_arr[]" class="uap-delete-affiliates"/></th>
							<td><?php echo $id;?></td>
							<td>
								<span><?php echo $this->print_flag_for_affiliate($arr['uid']) . $arr['username'];?></span>
								<?php
									if ($arr['payment_settings']):

										switch ($arr['payment_settings']['type']):
											case 'paypal':
												$payment_class = ($arr['payment_settings']['is_active']) ? 'uap-payment-type-active-paypal' : '';
												?>
												<a href="<?php echo $data['base_view_payment_settings_url'] . $arr['uid'];?> " target="_blank">
													<span class="uap-admin-aff-payment-type <?php echo $payment_class;?>"><?php _e('PayPal', 'uap');?></span>
												</a>
												<?php
												break;
											case 'bt':
												$payment_class = ($arr['payment_settings']['is_active']) ? 'uap-payment-type-active-bt' : '';
												?>
												<a href="<?php echo $data['base_view_payment_settings_url'] . $arr['uid'];?> " target="_blank">
													<span class="uap-admin-aff-payment-type <?php echo $payment_class;?>"><?php _e('Bank Transfer', 'uap');?></span>
												</a>
												<?php
												break;
											case 'stripe':
												$payment_class = ($arr['payment_settings']['is_active']) ? 'uap-payment-type-active-stripe' : '';
												?>
												<a href="<?php echo $data['base_view_payment_settings_url'] . $arr['uid'];?> " target="_blank">
													<span class="uap-admin-aff-payment-type <?php echo $payment_class;?>"><?php _e('Stripe', 'uap');?></span>
												</a>
												<?php
												break;
											case 'stripe_v2':
												$payment_class = ($arr['payment_settings']['is_active']) ? 'uap-payment-type-active-stripe' : '';
												?>
												<a href="<?php echo $data['base_view_payment_settings_url'] . $arr['uid'];?> " target="_blank">
													<span class="uap-admin-aff-payment-type <?php echo $payment_class;?>"><?php _e('Stripe V2', 'uap');?></span>
												</a>
												<?php
												break;
										endswitch;
									else :
										?>
										<span class="uap-admin-aff-payment-type">-</span>
										<?php
									endif;
								?>
								<div id="aff_<?php echo $id;?>" style="visibility: hidden;">
									<a href="<?php echo $data['url-add_edit'] . '&id=' . $arr['uid'];?>"><?php _e('Edit', 'uap');?></a> | <a onclick="uap_delete_from_table(<?php echo $id;?>, 'Affiliate', '#delete_affiliate', '#form_affiliates');" href="javascript:return false;" style="color: red;"><?php _e('Delete', 'uap');?></a>
									<?php if ($arr['role']=='pending_user'):?>
										| <a onClick="uap_approve_affiliate(<?php echo $arr['uid'];?>);" href="javascript:return false;"><?php _e('Approve Affiliate', 'uap');?></a>
									<?php endif;?>
									<?php if ($arr['email_status']==-1): ?>
										<span id="<?php echo 'approve_email_' . $arr['uid'];?>" onClick="uap_approve_email(<?php echo $arr['uid'];?>, '<?php _e("Verified", "uap");?>');">
										| <span style="cursor:pointer; color: #0074a2;"><?php _e('Approve E-mail', 'uap');?></span>
										</span>
									<?php endif;?>
								</div>
							</td>
							<td><div class="uap-list-affiliates-name-label"><?php echo $arr['name'];?></div></td>
							<td><?php echo $arr['email'];?></td>
							<?php $style = (isset($arr['rank_color'])) ? 'background-color:#' . $arr['rank_color'] : 'background-color:#c9c9c9;';?>
							<td><div class="rank-type-list" style="<?php echo $style;?>"><?php echo $arr['rank_label'];?></div></td>

							<td class="uap-affiliate-list-counts">
								<div><?php echo @$arr['stats']['visits'];?></div>
								<?php if (!empty($arr['stats']['visits'])): ?>
									<a href="<?php echo $data['base_visits_url'] . '&affiliate_id=' . $id;?>"><?php _e('View', 'uap');?></a>
								<?php endif;?>
							</td>
                            <!--td class="uap-affiliate-list-counts">
								<div><?php echo @$arr['stats']['converted'];?></div>
                            </td-->
                            <td class="uap-affiliate-list-counts">
								<div>
									<?php echo @$arr['stats']['referrals'];?>
								</div>
								<?php if (!empty($data['base_referrals_url']) && $arr['stats']['referrals']): ?>
									<a href="<?php echo $data['base_referrals_url'] . '&affiliate_id=' . $id;?>"><?php _e('View', 'uap');?></a>
								<?php endif;?>
							</td>
							<td class="uap-affiliate-list-counts">
								<div><?php echo uap_format_price_and_currency($currency, @$arr['stats']['paid_payments_value']);?></div>
								<?php if (!empty($arr['stats']['paid_payments_value'])): ?>
									<a href="<?php echo $data['base_paid_url'] . '&affiliate=' . $id;?>"><?php _e('View', 'uap');?></a>
								<?php endif;?>
							</td>
							<td class="uap-affiliate-list-counts">
								<strong style="color: #9b4449;"><?php echo uap_format_price_and_currency($currency, @$arr['stats']['unpaid_payments_value']);?></strong>
								<?php if (!empty($arr['stats']['unpaid_payments_value'])):?>
									<div><a href="<?php echo $data['base_unpaid_url'] . '&affiliate=' . $id;?>"><?php _e('Proceed', 'uap');?></a> | <a href="<?php echo $data['base_pay_now'] . '&affiliate=' . $id;?>"><?php _e('Pay All', 'uap');?></a></div>
								<?php endif;?>
							</td>
									<td class="uap-metrics-cell">
									<div class="uap-metris-leftside">
									<?php if (!empty($data['show_ppc'])):?>
										<div>
											<?php $ppc = $indeed_db->getReferralsBySourceAndAffiliate('ppc', $id);?>
											<?php echo __('CPC: ', 'uap') . $ppc;?>
										</div>
									<?php endif;?>
                                    <?php if (!empty($data['show_cpm'])): ?>
									<div>
									<?php
											$cpm = $indeed_db->getReferralsBySourceAndAffiliate('cpm', $id);
											$number = $indeed_db->getCPMForAffiliate($id);
											if ($number){
													$number = $number / 10;
											}
										echo __('CPM: ', 'uap') . $cpm ;?>
                                      <div class="uap-progress-bar"><div class="uap-progress-completed" style="width:<?php echo  $number; ?>%;"></div></div>  									</div>
									<?php endif;?>
                                    </div>
                                    <div class="uap-metris-rightside">
                                      <div>
											<?php $epc3 = $indeed_db->getEPCdata('3months', $id);?>
											<?php echo __('3 months EPC: ', 'uap');
												echo uap_format_price_and_currency($currency, $epc3); ;?>
										</div>

                                      <div>
											<?php $epc7 = $indeed_db->getEPCdata('7days', $id);?>
											<?php echo __('7 days EPC: ', 'uap');
												echo uap_format_price_and_currency($currency, $epc7); ;?>
									  </div>
                                    </div>
                                    <div class="uap-clear"></div>
								</td>
							<?php $pending = ($arr['role']=='pending_user') ? 'uap-pending' : '';?>
							<td><div class="uap-subcr-type-list <?php echo $pending;?>"><?php if (isset($data['ranks_list'][$arr['role']])) echo $data['ranks_list'][$arr['role']];?></div></td>
							<?php if (!empty($data['email_verification'])):?>
							<td><?php
			    				$div_id = "user_email_" . $arr['uid'] . "_status";
			    				$class = 'uap-subcr-type-list';
			    				if ($arr['email_status']==1){
			    					$label = __('Verified', 'ihc');
			    				} else if ($arr['email_status']==-1){
			    					$label = __('Unapproved', 'ihc');
			    					$class .= ' uap-pending';
			    				} else {
				   					$label = '-';
								}
			    				?>
			    				<div id="<?php echo $div_id;?>">
			    					<span class="<?php echo $class;?>"><?php echo $label;?></span>
			    				</div>
			    			</td>
							<?php endif;?>
							<td style="color: #396;"><?php echo uap_convert_date_to_us_format($arr['start_data']);?></td>
							<td>
								<div class="referral-status-verified" style="display: inline-block; margin: 3px;background-color: #38cbcb; font-size:9px;"><a style="color:#fff;" href="<?php echo $data['base_transations_url'] . '&affiliate=' . $id;?>"><?php _e('Transactions', 'uap');?></a></div>
								<?php if (!empty($data['mlm_on']) && $indeed_db->affiliate_has_childrens($id) ) : ?>
									<div class="referral-status-unverified" style="display: inline-block; margin: 3px;background-color: #0a9fd8; font-size:9px;"><a style="color:#fff;" href="<?php echo $data['mlm_matrix_link'] . $arr['username'];?>"><?php _e('MLM Matrix', 'uap');?></a></div>
								<?php endif;?>
								<div class="referral-status-unverified" style="display: inline-block; margin: 3px;background-color: #f1505b; font-size:9px;"><a style="color:#fff;" href="<?php echo $data['base_reports_url'] . '&affiliate_id=' . $id;?>"><?php _e('Reports', 'uap');?></a></div>
								<div class="uap_frw_button uap_small_grey_button uap-admin-do-send-email-via-ump" data-uid="<?php echo $arr['uid'];?>"><?php _e('Direct Email', 'uap');?></div>
							</td>
						</tr>
					<?php $i++;
						endforeach;?>
				</tbody>
			</table>
			<input type="hidden" value='' name="delete_affiliate" id="delete_affiliate" />
		</form>
		<?php
			if (!empty($data['pagination'])) :
				echo $data['pagination'];
			endif;
		?>

		<?php else : ?>
			<h4 style="margin-top:50px;"><?php _e('No Affiliates Stored!', 'uap');?></h4>
		<?php endif;?>
</div>
</div><!-- end of uap-dashboard-wrap -->
