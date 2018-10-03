function uap_delete_from_table(i, t, h, f){
	var m = window.uap_messages['general_delete'] + t + "?";
 	var c = confirm(m);
	if (c){
		jQuery(h).val(i);
		jQuery(f).submit();
	}
}

function uap_select_all_checkboxes(c, t){
	if (jQuery(c).is(':checked')){
		jQuery(t).attr('checked', 'checked');
	} else {
		jQuery(t).removeAttr('checked');
	}
}

function uap_dh_selector(t, v){
	if (v){
		var d = 'visible';
	} else {
		var d = 'hidden';
	}
	jQuery(t).css('visibility', d);
}

function uap_delete_banner_confirm(i){
	var c = confirm("Delete This Banner?");
	if (c){
		jQuery('#delete_banner_id').val(i);
		jQuery('#form_banners').submit();
	}
}

function uap_delete_notification_confirm(i){
	var c = confirm("Delete This Notification?");
	if (c){
		jQuery('#delete_notification_id').val(i);
		jQuery('#form_notification').submit();
	}
}

function open_media_up(target){
    //If the uploader object has already been created, reopen the dialog
  var custom_uploader;
  if (custom_uploader) {
      custom_uploader.open();
      return;
  }
  //Extend the wp.media object
  custom_uploader = wp.media.frames.file_frame = wp.media({
      title: 'Choose Image',
      button: {
          text: 'Choose Image'
      },
      multiple: false
  });
  //When a file is selected, grab the URL and set it as the text field's value
  custom_uploader.on('select', function() {
      attachment = custom_uploader.state().get('selection').first().toJSON();
      jQuery(target).val(attachment.url);
  });
  //Open the uploader dialog
  custom_uploader.open();
}

jQuery(document).ready(function(){

	jQuery('#uap-register-fields-table tbody').sortable({
		 update: function(e, ui) {
		        jQuery('#uap-register-fields-table tbody tr').each(function (i, row) {
		        	var id = jQuery(this).attr('id');
		        	var newindex = jQuery("#uap-register-fields-table tbody tr").index(jQuery('#'+id));
		        	jQuery('#'+id+' .uap-order').val(newindex);
		        });
		    }
	});

	jQuery('#uap_reorder_menu_items tbody').sortable({
		 update: function(e, ui) {
		        jQuery('#uap_reorder_menu_items tbody tr').each(function (i, row) {
		        	var id = jQuery(this).attr('id');
		        	jQuery('#'+id+' .uap_account_page_menu_order').val(i);
		        });
		 }
	});

});

function uap_register_fields(v){
	jQuery('#uap-register-field-values').fadeOut(200);
	jQuery('#uap-register-field-plain-text').fadeOut(200);
	jQuery('#uap-register-field-conditional-text').fadeOut(200);
	if (v=='select' || v=='checkbox' || v=='radio' || v=='multi_select'){
		jQuery('#uap-register-field-values').fadeIn(200);
	} else if (v=='plain_text'){
		jQuery('#uap-register-field-plain-text').fadeIn(200);
	} else if (v=='conditional_text'){
		jQuery('#uap-register-field-conditional-text').fadeIn(200);
	}
}

function uap_add_new_register_field_value(){
	var s = '<div style="display: block;margin-bottom: 5px;" class="uap-custom-field-item-wrapp">';
	s += '<input type="text" name="values[]" value=""/> ';
	s += '<i class="fa-uap fa-remove-uap" style="cursor: pointer;" onclick="jQuery(this).parent().remove();"></i>';
	s += '<i class="fa-uap fa-arrows-uap"></i>';
	s += '</div>';
	jQuery('.uap-register-the-values').append(s);
}

function uap_check_and_h(from, target){
	if (jQuery(from).is(":checked")) jQuery(target).val(1);
	else jQuery(target).val(0);
}

function check_and_h(id, target){
	if(jQuery(id).is(':checked')){
		jQuery(target).val(1);
	}else{
		jQuery(target).val(0);
	}
}

function uap_register_preview(){
   	jQuery.ajax({
        type : 'post',
        url : window.uap_url + '/wp-admin/admin-ajax.php',
        data : {
                   action: 'uap_register_preview_ajax',
                   template: jQuery('#uap_register_template').val(),
                   custom_css: jQuery('#uap_register_custom_css').val(),
               },
        success: function (response) {
        	jQuery('#register_preview').fadeOut(200, function(){
        		jQuery(this).html(response);
        		jQuery(this).fadeIn(400);
        	});
        }
   });
}

