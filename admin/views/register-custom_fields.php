			<form action="" method="post" id="custom_fields_form">
				<span class="uap-add-new-like-wp">
					<i class="fa-uap fa-add-uap"></i>
					<a href="<?php echo $data['url_edit_custom_fields'];?>" class="uap-add-new-like-wp"><?php _e('Add New Register Form Field', 'uap');?></a>
				</span>				
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Registration Form', 'uap');?></h3>
					<div class="inside">						
						<div style="margin-bottom: 15px;">
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>
								<div class="uap-sortable-table-wrapp">

									<table class="wp-list-table widefat fixed tags uap-alternate" id='uap-register-fields-table'>
										    <thead>
												<tr>
													<th class="manage-column"><?php _e('Slug', 'uap');?></th>
													<th class="manage-column"><?php _e('Label', 'uap');?></th>
													<th class="manage-column"><?php _e('Field Type', 'uap');?></th>
													<th class="manage-column"><?php _e('On Admin', 'uap');?></th>
													<th class="manage-column"><?php _e('On Register Page', 'uap');?></th>
													<th class="manage-column"><?php _e('On Account Page', 'uap');?></th>
													<th class="manage-column"><?php _e('Required', 'uap');?></th>
													<th class="manage-column"><?php _e('WP Native', 'uap');?></th>
													<th class="manage-column" style="width: 25px;"></th>
													<th class="manage-column" style="width: 35px;"></th>
												</tr>
											</thead>
											<tfoot>
												<tr>
													<th class="manage-column"><?php _e('Slug', 'uap');?></th>
													<th class="manage-column"><?php _e('Label', 'uap');?></th>
													<th class="manage-column"><?php _e('Field Type', 'uap');?></th>
													<th class="manage-column"><?php _e('On Admin', 'uap');?></th>
													<th class="manage-column"><?php _e('On Register Page', 'uap');?></th>
													<th class="manage-column"><?php _e('On Account Page', 'uap');?></th>
													<th class="manage-column"><?php _e('Required', 'uap');?></th>
													<th class="manage-column"><?php _e('WP Native', 'uap');?></th>
													<th class="manage-column" style="width: 25px;"></th>
													<th class="manage-column" style="width: 35px;"></th>
												</tr>								
											</tfoot>	
											<tbody>
									<?php 				 
									$no_edit = array(
														'uap_social_media',
														'user_email',
														'first_name',
														'last_name',
														'user_url',
														'pass1',
														'pass2',
														'description',
									);
												
									$no_delete_fields = array(
														'user_login',
														'uap_avatar', 
														'recaptcha', 
														'tos', 
														'uap_country',
														'uap_social_media',
														'user_email',
														'first_name',
														'last_name',
														'user_url',
														'pass1',
														'pass2',
														'description',														
									);
									foreach ($data['register_fields'] as $k=>$v){
											
										switch ($v['name']){
											case 'uap_avatar':
												$tr_extra_class = "uap-avatar-tr-dashboard";
												break;
											default:
												$tr_extra_class = '';
												break;
										}																				
										?>
										<tr class="<?php echo $tr_extra_class;?>" id="tr_<?php echo $k;?>">
											<td>
												<?php echo $v['name'];?>
												<input type="hidden" value="<?php echo $k;?>" name="uap-order-<?php echo $k;?>" class="uap-order" />	
											</td>
											<td><?php 
													if ($v['native_wp']){
														_e($v['label'], 'uap');
													} else {
														echo $v['label'];
													}													
												?>
											</td>
											<td><?php echo $v['type'];?></td>
											<td>
												<?php 
													if($v['name']=='uap_social_media' || $v['name']=='payment_select'){
														echo '-';
													} else if ($v['display_admin']==2){
														_e('Always', 'uap');
													} else {
														?>
														<input type="checkbox" onClick="check_and_h(this, '#uap-field-display-admin<?php echo $k;?>');inc_req(this, <?php echo $k;?>);" <?php if($v['display_admin']) echo 'checked';?> />
														<input type="hidden" value="<?php echo $v['display_admin'];?>" name="uap-field-display-admin<?php echo $k;?>" id="uap-field-display-admin<?php echo $k;?>" />
														<?php 
													}
												?>
											</td>
											<td>
												<?php 
													if ($v['display_public_reg']==2){
														_e('Always', 'uap');
													} else {
														?>
														<input type="checkbox" onClick="check_and_h(this, '#uap-field-display-public-reg<?php echo $k;?>');inc_req(this, <?php echo $k;?>);" <?php if($v['display_public_reg']) echo 'checked';?> />
														<input type="hidden" value="<?php echo $v['display_public_reg'];?>" name="uap-field-display-public-reg<?php echo $k;?>" id="uap-field-display-public-reg<?php echo $k;?>" />
														<?php 
													}
												?>
											</td>
											<td>
												<?php 
													if ($v['display_public_ap']==2){
														_e('Always', 'uap');
													} else if (in_array($v['name'], array('uap_social_media', 'pass2', 'pass1'))){ 
														echo '-';
													} else {
														?>
														<input type="checkbox" onClick="check_and_h(this, '#uap-field-display-public-ap<?php echo $k;?>');inc_req(this, <?php echo $k;?>);" <?php if($v['display_public_ap']) echo 'checked';?> />
														<input type="hidden" value="<?php echo $v['display_public_ap'];?>" name="uap-field-display-public-ap<?php echo $k;?>" id="uap-field-display-public-ap<?php echo $k;?>" />
														<?php 
													}
												?>
											</td>																				
											<td>
												<?php 
													if ($v['display_public_reg']==2){
														_e('Always', 'uap');
													} else if ($v['req']==2){
														_e('Required When Selected', 'uap');
													} else if ($v['name']=='uap_social_media'){
														echo '-';
													} else {
														?>
														<input type="checkbox" onClick="check_and_h(this, '#uap-require-<?php echo $k;?>');" <?php if ($v['req']) echo 'checked';?> id="req-check-<?php echo $k;?>" />
														<input type="hidden" value="<?php echo $v['req'];?>" name="uap-require-<?php echo $k;?>" id="uap-require-<?php echo $k;?>" />												
														<?php 	
													}
												?>
											</td>
											<td>
												<?php 
													if ($v['native_wp']){
														_e('Yes', 'uap');
													} else {
														_e('No', 'uap');
													}
												?>
											</td>
											<td>
												<?php
													if(in_array($v['name'], $no_edit) ){ ///$no_edit
														echo '-';
													} else {
														?>
														<a href="<?php echo $data['url_edit_custom_fields'] . '&id='.$k;?>">
															<i class="fa-uap fa-edit-uap"></i>
														</a>
														<?php 	
													}
												?>											
											</td>
											<td>
												<?php 
													if ( in_array($v['name'], $no_delete_fields)){ ///$v['native_wp'] ||
														echo '-';
													} else {
														?>
															<a href="javascript: return false;" onClick="uap_delete_from_table(<?php echo $k;?>, 'custom_field', '#delete_custom_field', '#custom_fields_form');">
																<i class="fa-uap fa-remove-uap"></i>
															</a>												
														<?php 	
													}
												?>
											</td>
										</tr>
										<?php 		
										}
									?>
										</tbody>
									</table>
								</div>	
						<input type="hidden" value="" name="delete_custom_field" id="delete_custom_field" />
						<div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>	
					</div>
				</div>
			</form>		