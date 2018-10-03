<?php
class Uap_Ajax{

	public function __construct(){
		/*
		 * REGISTER ALL AJAX CALLS HERE
		 * @param none
		 * @return none
		 */

		/// PUBLIC
		add_action('wp_ajax_ia_ajax_return_url_for_aff',array($this, 'ia_ajax_return_url_for_aff'));
		add_action('wp_ajax_nopriv_ia_ajax_return_url_for_aff', array($this, 'ia_ajax_return_url_for_aff'));

		add_action('wp_ajax_uap_check_reg_field_ajax',array($this, 'uap_check_reg_field_ajax'));
		add_action('wp_ajax_nopriv_uap_check_reg_field_ajax', array($this, 'uap_check_reg_field_ajax'));

		add_action('wp_ajax_uap_check_logic_condition_value',array($this, 'uap_check_logic_condition_value'));
		add_action('wp_ajax_nopriv_uap_check_logic_condition_value', array($this, 'uap_check_logic_condition_value'));

		add_action('wp_ajax_uap_make_wp_user_affiliate_from_public',array($this, 'uap_make_wp_user_affiliate_from_public'));
		add_action('wp_ajax_nopriv_uap_make_wp_user_affiliate_from_public', array($this, 'uap_make_wp_user_affiliate_from_public'));

		add_action('wp_ajax_uap_get_amount_for_referral_list', array($this, 'uap_get_amount_for_referral_list'));
		add_action('wp_ajax_nopriv_uap_get_amount_for_referral_list', array($this, 'uap_get_amount_for_referral_list'));

		add_action('wp_ajax_uap_delete_wallet_item_via_ajax', array($this, 'uap_delete_wallet_item_via_ajax'));
		add_action('wp_ajax_nopriv_uap_delete_wallet_item_via_ajax', array($this, 'uap_delete_wallet_item_via_ajax'));

		add_action('wp_ajax_nopriv_uap_delete_attachment_ajax_action', array($this, 'uap_delete_attachment_ajax_action'));
		add_action('wp_ajax_uap_delete_attachment_ajax_action', array($this, 'uap_delete_attachment_ajax_action'));

		add_action('wp_ajax_nopriv_uap_check_if_username_is_affiliate', array($this, 'uap_check_if_username_is_affiliate'));
		add_action('wp_ajax_uap_check_if_username_is_affiliate', array($this, 'uap_check_if_username_is_affiliate'));

		add_action('wp_ajax_nopriv_uap_ap_reset_custom_banner', array($this, 'uap_ap_reset_custom_banner'));
		add_action('wp_ajax_uap_ap_reset_custom_banner', array($this, 'uap_ap_reset_custom_banner'));

		/// ADMIN
		add_action( 'wp_ajax_uap_register_preview_ajax', array($this, 'uap_register_preview_ajax'));
		add_action( 'wp_ajax_uap_login_form_preview', array($this, 'uap_login_form_preview'));
		add_action( 'wp_ajax_uap_serialize_json', array($this, 'uap_serialize_json'));
		add_action( 'wp_ajax_uap_make_ranks_reorder', array($this, 'uap_make_ranks_reorder'));
		add_action( 'wp_ajax_uap_update_aweber', array($this, 'uap_update_aweber') );
		add_action( 'wp_ajax_uap_get_cc_list', array($this, 'uap_get_cc_list') );
		add_action( 'wp_ajax_uap_get_notification_default_by_type', array($this, 'uap_get_notification_default_by_type'));
		add_action( 'wp_ajax_uap_ajax_admin_popup_the_shortcodes', array($this, 'uap_ajax_admin_popup_the_shortcodes'));
		add_action('wp_ajax_uap_approve_affiliate', array($this, 'uap_approve_affiliate'));
		add_action('wp_ajax_uap_make_wp_user_affiliate', array($this, 'uap_make_wp_user_affiliate'));
		add_action('wp_ajax_uap_delete_currency_code_ajax', array($this, 'uap_delete_currency_code_ajax'));
		add_action('wp_ajax_uap_remove_slug_from_aff', array($this, 'uap_remove_slug_from_aff'));
		add_action('wp_ajax_uap_preview_user_listing', array($this, 'uap_preview_user_listing'));
		add_action('wp_ajax_uap_affiliate_simple_user', array($this, 'uap_affiliate_simple_user'));
		add_action('wp_ajax_uap_approve_user_email', array($this, 'uap_approve_user_email'));
		add_action('wp_ajax_uap_check_mail_server', array($this, 'uap_check_mail_server'));
		add_action('wp_ajax_do_generate_payments_csv', array($this, 'do_generate_payments_csv'));
		add_action('wp_ajax_uap_get_font_awesome_popup', array($this, 'do_get_font_awesome_popup'));
		add_action('wp_ajax_uap_make_export_file', array($this, 'make_export_file'));
		add_action('wp_ajax_uap_trigger_migration', array($this, 'uap_trigger_migration'));
		add_action('wp_ajax_uap_get_empty_progress_bar', array($this, 'uap_get_empty_progress_bar'));
		add_action('wp_ajax_uap_migrate_get_status', array($this, 'uap_migrate_get_status'));
		add_action('wp_ajax_uap_migrate_reset_log', arraY($this, 'uap_migrate_reset_log'));

		add_action('wp_ajax_uap_admin_send_email_popup', arraY($this, 'uap_admin_send_email_popup'));
		add_action('wp_ajax_uap_admin_do_send_email', arraY($this, 'uap_admin_do_send_email'));

	}