function uap_login_preview(){
   	jQuery.ajax({
        type : "post",
        url : decodeURI(window.uap_url)+'/wp-admin/admin-ajax.php',
        data : {
                   action: "uap_login_form_preview",
                   remember: jQuery('#uap_login_remember_me').val(),
                   register_link: jQuery('#uap_login_register').val(),
                   pass_lost: jQuery('#uap_login_pass_lost').val(),
                   css: jQuery('#uap_login_custom_css').val(),
                   template: jQuery('#uap_login_template').val(),
                   uap_login_show_recaptcha: jQuery('#uap_login_show_recaptcha').val(),
               },
        success: function (d) {
        	jQuery('#uap-preview-login').fadeOut(200, function(){
        		jQuery(this).html(d);
        		jQuery(this).fadeIn(400);
        	});
        }
   });
}

function uap_add_new_achieve_rule(){

	var t = jQuery('#achieve_type').val();
	var v = jQuery('#achieve_value').val();
	var print = '';

	if (t==-1){return;}

	if (jQuery('#achieve_relation_div').css('display')=='none'){
		jQuery('#achieve_relation_div').css('display', 'block');
	}

	var str = jQuery('#achieve_type_value').val();
	if (str==''){
		var n = 1;
		var obj = {i: n, type_1: t, value_1: v};
	} else {
		var obj = JSON.parse(str);
		obj.i++;
		var n = obj.i;
		obj["type_"+obj.i] = jQuery('#achieve_type').val();
		obj["value_"+obj.i] = jQuery('#achieve_value').val();
		obj["relation_"+obj.i] = jQuery('#achieve_relation').val();
		print += '<div class="achieve-item-relation">' + obj["relation_"+obj.i] + '</div>';
	}
	var str = JSON.stringify(obj);
	jQuery('#achieve_type_value').val(str);

	var achieve_type = jQuery("#achieve_type option[value='"+t+"']").text();
	print += '<div class="achieve-item" id="achieve_item_' + n + '"><div style="font-weight:bold;font-size:14px;">'+'' + achieve_type + '</div><div>' + 'From: ' + v + '</div></div>';
	jQuery("#achieve_type option[value='"+t+"']").remove();

	var c = 0;
	jQuery("#achieve_type option").each(function(){
		c++;
	});
	//console.log(c);
	if (c==1){
		jQuery('#achieve_type').attr('disabled', 'disabled');
		jQuery('#add_new_achieve').css('display', 'none');
	}

	var initial = jQuery('#achieve_rules_view').html();
	jQuery('#achieve_rules_view').html(initial + print);

	jQuery('#achieve_type').val('');
	jQuery('#achieve_value').val('');

	if (jQuery('#achieve_reset').css('display')=='none'){
		jQuery('#achieve_reset').css('display', 'inline-block');
	}

}

function uap_achieve_reset(){
	jQuery('#achieve_type').removeAttr('disabled');
	jQuery('#add_new_achieve').css('display', 'inline-block');
	jQuery("#achieve_type option").each(function(){
		jQuery(this).remove();
	});
	jQuery.each(window.achieve_arr, function(k, v){
		if (typeof v!='undefined'){
			jQuery('#achieve_type').append('<option value="'+v.value+'">'+v.label+'</option>');
		}
	});

	jQuery('#achieve_rules_view').html('');
	jQuery('#achieve_reset').css('display', 'none');
	jQuery('#achieve_type_value').val('');
	jQuery('#achieve_relation_div').css('display', 'none');
}

function uap_chage_color(id, value, where ){
    jQuery('#uap_colors_ul li').each(function(){
        jQuery(this).attr('class', 'uap-color-scheme-item');
    });
    jQuery(id).attr('class', 'uap-color-scheme-item-selected');
    jQuery(where).val(value);
}


function uap_autocomplete_write_tag(value_id, hiddenId, viewDivId, prevDivPrefix, label){
	/*
	 * viewDivId - parent
	 * prevDivPrefix - prefix of tag
	 * hiddenId - where values are
	 */
	id = prevDivPrefix + value_id;
	jQuery(viewDivId).append('<div id="'+id+'" class="uap-tag-item">'+label+'<div class="uap-remove-tag" onclick="uap_remove_tag(\''+value_id+'\', \'#'+id+'\', \''+hiddenId+'\');" title="Removing tag">x</div></div>');
}

