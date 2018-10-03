<div class="uap-wrapper">
	<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Visits', 'uap');?></span></div>
	
		<?php if (!empty($data['subtitle'])):?>
			<h4><?php echo $data['subtitle'];?></h4>
		<?php endif;?>
	
	<div class="uap-special-box">
		<?php echo $data['filter'];?>
	</div>
	
	<?php if (!empty($data['listing_items'])) : ?>
		
			<div style="display: inline-block;float: right;margin-right:10px;    margin: 10px 0px 10px 30px;">
				<strong><?php _e('Number of Visits to Display:', 'uap');?></strong> 
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
		
			<form action="" method="post" id="form_visits">
				<div class="uap-delete-wrapp">
					<input type="submit" value="<?php _e('Delete', 'uap');?>" name="delete" class="button button-primary button-large">
				</div>
					<table class="wp-list-table widefat fixed tags uap-admin-tables">
						<thead>
							<tr>
								<th style="width: 30px;"><input type="checkbox" onClick="uap_select_all_checkboxes( this, '.uap-delete-visit' );" /></th>
								<th><?php _e('IP', 'uap');?></th>
								<th><?php _e('Affiliate Username', 'uap');?></th>
								<th style="width: 80px;"><?php _e('Referral ID', 'uap');?></th>
								<th style="min-width: 210px"><?php _e('URL', 'uap');?></th>								
								<th style="width: 70px;"><?php _e('Browser', 'uap');?></th>
								<th style="width: 56px;"><?php _e('Device', 'uap');?></th>
								<th><?php _e('Date', 'uap');?></th>
								<th><?php _e('Status', 'uap');?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>	
								<th style="width: 30px;"><input type="checkbox" onClick="uap_select_all_checkboxes( this, '.uap-delete-visit' );" /></th>
								<th><?php _e('IP', 'uap');?></th>
								<th style="width: 80px;"><?php _e('Affiliate Username', 'uap');?></th>
								<th><?php _e('Referral ID', 'uap');?></th>
								<th style="min-width: 210px"><?php _e('URL', 'uap');?></th>								
								<th style="width: 70px;"><?php _e('Browser', 'uap');?></th>
								<th style="width: 56px;"><?php _e('Device', 'uap');?></th>
								<th><?php _e('Date', 'uap');?></th>
								<th><?php _e('Status', 'uap');?></th>
							</tr>
						</tfoot>
						<tbody class="ui-sortable uap-alternate">
							<?php foreach ($data['listing_items'] as $array) : ?>
								<tr onmouseover="uap_dh_selector('#visit_<?php echo $array['id'];?>', 1);" onmouseout="uap_dh_selector('#visit_<?php echo $array['id'];?>', 0);">
									<th><input type="checkbox" value="<?php echo $array['id'];?>" name="delete_visits[]" class="uap-delete-visit"/></th>
									<td>
										<?php echo $array['ip'];?>
										<div id="visit_<?php echo $array['id'];?>" style="visibility: hidden;">
											<a onclick="uap_delete_from_table(<?php echo $array['id'];?>, 'Visit', '#delete_visit_h', '#form_visits');" href="javascript:return false;" style="color: red;"><?php _e('Delete', 'uap');?></a>										
										</div>	
									</td>
									<td><?php
									echo '<div class="uap-list-affiliates-name-label">';
										if (!empty($array['username']))
											echo $array['username'];
										else _e('Unknown', 'uap');
									echo '</div>';
									?></td>
									<td><?php if (empty($array['referral_id'])) echo '-'; else echo $array['referral_id'];?></td>
									<td><a href="<?php echo $array['url'];?>" target="_blank"><?php echo $array['url'];?></a></td>
									<td><?php echo $array['browser'];?></td>
									<td><i class="<?php echo "fa-uap fa-" . $array['device'] . "-uap";?>"></i></td>
									<td style="color: #396;"><?php echo uap_convert_date_to_us_format($array['visit_date']);?></td>
									<td><?php 
										if (!empty($array['referral_id'])) echo '<div class="referral-status-verified">' . __('Converted', 'uap') . '</div>';
										else echo '<div class="referral-status-refuse">' . __('Just Visit', 'uap') . '</div>';
									?></td>
								</tr>
							<?php endforeach;?>
						</tbody>
					</table>
				<div class="uap-delete-wrapp">
					<input type="submit" value="<?php _e('Delete', 'uap');?>" name="delete" class="button button-primary button-large">
				</div>
				<div style="float:right; display:inline-block">
					<?php 
						if (!empty($data['pagination'])) : 
							echo $data['pagination'];
						endif;
					?>
				</div>				
				<input type="hidden" name="delete_visits[]" value="" id="delete_visit_h" />
			</form>
		
		<?php else : ?>
			<h4 style="margin-top:50px;"><?php _e('No Visits Stored!', 'uap');?></h4>
		<?php endif;?>	
</div>
</div><!-- end of uap-dashboard-wrap -->