	public function ia_ajax_return_url_for_aff(){
		/*
		 * AJAX CALL
		 * @param none
		 * @return string
		 */
		if (!empty($_REQUEST['aff_id']) && !empty($_REQUEST['url'])){
			$param = 'ref';
			$value = $_REQUEST['aff_id'];
			$campaign_variable = '';
			$campaign_value = '';

			global $indeed_db;
			$settings = $indeed_db->return_settings_from_wp_option('general-settings');
			if (!empty($settings['uap_referral_variable'])){
				$param = $settings['uap_referral_variable'];
			}

			$uid = $indeed_db->get_uid_by_affiliate_id($_REQUEST['aff_id']);
			if (!empty($_REQUEST['slug'])){
				$slug = $indeed_db->get_custom_slug_for_uid($uid);
				if ($slug){
					$value = $slug;
				}
			} else if ($uid && $settings['uap_default_ref_format']=='username'){
				$user_info = get_userdata($uid);
				if (!empty($user_info->user_login)){
					///$value = $user_info->user_login;
					$value = urlencode($user_info->user_login);
				}
			}

			$url = $_REQUEST['url'];

			if (!empty($_REQUEST['campaign'])){
				$campaign_variable = get_option('uap_campaign_variable');
				$campaign_value = $_REQUEST['campaign'];
			}

			$arr['url'] = uap_create_affiliate_link($url, $param, $value, $campaign_variable, $campaign_value, $_REQUEST['friendly_links']);
			$arr['social'] = '';
			$arr['qr'] = '';
			/// SOCIAL
			if (uap_is_social_share_intalled_and_active() && get_option('uap_social_share_enable')){
				$shortcode = get_option('uap_social_share_shortcode');
				if ($shortcode){
					$shortcode = stripslashes($shortcode);
					$shortcode = str_replace(']', '', $shortcode);
					$shortcode .= " is_affiliates=1"; ///just for safe
					$shortcode .= " custom_description='" . get_option('uap_social_share_message') . "'";
					$shortcode .= " uap_no_fb_js=1 ";
					$shortcode .= " custom_url='" . $arr['url'] ."']";
					$arr['social'] = do_shortcode($shortcode);
				}
			}
			/// QR CODE
			if ($indeed_db->is_magic_feat_enable('qr_code')){
				$img = uap_generate_qr_code($arr['url'], $_REQUEST['aff_id'] . '_custom_url');
				$arr['qr_code'] = '<div class="uap-qr-code-wrapper">
								<img src="' . $img . '" />
								<a href="' . $img . '" download="' . basename($img) . '" class="uap-qr-code-download">' . __('Download', 'uap') . '</a>
				</div>';
			}
			echo json_encode($arr);
		}
		die();
	}

	public function uap_register_preview_ajax(){
		/*
		 * @param none
		 * @return none
		 */
		global $indeed_db;
		require_once UAP_PATH . 'classes/Uap_Add_Edit_Affiliate.class.php';
		$args = array(
					'user_id' => false,
					'type' => 'create',
					'tos' => true,
					'captcha' => true,
					'action' => '',
					'is_public' => true,
					'register_template' => @$_REQUEST['template'],
		);
		$obj = new Uap_Add_Edit_Affiliate($args);
		$data = $indeed_db->return_settings_from_wp_option('register');
		$data = $obj->form();
		$data['template'] = @$_REQUEST['template'];
		ob_start();
		require_once UAP_PATH . 'public/views/register.php';
		$output = ob_get_contents();
		ob_end_clean();
		echo $output;
		die();
	}

