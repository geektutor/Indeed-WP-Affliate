<div class="uap-wrapper">
	<div class="uap-stuffbox">
		<form action="<?php echo $data['url-manage'];?>" method="post">
					
			<h3 class="uap-h3"><?php _e('Add/Edit Coupons', 'uap');?></h3>
			<div class="inside">
				
				<div class="uap-inside-item uap-special-line">
					<div class="row">	
						<div class="col-xs-6">
							<h4 style="margin-top:20px;"><?php _e('Source', 'uap');?></h4>
							<select name="type" id="the_source"  class="form-control m-bot15" onChange="jQuery('#coupon_code').autocomplete( 'option', { source: '<?php echo UAP_URL . 'admin/Uap_Coupons_Ajax_Autocomplete.php';?>?source='+this.value } );"><?php 
								$values = uap_get_active_services();
								foreach ($values as $k=>$v){
									$selected = ($data['metas']['type']==$k) ? 'selected' : '';
									?>
									<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
									<?php 
								}
							?></select>
							
							<div class="input-group" style="margin-top:10px;">
								<span class="input-group-addon" id="basic-addon1"><?php _e('Coupon Code', 'uap');?></span>
								<input type="text"  class="form-control" value="<?php echo $data['metas']['code'];?>" name="code" id="coupon_code" />
							</div>

							<div class="input-group" style="margin-top:10px;">
								<span class="input-group-addon" id="basic-addon1"><?php _e('Affiliate', 'uap');?></span>
								<input type="text"  class="form-control" value="<?php echo $data['affiliate'];?>" id="affiliate_name" />
								<input type="hidden" id="affiliate_id_hidden" name="affiliate_id" value="<?php echo $data['metas']['affiliate_id'];?>]"/>
							</div>		
												
						</div>
					</div>
				</div>			
				
				<div class="uap-inside-item">		
					
					<div class="row">
						<div class="col-xs-6">
						<h4><?php _e('Activate/Hold Coupon', 'uap');?></h4>
							<p><?php _e('Activate or deactivate a coupon without needing to delete it.', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['status']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#offer_status');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="status" value="<?php echo $data['metas']['status'];?>" id="offer_status" /> 	
						</div>
					</div>	
					
					<div class="uap-inside-item">
						<div class="row">	
							<div class="col-xs-4">
								<h4><?php _e('Coupon Amount', 'uap');?></h4>
								<p><?php _e('A special amount for this specific coupon needs to be set which will replace the standard amount rank.', 'uap');?></p>	
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
					
				</div>	
				<div class="uap-submit-form">
					<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large">
				</div>										
			</div>	
				
			<input type="hidden" name="id" value="<?php echo $data['metas']['id'];?>" />
		</form>
	</div>
</div>


<script>
var uap_source = jQuery('#the_source').val();

jQuery(document).ready(function(){
	jQuery('#the_source').on('change', function(){
		uap_source = jQuery(this).val();
		jQuery('#coupon_code').val('');
	});	
});

jQuery(function() {
	jQuery( "#coupon_code" ).bind( "keydown", function( event ) {
		if ( event.keyCode === jQuery.ui.keyCode.TAB &&
			jQuery( this ).autocomplete( "instance" ).menu.active ) {
		 	event.preventDefault();
		}
	}).autocomplete({
		focus: function( event, ui ){},
		minLength: 0,
		source: '<?php echo UAP_URL . 'admin/Uap_Coupons_Ajax_Autocomplete.php';?>?source='+uap_source,
		select: function( event, ui ) {
			var v = ui.item.label;
			jQuery('#coupon_code').val(v);	
		 	return false;

		}
	});

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