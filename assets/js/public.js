function ia_generate_link(aff_id){
	jQuery('.uap-ap-generate-links-result').css('visibility', 'hidden');
	jQuery('.uap-ap-generate-social-result').css('visibility', 'hidden');
	var the_url = jQuery('#ia_generate_aff_custom_url').val();
	if (jQuery('#campaigns_select').length){
		var c = jQuery('#campaigns_select').val();
		if (c=='...') c = '';
	} else {
		var c = '';
	}
	var refType = 0;
	var friendlyLinks = 0;
	if (jQuery('#ref_type').html() && jQuery('#ref_type').val()==1){
		var refType = 1;
	}
	if (jQuery('#friendly_links').html() && jQuery('#friendly_links').val()==1){
		var friendlyLinks = 1;
	}
   	jQuery.ajax({
        type : "post",
        url : decodeURI(ajax_url),
        data : {
                   action: "ia_ajax_return_url_for_aff",
                   aff_id: aff_id,
                   url: the_url,
                   campaign: c,
                   slug: refType,
                   friendly_links: friendlyLinks,
               },
        success: function (r) {
        	if (r){
        		var obj = JSON.parse(r);
        		var u = obj.url;
        		var s = obj.social;
        		var qr = obj.qr_code;
        		if (u!=0){
                	jQuery('.uap-ap-generate-links-result').html(u);
                	jQuery('.uap-ap-generate-links-result').css('visibility', 'visible');
        		}
        		if (s){
        			jQuery('.uap-ap-generate-social-result').html(s);
                	jQuery('.uap-ap-generate-social-result').css('visibility', 'visible');
        		}
        		if (qr){
        			jQuery('.uap-ap-generate-qr-code').html(qr);
                	jQuery('.uap-ap-generate-qr-code').css('visibility', 'visible');
        		}
        	}
        }
   });
}


function uap_register_check_via_ajax(the_type){
	var target_id = '#' + jQuery('.uap-form-create-edit [name='+the_type+']').parent().attr('id');
	var val1 = jQuery('.uap-form-create-edit [name='+the_type+']').val();
	var val2 = '';

	if (the_type=='pass2'){
		val2 = jQuery('.uap-form-create-edit [name=pass1]').val();
	} else if (the_type=='confirm_email'){
		val2 = jQuery('.uap-form-create-edit [name=user_email]').val();
	}

   	jQuery.ajax({
        type : "post",
        url : decodeURI(ajax_url),
        data : {
                   action: "uap_check_reg_field_ajax",
                   type: the_type,
                   value: val1,
                   second_value: val2
               },
        success: function (data) {
        	//remove prev notice, if its case
        	jQuery(target_id + ' .uap-register-notice').remove();
        	jQuery('.uap-form-create-edit [name='+the_type+']').removeClass('uap-input-notice');
        	if (data==1){
        		// it's all good
        	} else {
        		jQuery(target_id).append('<div class="uap-register-notice">'+data+'</div>');
        		jQuery('.uap-form-create-edit [name='+the_type+']').addClass('uap-input-notice');
        	}
        }
   	});
}

//////////////logic condition

function uap_ajax_check_field_condition_onblur_onclick(check_name, field_id, field_name, show){
	var check_value = jQuery(".uap-form-create-edit [name="+check_name+"]").val();
	uap_ajax_check_field_condition(check_value, field_id, field_name, show);
}

function uap_ajax_check_onClick_field_condition(check_name, field_id, field_name, type, show){
	if (type=='checkbox'){
		var vals = [];
		jQuery(".uap-form-create-edit [name='"+check_name+"[]']:checked").each(function() {
			vals.push(jQuery(this).val());
	    });
		var check_value = vals.join(',');
	} else {
		var check_value = jQuery(".uap-form-create-edit [name="+check_name+"]:checked").val();
	}

	uap_ajax_check_field_condition(check_value, field_id, field_name, show);
}