	public function uap_login_form_preview(){
		/*
		 * @param none
		 * @return none
		 */
		$metas['uap_login_remember_me'] = $_REQUEST['remember'];
		$metas['uap_login_register'] = $_REQUEST['register_link'];
		$metas['uap_login_pass_lost'] = $_REQUEST['pass_lost'];
		$metas['uap_login_template'] = $_REQUEST['template'];
		$metas['uap_login_custom_css'] = $_REQUEST['css'];
		$metas['uap_login_show_recaptcha'] = $_REQUEST['uap_login_show_recaptcha'];
		require_once UAP_PATH . 'public/Uap_Login.class.php';
		$object = new Uap_Login();
		echo $object->print_login_form($metas, 'unreg');
		die();
	}


	public function uap_check_reg_field_ajax(){
		/*
		 * @param none
		 * @return none
		 */
		global $indeed_db;
		$register_msg = $indeed_db->return_settings_from_wp_option('register-msg');
		if (isset($_REQUEST['type']) && isset($_REQUEST['value'])){
			echo uap_check_value_field($_REQUEST['type'], $_REQUEST['value'], $_REQUEST['second_value'], $register_msg);
		} else if (isset($_REQUEST['fields_obj'])){
			$arr = $_REQUEST['fields_obj'];
			foreach ($arr as $k=>$v){
				$return_arr[] = array( 'type' => $v['type'], 'value' => uap_check_value_field($v['type'], $v['value'], $v['second_value'], $register_msg) );
			}
			echo json_encode($return_arr);
		}
		die();
	}

	public function uap_check_logic_condition_value(){
		/*
		 * @param none
		 * @return none
		 */
		if (isset($_REQUEST['val']) && isset($_REQUEST['field'])){
			global $indeed_db;
			$fields_meta = $indeed_db->register_get_custom_fields();
			$key = uap_array_value_exists($fields_meta, $_REQUEST['field'], 'name');
			if ($key!==FALSE){
				if (isset($fields_meta[$key]['conditional_logic_corresp_field_value'])){
					if ($fields_meta[$key]['conditional_logic_cond_type']=='has'){
						//has value
						if ($fields_meta[$key]['conditional_logic_corresp_field_value']==$_REQUEST['val']){
							echo 1;
							die();
						}
					} else {
						//contain value
						if (strpos($_REQUEST['val'], $fields_meta[$key]['conditional_logic_corresp_field_value'])!==FALSE){
							echo 1;
							die();
						}
					}
				}
			}
		}
		echo 0;
		die();
	}


	public function uap_make_ranks_reorder(){
		/*
		 * @param none
		 * @return string
		 */
		if (!empty($_REQUEST['new_order']) && isset($_REQUEST['rank_id']) && isset($_REQUEST['current_label'])){
			global $indeed_db;
			$data = $indeed_db->get_ranks();
			if ($_REQUEST['rank_id']==0){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_ranks';
				$data_db = $wpdb->get_row("SELECT rank_order FROM $table ORDER BY rank_order DESC LIMIT 1");
				$old_order = (empty($data_db->rank_order)) ? 1 : $data_db->rank_order + 1;
				$arr['rank_order'] = $old_order;
				$arr['id'] = 0;
				$arr['label'] = $_REQUEST['current_label'];
				$object = new stdClass();
				foreach ($arr as $key => $value){
					$object->$key = $value;
				}
				$data[] = $object;
			}
			$ranks = uap_custom_reorder_rank($data, $_REQUEST['rank_id'], $_REQUEST['new_order']);
			if (!function_exists('uap_create_ranks_graphic')){
				require_once UAP_PATH . 'admin/utilities.php';
			}
			echo uap_create_ranks_graphic($ranks, $_REQUEST['rank_id']);
		}
		die();
	}

	public function uap_update_aweber(){
		/*
		 * @param none
		 * @return int 1
		 */
		include_once UAP_PATH .'classes/email_services/aweber/aweber_api.php';
		list($consumer_key, $consumer_secret, $access_key, $access_secret) = AWeberAPI::getDataFromAweberID( $_REQUEST['auth_code'] );
		update_option( 'uap_aweber_consumer_key', $consumer_key );
		update_option( 'uap_aweber_consumer_secret', $consumer_secret );
		update_option( 'uap_aweber_acces_key', $access_key );
		update_option( 'uap_aweber_acces_secret', $access_secret );
		echo 1;
		die();
	}

