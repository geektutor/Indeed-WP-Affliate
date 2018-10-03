<script>
	var custom_aff_base_url = '<?php echo $url;?>';
</script>
<div class="uap-wrapper">
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Custom Affiliate Slug', 'uap');?></h3>
		<div class="inside">
			<div class="row">
				<div class="col-xs-7">
					<h3><?php _e('Activate/Hold Custom Affiliate Slug', 'uap');?></h3>
					<p><?php _e('Provides personal slugs besides the default username or ID so affiliates can hide their identity or company name behind a custom slug.', 'uap');?></p>
					<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<?php $checked = ($data['metas']['uap_custom_affiliate_slug_on']) ? 'checked' : '';?>
						<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_custom_affiliate_slug_on');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="uap_custom_affiliate_slug_on" value="<?php echo $data['metas']['uap_custom_affiliate_slug_on'];?>" id="uap_custom_affiliate_slug_on" /> 			
				</div>
			</div>
			<div class="row" style="margin-top: 30px;">
				<div class="col-xs-8">
					<p><?php _e('Establish conditional requirements when affiliates want to set their personal custom slug. Hint: The custom slug is unique and future users may not register it as their username or custom slug.', 'uap');?></p>
				</div>
			</div>	
			<div class="row" style="margin-bottom: 20px;">	
				<div class="col-xs-4">
					<div class="input-group" style="margin-top: 10px;">
						<label class="input-group-addon"><?php _e('Minimum number of characters', 'uap');?></label>
						<input type="number" class="form-control" value="<?php echo $data['metas']['uap_custom_affiliate_slug_min_ch'];?>" min="3" name="uap_custom_affiliate_slug_min_ch" />
					</div>
					<div class="input-group" style="margin-top: 10px;">
						<label class="input-group-addon"><?php _e('Maximum number of characters', 'uap');?></label>
						<input type="number" class="form-control" value="<?php echo $data['metas']['uap_custom_affiliate_slug_max_ch'];?>" min="3" name="uap_custom_affiliate_slug_max_ch" />
					</div>		
					<div class="input-group" style="margin-top: 10px;">
						<label class="input-group-addon"><?php _e('Slug characters rule', 'uap');?></label>
						<select name="uap_custom_affiliate_slug_rule">
							<?php foreach (array(0=>__('Standard', 'uap'), 1=>__('Characters and digits'), 2=>__('Characters, digits, minimum one uppercase letter', 'uap')) as $k=>$v):?>
								<?php $selected = ($k==$data['metas']['uap_custom_affiliate_slug_rule']) ? 'selected' : '';?>
								<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
							<?php endforeach;?>
						</select>
					</div>						
				</div>			
			</div>
			<div class="uap-submit-form"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>								
		</div>
	</div>
</form>			

<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Add/Edit Slug', 'uap');?></h3>
		<div class="inside">
			<div class="row">
				<div class="col-xs-7">
				<p><?php _e('You can add or edit a custom slug for a specific affiliate from your side.', 'uap');?></p>
					<div class="input-group" style="margin-top:10px;">
						<span class="input-group-addon" id="basic-addon1"><?php _e('Affiliate', 'uap');?></span>
						<input type="text"  class="form-control" value=""  id="affiliate_name" />
						<input type="hidden" id="affiliate_id_hidden" name="affiliate_id" value="<?php echo $data['metas']['affiliate_id'];?>]"/>
					</div>	
					<div class="input-group" style="margin-top:10px;">
						<span class="input-group-addon" id="basic-addon1"><?php _e('Slug', 'uap');?></span>
						<input type="text"  class="form-control" value="" name="slug" id="" />
					</div>										
				</div>
			</div>
			<div class="uap-submit-form" style="margin-top: 10px;"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save_slug" class="button button-primary button-large" />
			</div>				
		</div>
	</div>
</form>
	
<?php if ($data['items']):?>
	<div style="width: 98%;">
		<table class="wp-list-table widefat fixed tags uap-admin-tables">
			<thead>
				<tr>
					<th><?php _e('Username', 'uap');?></th>
					<th><?php _e('Slug', 'uap');?></th>
					<th><?php _e('Action', 'uap');?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><?php _e('Username', 'uap');?></th>
					<th><?php _e('Slug', 'uap');?></th>
					<th><?php _e('Action', 'uap');?></th>
				</tr>
			</tfoot>
		
				<?php  $i = 1;
				foreach ($data['items'] as $item):?>
					<tr class="<?php if($i%2==0) echo 'alternate';?>">
						<td><?php echo $item['username'];?></td>
						<td><?php echo $item['meta_value'];?></td>
						<td><i class="fa-uap fa-trash-uap" onClick="uap_remove_slug(<?php echo $item['user_id'];?>);"></i></td>
					</tr>
				<?php $i++;  
				endforeach;?>
		</table>	
	</div>
<?php endif;?>

<?php if ($data['pagination']):?>
<?php echo $data['pagination'];?>
<?php endif?>

<script>
jQuery(function() {

	/// USERNAME SEARCH
	jQuery( "#affiliate_name" ).bind( "keydown", function( event ) {
		if ( event.keyCode === jQuery.ui.keyCode.TAB &&
			jQuery( this ).autocomplete( "instance" ).menu.active ) {
		 	event.preventDefault();
		}
	}).autocomplete({
		minLength: 0,
		source: '<?php echo UAP_URL . 'admin/Uap_Coupons_Ajax_Autocomplete.php';?>?users=true',
		focus: function() {},
		select: function( event, ui ) {			
			var v = ui.item.id;
			var l = ui.item.label;				
			jQuery('#affiliate_name').val(l);		 	
		 	jQuery('#affiliate_id_hidden').val(v);//send to input hidden	
		 	return false;
		}
	});
	
});

</script>
</div>