function uap_ajax_check_onChange_multiselect_field_condition(check_name, field_id, field_name, show){
	var obj = jQuery(".uap-form-create-edit [name='"+check_name+"[]']").val();
	if (obj!=null){
		var check_value = obj.join(',');
		uap_ajax_check_field_condition(check_value, field_id, field_name, show);
	}
}

function uap_ajax_check_field_condition(check_value, field_id, field_name, show){
   	jQuery.ajax({
        type : "post",
        url : decodeURI(ajax_url),
        data : {
                   action: "uap_check_logic_condition_value",
                   val: check_value,
                   field: field_name
               },
        success: function (data){
        	var str = jQuery("#uap_exceptionsfields").val();
        	if (str){
            	var arr = str.split(',');
            	var index = arr.indexOf(field_name);
        	} else {
        		var arr = [];
        	}

        	if (data=='1'){
                if (show==1){
                	jQuery(field_id).fadeIn(200);
                	if (arr.indexOf(field_name)!=-1){
                        arr.splice(index, 1);
                	}
                } else {
                	jQuery(field_id).fadeOut(200);
                	if (arr.indexOf(field_name)==-1){
                		arr.push(field_name);
                	}

                }
        	} else {
                    if (show==1){
                    	jQuery(field_id).fadeOut(200);
                    	if (arr.indexOf(field_name)==-1){
                    		arr.push(field_name);
                    	}
                    } else {
                    	jQuery(field_id).fadeIn(200);
                    	if (arr.indexOf(field_name)!=-1){
                            arr.splice(index, 1);
                    	}
                    }
        	}
        	if (arr){
            	var str = arr.join(',');
            	jQuery("#uap_exceptionsfields").val(str);
        	}
        }
   	});
}

function uap_get_checkbox_radio_value(type, selector){
	if (type=='radio'){
		var r = jQuery('[name='+selector+']:checked').val();
		if (typeof r!='undefined'){
			return r;
		}
	} else {
		var arr = [];
		jQuery('[name=\''+selector+'[]\']:checked').each(function(){
			arr.push(this.value);
		});
		if (arr.length>0){
			return arr.join(',');
		}
	}
	return '';
}

