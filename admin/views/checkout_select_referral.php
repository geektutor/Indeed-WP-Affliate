<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Fair Checkout Reward', 'uap');?></h3>
		<div class="inside">
			<div class="row">
				<div class="col-xs-8">
					<h3><?php _e('Activate/Hold  Checkout Reward', 'uap');?></h3>
					<p><?php _e('Once activated the customer will have the option to select an affiliate for commission during the checkout process.', 'uap');?></p>
					
						
						<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
							<?php $checked = ($data['metas']['uap_checkout_select_referral_enable']) ? 'checked' : '';?>
							<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_checkout_select_referral_enable');" <?php echo $checked;?> />
							<div class="switch" style="display:inline-block;"></div>
						</label>
						<input type="hidden" name="uap_checkout_select_referral_enable" value="<?php echo $data['metas']['uap_checkout_select_referral_enable'];?>" id="uap_checkout_select_referral_enable" /> 
				</div>
			</div>
			<div class="uap-line-break"></div>	
			<div class="row">
				<div class="col-xs-5">	
					<h3><?php _e('Form Label field', 'uap');?></h3>
					<p><?php _e('The label name for the additional field available inside the checkout form.', 'uap');?></p>
					<div class="input-group" style="margin-top: 10px; margin-bottom:20px;">
					<label class="input-group-addon"><?php _e('Label', 'uap');?></label>
						<input type="text" class="form-control" name="uap_checkout_select_referral_label" value="<?php echo $data['metas']['uap_checkout_select_referral_label'];?>" /> 
					</div>
				</div>
			</div>
			<div class="uap-line-break"></div>	
			
			<div class="row">
				<div class="col-xs-4">			
					<h3><?php _e('Field Settings', 'uap');?></h3>	
					<div class="uap-form-line">
						<h4><?php _e('Selection Type', 'uap');?></h4>
						<select name="uap_checkout_select_referral_s_type" class="form-control m-bot15"><?php 
							foreach (array(1 => __('Client select Affiliate from list', 'uap'), 2 => __('Client write the Username of Affiliate', 'uap')) as $k=>$v):
								$selected = ($data['metas']['uap_checkout_select_referral_s_type']==$k) ? 'selected' : '';
								?>
								<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
								<?php	
							endforeach;	
						?></select>
					</div>				
					<div class="uap-form-line">
						<h4><?php _e('Affiliate Name Display', 'uap');?></h4>
						<p><?php _e('How affiliates will be displayed.', 'uap');?></p>
						<select name="uap_checkout_select_referral_name" class="form-control m-bot15"><?php 
							foreach (array('user_login' => __('Username', 'uap'), 'display_name' => __('Display Name', 'uap'), 'user_nicename' => __('User Nicename', 'uap') ) as $k=>$v):
								$selected = ($data['metas']['uap_checkout_select_referral_name']==$k) ? 'selected' : '';
								?>
								<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
								<?php	
							endforeach;	
						?></select>
					</div>
				</div>	
			</div>	
			<div class="row">
				<div class="col-xs-4">
					<div class="uap-form-line">
						<h4><?php _e('Affiliate List', 'uap');?></h4>
						<p><?php _e('Choose specific affiliates to show up in checkout selection if you do not want to display all of them.', 'uap');?></p>
						<input type="text" id="usernames_search" class="form-control"/>
						<?php $value = (is_array($data['metas']['uap_checkout_select_affiliate_list'])) ? implode(',', $data['metas']['uap_checkout_select_affiliate_list']) : $data['metas']['uap_checkout_select_affiliate_list'];?>
						<input type="hidden" value="<?php echo $value;?>" name="uap_checkout_select_affiliate_list" id="usernames_search_hidden" />
						<div id="uap_username_search_tags"><?php
							if (!empty($aff_list)){								
								foreach ($aff_list as $value){
									if ($value){
										$id = 'uap_username_tag_' . $value;
										?>
										<div id="<?php echo $id;?>" class="uap-tag-item"><?php echo $usernames[$value];?><div class="uap-remove-tag" onclick="uap_remove_tag('<?php echo $value;?>', '#<?php echo $id;?>', '#usernames_search_hidden');" title="<?php _e('Removing tag', 'uap');?>">x</div></div>	
										<?php 
										}
									}
								}
							?></div>			
					</div>
					
				</div>
			</div>
			
			<div class="uap-line-break"></div>	
			
			<div class="row">
				<div class="col-xs-5">	
				<h3><?php _e('Additional Settings', 'uap');?></h3>								
					<div class="uap-form-line">
						<h4><?php _e('Rewrite current Affiliate', 'uap');?></h4>
						<p><?php _e('If there is an affiliate already assigned for the current visitor/customer this one can be ignored. If not, the checkout option will not show up.', 'uap');?></p>
						<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
							<?php $checked = ($data['metas']['uap_checkout_select_referral_rewrite']) ? 'checked' : '';?>
							<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_checkout_select_referral_rewrite');" <?php echo $checked;?> />
							<div class="switch" style="display:inline-block;"></div>
						</label>
						<input type="hidden" name="uap_checkout_select_referral_rewrite" value="<?php echo $data['metas']['uap_checkout_select_referral_rewrite'];?>" id="uap_checkout_select_referral_rewrite" /> 
					</div>

												
					<div class="uap-form-line">
						<h4><?php _e('Require select Affiliate', 'uap');?></h4>
						<p><?php _e('Force the customer to select an affiliate to continue the checkout process. The additional field will be set as required.', 'uap');?></p>
						
						<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
							<?php $checked = ($data['metas']['uap_checkout_select_referral_require']) ? 'checked' : '';?>
							<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_checkout_select_referral_require');" <?php echo $checked;?> />
							<div class="switch" style="display:inline-block;"></div>
						</label>
						<input type="hidden" name="uap_checkout_select_referral_require" value="<?php echo $data['metas']['uap_checkout_select_referral_require'];?>" id="uap_checkout_select_referral_require" /> 
					</div>						
						
					</div>
									
				</div>
				<div class="uap-line-break"></div>
					<div class="row">
						<div class="col-xs-4">
						</div>
					</div>	

			<div class="uap-submit-form"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>					
				
			</div>
							
		</div>
	</div>
</form>	

<script>

function uap_split(v){
	if (v.indexOf(',')!=-1){
	    return v.split( /,\s*/ );
	} else if (v!=''){
		return [v];
	}
	return [];
}

jQuery(function() {
	/// USERNAME SEARCH
	jQuery( "#usernames_search" ).bind( "keydown", function( event ) {
		if ( event.keyCode === jQuery.ui.keyCode.TAB &&
			jQuery( this ).autocomplete( "instance" ).menu.active ) {
		 	event.preventDefault();
		}
	}).autocomplete({
		minLength: 0,
		source: '<?php echo UAP_URL . 'admin/Uap_Offers_Ajax_Autocomplete.php';?>?users=true',
		focus: function() {},
		select: function( event, ui ) {			
			var input_id = '#usernames_search_hidden';			
		 	var terms = uap_split(jQuery(input_id).val());//get items from input hidden
			var v = ui.item.id;
			var l = ui.item.label;				
		 	if (!contains(terms, v)){
				terms.push(v);			 	
			 	// print the new shiny box
			 	uap_autocomplete_write_tag(v, input_id, '#uap_username_search_tags', 'uap_username_tag_', l);
			 }
		 	var str_value = terms.join( "," );		 	
		 	jQuery(input_id).val(str_value);//send to input hidden
			this.value = '';//reset search input		
		 	return false;
		}
	});
	
});

function contains(a, obj) {
    return a.some(function(element){return element == obj;})
}

</script>	