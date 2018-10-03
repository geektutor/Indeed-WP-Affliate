<style>
	<?php foreach ($data['menu'] as $slug => $item):?>
		<?php echo '.fa-' . $slug . '-account-uap:before';?>{
			content: '\<?php echo $item['uap_tab_' . $slug . '_icon_code'];?>';
			font-size: 20px;
		}
	<?php endforeach;?>
</style>

<div class="uap-wrapper">
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Account Page - Customize Menu', 'uap');?></h3>
		<div class="inside">
			
			<div class="uap-form-line">
				<h2><?php _e('Activate/Hold Customize Menu', 'uap');?></h2>
				<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_account_page_menu_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_account_page_menu_enabled');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="uap_account_page_menu_enabled" value="<?php echo $data['metas']['uap_account_page_menu_enabled'];?>" id="uap_account_page_menu_enabled" /> 												
			</div>					
											
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>		
					
		</div>
	</div>				


	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Add new Menu Item', 'uap');?></h3>
		<div class="inside">
			<div class="uap-form-line">
				<div class="row" style="margin-left:0px;">
					<div class="col-xs-4" style="margin-bottom: 10px;">
				   		<div class="input-group" style="margin:0px 0 15px 0;">
							<span class="input-group-addon" id="basic-addon1"><?php _e('Slug', 'uap');?></span>										
							<input type="text" name="slug" class="form-control" value="">
				   		</div>	
				   		<div class="input-group" style="margin:0px 0 15px 0;">
							<span class="input-group-addon" id="basic-addon1"><?php _e('Label', 'uap');?></span>										
							<input type="text" name="label" class="form-control" value="">
				   		</div>	
				   		<div class="input-group" style="margin:0px 0 15px 0;">
							<label><?php _e('Icon', 'uap');?></label>		
							<div class="uap-icon-select-wrapper">
								<div class="uap-icon-input">
									<div id="indeed_shiny_select" class="uap-shiny-select-html"></div>	
								</div>							
				   				<div class="uap-icon-arrow"><i class="fa-uap fa-arrow-uap"></i></div>
								<div class="uap-clear"></div>
							</div>
						</div>	
					</div>
				</div>				
			 </div>
			
											
			<div class="uap-submit-form"> 
				<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>		
					
		</div>
	</div>	

	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('ReOrder Menu Items', 'uap');?></h3>
		<div class="inside">
			<div class="uap-sortable-table-wrapp">
				<table class="wp-list-table widefat fixed tags uap-admin-tables" id="uap_reorder_menu_items" style="width:100%;position:relative;">
					<thead>
						<tr>
							<th class="manage-column"><?php _e('Slug', 'uap');?></th>
							<th class="manage-column"><?php _e('Label', 'uap');?></th>
							<th class="manage-column"><?php _e('Icon', 'uap');?></th>
							<th class="manage-column" style="width: 70px;"><?php _e('Delete', 'uap');?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th class="manage-column"><?php _e('Slug', 'uap');?></th>
							<th class="manage-column"><?php _e('Label', 'uap');?></th>
							<th class="manage-column"><?php _e('Icon', 'uap');?></th>
							<th class="manage-column" style="width: 70px;"><?php _e('Delete', 'uap');?></th>
						</tr>						
					</tfoot>
					<tbody style="width:100%;">
						<?php $k = 0;?>
						<?php $data['menu'] = uap_reorder_menu_items($data['metas']['uap_account_page_menu_order'], $data['menu']);?>
						<?php foreach ($data['menu'] as $slug=>$item):?>
							<?php $value = isset($data['metas']['uap_account_page_menu_order'][$slug]) ? $data['metas']['uap_account_page_menu_order'][$slug] : $k;?>
							<tr class="<?php if($k%2==0) echo 'alternate';?>" id="tr_<?php echo $slug;?>" style="width:100%;">
								<td style="position:relative; width:30%; min-width:200px;"><input type="hidden" value="<?php echo $value;?>" name="uap_account_page_menu_order[<?php echo $slug;?>]" class="uap_account_page_menu_order" />
								<?php echo $slug;?></td>
								<td style="position:relative; min-width:200px; width:30%;color: #21759b; font-weight:bold;font-family: 'Oswald', arial, sans-serif !important;font-size: 14px;font-weight: 400;"><?php 
									if (isset($item['uap_tab_' . $slug . '_menu_label'])){
										echo $item['uap_tab_' . $slug . '_menu_label'];
									} else {
										echo $item['label'];		
									}
								?></td>
								<td style="position:relative; width:20%; min-width:100px;">
									<?php if (!empty($item['uap_tab_' . $slug . '_icon_code'])):?>
										<i class="<?php echo 'fa-uap fa-' . $slug . '-account-uap';?>"></i></td>										
									<?php else:?>
										-									
									<?php endif;?>
								<td style="position:relative; width:20%; min-width:100px;">
									<?php 
										if (isset($data['standard_tabs'][$slug])){
											echo '-';		
										} else {
											?>
											<a href="<?php echo admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=account_page_menu&delete=' . $slug);?>">
											<i class="fa-uap uap-icon-remove-e"></i>											
											<?php
										}							
									?>
								</a></td>						
							</tr>
							<?php $k++;?>
						<?php endforeach;?>	
					</tbody>
				</table>
			</div>
									
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>				
			
		</div>
	</div>

</form>

<?php
require_once UAP_PATH . 'public/font_awesome_codes.php';
$font_awesome = uap_return_font_awesome();
?>
<style>
<?php foreach ($font_awesome as $base_class => $code):?>
	<?php echo '.' . $base_class . ':before';?>{
		content: '\<?php echo $code;?>'
	}
<?php endforeach;?>
</style>
<script>
jQuery(document).ready(function(){
	var indeed_shiny_object = new indeed_shiny_select({
				selector: '#indeed_shiny_select', 
				item_selector: '.uap-font-awesome-popup-item', 
				option_name_code: 'icon_code', 
				option_name_icon: 'icon_class',
				default_icon: '',
				default_code: '',
				init_default: false,
				second_selector: '.uap-icon-arrow'
	});
});
</script>
</div>