	public function uap_get_cc_list(){
		/*
		 * @param none
		 * @return none
		 */
		echo json_encode(uap_return_cc_list($_REQUEST['uap_cc_user'],$_REQUEST['uap_cc_pass']));
		die();
	}

	public function uap_get_notification_default_by_type(){
		/*
		 * @param none
		 * @return string
		 */
		if (!empty($_POST['type'])){
			$template = uap_return_default_notification_content($_POST['type']);
			if ($template){
				echo json_encode($template);
			}
		}
		die();
	}

	public function uap_ajax_admin_popup_the_shortcodes(){
		/*
		 * @param none
		 * @return string
		 */
		require_once UAP_PATH . 'admin/views/popup-shortcodes.php';
		die();
	}

	public function uap_approve_affiliate(){
		/*
		 * @param none
		 * @return int
		 */
		if (!empty($_REQUEST['uid'])){
			$role = get_option('uap_after_approve_role');
			if (empty($role)){
					$role = get_option('default_role');
			}
			$new_role = empty($role) ? 'subscriber' : $role;
			$uid = wp_update_user(array( 'ID' => $_REQUEST['uid'], 'role' => $new_role));
			uap_send_user_notifications($_REQUEST['uid'], 'affiliate_account_approve');
			echo 1;
		}
		die();
	}

	public function uap_make_wp_user_affiliate(){
		/*
		 * @param none
		 * @return none
		 */
		 if (!empty($_REQUEST['uid'])){
		 	global $indeed_db;
		 	if ($indeed_db->is_user_admin($_REQUEST['uid'])){
		 		echo 2;
				die();
		 	}
			$inserted = $indeed_db->save_affiliate($_REQUEST['uid']);
			if ($inserted){
				/// put default rank on this new affiliate
				$default_rank = get_option('uap_register_new_user_rank');
				$indeed_db->update_affiliate_rank_by_uid($_REQUEST['uid'], $default_rank);
				echo 1;
			}
		 }
		 die();
	}

	public function uap_make_wp_user_affiliate_from_public(){
		/*
		 * @param none
		 * @return none
		 */
		 global $current_user, $indeed_db;
		 if (!empty($current_user) && !empty($current_user->ID)){
		 	$uid = $current_user->ID;
		 	if ($indeed_db->is_user_admin($uid)){
		 		echo 0;
				die();
		 	}

			$inserted = $indeed_db->save_affiliate($uid);
			if ($inserted){
				/// put default rank on this new affiliate
				$default_rank = get_option('uap_register_new_user_rank');
				$indeed_db->update_affiliate_rank_by_uid($uid, $default_rank);

				/// SET MLM RELATION
				$indeed_db->set_mlm_relation_on_new_affiliate($inserted);

				//SEND NOTIFICATIONS
				uap_send_user_notifications($uid, 'register', $default_rank);//notify the affiliate
				uap_send_user_notifications($uid, 'admin_user_register', $default_rank);//notify the admin
			}
			$pid = get_option('uap_general_user_page');
			if ($pid){
				$new_url = get_permalink($pid);
			}
			if (!$new_url){
				$new_url = get_home_url();
			}
			echo $new_url;
		 }
		 die();
	}

	public function uap_delete_currency_code_ajax(){
		/*
		 * @param none
		 * @return  none
		 */
		if (isset($_REQUEST['code'])){
			$data = get_option('uap_currencies_list');
			if (!empty($data[$_REQUEST['code']])){
				unset($data[$_REQUEST['code']]);
				echo 1;
			}
			update_option('uap_currencies_list', $data);
		}
		die();
	}

	public function uap_remove_slug_from_aff(){
		/*
		 * @param none
		 * @return none
		 */
		 if (!empty($_REQUEST['uid'])){
		 	update_user_meta($_REQUEST['uid'], 'uap_affiliate_custom_slug', '');
		 }
		 die();
	}

	public function uap_get_amount_for_referral_list(){
		if (!empty($_REQUEST['r'])){
			global $indeed_db;
			$referral_list = explode(',', $_REQUEST['r']);
			if (!empty($referral_list) && count($referral_list)){
				$amount = $indeed_db->get_amount_for_referrals($referral_list);
				echo $amount;
			}
		}
		die();
	}

