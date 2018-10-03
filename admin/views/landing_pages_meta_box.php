<script href="" src="<?php echo UAP_URL . 'assets/js/admin.js';?>"></script>
<div class="uap-padding">
  <div><strong><?php _e('Set this Post for affiliate:', 'uap');?></strong></div>

    <div class="input-group">
    		<span class="input-group-addon" id="basic-addon1">Username</span>
    		<input type="text" class="form-control ui-autocomplete-input" id="usernames_search" autocomplete="off">

        <input type="hidden" value="<?php echo $data['uap_landing_page_affiliate_id'];?>" name="uap_landing_page_affiliate_id" id="uap_landing_page_affiliate_id" />
        <div id="uap_username_search_tags"><?php
              if ($data['uap_landing_page_affiliate_id']){
              $id = 'uap_username_tag_' . $data['uap_landing_page_affiliate_id'];
              ?>
              <div id="<?php echo $id;?>" class="uap-tag-item"><?php echo $data['affiliate_usename'];?><div class="uap-remove-tag" onclick="uap_remove_tag('<?php echo $data['uap_landing_page_affiliate_id'];?>', '#<?php echo $id;?>', '#usernames_search_hidden');" title="<?php _e('Removing tag', 'uap');?>">x</div></div>
              <?php
              }
        ?></div>

    </div>

</div>


<script>
jQuery(document).ready(function(){
  jQuery( "#usernames_search" ).bind( "keydown", function( event ) {
		if ( event.keyCode === jQuery.ui.keyCode.TAB &&
			jQuery( this ).autocomplete( "instance" ).menu.active ) {
		 	event.preventDefault();
		}
	}).autocomplete({
		minLength: 0,
		source: '<?php echo UAP_URL . 'admin/Uap_Offers_Ajax_Autocomplete.php';?>?users=true&without_all=true',
		focus: function() {},
		select: function( event, ui ) {
      console.log(ui)
      var input_id = '#uap_landing_page_affiliate_id';
			var v = ui.item.id;
			var l = ui.item.label;
      uap_autocomplete_write_and_replace_tag(v, input_id, '#uap_username_search_tags', 'uap_username_tag_', l);
		 	jQuery(input_id).val(v);//send to input hidden
			this.value = '';//reset search input
		 	return false;
		}
	});
})
</script>

<?php