function uap_autocomplete_write_and_replace_tag(value_id, hiddenId, viewDivId, prevDivPrefix, label){
	/*
	 * viewDivId - parent
	 * prevDivPrefix - prefix of tag
	 * hiddenId - where values are
	 */
	id = prevDivPrefix + value_id;
	jQuery(viewDivId).html('<div id="'+id+'" class="uap-tag-item">'+label+'<div class="uap-remove-tag" onclick="uap_remove_tag(\''+value_id+'\', \'#'+id+'\', \''+hiddenId+'\');" title="Removing tag">x</div></div>');
}

function uap_remove_tag(removeVal, removeDiv, hiddenId){
	jQuery(removeDiv).fadeOut(200, function(){
		jQuery(this).remove();
	});

    hidden_i = jQuery(hiddenId).val();
    show_arr = hidden_i.split(',');

    show_arr = remove_array_element(removeVal, show_arr);
    str = show_arr.join(',');
	jQuery(hiddenId).val(str);
}

function remove_array_element(elem, arr){
	for (i=0;i<arr.length;i++) {
	    if(arr[i]==elem){
	    	arr.splice(i, 1);
	    }
	}
	return arr;
}

function uap_add_zero(i){
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}

function uap_rank_change_order_preview(r, v){
	jQuery('.uap-rank-graphic').css('visibility', 'none');
   	jQuery.ajax({
        type : "post",
        url : decodeURI(window.uap_url)+'/wp-admin/admin-ajax.php',
        data : {
                   action: "uap_make_ranks_reorder",
                   new_order: v,
                   rank_id: r,
                   current_label: jQuery('#rank_label').val(),
               },
        success: function (d) {
        	jQuery('.uap-rank-graphic').html(d);
        	jQuery('.uap-rank-graphic').css('visibility', 'visible');
        }
   });
}

function uap_make_inputh_string(divCheck, showValue, hidden_input_id){
    str = jQuery(hidden_input_id).val();
    if(str==-1) str = '';
    if(str!='') show_arr = str.split(',');
    else show_arr = new Array();
    if(jQuery(divCheck).is(':checked')){
        show_arr.push(showValue);
    }else{
        var index = show_arr.indexOf(showValue);
        show_arr.splice(index, 1);
    }
    str = show_arr.join(',');
    if(str=='') str = -1;
    jQuery(hidden_input_id).val(str);
}

//OPT IN
function uap_connect_aweber(t){
    jQuery.ajax({
        type : "post",
        url : decodeURI(window.uap_url)+'/wp-admin/admin-ajax.php',
        data : {
                action: "uap_update_aweber",
                auth_code: jQuery(t).val()
            },
        success: function (data) {
            alert('Connected');
        }
	});
}


function uap_get_cc_list(uap_cc_user, uap_cc_pass){
    jQuery("#uap_cc_list").find('option').remove();
	jQuery.ajax({
            type : "post",
			dataType: 'JSON',
            url : decodeURI(window.uap_url)+'/wp-admin/admin-ajax.php',
            data : {
                    action: "uap_get_cc_list",
                    uap_cc_user: jQuery( uap_cc_user ).val(),
                    uap_cc_pass: jQuery( uap_cc_pass ).val()
            },
            success: function (data) {
					jQuery.each(data, function(i, option){
						jQuery("<option/>").val(i).text(option.name).appendTo("#uap_cc_list");
					});
			}
    });
}

function uap_return_notification(){
    jQuery.ajax({
        type : "post",
        url : decodeURI(window.uap_url)+'/wp-admin/admin-ajax.php',
        data : {
                action: "uap_get_notification_default_by_type",
                type: jQuery('#notf_type').val(),
            },
        success: function (r) {
        	var o = jQuery.parseJSON(r);
        	jQuery('#notf_subject').val(o.subject);
        	jQuery('#notf_message').val(o.content);
        	jQuery("#notf_message_ifr" ).contents().find( '#tinymce' ).html(o.content);
        }
	});
}

function uap_matrix_type_condition(v){
	if (v=='unilevel'){
		jQuery('#children_limit_div').css('display', 'none');
		jQuery('#uap_mlm_child_limit').removeAttr('max');
	}
	else {
		jQuery('#children_limit_div').css('display', 'table');
		if (v=='binary'){
			jQuery('#uap_mlm_child_limit').attr('max', 2);
			jQuery('#uap_mlm_child_limit').val(2);
		} else {
			jQuery('#uap_mlm_child_limit').removeAttr('max');
		}
	}
}

