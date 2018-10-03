<div>
	<div class="col-xs-6">	
		<?php if (!empty($attr['title'])):?>
		<h3><?php echo $attr['title'];?></h3>
		<?php endif;?>
		<div class="input-group">
			<span class="input-group-addon" id="basic-addon1"><?php echo $attr['label'];?></span>
			<input type="text"  class="form-control" <?php echo $attr['field_style'];?> id="usernames_search" />
			<input type="hidden" name="<?php echo $attr['hidden_name'];?>" value="" id="<?php echo $attr['hidden_name'];?>" />
		</div>	
	</div>
	<div class="uap-clear"></div>	
</div>
<?php
	$url = UAP_URL . 'admin/Uap_Offers_Ajax_Autocomplete.php?users=true&without_all=true';
	if (!empty($attr['exclude_user_id'])){
		$url .= '&exclude_user=' . $attr['exclude_user_id'];
	}
?>
<script>
function uap_split(v){
	if (v.indexOf(',')!=-1){
	    return v.split( /,\s*/ );
	} else if (v!=''){
		return [v];
	}
	return [];
}

function contains(a, obj) {
    return a.some(function(element){return element == obj;})
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
		source: '<?php echo $url;?>',
		focus: function() {},
		select: function( event, ui ) {			
			var input_id = '#<?php echo $attr['hidden_name'];?>';			
		 	var terms = uap_split(jQuery(input_id).val());//get items from input hidden
			var v = ui.item.id;
			var l = ui.item.label;					 	
		 	jQuery(input_id).val(v);//send to input hidden
			this.value = l;//reset search input		
		 	return false;
		}
	});
	
});
	
</script>