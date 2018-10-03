<div class="uap-wrapper">
	<div class="uap-stuffbox">
	<form action="<?php echo $data['url-manage'];?>" method="post">
				
	<h3 class="uap-h3"><?php _e('Manage Offers', 'uap');?></h3>
	<div class="inside">
		<div class="uap-inside-item">
				<div class="row">
					<div class="col-xs-6">
					<h4><?php _e('Activate/Hold Offer', 'uap');?></h4>
						<p><?php _e('Activate or deactivate an offer without needing to delete it.', 'uap');?></p>
						<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
							<?php $checked = ($data['metas']['status']) ? 'checked' : '';?>
							<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#offer_status');" <?php echo $checked;?> />
							<div class="switch" style="display:inline-block;"></div>
						</label>
						<input type="hidden" name="status" value="<?php echo $data['metas']['status'];?>" id="offer_status" /> 	
					</div>
				</div>	
			</div>
			<div class="uap-line-break"></div>	
				<div class="uap-inside-item">
					<div class="row">
						<div class="col-xs-6">
							<div class="input-group">
								<span class="input-group-addon" id="basic-addon1"><?php _e('Name', 'uap');?></span>
								<input type="text" class="form-control" placeholder="special offer"  value="<?php echo $data['metas']['name'];?>" name="name" />
							</div>
						</div>
					</div>	
				</div>
			<div class="uap-line-break"></div>	
			<div class="uap-inside-item">
					<div class="row">	
						<div class="col-xs-4">
							<h3><?php _e('Offer Amount', 'uap');?></h3>
							<p><?php _e('A special amount for this specific offer needs to be set which will replace the standard amount rank.', 'uap');?></p>	
							<div style="margin-bottom:15px;">
									<select name="amount_type" class="form-control m-bot15"><?php 
										foreach ($data['amount_types'] as $k=>$v):
											$selected = ($data['metas']['amount_type']==$k) ? 'selected' : '';
											?>
											<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
											<?php 
										endforeach;
									?></select>
							 </div>
							<div class="input-group">
								<span class="input-group-addon" id="basic-addon1"><?php _e('Value', 'uap');?></span>
								<input type="number" min="0" step="0.01" class="form-control" name="amount_value" value="<?php echo $data['metas']['amount_value'];?>" aria-describedby="basic-addon1">
							</div>
								
						</div>
					</div>
				</div>
				<div class="uap-line-break"></div>	
				<div class="uap-inside-item">
					<div class="row">	
						<div class="col-xs-4">
							<h3><?php _e('Date Range', 'uap');?></h3>
							<p><?php _e('The offer will be active during a certain time interval based on your selling strategy.', 'uap');?></p>
							
							<input type="text" id="start_date" name="start_date" value="<?php echo $data['metas']['start_date'];?>" class="uap-datepick" />
							 - 
							<input type="text" id="end_date" name="end_date" value="<?php echo $data['metas']['end_date'];?>" class="uap-datepick" />
						</div>
					</div>
				</div>		
				<div class="uap-inside-item uap-special-line">
					<div class="row">	
						<div class="col-xs-4">
							<h3><?php _e('Targeting', 'uap');?></h3>
							<p><?php _e('Based on referral source and only for certain affiliates, the offer will be available.', 'uap');?></p>
							<h4 style="margin-top:20px;"><?php _e('Source', 'uap');?></h4>
							<select name="source" id="the_source"  class="form-control m-bot15" onChange="jQuery('#reference_search').autocomplete( 'option', { source: '<?php echo UAP_URL . 'admin/Uap_Offers_Ajax_Autocomplete.php';?>?source='+this.value } );"><?php 
								$values = uap_get_active_services();
								if ($values):
									foreach ($values as $k=>$v){
										$selected = ($data['metas']['source']==$k) ? 'selected' : '';
										?>
										<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
										<?php 
									}
								endif;
							?></select>
							<div class="input-group" style="margin-top:10px;">
								<span class="input-group-addon" id="basic-addon1"><?php _e('Products', 'uap');?></span>
								<input type="text"  class="form-control" value="" name="" id="reference_search" />
							</div>
								<?php $value = (is_array($data['metas']['products'])) ? implode(',', $data['metas']['products']) : $data['metas']['products'];?>
								<input type="hidden" value="<?php echo $value;?>" name="products" id="reference_search_hidden" />
								<div id="uap_reference_search_tags"><?php 
									if (!empty($data['metas']['products'])){
										foreach ($data['metas']['products'] as $value){
											if ($value){
											$id = 'uap_reference_tag_' . $value;
											?>
											<div id="<?php echo $id;?>" class="uap-tag-item"><?php echo $data['products']['label'][$value];?><div class="uap-remove-tag" onclick="uap_remove_tag('<?php echo $value;?>', '#<?php echo $id;?>', '#reference_search_hidden');" title="Removing tag">x</div></div>	
											<?php 
											}
										}
									}
								?></div>
							<h4 style="margin-top:20px;"><?php _e('Specific Affiliate', 'uap');?></h4>	
							<p><?php _e('Choose certain affiliates or type "All" to provide this offer for all of your affiliate users.', 'uap');?></p>
							<div class="input-group">
								<span class="input-group-addon" id="basic-addon1"><?php _e('Username', 'uap');?></span>
								<input type="text"  class="form-control" id="usernames_search" />
							</div>
								<?php $value = (is_array($data['metas']['affiliates'])) ? implode(',', $data['metas']['affiliates']) : $data['metas']['affiliates'];?>
								<input type="hidden" value="<?php echo $value;?>" name="affiliates" id="usernames_search_hidden" />
								<div id="uap_username_search_tags"><?php
									if (!empty($data['metas']['affiliates'])){								
										foreach ($data['metas']['affiliates'] as $value){
											if ($value){
											$id = 'uap_username_tag_' . $value;
											?>
											<div id="<?php echo $id;?>" class="uap-tag-item"><?php echo $data['affiliates']['username'][$value];?><div class="uap-remove-tag" onclick="uap_remove_tag('<?php echo $value;?>', '#<?php echo $id;?>', '#usernames_search_hidden');" title="<?php _e('Removing tag', 'uap');?>">x</div></div>	
											<?php 
											}
										}
									}
								?></div>
						</div>
					</div>
				</div>
				<div class="uap-inside-item">
					<div class="row">	
						<div class="col-xs-4">
							<h3><?php _e('Offer Color', 'uap');?></h3>						
							<div style="margin-bottom:15px;">	
							<ul id="uap_colors_ul" class="uap-colors-ul" style="display: inline-block; vertical-align: bottom;">
                        	<?php
                                 $color_scheme = array('0a9fd8', '38cbcb', '27bebe', '0bb586', '94c523', '6a3da3', 'f1505b', 'ee3733', 'f36510', 'f8ba01');
                                 $i = 0;
                                 if (empty($data['metas']['color'])){
                                 	$data['metas']['color'] = $color_scheme[rand(0,9)];
                                 }
                                 foreach ($color_scheme as $color){
                            	     if ($i==5) echo "<div class='clear'></div>";
                                	     $class = ($color==$data['metas']['color']) ? 'uap-color-scheme-item-selected' : 'uap-color-scheme-item';
                                         ?>
                                            <li class="<?php echo $class;?>" onClick="uap_chage_color(this, '<?php echo $color;?>', '#uap_color');" style="background-color: #<?php echo $color;?>;"></li>
                                         <?php
                                         $i++;
                                     }
                                 ?>
                            </ul>
                            <input type="hidden" name="color" id="uap_color" value="<?php echo $data['metas']['color'];?>" />
							</div>
						</div>
					</div>
				</div>					
						
					<div class="uap-submit-form">
						<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large">
					</div>										
				</div>	
				
				<input type="hidden" name="id" value="<?php echo $data['metas']['id'];?>" />
					
			</form>
		</div>
	