function uap_mlm_update_tbl(v){
	var last = parseInt(jQuery('#mlm-amount-for-each-level tr').last().attr('data-tr'));
	console.log('last: '+last);
	console.log('current: '+v);
	if (v<last){
		for (var i=last; i>v; i--){
			jQuery('#uap_mlm_level_' + i).remove();
		}
	} else {
		var str_model = jQuery('#uap_mlm_model tbody').html();
		var default_type = jQuery('#uap_mlm_default_amount_type').val();
		var default_value = jQuery('#uap_mlm_default_amount_value').val();
		for (var i=last+1; i<=v; i++){
			var str = str_model;
			str = str.replace(/{{i}}/g, i);
			jQuery('#mlm-amount-for-each-level tbody').append(str);
			jQuery('#uap_mlm_level_' + i + ' td select').val(default_type);
			jQuery('#uap_mlm_level_' + i + ' td input').val(default_value);
		}
	}
}

function uap_closePopup(){
	jQuery('#popup_box').fadeOut(300, function(){
		jQuery(this).remove();
	});
}

function uap_approve_affiliate(i){
   	jQuery.ajax({
        type: 'post',
        url: decodeURI(window.uap_url)+'/wp-admin/admin-ajax.php',
        data: {
               action: 'uap_approve_affiliate',
               uid: i,
        },
        success: function () {
        	location.reload();
        }
   	});
}

function uap_payment_form_payment_status(v){
	if (v=='bank_transfer'){
		jQuery('#payment_status_div').css('display', 'block');
	} else {
		jQuery('#payment_status_div').css('display', 'none');
	}
}

function uap_do_delete(t, f){
	var m = window.uap_messages[t];
	var c = confirm(m);
	if (c){
		jQuery(f).submit();
	} else {
		return false;
	}
}

function uap_ap_make_visible(t, m){
	jQuery('.uap-ap-tabs-list-item').removeClass('uap-ap-tabs-selected-item');
	jQuery(m).addClass('uap-ap-tabs-selected-item');
	jQuery('.uap-ap-tabs-settings-item').fadeOut(200, function(){
		jQuery('#uap_tab_item_' + t).css('display', 'block');
	});
}

function check_submit_affiliate_action(){
	if (jQuery('[name=do_action]').val()=='delete'){
		var m = window.uap_messages['affiliates'] +  "?";
		var c = confirm(m);
		if (c){
			jQuery('#form_affiliates').submit();
		}
	} else {
		jQuery('#form_affiliates').submit();
	}
}

function uap_make_user_affiliate(i){
   	jQuery.ajax({
        type: 'post',
        url: decodeURI(window.uap_url)+'/wp-admin/admin-ajax.php',
        data: {
               action: 'uap_make_wp_user_affiliate',
               uid: i,
        },
        success: function (data){
        	if (data==2){
        		alert('Admin cannot become Affiliate!');
        	} else {
   	        	location.reload();
        	}
        }
   	});
}

function uap_make_affiliate_simple_user(i){
	jQuery.ajax({
        type: 'post',
        url: decodeURI(window.uap_url)+'/wp-admin/admin-ajax.php',
        data: {
               action: 'uap_affiliate_simple_user',
               uid: i,
        },
        success: function () {
        	location.reload();
        }
   	});
}

function uap_remove_currency(c){
   	jQuery.ajax({
        type : 'post',
        url : decodeURI(window.uap_url)+'/wp-admin/admin-ajax.php',
        data : {
                   action: 'uap_delete_currency_code_ajax',
                   code: c
        },
        success: function (r) {
        	if (r){
        		jQuery("#uap_div_"+c).fadeOut(300);
        	}
        }
   });
}

function uap_remove_slug(i){
	jQuery.ajax({
        type : 'post',
        url : decodeURI(window.uap_url)+'/wp-admin/admin-ajax.php',
        data : {
                   action: 'uap_remove_slug_from_aff',
                   uid: i
        },
        success: function (r) {
        	window.location = window.custom_aff_base_url;
        }
   });
}

