<?php
if (!class_exists('Affiliate_Account_Page')){
	class Affiliate_Account_Page{
		private $uid;
		private $affiliate_id;
		private $general_settings;
		private $current_url;
		private $account_page_base_url;
		private $account_page_settings;
		private $public_extra_settings;

		public function __construct($uid, $affiliate_id){
			/*
			 * @param int
			 * @return none
			 */
			global $indeed_db;
			$this->uid = $uid;
			$this->affiliate_id = $affiliate_id;
			$this->general_settings = $indeed_db->return_settings_from_wp_option('general-settings');
			$this->public_extra_settings = $indeed_db->return_settings_from_wp_option('general-public_workflow');
			$this->account_page_settings = $indeed_db->return_settings_from_wp_option('account_page');
			$this->current_url = UAP_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; /// $_SERVER['SERVER_NAME']

			/// CREATE BASE URL
			$this->account_page_base_url = $this->current_url;
			$remove_get_attr = array('uap_aff_subtab', 'uap_register', 'uap_list_item', 'udf', 'udu', 'u_sts', 'add_new');
			foreach ($remove_get_attr as $key){
				if (!empty($_GET[$key])){
					$this->account_page_base_url = remove_query_arg($key, $this->account_page_base_url);
				}
			}
		}

		public function output(){
			/*
			 * @param none
			 * @return string
			 */
			$tab = (empty($_GET['uap_aff_subtab'])) ? 'overview' : $_GET['uap_aff_subtab'];

			/// HEAD
			$this->head();

			/// CONTENT
			if (empty($tab)){
				$this->run_overview();
			} else {
					switch ($tab){
						case 'affiliate_link':
							$this->run_affiliate_link();
							break;
						case 'banners':
							$this->run_banners();
							break;
						case 'change_pass':
							$this->run_change_password();
							break;
						case 'edit_account':
							$this->run_edit_account();
							break;
						case 'campaigns':
							$this->run_campaigns();
							break;
						case 'visits':
							$this->run_visits();
							break;
						case 'campaign_reports':
							$this->run_campaign_reports();
							break;
						case 'referrals':
							$this->run_referrals();
							break;
						case 'reports':
							$this->run_reports();
							break;
						case 'payments':
							$this->run_payments();
							break;
						case 'overview':
							$this->run_overview();
							break;
						case 'payments_settings':
							$this->run_payment_settings();
							break;
						case 'referrals_history':
							$this->run_referrals_history();
							break;
						case 'help':
							$this->run_help();
							break;
						case 'coupons':
							$this->print_coupons();
							break;
						case 'custom_affiliate_slug':
							$this->print_custom_affiliate_slug();
							break;
						case 'mlm':
							$this->print_mlm_page();
							break;
						case 'wallet':
							$this->print_wallet();
							break;
						case 'referral_notifications':
							$this->print_referral_notifications();
							break;
						case 'source_details':
							$this->print_source_details();
							break;
						case 'pushover_notifications':
							$this->print_pushover_notifications();
							break;
						case 'simple_links':
							$this->run_simple_links();
							break;
						case 'landing_pages':
							$this->landing_pages();
							break;
						default:
							$this->run_custom_tab($tab);
							break;
					}
			}


			/// FOOTER
			$this->footer();
		}

		private function create_link_for_aff($link='', $slug=''){
			/*
			 * @param string, boolean
			 * @return string
			 */
			if ($link){
				$value = $this->affiliate_id;
				$param = 'ref';

				if (!empty($this->general_settings['uap_referral_variable'])){
					$param = $this->general_settings['uap_referral_variable'];
				}
				if ($slug){
					$value = $slug;
				} else if (!empty($this->general_settings['uap_default_ref_format']) && $this->general_settings['uap_default_ref_format']=='username'){
					$user_info = get_userdata($this->uid);
					if (!empty($user_info->user_login)){
						//$value = $user_info->user_login;
						$value = urlencode($user_info->user_login);
					}
				}
				$link = uap_create_affiliate_link($link, $param, $value);
			}
			return $link;
		}

		private function head(){
			/*
			 * @param none
			 * @return none (print html)
			 */
			global $indeed_db;

			$exclude_tabs = array();
			if (!$indeed_db->is_magic_feat_enable('coupons')){
				$exclude_tabs[] = 'coupons';
			}
			if (!$indeed_db->is_magic_feat_enable('custom_affiliate_slug')){
				$exclude_tabs[] = 'custom_affiliate_slug';
			}
			if (!$indeed_db->is_magic_feat_enable('mlm')){
				$exclude_tabs[] = 'mlm';
			}
			if (!$indeed_db->is_magic_feat_enable('pushover')){
				$exclude_tabs[] = 'pushover';
			}
			if (!$indeed_db->is_magic_feat_enable('wallet')){
				$exclude_tabs[] = 'wallet';
			}
			if (!$indeed_db->is_magic_feat_enable('referral_notifications') && !$indeed_db->is_magic_feat_enable('periodically_reports')){
				$exclude_tabs[] = 'referral_notifications';
			}
			if (!$indeed_db->is_magic_feat_enable('simple_links')){
				$exclude_tabs[] = 'simple_links';
			}
			if (!$indeed_db->is_magic_feat_enable('landing_pages')){
				$exclude_tabs[] = 'landing_pages';
			}

			$custom_menu = $indeed_db->account_page_get_custom_menu_items();

			$data['show_tab_list'] = $this->account_page_settings['uap_ap_tabs'];
			if ($data['show_tab_list']){
				$data['show_tab_list'] = explode(',', $data['show_tab_list']);
				foreach ($data['show_tab_list'] as $k=>$v){
					$data['urls'][$v] = add_query_arg('uap_aff_subtab', $v, $this->account_page_base_url);
				}
				$data['urls']['logout'] = add_query_arg('uapaction', 'logout', $this->account_page_base_url );//modify logout link
			} else {
				$data['show_tab_list'] = array();
			}

			$data['uap_account_page_custom_css'] = stripslashes($this->account_page_settings['uap_account_page_custom_css']);

			$data['message'] = uap_replace_constants($this->account_page_settings['uap_ap_welcome_msg'], $this->uid);
			$data['message'] = stripslashes(uap_format_str_like_wp($data['message']));
			if ($this->account_page_settings['uap_ap_edit_show_avatar']){
				$avatar = get_user_meta($this->uid, 'uap_avatar', true);
				if (strpos($avatar, "http")===0){
					$avatar_url = $avatar;
				} else {
					$avatar_url = wp_get_attachment_url($avatar);
				}
				$data['avatar'] = ($avatar_url) ? $avatar_url : UAP_URL . 'assets/images/no-avatar.png';
			}
			if ($this->account_page_settings['uap_ap_edit_show_rank']) $data['top-rank'] = 1;
			if ($this->account_page_settings['uap_ap_edit_show_earnings']) $data['top-earning'] = 1;
			if ($this->account_page_settings['uap_ap_edit_show_referrals']) $data['top-referrals'] = 1;
			if ($this->account_page_settings['uap_ap_edit_show_achievement']) $data['top-achievement'] = 1;
			if ($this->account_page_settings['uap_ap_edit_background']) $data['top-background'] = 1;
			if ($this->account_page_settings['uap_ap_edit_background_image']) $data['top-background-image'] = $this->account_page_settings['uap_ap_edit_background_image'];
			$data['uap_ap_edit_show_metrics'] = $this->account_page_settings['uap_ap_edit_show_metrics'];
			if ($data['uap_ap_edit_show_metrics']){
					$data['metrics'] = '';
					$data['metrics'][3] = $indeed_db->getEPCdata('3months', $this->affiliate_id);
					$data['metrics'][7] = $indeed_db->getEPCdata('7days', $this->affiliate_id);
			}


			$data['uap_ap_top_theme'] = (empty($this->account_page_settings['uap_ap_top_theme'])) ? 'uap-ap-top-theme-1' : $this->account_page_settings['uap_ap_top_theme'];
			$data['uap_ap_theme'] = (empty($this->account_page_settings['uap_ap_theme'])) ? 'uap-ap-theme-1' : $this->account_page_settings['uap_ap_theme'];
			$data['selected_tab'] = (empty($_GET['uap_aff_subtab'])) ? '' : $_GET['uap_aff_subtab'];

			$data['stats'] = $indeed_db->get_stats_for_payments($this->affiliate_id);
			$data['stats']['currency'] = get_option('uap_currency');

			 $current_rank_id = $indeed_db->get_affiliate_rank($this->affiliate_id);
			if(!empty($current_rank_id) && $current_rank_id>0){
				$current_rank = $indeed_db->get_rank($current_rank_id);
				$data['rank'] = $current_rank;
			}

			$data['achieved'] = $indeed_db->get_next_rank_achieved_percetage($this->affiliate_id);

			$order = get_option('uap_account_page_menu_order');
			$data['available_tabs'] = $indeed_db->account_page_get_menu();
			$data['uap_account_page_personal_header'] = get_user_meta($this->uid, 'uap_account_page_personal_header', true);

			$fullPath = UAP_PATH . 'public/views/account_page-header.php';
			$searchFilename = 'account_page-header.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function print_top_messages(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$payment_settings = $indeed_db->get_affiliate_payment_type($this->uid);

			if (empty($payment_settings['is_active']) && empty($this->public_extra_settings['uap_hide_payments_warnings'])){
				$data['warning_messages'][] = __('Payment Service Settings are not set properly', 'uap');
			}

			$fullPath = UAP_PATH . 'public/views/account_page-top_messages.php';
			$searchFilename = 'account_page-top_messages.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function footer(){
			global $indeed_db;

			$data['footer_content'] = uap_replace_constants($this->account_page_settings['uap_ap_footer_msg'], $this->uid);
			$data['footer_content'] = stripslashes(uap_format_str_like_wp($data['footer_content']));

			$fullPath = UAP_PATH . 'public/views/account_page-footer.php';
			$searchFilename = 'account_page-footer.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_affiliate_link(){
			/*
			 * @param none
			 * @return none (print html)
			 */
			global $indeed_db;
			$do_qr = $indeed_db->is_magic_feat_enable('qr_code');

			$link_for_aff = get_option('uap_referral_custom_base_link');
			if (empty($link_for_aff)){
					$link_for_aff = get_home_url();
			}
			$data['home_url'] = $this->create_link_for_aff($link_for_aff);
			if ($do_qr){
				$data['qr_home'] = uap_generate_qr_code($data['home_url'], $this->affiliate_id . '_home_url');
			}
			$custom_affiliate_slug = $indeed_db->is_magic_feat_enable('custom_affiliate_slug');
			if ($custom_affiliate_slug){
				$the_slug = $indeed_db->get_custom_slug_for_uid($this->uid);
				if (!empty($the_slug)){
					$data['home_url_slug'] = $this->create_link_for_aff(get_home_url(), $the_slug);
					if ($do_qr){
						$data['qr_custom_slug'] = uap_generate_qr_code($data['home_url_slug'], $this->affiliate_id . '_home_url_slug');
					}
				}
			}
			$custom_affiliate_slug = $indeed_db->is_magic_feat_enable('custom_affiliate_slug');
			$friendly_links = $indeed_db->is_magic_feat_enable('friendly_links');

			$data['affiliate_id'] = $this->affiliate_id;
			$data['campaigns'] = $indeed_db->get_campaigns_for_affiliate_id($this->affiliate_id);
			$data['campaigns'][-1] = '...';
			ksort($data['campaigns']);
			$data['social_links'] = '';
			if (uap_is_social_share_intalled_and_active() && get_option('uap_social_share_enable')){
				$shortcode = get_option('uap_social_share_shortcode');
				if ($shortcode){
					$shortcode = stripslashes($shortcode);
					$shortcode = str_replace(']', '', $shortcode);
					$shortcode .= " is_affiliates=1"; ///just for safe
					$shortcode .= " custom_description='" . get_option('uap_social_share_message') . "'";
					$shortcode .= " custom_url='" . $data['home_url'] ."']";
					$data['social_links'] = do_shortcode($shortcode);
				}
			}
			if (get_option('uap_default_ref_format')=='username' && $this->uid){
				$user_info = get_userdata($this->uid);
				$data['print_username'] = (empty($user_info->user_login)) ? '' : $user_info->user_login;
			}
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_affiliate_link_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_affiliate_link_title'];

			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-generate_links.php';
			$searchFilename = 'account_page-generate_links.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_overview(){
			/*
			 * @param none
			 * @return none (print html)
			 */

			global $indeed_db;

			$post_overview = get_user_meta($this->uid, 'uap_overview_post', true);
			if ($post_overview && $post_overview!=-1){
				//print the post for user
				$post = get_post($post_overview);
				$data['message'] = $post->post_content;
			} else {
				//predifined message
				$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_overview_content'], $this->uid);
				$data['message'] = uap_format_str_like_wp($data['message']);
				$data['message'] = uap_correct_text($data['message']);
			}
			$data['title'] = $this->account_page_settings['uap_tab_overview_title'];

			$data['stats'] = $indeed_db->get_stats_for_payments($this->affiliate_id);
			$data['stats']['currency'] = get_option('uap_currency');

			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-overview.php';
			$searchFilename = 'account_page-overview.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_banners(){
			/*
			 * @param none
		 	 * @return none (print html)
			 */
			global $indeed_db;
			$data['listing_items'] = $indeed_db->get_banners();
			if ($data['listing_items'] && is_array($data['listing_items'])){
				$param = 'ref';
				$value = $this->affiliate_id;
				if (!empty($this->general_settings['uap_referral_variable'])){
					$param = $this->general_settings['uap_referral_variable'];
				}
				if (!empty($this->general_settings['uap_default_ref_format']) && $this->general_settings['uap_default_ref_format']=='username'){
					$user_info = get_userdata($this->uid);
					if (!empty($user_info->user_login)){
						$value = $user_info->user_login;
					}
				}

				$prettify = $indeed_db->is_magic_feat_enable('friendly_links');
				$pixelTracking = $indeed_db->is_magic_feat_enable('cpm_commission');
				$data['pixel_tracking'] = '';
				if ($pixelTracking){
						$pixelTrackingUrl = get_home_url() . '?uap_act=tracking&type=cpm&affiliate=' . $this->affiliate_id;
						$data['pixel_tracking'] = "<img src='{$pixelTrackingUrl}' style='display: none; width: 1px; height: 1px; border: none;'/>";
				}

				foreach ($data['listing_items'] as $k => $arr){
					if (isset($data['listing_items'][$k]->url)){
						$data['listing_items'][$k]->url =  uap_create_affiliate_link($data['listing_items'][$k]->url, $param, $value, '', '', $prettify);
					}
				}
			}
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_banners_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_banners_title'];

			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-banners.php';
			$searchFilename = 'account_page-banners.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_change_password(){
			/*
			 * @param none
			 * @return string
			 */
			if (!empty($_POST['update_pass'])){
				if (empty($_POST['old_pass']) || empty($_POST['pass1']) || empty($_POST['pass2'])){
					$data['error'] = __("Please complete all fields!", 'uap');
				} else {
					$user = get_user_by( 'id', $this->uid );
					if ( $user && wp_check_password( $_POST['old_pass'], $user->data->user_pass,  $this->uid) ){
						if ($_POST['pass1']==$_POST['pass2']){
							wp_set_password($_POST['pass1'],  $this->uid);
							$data['success'] = __("Your have changed your Password!", 'uap');
						} else {
							$data['error'] = __("Passwords not match!", 'uap');
						}
					} else {
						$data['error'] = __("Old Password is not correct!", 'uap');
					}
				}
			}
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_change_pass_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_change_pass_title'];

			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-change_password.php';
			$searchFilename = 'account_page-change_password.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_edit_account(){
			/*
			 * @param none
			 * @return string
			 */
			require_once UAP_PATH . 'classes/Uap_Add_Edit_Affiliate.class.php';

			$args = array(
						'user_id' => $this->uid,
						'type' => 'edit',
						'tos' => FALSE,
						'captcha' => FALSE,
						'action' => '',
						'is_public' => TRUE,
			);
			$obj = new Uap_Add_Edit_Affiliate($args);
			$data = $obj->form();
			$data['template'] = get_option('uap_register_template');
			$data['action'] = '';
			ob_start();
			require_once UAP_PATH . 'public/views/register.php';
			$data['output'] = ob_get_contents();
			ob_end_clean();
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_edit_account_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_edit_account_title'];

			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-edit_account.php';
			$searchFilename = 'account_page-edit_account.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_campaigns(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			if (!empty($_POST['uap_delete_campaign'])){
				$indeed_db->delete_campaign($this->affiliate_id, $_POST['uap_delete_campaign']);
			} else if (!empty($_POST['campaign_name'])){
				$indeed_db->add_empty_campaign($this->affiliate_id, $_POST['campaign_name']);
			}
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_campaigns_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_campaigns_title'];

			$data['campaigns'] = $indeed_db->get_campaigns_for_affiliate_id($this->affiliate_id);

			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-campaigns.php';
			$searchFilename = 'account_page-campaigns.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_visits(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$limit = 10;
			$url = add_query_arg('uap_aff_subtab', 'visits', $this->account_page_base_url);
			$where = array();
			if (!empty($_REQUEST['udf']) && !empty($_REQUEST['udu'])){
				$where[] = "v.visit_date>'" . $_REQUEST['udf'] . "' ";
				$where[] = "v.visit_date<'" . $_REQUEST['udu'] . "' ";
				$url .= '&udf=' . $_REQUEST['udf'] . '&udu=' . $_REQUEST['udu'];
			}
			$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
			$where[] = "v.affiliate_id=" . $this->affiliate_id;

			$total_items = (int)$indeed_db->get_visits(-1, -1, TRUE, '', '', $where);

			if ($current_page>1){
				$offset = ( $current_page - 1 ) * $limit;
			} else {
				$offset = 0;
			}
			if ($offset + $limit>$total_items){
				$limit = $total_items - $offset;
			}
			$data['items'] = $indeed_db->get_visits($limit, $offset, FALSE, 'visit_date', 'DESC', $where);

			$limit = 10;
			require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
			$pagination = new Indeed_Pagination(array(
														'base_url' => $url,
														'param_name' => 'uap_list_item',
														'total_items' => $total_items,
														'items_per_page' => $limit,
														'current_page' => $current_page,
			));


			$data['pagination'] = $pagination->output();
			$data['filter'] = uap_return_date_filter($url);
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_visits_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_visits_title'];
			$data['stats'] = $indeed_db->get_stats_for_reports('', $this->affiliate_id);

			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-list_visits.php';
			$searchFilename = 'account_page-list_visits.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_campaign_reports(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$limit = 10;
			$url = add_query_arg('uap_aff_subtab', 'campaign_reports', $this->account_page_base_url);
			$where = array();
			$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
			$total_items = (int)$indeed_db->get_campaigns_reports_for_affiliate_id($this->affiliate_id, 0, 0, TRUE, '', '', $where);
			if ($current_page>1){
				$offset = ( $current_page - 1 ) * $limit;
			} else {
				$offset = 0;
			}
			if ($offset + $limit>$total_items){
				$limit = $total_items - $offset;
			}
			$data['items'] = $indeed_db->get_campaigns_reports_for_affiliate_id($this->affiliate_id, $limit, $offset, FALSE, '', '', $where);

			require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
			$limit = 10;
			$pagination = new Indeed_Pagination(array(
					'base_url' => $url,
					'param_name' => 'uap_list_item',
					'total_items' => $total_items,
					'items_per_page' => $limit,
					'current_page' => $current_page,
			));
			$data['pagination'] = $pagination->output();
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_campaign_reports_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_campaign_reports_title'];

			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-campaign_reports.php';
			$searchFilename = 'account_page-campaign_reports.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_referrals(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$limit = 10;
			$url = add_query_arg('uap_aff_subtab', 'referrals', $this->account_page_base_url);
			$where = array();
			if (!empty($_REQUEST['udf']) && !empty($_REQUEST['udu'])){
				$where[] = " r.date>'" . $_REQUEST['udf'] . "' ";
				$where[] = " r.date<'" . $_REQUEST['udu'] . "' ";
				$url .= '&udf=' . $_REQUEST['udf'] . '&udu=' . $_REQUEST['udu'];
			}
			if (isset($_REQUEST['u_sts']) && $_REQUEST['u_sts']!=-1){
				$where[] = " r.status='" . $_REQUEST['u_sts'] . "' ";
				$url .= '&u_sts=' . $_REQUEST['u_sts'];
			}
			$where[] = "r.affiliate_id=" . $this->affiliate_id;
			$where[] = "r.payment=0";
			$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
			$total_items = (int)$indeed_db->get_referrals(-1, -1, TRUE, '', '', $where);
			if ($current_page>1){
				$offset = ( $current_page - 1 ) * $limit;
			} else {
				$offset = 0;
			}
			if ($offset + $limit>$total_items){
				$limit = $total_items - $offset;
			}

			$data['items'] = $indeed_db->get_referrals($limit, $offset, FALSE, '', '', $where);

			require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
			$limit = 10;
			$pagination = new Indeed_Pagination(array(
														'base_url' => $url,
														'param_name' => 'uap_list_item',
														'total_items' => $total_items,
														'items_per_page' => $limit,
														'current_page' => $current_page,
			));
			$data['pagination'] = $pagination->output();
			$filter_url = add_query_arg('uap_aff_subtab', 'referrals', $this->account_page_base_url);
			$data['filter'] = uap_return_date_filter($filter_url,
														array(	0 => __('Refuse', 'uap'),
					 											1 => __('Unverified', 'uap'),
					 											2 => __('Verified', 'uap'),
														));
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_referrals_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_referrals_title'];
			$data['stats'] = $indeed_db->get_stats_for_referrals('', $this->affiliate_id);
			$data['currency'] = get_option('uap_currency');

			$data['print_source_details'] = $indeed_db->is_magic_feat_enable('source_details');
			$data['source_details_url'] = add_query_arg('uap_aff_subtab', 'source_details', $this->account_page_base_url);

			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-referrals.php';
			$searchFilename = 'account_page-referrals.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_payments(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$limit = 10;
			$url = add_query_arg('uap_aff_subtab', 'payments', $this->account_page_base_url);
			$where = array();
			if (!empty($_REQUEST['udf']) && !empty($_REQUEST['udu'])){
				$where[] = " create_date>'" . $_REQUEST['udf'] . "' ";
				$where[] = " create_date<'" . $_REQUEST['udu'] . "' ";
				$url .= '&udf=' . $_REQUEST['udf'] . '&udu=' . $_REQUEST['udu'];
			}
			if (isset($_REQUEST['u_sts']) && $_REQUEST['u_sts']!=-1){
				$where[] = " status='" . $_REQUEST['u_sts'] . "' ";
				$url .= '&u_sts=' . $_REQUEST['u_sts'];
			}

			$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
			$total_items = (int)$indeed_db->get_transactions($this->affiliate_id, 0, 0, TRUE, '', '', $where);
			if ($current_page>1){
				$offset = ( $current_page - 1 ) * $limit;
			} else {
				$offset = 0;
			}
			if ($offset + $limit>$total_items){
				$limit = $total_items - $offset;
			}
			require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
			$limit = 10;
			$pagination = new Indeed_Pagination(array(
					'base_url' => $url,
					'param_name' => 'uap_list_item',
					'total_items' => $total_items,
					'items_per_page' => $limit,
					'current_page' => $current_page,
			));
			$data['pagination'] = $pagination->output();
			$data['listing_items'] = $indeed_db->get_transactions($this->affiliate_id, $limit, $offset, FALSE, '', '', $where);
			$filter_url = add_query_arg('uap_aff_subtab', 'payments', $this->account_page_base_url);
			$data['filter'] = uap_return_date_filter($filter_url,
														array(
																0 => __('Fail', 'uap'),
																1 => __('Pending', 'uap'),
					 											2 => __('Complete', 'uap'),
														)
			);
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_payments_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_payments_title'];
			$data['stats'] = $indeed_db->get_stats_for_payments($this->affiliate_id);
			$data['currency'] = get_option('uap_currency');

			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-payments.php';
			$searchFilename = 'account_page-payments.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_reports(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$data = $indeed_db->get_stats_for_reports('', $this->affiliate_id);
			$data['achivements'] = $indeed_db->get_last_rank_achievements(-1, '', $this->affiliate_id);
			$data['currency'] = get_option('uap_currency');
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_reports_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_reports_title'];
			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-reports.php';
			$searchFilename = 'account_page-reports.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_payment_settings(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			if (!empty($_POST['save_settings'])){
				if ($_POST['uap_affiliate_payment_type']=='stripe_v2'){
					/// STRIPE V2
					$indeed_db->save_stripe_v2_meta_user_data($this->uid, $_POST); /// save meta
					require_once UAP_PATH . 'classes/Uap_Stripe_V2.class.php';
					$stripe_v2 = new Uap_Stripe_V2();
					if (!empty($_POST['stripe_v2_meta_data']['stripe_v2_tos'])){
						$stripe_id = $stripe_v2->register_user($this->uid); /// register user to stripe
					}
					if (empty($stripe_id)){
						$data['errors'] = $stripe_v2->getErrorMessage();
						if (empty($data['errors'])){
								$data['errors'] = __('You must accept the \'Terms of service\' before saving the settings.', 'uap');
						}
					}
				}
				$indeed_db->save_affiliate_payment_settings($this->uid, $_POST);
			}
			$data['metas'] = $indeed_db->get_affiliate_payment_settings($this->uid);
			$data['stripe_v2'] = $indeed_db->get_affiliate_stripe_v2_payment_settings($this->uid);
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_payments_settings_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_payments_settings_title'];
			$data['payment_types'] = $indeed_db->get_payment_types_available();
			$data['stripe_card_types'] = array(
												'individual' => __('Individual', 'uap'),
												'corporation' => __('Corporation', 'uap'),
			);
			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-payment_settings.php';
			$searchFilename = 'account_page-payment_settings.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_referrals_history(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$limit = 10;
			$url = add_query_arg('uap_aff_subtab', 'referrals_history', $this->account_page_base_url);//// referrals
			$where = array();
			if (!empty($_REQUEST['udf']) && !empty($_REQUEST['udu'])){
				$where[] = " r.date>'" . $_REQUEST['udf'] . "' ";
				$where[] = " r.date<'" . $_REQUEST['udu'] . "' ";
				$url .= '&udf=' . $_REQUEST['udf'] . '&udu=' . $_REQUEST['udu'];
			}
			if (isset($_REQUEST['u_sts']) && $_REQUEST['u_sts']!=-1){
				$where[] = " r.status='" . $_REQUEST['u_sts'] . "' ";
				$url .= '&u_sts=' . $_REQUEST['u_sts'];
			}
			$where[] = "r.affiliate_id=" . $this->affiliate_id;

			$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
			$total_items = (int)$indeed_db->get_referrals(-1, -1, TRUE, '', '', $where);
			if ($current_page>1){
				$offset = ( $current_page - 1 ) * $limit;
			} else {
				$offset = 0;
			}
			if ($offset + $limit>$total_items){
				$limit = $total_items - $offset;
			}

			$data['items'] = $indeed_db->get_referrals($limit, $offset, FALSE, '', '', $where);

			require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
			$limit = 10;
			$pagination = new Indeed_Pagination(array(
														'base_url' => $url,
														'param_name' => 'uap_list_item',
														'total_items' => $total_items,
														'items_per_page' => $limit,
														'current_page' => $current_page,
			));
			$data['pagination'] = $pagination->output();
			$filter_url = add_query_arg('uap_aff_subtab', 'referrals_history', $this->account_page_base_url);/// referrals
			$data['filter'] = uap_return_date_filter($filter_url);

			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_referrals_history_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_referrals_history_title'];
			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-referrals_history.php';
			$searchFilename = 'account_page-referrals_history.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function run_help(){
			/*
			 * @param none
			 * @return string
			 */
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_help_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_help_title'];
			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-help.php';
			$searchFilename = 'account_page-help.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function print_coupons(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_coupons_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_coupons_title'];
			$this->print_top_messages();
			$data['codes'] = $indeed_db->get_coupons_for_affiliate($this->affiliate_id);
			$data['currency'] = get_option('uap_currency');

			$fullPath = UAP_PATH . 'public/views/account_page-coupons.php';
			$searchFilename = 'account_page-coupons.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function print_custom_affiliate_slug(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_custom_affiliate_slug_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_custom_affiliate_slug_title'];
			$this->print_top_messages();
			if (isset($_POST['uap_affiliate_custom_slug'])){
				$saved = $indeed_db->save_custom_slug_for_uid($this->uid, $_POST['uap_affiliate_custom_slug']);
			}
			$data['uap_affiliate_custom_slug'] = $indeed_db->get_custom_slug_for_uid($this->uid);

			$fullPath = UAP_PATH . 'public/views/account_page-custom_affiliate_slug.php';
			$searchFilename = 'account_page-custom_affiliate_slug.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function landing_pages()
		{
				global $wpdb, $indeed_db;
				$data['pages'] = $indeed_db->getLandingPagesForAffiliate($this->affiliate_id);
				$data['content'] = (isset($this->account_page_settings['uap_tab_landing_pages_content'])) ? uap_replace_constants($this->account_page_settings['uap_tab_landing_pages_content'], $this->uid) : '';
				$data['title'] = (isset($this->account_page_settings['uap_tab_landing_pages_title'])) ? uap_replace_constants($this->account_page_settings['uap_tab_landing_pages_title'], $this->uid) : '';
				$data['content'] = $this->clean_text($data['content']);
				$data['title'] = $this->clean_text($data['title']);

				$fullPath = UAP_PATH . 'public/views/account_page-landing_pages.php';
				$searchFilename = 'account_page-landing_pages.php';
				$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

				require_once $template;
		}

		private function print_mlm_page(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_mlm_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_mlm_title'];
			$this->print_top_messages();
			require_once UAP_PATH . 'classes/MLM_Get_All_Children.class.php';
			$children_object = new MLM_Get_All_Children($this->affiliate_id);
			$data['items'] = $children_object->get_results();
			$data['uap_affiliate_custom_slug'] = $indeed_db->get_custom_slug_for_uid($this->uid);
			$data['username'] = $indeed_db->get_username_by_wpuid($this->uid);
			$data['parent'] = $indeed_db->mlm_get_parent($this->affiliate_id);
			if (empty($data['parent'])){
					$data['parent'] = '';
			} else {
					$parentUid = $indeed_db->get_uid_by_affiliate_id($data['parent']);
					$data['parent'] = $indeed_db->get_username_by_wpuid($parentUid);
			}

			$fullPath = UAP_PATH . 'public/views/account_page-mlm.php';
			$searchFilename = 'account_page-mlm.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function print_wallet(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$this->print_top_messages();
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_wallet_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_wallet_title'];
			$data['currency'] = get_option('uap_currency');

			$settings = $indeed_db->return_settings_from_wp_option('wallet');
			if (empty($_GET['add_new'])){
				//// LISTING
				$hash_check = get_user_meta($this->uid, 'uap_wallet_hash', TRUE);
				if (isset($_POST['save']) && !empty($_POST['uapcheck']) && !empty($hash_check)
							&& $hash_check==$_POST['uapcheck'] && !empty($_POST['referrals'])){
					$referral_list = explode(',', $_POST['referrals']);
					$indeed_db->create_wallet_item($_POST['service_type'], $referral_list, $this->affiliate_id);
					update_user_meta($this->uid, 'uap_wallet_hash', '');
				}
				$data['stats'] = $indeed_db->get_stats_for_payments($this->affiliate_id, @$settings['uap_wallet_exclude_sources']);
				$data['items'] = $indeed_db->get_all_wallet_items_for_affiliate($this->affiliate_id);
				$data['stats']['wallet'] = 0;
				if ($data['items']){
					foreach ($data['items'] as $item){
						$data['stats']['wallet'] += $item['amount'];
					}
				}
				$base_url = add_query_arg('uap_aff_subtab', 'wallet', $this->account_page_base_url);
				$data['add_new'] = add_query_arg('add_new', 'true', $base_url);
				require_once UAP_PATH . 'public/views/wallet.php';
			} else {
				/// ADD NEW
				$data['form_action'] = add_query_arg('uap_aff_subtab', 'wallet', $this->account_page_base_url);
				$data['services'] = uap_get_active_services();
				if (!empty($settings['uap_wallet_exclude_sources']) && $settings['uap_wallet_exclude_sources']!=-1){
						$excluded_sevices = explode(',', $settings['uap_wallet_exclude_sources']);
						foreach ($excluded_sevices as $service_type){
								if (isset($data['services'][$service_type])) unset($data['services'][$service_type]);
						}
				}
				$where[] = "r.payment=0 ";
				$where[] = "r.status=2 ";
				$where[] = "r.affiliate_id=" . $this->affiliate_id;

				//if (!empty($settings['uap_wallet_exclude_sources'])){
				//		$where [] = "r.source NOT IN ('{$settings['uap_wallet_exclude_sources']}')";
				//}

				$data['referrals'] = $indeed_db->get_referrals(-1, -1, FALSE, '', '', $where);
				$data['hash'] = time() . $this->uid;
				update_user_meta($this->uid, 'uap_wallet_hash', $data['hash']);

				$fullPath = UAP_PATH . 'public/views/wallet-add_new.php';
				$searchFilename = 'wallet-add_new.php';
				$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

				require_once $template;
			}

		}


		private function print_referral_notifications(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			if (!$indeed_db->is_magic_feat_enable('referral_notifications') && !$indeed_db->is_magic_feat_enable('periodically_reports')){
				return;///out
			}
			if (!empty($_POST['save_settings'])){
				$indeed_db->save_meta_user_options('user_notifications', $this->uid, $_POST);

				/// REPORTS
				$data['report_settings'] = $indeed_db->affiliate_get_report_settings($this->affiliate_id);
				if ($data['report_settings']['period']!=$_POST['period']){
					/// a change was made so we must do update
					if ($_POST['period']>0){
						/// enable the reports
						$array['affiliate_id'] = $this->affiliate_id;
						$array['email'] = $indeed_db->get_email_by_uid($this->uid);
						$array['period'] = $_POST['period'];
						$indeed_db->save_affiliate_report_settings($this->affiliate_id, $array);
					} else {
						$indeed_db->delete_affiliate_report_settings($this->affiliate_id);
					}
				}
			}
			$data['message'] = uap_replace_constants($this->account_page_settings['uap_tab_referral_notifications_content'], $this->uid);
			$data['message'] = uap_format_str_like_wp($data['message']);
			$data['message'] = uap_correct_text($data['message']);
			$data['title'] = $this->account_page_settings['uap_tab_referral_notifications_title'];
			$data['metas'] = $indeed_db->uap_get_meta_user_options('user_notifications', $this->uid);
			$data['module_settings_notf'] = $indeed_db->return_settings_from_wp_option('referral_notifications');
			$data['module_settings_reports'] = $indeed_db->return_settings_from_wp_option('periodically_reports');
			$data['report_settings'] = $indeed_db->affiliate_get_report_settings($this->affiliate_id);

			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-referral_notifications.php';
			$searchFilename = 'account_page-referral_notifications.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}

		private function print_source_details(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			$referral_id = (empty($_GET['reference'])) ? 0 : $_GET['reference'];
			global $indeed_db;
			$data['fields_data'] = $indeed_db->get_source_details_for_reference($referral_id);
			$data['all_fields'] = array(
												'user_login' => 'Username',
												'first_name' => __('First Name', 'uap'),
												'last_name' => __('Last Name', 'uap'),
												'phone' => __('Phone', 'uap'),
												'email' => __('E-mail', 'uap'),
												'order_date' => __('Order Date', 'uap'),
												'order_amount' => __('Order Amount', 'uap'),
												'shipping_address' => __('Shipping Address', 'uap'),
												'billing_address' => __('Billing Address', 'uap'),
												'cart_items' => __('Cart Items', 'uap'),
			);

			$fullPath = UAP_PATH . 'public/views/account_page-source_details.php';
			$searchFilename = 'account_page-source_details.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}


		/*
		 * @param none
		 * @return none
		 */
		private function print_pushover_notifications(){
			global $current_user;
			$uid = empty($current_user->ID) ? 0 : $current_user->ID;
			if (!empty($_POST['uap_pushover_token'])){
				update_user_meta($uid, 'uap_pushover_token', $_POST['uap_pushover_token']);
			}
			$data['uap_pushover_token'] = get_user_meta($uid, 'uap_pushover_token', TRUE);
			$data['content'] = (isset($this->account_page_settings['uap_tab_pushover_notifications_content'])) ? uap_replace_constants($this->account_page_settings['uap_tab_pushover_notifications_content'], $this->uid) : '';
			$data['title'] = (isset($this->account_page_settings['uap_tab_pushover_notifications_title'])) ? uap_replace_constants($this->account_page_settings['uap_tab_pushover_notifications_title'], $this->uid) : '';

			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-pushover_notifications.php';
			$searchFilename = 'account_page-pushover_notifications.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}


		/*
		 * @param none
		 * @return string
		 */
		private function run_simple_links(){
			global $indeed_db;
			if (!empty($_POST['save'])){
				$_POST['affiliate_id'] = $this->affiliate_id;
				$inserted = $indeed_db->simple_links_save_link($_POST, 0);///set status as 0 (pending)
				if ($inserted==1){
					$data['err'] = '';
				} else if ($inserted==0){
					$data['err'] = __('You have an error.', 'uap');
				} else if ($inserted==-1){
					$data['err'] = __('This URL already was registered.', 'uap');
				}
			} else if (!empty($_GET['del'])){
				$indeed_db->simple_links_delete_link($_GET['del']);
			}
			$data['url'] = remove_query_arg('del', $this->current_url);
			$data['items'] = $indeed_db->simple_links_get_items_for_affiliate($this->affiliate_id);
			$data['content'] = (isset($this->account_page_settings['uap_tab_simple_links_content'])) ? uap_replace_constants($this->account_page_settings['uap_tab_simple_links_content'], $this->uid) : '';
			$data['title'] = (isset($this->account_page_settings['uap_tab_simple_links_title'])) ? uap_replace_constants($this->account_page_settings['uap_tab_simple_links_title'], $this->uid) : '';
			$this->print_top_messages();

			$fullPath = UAP_PATH . 'public/views/account_page-simple_links.php';
			$searchFilename = 'account_page-simple_links.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}


		/*
		 * @param string
		 * @return string
		 */
		private function run_custom_tab($tab=''){
			$key = $tab;
			$data['content'] = (isset($this->account_page_settings['uap_tab_' . $key . '_content'])) ? uap_replace_constants($this->account_page_settings['uap_tab_' . $key . '_content'], $this->uid) : '';
			$data['title'] = (isset($this->account_page_settings['uap_tab_' . $key . '_title'])) ? uap_replace_constants($this->account_page_settings['uap_tab_' . $key . '_title'], $this->uid) : '';
			$data['content'] = $this->clean_text($data['content']);
			$data['title'] = $this->clean_text($data['title']);

			$fullPath = UAP_PATH . 'public/views/account_page-custom_tab.php';
			$searchFilename = 'account_page-custom_tab.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			require_once $template;
		}


		/*
		 * @param string
		 * @return stirng
		 */
		private function clean_text($string=''){
			/*
			 * @param string
			 * @return string
			 */
			 return stripslashes($string);
		}

	}//end class
}//end if