</div>


</div><!-- end of uap-dashboard-wrap -->

<script>
var uap_offer_source = jQuery('#the_source').val();

jQuery(document).ready(function(){
	jQuery('#the_source').on('change', function(){
		uap_offer_source = jQuery(this).val();
		jQuery('#uap_reference_search_tags').empty();
		jQuery('#reference_search_hidden').val('');
	});	
});

function uap_split(v){
	if (v.indexOf(',')!=-1){
	    return v.split( /,\s*/ );
	} else if (v!=''){
		return [v];
	}
	return [];
}
function uap_extract(t) {
    return uap_split(t).pop();
}

jQuery(function() {
    /// REFERENCE SEARCH
	jQuery( "#reference_search" ).bind( "keydown", function( event ) {
		if ( event.keyCode === jQuery.ui.keyCode.TAB &&
			jQuery( this ).autocomplete( "instance" ).menu.active ) {
		 	event.preventDefault();
		}
	}).autocomplete({
		focus: function( event, ui ){},
		minLength: 0,
		source: '<?php echo UAP_URL . 'admin/Uap_Offers_Ajax_Autocomplete.php';?>?source='+uap_offer_source,
		select: function( event, ui ) {
			var input_id = '#reference_search_hidden';
		 	var terms = uap_split(jQuery(input_id).val());//get items from input hidden
			var v = ui.item.id;
			var l = ui.item.label;
		 	if (!contains(terms, v)){
				terms.push(v);			 	
			 	uap_autocomplete_write_tag(v, input_id, '#uap_reference_search_tags', 'uap_reference_tag_', l);// print the new shiny box
			 }
			var str_value = terms.join( "," );	 	
		 	jQuery(input_id).val(str_value);//send to input hidden
			this.value = '';//reset search input			
		 	return false;

		}
	});

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

/// DATE PICKERS
jQuery(document).ready(function() {
	jQuery('.uap-datepick').each(function(){
		jQuery(this).datepicker({
            dateFormat : 'yy-mm-dd ',
            onSelect: function(datetext){
                var d = new Date();
                datetext = datetext+d.getHours()+":"+uap_add_zero(d.getMinutes())+":"+uap_add_zero(d.getSeconds());
                jQuery(this).val(datetext);
            }
        });
    });
});
</script>
                          