function uap_change_color_scheme(id, value, where ){
    jQuery('#colors_ul li').each(function(){
        jQuery(this).attr('class', 'color-scheme-item');
    });
    jQuery(id).attr('class', 'color-scheme-item-selected');
    jQuery(where).val(value);
}

function uap_preview_u_list(){
	jQuery('#preview').html('');
	jQuery("#preview").html('<div style="background:#fff;width: 100%;text-align:center;"><img src="'+window.uap_plugin_url+'/assets/images/loading.gif" class=""/></div>');
	var meta = [];
	meta.num_of_entries = jQuery('#num_of_entries').val();
	meta.entries_per_page = jQuery('#entries_per_page').val();
	meta.order_by = jQuery('#order_by').val();
	meta.order_type = jQuery('#order_type').val();
	if (jQuery('#filter_by_rank').is(":checked")){
		meta.filter_by_rank = 1;
		meta.ranks_in = jQuery('#ranks_in').val();
	}
	meta.user_fields = jQuery('#user_fields').val();

	//console.log(meta.user_fields);

	if (jQuery('#include_fields_label').is(':checked')){
		meta.include_fields_label = 1;
	}
	meta.theme = jQuery('#theme').val();
	meta.color_scheme = jQuery('#color_scheme').val();
	meta.columns = jQuery('#columns').val();
	if (jQuery('#align_center').is(":checked")){
		meta.align_center = 1;
	}
	if (jQuery('#inside_page').is(":checked")){
		meta.inside_page = 1;
	}
	if (jQuery('#slider_set').is(":checked")){
		meta.slider_set = 1;
		meta.items_per_slide = jQuery('#items_per_slide').val();
		meta.speed = jQuery("#speed").val();
		meta.pagination_speed = jQuery('#pagination_speed').val();
		meta.pagination_theme = jQuery('#pagination_theme').val();
		meta.animation_in = jQuery('#animation_in').val();
		meta.animation_out = jQuery('#animation_out').val();
		var slider_special_metas = ['bullets', 'nav_button', 'autoplay', 'stop_hover', 'responsive', 'autoheight', 'lazy_load', 'loop'];
		for (var i=0; i<slider_special_metas.length; i++){
			if (jQuery('#'+slider_special_metas[i]).is(":checked")){
				meta[slider_special_metas[i]] = 1;
			}
		}
	}

	///SHORTCODE
	var str = "[uap-listing-affiliates ";
	for (var key in meta) {
		str += key + " ='" + meta[key] +"' ";
	}
	str += ']';
    jQuery('.the-shortcode').html(str);
    jQuery(".php-code").html('&lt;?php echo do_shortcode("'+str+'");?&gt;');

    //AJAX CALL
   	jQuery.ajax({
        type : 'post',
        url : decodeURI(window.uap_url) + '/wp-admin/admin-ajax.php',
        data : {
                   action: 'uap_preview_user_listing',
                   shortcode: str
               },
        success: function (r) {
        	jQuery('#preview').html(r);
        }
   	});
}

function uap_checkbox_div_relation(c, t){
	/*
	 * c = checkbox id to check
	 * t = target div
	 */
	var o = 0.5;
	if (jQuery(c).is(":checked")){
		o = 1;
	}
	jQuery(t).css("opacity", o);
}

function uap_writeTagValue_list_users(id, hiddenId, viewDivId, prevDivPrefix){
    if(id.value==-1) return;
    hidden_i = jQuery(hiddenId).val();

    if(hidden_i!='') show_arr = hidden_i.split(',');
    else show_arr = new Array();

    if(show_arr.indexOf(id.value)==-1){
        show_arr.push(id.value);

	    str = show_arr.join(',');
	    jQuery(hiddenId).val(str);

		label = jQuery(id).find("option:selected").text();
		jQuery(viewDivId).append('<div id="'+prevDivPrefix+id.value+'" class="uap-tag-item">'+label+'<div class="uap-remove-tag" onclick="uapremoveTag(\''+id.value+'\', \'#'+prevDivPrefix+'\', \''+hiddenId+'\');uap_preview_u_list();" title="Removing tag">x</div></div>');
    }

    jQuery(id).val(-1);
}

function uap_show_hide_drip(){
	if (jQuery('#ihc_mb_type').val()=='show'){
		jQuery('#ihc_drip_content_empty_meta_box').css('display', 'none');
		jQuery('#ihc_drip_content_meta_box').css('display', 'block');
	} else {
		jQuery('#ihc_drip_content_empty_meta_box').css('display', 'block');
		jQuery('#ihc_drip_content_meta_box').css('display', 'none');
	}
}