	public function uap_delete_wallet_item_via_ajax(){
		/*
		 * @param none
		 * @return int
		 */
		 if (!empty($_REQUEST['code']) && !empty($_REQUEST['type'])){
		 	 global $indeed_db;
			 global $current_user;
			 @$uid = (empty($current_user->ID)) ? 0 : $current_user->ID;
			 @$affiliate_id = $indeed_db->get_affiliate_id_by_wpuid($uid);
			 if ($affiliate_id){
				 $indeed_db->delete_wallet_item($_REQUEST['type'], $affiliate_id, $_REQUEST['code']);
				 echo 1;
			 }
		 }
		 die();
	}

	public function uap_preview_user_listing(){
		/*
		 * @param none
		 * @return stirng
		 */
		if (!empty($_REQUEST['shortcode'])){
			define('IS_PREVIEW', TRUE);
			$shortcode = stripslashes($_REQUEST['shortcode']);
			require_once UAP_PATH . 'public/Uap_Main_Public.class.php';
			echo do_shortcode($shortcode);
		}
		die();
	}

	public function uap_delete_attachment_ajax_action(){
		/*
		 * @param none
		 * @return string
		 */
		 $uid = isset($_POST['user_id']) ? esc_sql($_POST['user_id']) : 0;
		 $field_name = isset($_POST['field_name']) ? esc_sql($_POST['field_name']) : '';
		 $attachment_id = isset($_POST['attachemnt_id']) ? esc_sql($_POST['attachemnt_id']) : 0;

		 if (function_exists('is_user_logged_in') && is_user_logged_in()){
			 $current_user = wp_get_current_user();
			 if ( !empty($uid) && $uid == $current_user->ID ){
					 /// registered users
					 if (!empty($attachment_id)){
							 $verify_attachment_id  = get_user_meta($uid, $field_name, TRUE);
							 if ($verify_attachment_id==$attachment_id){
									 wp_delete_attachment($attachment_id, TRUE);
									 update_user_meta($uid, $field_name, '');
									 echo 0;
									 die();
							 }
					 }
			 } else if (current_user_can('administrator')){
					/// ADMIN, no extra checks
					wp_delete_attachment($attachment_id, TRUE);
					update_user_meta($uid, $field_name, '');
			 }
		 } else if ($uid==-1){
				 /// unregistered user
				 $hash_from_user = isset($_POST['h']) ? esc_sql($_POST['h']) : '';
				 $attachment_url = wp_get_attachment_url($attachment_id);
				 $attachment_hash = md5($attachment_url);
				 if (empty($hash_from_user) || empty($attachment_hash) || $hash_from_user!==$attachment_hash){
						 echo 1;die;
				 } else {
						 wp_delete_attachment($attachment_id, TRUE);
						 echo 0;die;
				 }
		 }

		 echo 1;
		 die();
	}

	public function uap_affiliate_simple_user(){
		/*
		 * @param none
		 * @return string
		 */
		 if (!empty($_REQUEST['uid'])){
		 	global $indeed_db;
			$indeed_db->remove_user_from_affiliate($_REQUEST['uid']);
		 }
		 die();
	}

	public function uap_approve_user_email(){
		/*
		 * @param none
		 * @return string
		 */
		 if (!empty($_REQUEST['uid'])){
		 	 update_user_meta($_REQUEST['uid'], 'uap_verification_status', 1);
			 echo 1;
		 }
		 die();
	}

	public function uap_check_mail_server(){
		/*
		 * @param none
		 * @return string
		 */
		 $from_email = '';
		 $from_name = '';
		 $from_email = get_option('uap_notification_email_from');
		 if (!$from_email){
			$from_email = get_option('admin_email');
		 }
		 $from_name = get_option('uap_notification_name');
		 if (empty($from_name)){
			$from_name = get_option("blogname");
		 }
		 $headers[] = "From: $from_name <$from_email>";
		 $headers[] = 'Content-Type: text/html; charset=UTF-8';

		 $to = get_option('admin_email');
		 $subject = get_option('blogname') . ': ' . __('Testing Your E-mail Server', 'uap');
		 $content = __('Just a simple message to test if Your E-mail Server is working', 'uap');
		 wp_mail($to, $subject, $content, $headers);
		 echo 1;
		 die();
	}

