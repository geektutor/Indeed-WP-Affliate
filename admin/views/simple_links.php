
<div class="uap-wrapper">
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Referrer Links', 'uap');?></h3>
		<div class="inside">
			<div class="row">
				<div class="col-xs-7">
				<h2><?php _e('Activate/Hold Simple Links', 'uap');?></h2>
				<p><?php _e('An affiliate’s name can now be masked by creating custom links. Users will no longer avoid links that could benefit a certain affiliate.', 'uap');?></p>
				<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_simple_links_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_simple_links_enabled');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="uap_simple_links_enabled" value="<?php echo $data['metas']['uap_simple_links_enabled'];?>" id="uap_simple_links_enabled" /> 												
				</div>	
			</div>			
			<div class="uap-line-break"></div>		
							
			<div class="uap-inside-item">		
				<div class="row" style="margin-left:0px;">
					<div class="col-xs-4" style="margin-bottom: 10px;">
						<h3><?php _e('Referrer Links Limit', 'uap');?></h3>
						<p><?php _e('The number of links that can be submitted by an affiliate in his “Account Page”.', 'uap');?></p>
						<div class="input-group" style="margin:0px 0 15px 0;">
							<span class="input-group-addon" id="basic-addon1"><?php _e('Links Limit per Affiliate', 'uap');?></span>										
							<input type="number" min="0" step="1" class="uap-field-text-with-padding form-control" name="uap_simple_links_limit" value="<?php echo $data['metas']['uap_simple_links_limit'];?>" />
						</div>	
					</div>
				</div>
			</div>				
																	
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>		
					
		</div>
	</div>	

	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Add New Referrer Link', 'uap');?></h3>
		<div class="inside">	
				<div class="row" style="margin-left:0px;">
					<div class="col-xs-5" style="margin-bottom: 10px;">
					<p><?php _e('Attach a referrer link to a specific affiliate user directly from the “UAP Dashboard”.', 'uap');?></p>
						<div class="input-group" style="margin:0px 0 15px 0;">
							<span class="input-group-addon" id="basic-addon1"><?php _e('Affiliate', 'uap');?></span>										
							<input type="text" class="uap-field-text-with-padding form-control" name="affiliate_name" id="affiliate_name"/>				
							<input type="hidden" class="uap-field-text-with-padding form-control" name="affiliate_id" id="affiliate_id"/>
						</div>
						<div class="input-group" style="margin:0px 0 15px 0;">
							<span class="input-group-addon" id="basic-addon1"><?php _e('Referrer Link', 'uap');?></span>										
							<input type="text" class="uap-field-text-with-padding form-control" name="url" />
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
			<h3 class="uap-h3"><?php _e('Search Links for Affiliate', 'uap');?></h3>
			<div class="inside">
				<div class="row" style="margin-left:0px;">
					<div class="col-xs-5" style="margin-bottom: 10px;">
						<div class="input-group" style="margin:0px 0 15px 0;">
							<span class="input-group-addon" id="basic-addon1"><?php _e('Affiliate Username', 'uap');?></span>
						<input type="text" class="uap-field-text-with-padding form-control" id="affiliate_name_search" />
						<input type="hidden" id="search_aff_id" name="search_aff_id" />
						</div>
						<span class="button button-primary button-large" onClick="uap_do_redirect('<?php echo admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=simple_links');?>', 'affiliate_id', '#search_aff_id');"><?php _e('Search', 'uap');?></span>
						
					</div>	
				</div>	
			</div>
		</div>	
		
<div class="uap-stuffbox">

	<table class="wp-list-table widefat fixed tags uap-admin-tables" style="font-size: 11px;">
		<thead>
			<tr>
				<th><?php _e('Affiliate', 'uap');?></th>
				<th><?php _e('Referrer Link', 'uap');?></th>
				<th><?php _e('Status', 'uap');?></th>
				<th><?php _e('Action', 'uap');?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><?php _e('Affiliate', 'uap');?></th>
				<th><?php _e('Referrer Link', 'uap');?></th>
				<th><?php _e('Status', 'uap');?></th>
				<th><?php _e('Action', 'uap');?></th>
			</tr>
		</tfoot>
		<tbody>
			<?php $i = 1;
				foreach ($data['items'] as $item):?>
			<tr class="<?php if($i%2==0) echo 'alternate';?>">
				<td style="color: #21759b; font-weight:bold; width:120px;font-family: 'Oswald', arial, sans-serif !important;font-size: 14px;font-weight: 400;"><?php echo $item['username'];?></td>
				<td><a href="<?php echo $item['url'];?>" target="_blank"><?php echo $item['url'];?></a></td>
				<td>
					<?php if ($item['status']):?>
						<div class="uap-subcr-type-list "><?php _e('Active', 'uap');?></div>						
					<?php else:?>
						<div class="uap-subcr-type-list uap-pending"><?php _e('Pending', 'uap');?></div>
					<?php endif;?>
				</td>
				<td>
					<?php if (!$item['status']):?>
						<a href="<?php echo admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=simple_links&approve=' . $item['id']);?>"><?php _e('Approve', 'uap');?></a> | 
					<?php endif;?>
					<a href="<?php echo admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=simple_links&delete=' . $item['id']);?>" style="color: red;"><?php _e('Delete', 'uap');?></a>
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
<?php endif;?>


<script>
jQuery(function() {

	/// USERNAME SEARCH
	jQuery("#affiliate_name").bind("keydown", function(event){
		if (event.keyCode===jQuery.ui.keyCode.TAB &&
			jQuery(this).autocomplete("instance").menu.active) {
		 	event.preventDefault();
		}
	}).autocomplete({
		minLength: 0,
		source: '<?php echo UAP_URL . 'admin/Uap_Coupons_Ajax_Autocomplete.php';?>?users=true',
		focus: function() {},
		select: function( event, ui ) {		
			jQuery('#affiliate_name').val(ui.item.label);	
			jQuery('#affiliate_id').val(ui.item.id);	 	 	
		 	return false;
		}
	});

	/// USERNAME SEARCH
	jQuery("#affiliate_name_search").bind("keydown", function(event){
		if (event.keyCode===jQuery.ui.keyCode.TAB &&
			jQuery(this).autocomplete("instance").menu.active) {
		 	event.preventDefault();
		}
	}).autocomplete({
		minLength: 0,
		source: '<?php echo UAP_URL . 'admin/Uap_Coupons_Ajax_Autocomplete.php';?>?users=true',
		focus: function() {},
		select: function( event, ui ) {		
			jQuery('#affiliate_name_search').val(ui.item.label);	
			jQuery('#search_aff_id').val(ui.item.id);	 	 	
		 	return false;
		}
	});
		
});

</script>
</div>