function uapremoveTag(removeVal, prevDivPrefix, hiddenId){
	jQuery(prevDivPrefix+removeVal).fadeOut(200, function(){
		jQuery(this).remove();
	});

    hidden_i = jQuery(hiddenId).val();
    show_arr = hidden_i.split(',');

    show_arr = removeArrayElement(removeVal, show_arr);
    str = show_arr.join(',');
	jQuery(hiddenId).val(str);
}

function removeArrayElement(elem, arr){
	for (i=0;i<arr.length;i++) {
	    if(arr[i]==elem){
	    	arr.splice(i, 1);
	    }
	}
	return arr;
}

function uap_delete_file_via_ajax(id, u_id, parent, name, hidden_id){
   	jQuery.ajax({
        type : "post",
        url : decodeURI(ajax_url),
        data : {
                   action: "uap_delete_attachment_ajax_action",
                   attachemnt_id: id,
                   user_id: u_id,
                   field_name: name,
               },
        success: function (data) {
        	jQuery(hidden_id).val('');
        	jQuery(parent + ' .ajax-file-upload-filename').remove();
        	jQuery(parent + ' .uap-delete-attachment-bttn').remove();
        	if (jQuery(parent + ' .uap-member-photo').length){
        		jQuery(parent + ' .uap-member-photo').remove();
        		if (name=='uap_avatar'){
        			jQuery(parent).prepend("<div class='uap-no-avatar uap-member-photo'></div>");
        			jQuery(parent + " .uap-file-upload").css("display", 'block');
        		}
        	}

        	if (jQuery(parent + " .uap-file-name-uploaded").length){
        		jQuery(parent + " .uap-file-name-uploaded").remove();
        	}

        	if (jQuery(parent + ' .ajax-file-upload-progress').length){
        		jQuery(parent + ' .ajax-file-upload-progress').remove();
        	}
        	if (jQuery(parent + ' .uap-icon-file-type').length){
        		jQuery(parent + ' .uap-icon-file-type').remove();
        	}
        }
   });
}


function uap_approve_email(id, new_label){
   	jQuery.ajax({
        type : 'post',
        url : decodeURI(ajax_url),
        data : {
                   action: 'uap_approve_user_email',
                   uid: id,
               },
        success: function (response) {
        	jQuery('#user_email_'+id+'_status').fadeOut(200, function(){
        		the_span_style = 'background-color: #f1f1f1;color: #666;padding: 3px 0px;font-size: 10px;font-weight: bold;display: inline-block; min-width: 70px; border: 1px solid #ddd;border-radius: 3px;text-align: center;';
        		jQuery(this).html('<span style="'+the_span_style+'">'+new_label+'</span>');
        		jQuery(this).fadeIn(200);

        		jQuery('#approve_email_'+id).fadeOut(200, function(){
        			jQuery(this).html('');
        		});
        	});
        }
   });
}

function uap_check_email_server(){
	jQuery.ajax({
			type : 'post',
	        url : decodeURI(ajax_url),
	        data : {
	                   action: 'uap_check_mail_server',
	               },
	        success: function (r){
	        	alert(window.uap_messages.email_server_check);
	        }
	});
}

function uap_check_login_field(t, e){
	var n = jQuery('#notice_' + t);
	n.fadeOut(500, function(){
		n.remove();
	});
	var target = jQuery('#uap_login_form [name='+t+']').parent();
	var v = jQuery('#uap_login_form [name='+t+']').val();
	if (v==''){
		jQuery(target).append('<div class="uap-login-notice" id="notice_' + t + '">' + e + '</div>');
	}
}

function uap_check_field_limit(limit, d){
	var val = jQuery(d).val().length;
	if (val>limit){
		jQuery(d).val('');
		alert(limit + ' is the maximum number of characters for this field!');
	}
}

function uap_generate_payments_csv(){
   	jQuery.ajax({
        type : 'post',
        url : decodeURI(ajax_url),
        data : {
                   action: 'do_generate_payments_csv',
                   min_date: jQuery('#csv_min_date').val(),
                   max_date: jQuery('#csv_max_date').val(),
                   payment_type: jQuery('#csv_payment_type').val(),
                   switch_status: jQuery('#csv_switch_status').val(),
        },
        success: function (response) {
        	if (response){
        		jQuery('.uap-hidden-download-link a').attr('href', response);
        		jQuery('.uap-hidden-download-link').fadeIn(200);
        		window.open(response, '_blank');
        	}
        }
   });

}