	public function uap_check_if_username_is_affiliate(){
		/*
		 * @param none
		 * @return none
		 */
		 if ($_REQUEST['username']){
		 	global $indeed_db;
		 	$affiliate_id = $indeed_db->get_affiliate_id_by_username($_REQUEST['username']);
			if (!$affiliate_id){
				echo 1;
				die();
			}
		 }
		 echo 0;
		 die();
	}


	/*
	 * @param none
	 * @return string
	 */
	public function do_generate_payments_csv(){
		require_once UAP_PATH . 'classes/Uap_Payments_Export.class.php';
		$obj = new Uap_Payments_Export();

		if (!empty($_REQUEST['min_date'])){
			$obj->set_min_date($_REQUEST['min_date']);
		}
		if (!empty($_REQUEST['max_date'])){
			$obj->set_max_date($_REQUEST['max_date']);
		}
		if (!empty($_REQUEST['payment_type'])){
			$obj->set_payment_type($_REQUEST['payment_type']);
		}
		if (!empty($_REQUEST['switch_status'])){
			$obj->set_new_status($_REQUEST['switch_status']);
		}

		echo $obj->generate_csv();
		die();
	}


	/*
	 * @param none
	 * @return string
	 */
	public function do_get_font_awesome_popup(){
		ob_start();
		require_once UAP_PATH . 'admin/views/popup_font_awesome.php';
		$output = ob_get_contents();
		ob_end_clean();
		echo $output;
		die();
	}


	/*
	 * @param none
	 * @return none
	 */
	public function make_export_file(){
		global $wpdb, $indeed_db;
		require_once UAP_PATH . 'classes/Indeed_Import_Export/IndeedExport.class.php';
		$export = new IndeedExport();
		$export->setFile(UAP_PATH . 'export.xml');
		if (!empty($_REQUEST['import_users'])){
			////////// USERS
			//$export->setEntity( array('full_table_name' => $wpdb->base_prefix . 'users', 'table_name' => 'users') );
			//$export->setEntity( array('full_table_name' => $wpdb->base_prefix . 'usermeta', 'table_name' => 'usermeta') );
			//$export->setEntity( array('full_table_name' => $wpdb->base_prefix . 'uap_affiliates', 'table_name' => 'uap_affiliates') );
			$export->setGetUsers(TRUE);
		}
		if (!empty($_REQUEST['import_settings'])){
			///////// SETTINGS
			$values = $indeed_db->get_all_ump_wp_options();
			$export->setEntity( array('full_table_name' => $wpdb->base_prefix . 'options', 'table_name' => 'options', 'values' => $values) );
			$export->setEntity( array('full_table_name' => $wpdb->prefix . 'uap_banners', 'table_name' => 'uap_banners') );
			$export->setEntity( array('full_table_name' => $wpdb->prefix . 'uap_notifications', 'table_name' => 'uap_notifications') );
			$export->setEntity( array('full_table_name' => $wpdb->prefix . 'uap_ranks', 'table_name' => 'uap_ranks') );
			$export->setEntity( array('full_table_name' => $wpdb->prefix . 'uap_offers', 'table_name' => 'uap_offers') );
			$export->setEntity( array('full_table_name' => $wpdb->prefix . 'uap_offers_affiliates_reference', 'table_name' => 'uap_offers_affiliates_reference') );
			$export->setEntity( array('full_table_name' => $wpdb->prefix . 'uap_mlm_relations', 'table_name' => 'uap_mlm_relations') );
			$export->setEntity( array('full_table_name' => $wpdb->prefix . 'uap_ranks_history', 'table_name' => 'uap_ranks_history') );
			$export->setEntity( array('full_table_name' => $wpdb->prefix . 'uap_landing_commissions', 'table_name' => 'uap_landing_commissions') );
			$export->setEntity( array('full_table_name' => $wpdb->prefix . 'uap_coupons_code_affiliates', 'table_name' => 'uap_coupons_code_affiliates') );
			$export->setEntity( array('full_table_name' => $wpdb->prefix . 'uap_reports', 'table_name' => 'uap_reports') );
			$export->setEntity( array('full_table_name' => $wpdb->prefix . 'uap_ref_links', 'table_name' => 'uap_ref_links') );
		}
		if ($export->run()){
			/// print link to file
			echo UAP_URL . 'export.xml';
		} else {
			/// no entity
			echo 0;
		}
		die();
	}

