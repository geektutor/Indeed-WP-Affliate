<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('WooCommerce Account Page', 'uap');?></h3>
		<div class="inside">
			
			<div class="row">
				<div class="col-xs-7">
					<h3><?php _e('Activate/Hold Affiliate Section', 'uap');?></h3>
					<p><?php _e('Fully integrate a user’s “Affiliate Account” in their “WooCommerce MyAccount”. Once activated, a new tab in their “ Woo MyAccount” menu will show up.', 'uap');?></p>
					<label class="uap_woo_account_page_enable" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_woo_account_page_enable']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_woo_account_page_enable');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="uap_woo_account_page_enable" value="<?php echo $data['metas']['uap_woo_account_page_enable'];?>" id="uap_woo_account_page_enable" /> 
				</div>
			</div>
						
			<div class="row">
				<div class="col-xs-5">
					<div class="input-group" style="margin:30px 0 15px 0;">
						<span class="input-group-addon" id="basic-addon1"><?php _e('Menu Label', 'uap');?></span>
						<input type="text" class="form-control" name="uap_woo_account_page_name" value="<?php echo $data['metas']['uap_woo_account_page_name'];?>" />
					</div>
				</div>
			</div>		

			<div class="row">
				<div class="col-xs-5">
					<div class="input-group" style="margin:30px 0 15px 0;">
						<span class="input-group-addon" id="basic-addon1"><?php _e('Menu Position', 'uap');?></span>
						<input type="number" class="form-control" name="uap_woo_account_page_menu_position" value="<?php echo $data['metas']['uap_woo_account_page_menu_position'];?>" min=1 />
					</div>
				</div>
			</div>		
			
			<div class="row">
				<div class="col-xs-7">
					<h3><?php _e('Non-Affiliate Users', 'uap');?></h3>
					<p><?php _e('Even the non-affiliate users will see the new option but instead of “Affiliate Account Page”, custom content will show up.', 'uap');?></p>
					<label class="woo_account_page_enable" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_woo_account_page_show_to_everyone']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_woo_account_page_show_to_everyone');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="uap_woo_account_page_show_to_everyone" value="<?php echo $data['metas']['uap_woo_account_page_show_to_everyone'];?>" id="uap_woo_account_page_show_to_everyone" /> 
				</div>
			</div>
						
			<div class="row">
				<div class="col-xs-12">
					<h3><?php _e('Custom Content for Non-Affiliate Users:', 'uap');?></h3>
					<div class="uap-wp_editor" style="width:65%; display: inline-block; vertical-align: top;">
					<?php wp_editor(stripslashes($data['metas']['uap_woo_account_page_non_affiliate_content']), 'uap_woo_account_page_non_affiliate_content', array('textarea_name'=>'uap_woo_account_page_non_affiliate_content', 'editor_height'=>200));?>
					</div>
				</div>
			</div>
											
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>		
					
		</div>
	</div>
</form>