function uap_do_redirect(base_url, param, value_input){
	var the_url = base_url + '&' + param + '=' + jQuery(value_input).val();
	window.location = the_url;
}



///// SHINY SELECT

function indeed_shiny_select(params){
	/*
	 * params selector, item_selector, option_name_code, option_name_icon, default_icon, default_code
	 */
	this.selector = params.selector; ///got # in front of it
	this.popup_id = 'indeed_select_' + params.option_name_code;
	this.popup_visible = false;
	this.option_name_code = params.option_name_code;
	this.option_name_icon = params.option_name_icon;
	this.item_selector = params.item_selector; /// got . in front of it
	this.init_default = params.init_default;
	this.second_selector = params.second_selector;
	this.default_code = params.default_code;
	var current_object = this;

	jQuery(current_object.selector).after('<input type="hidden" name="' + current_object.option_name_code + '" value="' + params.default_code + '" />');
	jQuery(current_object.selector).after('<input type="hidden" name="' + current_object.option_name_icon + '" value="' + params.default_icon + '" />');
	jQuery(current_object.selector).after('<div class="indeed_select_popup" style="display: none;" id="' + current_object.popup_id + '"></div>');

	///run init
	if (this.init_default){
		jQuery(current_object.selector).html('<i class="fa-uap-preview fa-uap ' + params.default_icon + '"></i>');
	}

	function get_data_and_close(){
		var code = jQuery(this).attr('data-code');
		var i_class = jQuery(this).attr('data-class');
		var the_html = jQuery(this).html();
		jQuery('[name=' + current_object.option_name_code + ']').val(code);
		jQuery('[name=' + current_object.option_name_icon + ']').val(i_class);
		jQuery(current_object.selector).html(the_html);
		remove_popup();
	}

	function load_data_via_ajax(){
		var img = "<img src='" + decodeURI(window.uap_url) + '/wp-content/plugins/indeed-affiliate-pro/assets/images/loading.gif' + "' style='width: 200px'/>";
		jQuery('#'+current_object.popup_id).html(img);
		jQuery('#'+current_object.popup_id).css('display', 'block');
		jQuery.ajax({
		    type : 'post',
		    dataType: "text",
		    url : decodeURI(window.uap_url) + '/wp-admin/admin-ajax.php',
		    data : {
		             action: 'uap_get_font_awesome_popup'
		    },
		    success: function (r){
		       	jQuery('#'+current_object.popup_id).html(r);
		       	jQuery(current_object.item_selector).on('click', get_data_and_close);
			}
		});
	}

	jQuery(current_object.selector).on('click', function(){
		if (!current_object.popup_visible){
			current_object.popup_visible = true;
			load_data_via_ajax();
		} else {
			remove_popup();
		}
	});

	jQuery(current_object.second_selector).on('click', function(){
		//// arrow
		if (!current_object.popup_visible){
			current_object.popup_visible = true;
			load_data_via_ajax();
		} else {
			remove_popup();
		}
	});

	function remove_popup(){
		jQuery('#'+current_object.popup_id).empty();
		jQuery('#'+current_object.popup_id).css('display', 'none');
		current_object.popup_visible = false;
	}

}

function uap_make_export_file(){
	var u = jQuery('#import_users').val();
	var s = jQuery('#import_settings').val();
	var pm = jQuery('#import_postmeta').val();
	jQuery('#ihc_loading_gif .spinner').css('visibility', 'visible');
	jQuery.ajax({
		type : 'post',
	    url : decodeURI(window.uap_url) + '/wp-admin/admin-ajax.php',
	    data : {
	            action: 'uap_make_export_file',
	            import_users: u,
	            import_settings: s,
	            import_postmeta: pm
	           },
	    success: function (response) {
	        if (response!=0){
	        	jQuery('.uap-hidden-download-link a').attr('href', response);
	        	jQuery('.uap-hidden-download-link').fadeIn(200);
				jQuery('#ihc_loading_gif .spinner').css('visibility', 'hidden');
	        }
	    }
	});
}

function uap_check_base_referral_link(v, u){
		if (v.indexOf(u)==-1){
				alert(jQuery('#base_referral_link_alert').html());
		}
}