function uap_register_check_via_ajax_rec(types_arr){
	jQuery('.uap-register-notice').remove();
	var fields_to_send = [];

	//EXCEPTIONS
	var exceptions = jQuery("#uap_exceptionsfields").val();
	if (exceptions){
		var exceptions_arr = exceptions.split(',');
	}

	for (var i=0; i<types_arr.length; i++){
		//CHECK IF FIELD is in exceptions
		if (exceptions_arr && exceptions_arr.indexOf(types_arr[i])>-1){
			continue;
		}

		jQuery('.uap-form-create-edit [name='+types_arr[i]+']').removeClass('uap-input-notice');

		var field_type = jQuery('.uap-form-create-edit [name=' + types_arr[i] + ']').attr('type');
		if (typeof field_type=='undefined'){
			var field_type = jQuery('.uap-form-create-edit [name=\'' + types_arr[i] + '[]\']').attr('type');
		}
		if (typeof field_type=='undefined'){
			var field_type = jQuery('.uap-form-create-edit [name=\'' + types_arr[i] + '\']').prop('nodeName');
		}
		if (typeof field_type=='undefined'){
			var field_type = jQuery('.uap-form-create-edit [name=\'' + types_arr[i] + '[]\']').prop('nodeName');
			if (field_type=='SELECT'){
				field_type = 'multiselect';
			}
		}

		if (field_type=='checkbox' || field_type=='radio'){
			var val1 = uap_get_checkbox_radio_value(field_type, types_arr[i]);
		} else if ( field_type=='multiselect' ){
			val1 = jQuery('.uap-form-create-edit [name=\'' + types_arr[i] + '[]\']').val();
			if (typeof val1=='object' && val1!=null){
				val1 = val1.join(',');
			}
		} else {
			var val1 = jQuery('.uap-form-create-edit [name='+types_arr[i]+']').val();
		}

		var val2 = '';
		if (types_arr[i]=='pass2'){
			val2 = jQuery('.uap-form-create-edit [name=pass1]').val();
		} else if (types_arr[i]=='confirm_email'){
			val2 = jQuery('.uap-form-create-edit [name=user_email]').val();
		} else if (types_arr[i]=='tos') {
			if (jQuery('.uap-form-create-edit [name=tos]').is(':checked')){
				val1 = 1;
			} else {
				val1 = 0;
			}
		}
		fields_to_send.push({type: types_arr[i], value: val1, second_value: val2});
	}

   	jQuery.ajax({
        type : "post",
        url : decodeURI(ajax_url),
        data : {
                   action: "uap_check_reg_field_ajax",
                   fields_obj: fields_to_send
               },
        success: function (data) {
        	var obj = JSON.parse(data);
        	var must_submit = 1;
        	for (var j=0; j<obj.length; j++){
        		var field_type = jQuery('.uap-form-create-edit [name=' + obj[j].type + ']').attr('type');
        		if (typeof field_type=='undefined'){
        			var field_type = jQuery('.uap-form-create-edit [name=\'' + obj[j].type + '[]\']').attr('type');
        		}
        		if (typeof field_type=='undefined'){
        			var field_type = jQuery('.uap-form-create-edit [name=\'' + obj[j].type + '\']').prop('nodeName');
        		}
        		if (typeof field_type=='undefined'){
        			var field_type = jQuery('.uap-form-create-edit [name=\'' + obj[j].type + '[]\']').prop('nodeName');
        			if (field_type=='SELECT'){
        				field_type = 'multiselect';
        			}
        		}

            	if (field_type=='radio'){
            		var target_id = jQuery('.uap-form-create-edit [name='+obj[j].type+']').parent().parent().attr('id');
            	} else if (field_type=='checkbox' && obj[j].type!='tos'){
            		var target_id = jQuery('.uap-form-create-edit [name=\''+obj[j].type+'[]\']').parent().parent().attr('id');
            	} else if ( field_type=='multiselect'){
            		var target_id = jQuery('.uap-form-create-edit [name=\''+obj[j].type+'[]\']').parent().attr('id');
            	} else {
            		var target_id = jQuery('.uap-form-create-edit [name='+obj[j].type+']').parent().attr('id');
            	}

            	if (obj[j].value==1){
            		// it's all good
            	} else {
            		//errors
                	if (typeof target_id=='undefined'){
                		//no target id...insert msg after input
                		jQuery('.uap-form-create-edit [name='+obj[j].type+']').after('<div class="uap-register-notice">'+obj[j].value+'</div>');
                		must_submit = 0;
                	} else {
                		jQuery('#'+target_id).append('<div class="uap-register-notice">'+obj[j].value+'</div>');
                		jQuery('.uap-form-create-edit [name=' + obj[j].type + ']').addClass('uap-input-notice');
                		must_submit = 0;
                	}
            	}
        	}

        	if (must_submit==1){
    			window.must_submit=1;
    			jQuery(".uap-form-create-edit").submit();
        	} else {
    			return false;
        	}
        }
   	});

}

function uap_show_subtabs(t){
	if (jQuery('#uap_public_ap_' + t).css('display')=='block'){
		jQuery('#uap_fa_sign-' + t).removeClass('fa-account-down-uap');
		jQuery('#uap_fa_sign-' + t).addClass('fa-account-right-uap');
		jQuery('.uap-public-ap-menu-subtabs').css('display', 'none');
	} else {
		jQuery('.uap-ap-menu-sign').removeClass('fa-account-down-uap');
		jQuery('.uap-ap-menu-sign').addClass('fa-account-right-uap');
		jQuery('.uap-public-ap-menu-subtabs').css('display', 'none');
		jQuery('#uap_public_ap_' + t).css('display', 'block');
		jQuery('#uap_fa_sign-' + t).removeClass('fa-account-right-uap');
		jQuery('#uap_fa_sign-' + t).addClass('fa-account-down-uap');
	}
}