	public function uap_trigger_migration()
	{
			$serviceType = isset($_POST['serviceType']) ? $_POST['serviceType'] : false;
			if (empty($serviceType)){
					echo 0;
					die;
			}
			$data = get_option('uap_do_migrate_log');
			if (isset($data[$serviceType])){
					unset($data[$serviceType]);
					update_option('uap_do_migrate_log', $data);
			}
			$assignRank = isset($_POST['assignRank']) ? $_POST['assignRank'] : '';
			$target = admin_url('admin.php?uap_act=migrate&service_type=' . $serviceType . '&assignRank=' . $assignRank);
			wp_redirect($target);
			exit;
	}

	public function uap_get_empty_progress_bar()
	{
			include UAP_PATH . 'admin/views/empty_progress_bar.php';
			die;
	}

	public function uap_migrate_get_status()
	{
			$serviceType = isset($_POST['serviceType']) ? $_POST['serviceType'] : '';
			if (empty($serviceType)){
					echo -1;
					die;
			}
			$data = get_option('uap_do_migrate_log');
			$logData = isset($data[$serviceType]) ? $data[$serviceType] : false;
			if (empty($logData)){
				 echo -1;
				 die;
			}
			if ($logData=='completed'){
					echo 100;
					die;
			}
			$total = 0;
			$total += empty($logData['affiliates-count']) ? 0 : $logData['affiliates-count'];
			$total += empty($logData['referrals-count']) ? 0 : $logData['referrals-count'];
			$current = 0;
			$current += empty($logData['affiliates-offset']) ? 0 : $logData['affiliates-offset'];
			$current += empty($logData['referrals-offset']) ? 0 : $logData['referrals-offset'];
			$percentage = (100 * $current)/$total;
			echo (int)$percentage;
			die;
	}

	public function uap_migrate_reset_log()
	{
		$serviceType = isset($_POST['serviceType']) ? $_POST['serviceType'] : '';
		if (empty($serviceType)){
				echo -1;
				die;
		}
		$data = get_option('uap_do_migrate_log');
		if (isset($data[$serviceType])){
				unset($data[$serviceType]);
				update_option('uap_do_migrate_log', $data);
				echo 1;
		}
		die;
	}

	public function uap_ap_reset_custom_banner()
	{
			global $current_user;
			$uid = isset($current_user->ID) ? $current_user->ID : 0;
			if (empty($uid)){
					die;
			}
			$banner = isset($_POST['oldBanner']) ? esc_sql($_POST['oldBanner']) : '';
			if (empty($banner)){
					die;
			}
			update_user_meta($uid, 'uap_account_page_personal_header', $banner);
			die;
	}

	public function uap_admin_send_email_popup()
	{
			global $indeed_db;
			$uid = empty($_POST['uid']) ? 0 : esc_sql($_POST['uid']);
			if (empty($uid)){
					die;
			}
			$toEmail = $indeed_db->get_user_col_value($uid, 'user_email');
			if (empty($toEmail)){
					die;
			}
			$fromEmail = '';
			$fromEmail = get_option('uap_notifications_from_email_addr');
			if (empty($fromEmail)){
					$fromEmail = get_option('admin_email');
			}
			$view = new \Indeed\Uap\IndeedView();
			$view->setTemplate(UAP_PATH . 'admin/views/send_email_popup.php');
			$view->setContentData([
															'toEmail' 		=> $toEmail,
															'fromEmail' 	=> $fromEmail,
															'fullName'		=> $indeed_db->getUserFullName($uid),
															'website'			=> get_option('blogname')
			], true);
			echo $view->getOutput();
			die;
	}

	public function uap_admin_do_send_email()
	{
			$to = empty($_POST['to']) ? '' : esc_sql($_POST['to']);
			$from = empty($_POST['from']) ? '' : esc_sql($_POST['from']);
			$subject = empty($_POST['subject']) ? '' : esc_sql($_POST['subject']);
			$message = empty($_POST['message']) ? '' :  stripslashes(htmlspecialchars_decode(uap_format_str_like_wp($_POST['message'])));
			$headers = [];

			if (empty($to) || empty($from) || empty($subject) || empty($message)){
					die;
			}

			$from_name = get_option('uap_notifications_from_name');
			$from_name = stripslashes($from_name);
			if (!empty($from) && !empty($from_name)){
				$headers[] = "From: $from_name <$from>";
			}
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			$sent = wp_mail($to, $subject, $message, $headers);
			echo $sent;
			die;
	}

}