function uap_payment_type(){
	jQuery.each(['paypal', 'bt', 'stripe', 'stripe_v2'], function(k, v){
		jQuery('#uap_payment_with_' + v).css('display', 'none');
	});
	var t = jQuery('[name=uap_affiliate_payment_type]').val();
	jQuery('#uap_payment_with_' + t).fadeIn(200);
}

function uap_become_affiliate_public(){
   	jQuery.ajax({
        type: 'post',
        url : decodeURI(ajax_url),
        data: {
               action: 'uap_make_wp_user_affiliate_from_public',
        },
        success: function (r) {
			if (r){
				window.location.href = r;
			}
        }
   	});
}

function uap_add_to_wallet(divCheck, showValue, hidden_input_id){
    var str = jQuery(hidden_input_id).val();
    if (str!=''){
    	show_arr = str.split(',');
    	for (a in show_arr ){
        	show_arr[a] = parseInt(show_arr[a])
		}
    } else {
    	show_arr = [];
    }

    if (jQuery(divCheck).is(':checked')){
    	if (show_arr.indexOf(showValue)==-1){
        	show_arr.push(showValue);
    	}
    } else {

        for (a in show_arr ){
        	if (parseInt(show_arr[a])==showValue){
        		show_arr.splice(a, 1);
        	}
		}
    }
    str = show_arr.join(',');
    jQuery(hidden_input_id).val(str);

    jQuery('#uap_total_amount').html('');
   	jQuery.ajax({
        type: 'post',
        url : decodeURI(ajax_url),
        data: {
               action: 'uap_get_amount_for_referral_list',
               r: str
        },
        success: function (r) {
						if (r){
							jQuery('#uap_total_amount').html(r);
						} else {
							jQuery('#uap_total_amount').html(0);
						}
        }
   	});
}

function uap_remove_wallet_item(t, c){
   	jQuery.ajax({
        type: 'post',
        url : decodeURI(ajax_url),
        data: {
               action: 'uap_delete_wallet_item_via_ajax',
               type: t,
               code: c,
        },
        success: function (r) {
			if (r){
				if (window.uap_current_url){
					window.location.href = window.uap_current_url;
				}
			}
        }
   	});
}

function uap_delete_file_via_ajax(id, u_id, parent, name, hidden_id){
	var r = confirm("Are you sure you want to delete?");
	if (r) {
			var s = jQuery(parent).attr('data-h');
	   	jQuery.ajax({
	        type : "post",
	        url : decodeURI(ajax_url),
	        data : {
	                   action: "uap_delete_attachment_ajax_action",
	                   attachemnt_id: id,
	                   user_id: u_id,
	                   field_name: name,
										 h: s
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

function uap_affiliate_username_test(v){
	jQuery('.uap-username-not-exists').remove();
   	jQuery.ajax({
        type: 'post',
        url : decodeURI(ajax_url),
        data: {
               action: 'uap_check_if_username_is_affiliate',
               username: v,
        },
        success: function (r) {
			if (r==1){
				jQuery('#uap_affiliate_username_text').after('<div class="uap-username-not-exists">Username that You write is not affiliate!</div>');
			}
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

function uap_stripe_v2_update_fields(){
	var country = jQuery('.stripe_v2_meta_data_country').val();
	var user_type = jQuery('.stripe_v2_meta_data_user_type').val();
	if (country!='us'){
		country = 'non_us';
	}
	jQuery('.uap-stripe-v2-field').each(function(){
		var temp_country = jQuery(this).attr('data-country');
		var temp_type = jQuery(this).attr('data-type');
		if (temp_country=='all' && temp_type=='all'){
			jQuery(this).css('display', 'block');
		} else if (country==temp_country){
			if (user_type==temp_type || temp_type=='all'){
				jQuery(this).css('display', 'block');
			}
		} else if (user_type==temp_type){
			if (country==temp_country || temp_country=='all'){
				jQuery(this).css('display', 'block');
			}
		} else {
			jQuery(this).css('display', 'none');
		}
	})
}
