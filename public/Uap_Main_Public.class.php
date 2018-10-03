<?php
if (!class_exists('Uap_Main_Public')){
	class Uap_Main_Public{
		private $current_url = '';
		private $affiliate_id = 0; // from uap_affiliate table
		private $user_id = 0; // from wp_users table
		private $is_admin = FALSE;
		private $user_role = '';

		public function __construct(){
			/*
			 * @param none
			 * @return none
			 */
			$this->current_url = UAP_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; /// $_SERVER['SERVER_NAME']

			$this->referral_action();

			add_action('init', array($this, 'do_tracking'), 20);
			add_action('init', array($this, 'set_user'), 21);
			add_action('init', array($this, 'check_for_form_actions'), 22);
			add_action('init', array($this, 'check_page'), 23);
			add_action('init', array($this, 'hide_admin_bar'), 24);
			add_action('user_register', array($this, 'all_new_users_become_affiliates'), 150, 1);
			add_filter('send_email_change_email', array($this, 'uap_affiliate_email_was_changed_filter'), 999, 3);

			/// SHORTCODES
			add_shortcode('uap-account-page', array($this, 'affiliate_print_account_page'));
			add_shortcode('uap-register', array($this, 'affiliate_print_register_form'));
			add_shortcode('uap-login-form', array($this, 'affiliate_print_login_form'));
			add_shortcode('uap-logout', array($this, 'affiliate_print_logout'));
			add_shortcode('uap-reset-password', array($this, 'affiliate_print_reset_password'));
			add_shortcode('uap-affiliate', array($this, 'affiliate_print_field'));
			add_shortcode('uap-user-become-affiliate', array($this, 'affiliate_print_become_affiliate_bttn'));
			add_shortcode('uap-public-affiliate-info', array($this, 'public_print_affiliate_info'));
			add_shortcode('uap-landing-commission', array($this, 'do_landing_commisions'));
			add_shortcode('uap-listing-affiliates', array($this, 'do_listing_affiliates'));
			add_shortcode('if_affiliate', array($this, 'uap_shortcode_if_affliate'));
			add_shortcode('if_not_affiliate', array($this, 'uap_shortcode_if_not_affliate'));
			add_shortcode('visitor_referred', [$this, 'uap_shortcode_visitor_is_referred'] );
			add_shortcode('visitor_not_referred', [$this, 'uap_shortcode_visitor_is_not_referred'] );

			/// FILTERS
			add_filter('the_content', array($this, 'uap_print_message'), 65);
			/// STYLE & SCRIPTS
			add_action('wp_enqueue_scripts', array($this, 'add_style_and_scripts'));

			if (!function_exists('is_plugin_active')){
	 			include_once ABSPATH . 'wp-admin/includes/plugin.php';
	 		}
			// WOO CUSTOM MENU ITEM
			global $indeed_db;
			$temp = $indeed_db->return_settings_from_wp_option('woo_account_page');
			if (!empty($temp['uap_woo_account_page_enable']) && is_plugin_active('woocommerce/woocommerce.php')){
				require_once UAP_PATH . 'classes/Uap_Custom_Woo_Endpoint.class.php';
				$woo_menu = new Uap_Custom_Woo_Endpoint();
			}

			/// BP CUSTOM MENU ITEM
			$temp = $indeed_db->return_settings_from_wp_option('bp_account_page');
			if (!empty($temp['uap_bp_account_page_enable']) && is_plugin_active('buddypress/bp-loader.php')){
				require_once UAP_PATH . 'classes/Uap_BP_Custom_Menu_item.class.php';
				$bp_menu = new Uap_BP_Custom_Menu_item();
	 		}
		}

		public function add_style_and_scripts(){
			/*
			 * @param none
			 * @return none
			 */
			wp_enqueue_style('uap_font_awesome', UAP_URL . 'assets/css/font-awesome.css');
			wp_enqueue_style( 'uap_public_style', UAP_URL . 'assets/css/main_public.css');
			wp_enqueue_style('uap_templates', UAP_URL . 'assets/css/templates.css');
			wp_enqueue_style('uap_jquery-ui.min.css', UAP_URL . 'assets/css/jquery-ui.min.css');
			wp_enqueue_style('uap_select2_style', UAP_URL . 'assets/css/select2.min.css' );
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-datepicker');
			wp_register_script('uap-public-functions', UAP_URL . 'assets/js/public.js', array(), null);
			wp_localize_script('uap-public-functions', 'ajax_url', admin_url( 'admin-ajax.php' ));
			wp_enqueue_script('uap-public-functions');
			wp_enqueue_script('uap-jquery_form_module', UAP_URL . 'assets/js/jquery.form.js', array(), null );
			wp_enqueue_script('uap-jquery.uploadfile', UAP_URL . 'assets/js/jquery.uploadfile.min.js', array(), null);
			wp_enqueue_script( 'uap-select2', UAP_URL . 'assets/js/select2.min.js', array(), null );
			wp_enqueue_script('uap-jquery.uploadfile-footer', UAP_URL . 'assets/js/jquery.uploadfile.min.js', array(), null, TRUE);
		}

		public function do_tracking(){
			/*
			 * TRACKING
			 * @param none
			 * @return none
			 */
			require_once UAP_PATH . 'public/Uap_Tracking.class.php';
			$tracking_object = new Uap_Tracking();
		}

		public function affiliate_print_account_page($args=array()){
			/*
			 * @param array
			 * @return string
			 */
			$output = '';
			if ($this->is_admin && !$this->affiliate_id){
				$output = $this->return_admin_info_message('account_page');
			} else if ($this->affiliate_id){
				/// ONLY FOR AFFILIATES
				require UAP_PATH . 'public/Affiliate_Account_Page.class.php';
				$obj = new Affiliate_Account_Page($this->user_id, $this->affiliate_id);
				$output = $obj->output();
			}
			return $output;
		}


		public function affiliate_print_register_form($attr=array()){
			/*
			 * @param array
			 * @return string
			 */
			$output = '';
			if ($this->is_admin){
				$output = $this->return_admin_info_message('register');
			} else if (!$this->affiliate_id){

				$shortcodes_attr = array();

				require_once UAP_PATH . 'classes/Uap_Add_Edit_Affiliate.class.php';
				$args = array(
						'user_id' => false,
						'type' => 'create',
						'tos' => true,
						'captcha' => true,
						'action' => '',
						'is_public' => true,
						'shortcodes_attr' => $shortcodes_attr,
				);
				$obj = new Uap_Add_Edit_Affiliate($args);
				$data = $obj->form();
				/// TEMPLATE
				$data['template'] = empty($attr['template']) ? get_option('uap_register_template') : $attr['template'];
				$data['css'] = get_option('uap_register_custom_css');

				$fullPath = UAP_PATH . 'public/views/register.php';
				$searchFilename = 'register.php';
				$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

				ob_start();
				$data['action'] = '';
				require $template;
				$output = ob_get_contents();
				ob_end_clean();

				if (!UAP_LICENSE_SET){
					$output .= $this->print_trial_message();
				}
			}
			return $output;
		}

		public function affiliate_print_login_form($args=array()){
			/*
			 * @param array
			 * @return string
			 */
			require_once UAP_PATH . 'public/Uap_Login.class.php';
			if ($this->is_admin){
				return $this->return_admin_info_message('login');
			} else {
				$object = new Uap_Login();
				if (!UAP_LICENSE_SET){
					echo $this->print_trial_message();
				}
				return $object->print_login_form($args, $this->user_role, $this->affiliate_id);
			}
			return '';
		}

		private function print_trial_message(){
			/*
			 * @param none
			 * @return string
			 */

		  $fullPath = UAP_PATH . 'public/views/trial_version_message.php';
			$searchFilename = 'trial_version_message.php';
			$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			ob_start();
			require $template;
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}

		public function check_for_form_actions(){
			/*
			 * @param none
			 * @return none
			 */
			if (!empty($_REQUEST['uapaction'])){ //can be GET || POST
				switch ($_REQUEST['uapaction']){
					case 'login':
						require_once UAP_PATH . 'public/Uap_Login.class.php';
						$object = new Uap_Login();
						$current_url_check = explode("?", $this->current_url);
						$this->current_url = $$current_url_check[0] ;

						$object->do_login($this->current_url);
						break;

					case 'logout':
						$this->do_logout();
						break;

					case 'register':
						/// REGISTER
						require_once UAP_PATH . 'classes/Uap_Add_Edit_Affiliate.class.php';
						$args = array(
										'user_id' => FALSE,
										'type' => 'create',
										'tos' => TRUE,
										'captcha' => TRUE,
										'action' => '',
										'is_public' => TRUE,
										'register_template' => '',
										'url' => $this->current_url
						);
						$obj = new Uap_Add_Edit_Affiliate($args);
						$obj->save_update_user();
						break;

					case 'update':
						/////////////////////// UPDATE
						if (is_user_logged_in()){

							require_once UAP_PATH . 'classes/Uap_Add_Edit_Affiliate.class.php';
							$args = array(
										'user_id' => $this->user_id,
										'type' => 'edit',
										'tos' => TRUE,
										'captcha' => TRUE,
										'action' => '',
										'is_public' => TRUE,
										'register_template' => '',
										'url' => $this->current_url
							);
							$obj = new Uap_Add_Edit_Affiliate($args);
							$obj->save_update_user();
						}
						break;
					case 'reset_pass':
						require_once UAP_PATH . 'public/Uap_Reset_Password.class.php';
						$object = new Uap_Reset_Password();
						$object->do_reset();
						break;
				}
			}
		}

		public function set_user(){
			/*
			 * @param none
			 * @return none
			 */
			global $current_user;
			global $indeed_db;
			$this->user_role = 'unreg';
			$this->is_admin = (current_user_can('administrator')) ? TRUE : FALSE;
			if (!empty($current_user->ID)){
				$this->user_id = $current_user->ID;
				$this->affiliate_id = $indeed_db->affiliate_get_id_by_uid($this->user_id);

				if ($this->is_admin){
					$this->user_role = 'admin';
 				} else {
 					if (isset($current_user->roles[0]) && $current_user->roles[0]=='pending_user'){
 						$this->user_role = 'pending';
 					} else {
 						$this->user_role = 'reg';
 					}
 				}
			}
			return FALSE;
		}

		public function uap_print_message($content=''){
			/*
			 * @param none
			 * @return string
			 */
			$str = '';
			if (!empty($_REQUEST['uap_register'])){
				switch ($_REQUEST['uap_register'] ){
					case 'create_message':
						$str .= '<div class="uap-reg-success-msg">' . uap_correct_text(get_option('uap_register_success_meg')) . '</div>';
						break;
					case 'update_message':
						$str .= '<div class="uap-reg-update-msg">' . uap_correct_text(get_option('uap_general_update_msg')) . '</div>';
						break;
				}
			}
			return do_shortcode($content) . $str;
		}

		public function affiliate_print_logout($attr=array()){
			/*
			 * @param array
			 * @return none
			 */
			$output = '';
			if ($this->user_id){ // && $this->affiliate_id
				if (isset($attr['uap_login_template'])){
					$data['metas']['uap_login_template'] = $attr['uap_login_template'];
				} else {
					$data['metas']['uap_login_template'] = get_option('uap_login_template');
				}
				$data['logout_link'] = add_query_arg( 'uapaction', 'logout', $this->current_url );
				$data['logout_label'] = __('Log Out', 'uap');

				$fullPath = UAP_PATH . 'public/views/logout.php';
				$searchFilename = 'logout.php';
				$template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

				ob_start();
				require $template;
				$output = ob_get_contents();
				ob_end_clean();
			}
			return $output;
		}

		public function affiliate_print_reset_password(){
			/*
			 * @param none
			 * @return string
			 */
			 if ($this->is_admin){
			 	return $this->return_admin_info_message('reset_password');
			 } else {
				require_once UAP_PATH . 'public/Uap_Reset_Password.class.php';
				$object = new Uap_Reset_Password();
				return $object->form();
			 }
		}

		private function do_logout(){
			/*
			 * @param none
			 * @return none
			 */

			$url = get_option('uap_general_logout_redirect');
			if ($url && $url!=-1){
				$link = get_permalink($url);
				if (!$link){
					$link = $this->current_url;
				}
			} else {
				//redirect to same page
				global $wp;
				$link = remove_query_arg('uapaction', $this->current_url);
			}
			wp_clear_auth_cookie();
			do_action('wp_logout');
			nocache_headers();
			wp_redirect( $link );
			exit();
		}

		public function referral_action(){
			/*
			 * @param
			 * @return
			 */

			/// main referral
			require_once UAP_PATH . 'public/Referral_Main.class.php';
			$object = new Referral_Main($this->user_id, $this->affiliate_id);

			/************** services ****************/
			/// WOO
			require_once UAP_PATH . 'public/services/Uap_Woo.class.php';
			$woo = new Uap_Woo();

			/// UMP
			require_once UAP_PATH . 'public/services/Uap_UMP.class.php';
			$ump = new Uap_UMP();

			/// EDD
			require_once UAP_PATH . 'public/services/Uap_Easy_Digital_Download.class.php';
			$edd = new Uap_Easy_Digital_Download();

			/// ULP
			require_once UAP_PATH . 'public/services/Uap_Ulp.php';
			$ulp = new Uap_Ulp();

			/// UAP
			$uap = new \Indeed\Uap\Services\Uap_Uap();

			/// Landing pages
			$landingPagesObject = new \Indeed\Uap\AffiliateLandingPages();
		}

		public function affiliate_print_field($attr=array()){
			/*
			 * @param array
			 * @return string
			 */
			 $str = '';
			 if (!empty($attr['field']) && !empty($this->user_id)){
				$search = "{" . $attr['field'] . "}";
				$return = uap_replace_constants($search, $this->user_id);
				if ($search!=$return){
					$str = $return;
				}
			}
			return $str;
		}

		public function return_admin_info_message($type=''){
			/*
			 * @param string
			 * @return string
			 */
			 $data['content'] = '';
			 switch ($type){
			 	case 'login':
					$data['content'] = __('Loggin Form is not showing up when You\'re logged.', 'uap');
					break;
				case 'register':
					$data['content'] = __('Register Form is not showing up when You\'re logged.', 'uap');
					break;
				case 'account_page':
					$data['content'] = __('Affiliate Account Page!', 'uap');
					break;
				case 'reset_password':
					$data['content'] = __('Affiliate Lost Password Page!', 'uap');
					break;
			 }
			 $fullPath = UAP_PATH . 'public/views/message_for_admin.php';
			 $searchFilename = 'message_for_admin.php';
			 $template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

			 ob_start();
			 require $template;
			 $output = ob_get_contents();
			 ob_end_clean();
			 return $output;
		}

		public function affiliate_print_become_affiliate_bttn(){
			/*
			 * @param none
			 * @return string
			 */
			 global $indeed_db;
			 if ($this->user_id && !$indeed_db->is_user_affiliate_by_uid($this->user_id)){
				 $fullPath = UAP_PATH . 'public/views/become_affiliate_bttn.php';
				 $searchFilename = 'become_affiliate_bttn.php';
				 $template = apply_filters('uap_filter_on_load_template', $fullPath, $searchFilename );

				ob_start();
			 	require_once $template;
			 	$output = ob_get_contents();
			 	ob_end_clean();
			 	return $output;
			 }
		}

		public function check_page(){
			/*
			 * Do Redirect if it's case
			 * @param none
			 * @return none
			 */
			if (defined('DOING_AJAX') && DOING_AJAX) {
				return;
			}
			if (isset($_REQUEST['uxb_iframe']) && isset($_REQUEST['post_id'])){
				return;
			}
			global $indeed_db;
			$url = UAP_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; /// $_SERVER['SERVER_NAME']

			/// GETTING CURRENT POST ID
			$post_id = url_to_postid($url);
			if ($post_id==0){
				$cpt_arr = $indeed_db->get_all_post_types();
				$the_cpt = FALSE;
				$post_name = FALSE;
				if (count($cpt_arr)){
					foreach ($cpt_arr as $cpt){
						if (!empty($_GET[$cpt])){
							$the_cpt = $cpt;
							$post_name = $_GET[$cpt];
							break;
						}
					}
				}
				if ($the_cpt && $post_name){
					$cpt_id = $indeed_db->get_post_id_by_cpt_name($the_cpt, $post_name);
					if ($cpt_id){
						$postid = $cpt_id;
					}
				} else {
					//test if its homepage
					$homepage = get_option('page_on_front');
					if ($url==get_permalink($homepage)){
						$postid = $homepage;
					}
				}
			}

			/// CHECK IF MUST DO REDIRECT
			$default_pages = $indeed_db->return_settings_from_wp_option('general-default_pages');
			$default_redirects = $indeed_db->return_settings_from_wp_option('general-redirects');
			if ($default_pages && $default_redirects && $post_id){
				switch ($post_id){
					case ($default_pages['uap_general_login_default_page']==$post_id):
						if ($this->affiliate_id){
							/// DO REDIRECT
							$pid = $default_redirects['uap_general_login_page_logged_users_redirect'];
						}
						break;
					case ($default_pages['uap_general_register_default_page']==$post_id):
						if ($this->affiliate_id){
							/// DO REDIRECT
							$pid = $default_redirects['uap_general_register_page_logged_users_redirect'];
						}
						break;
					case ($default_pages['uap_general_lost_pass_page']==$post_id):
						if ($this->affiliate_id){
							/// DO REDIRECT
							$pid = $default_redirects['uap_general_lost_pass_page_logged_users_redirect'];
						}
						break;
					case ($default_pages['uap_general_logout_page']==$post_id):
						if (!$this->affiliate_id){
							/// DO REDIRECT
							$pid = $default_redirects['uap_general_logout_page_non_logged_users_redirect'];
						}
						break;
					case ($default_pages['uap_general_user_page']==$post_id):
						if (!$this->affiliate_id){
							/// DO REDIRECT
							$pid = $default_redirects['uap_general_account_page_no_logged_redirect'];
						}
						break;
				}
				if (isset($pid) && $pid > 0){
					$target = get_permalink($pid);
					wp_redirect($target);
					exit;
				}
			}
		}

		public function public_print_affiliate_info($attr=array()){
			/*
			 * @param array
			 * @return string
			 */
			$str = '';
			/*
			if (empty($this->affiliate_id)){
				$affiliate_id = $this->check_and_return_affiliate_id(); /// get affiliate from cookie
			} else {
				$affiliate_id = $this->affiliate_id;
			}
			*/
			$affiliate_id = $this->check_and_return_affiliate_id(); /// get affiliate from cookie
			if (!empty($attr['field']) && !empty($affiliate_id)){
				$search = "{" . $attr['field'] . "}";
				global $indeed_db;
				$affiliate_wp_uid = $indeed_db->get_uid_by_affiliate_id($affiliate_id);
				if ($affiliate_wp_uid){
					$return = uap_replace_constants($search, $affiliate_wp_uid);
					if ($search!=$return){
						$str = $return;
					}
				}
			}
			return $str;
		}

		public function check_and_return_affiliate_id(){
			/*
			 * @param none
			 * @return int
			 */
			if (empty($_COOKIE['uap_referral'])){ /// SEARCH INTO DB
				global $indeed_db;
				$lifetime = get_option('uap_lifetime_commissions_enable');
				if ($lifetime && $this->user_id){ /// here was self::$user_id
					return $indeed_db->search_affiliate_id_for_current_user($this->user_id);
				}
			} else { /// SEARCH INTO COOKIE
				$cookie_data = unserialize(stripslashes($_COOKIE['uap_referral']));
				if (!empty($cookie_data['affiliate_id'])){
					return $cookie_data['affiliate_id'];
				}
			}
			return 0;
		}

		public function do_landing_commisions($arr=array()){
			/*
			 * @param array
			 * @return none
			 */
			 if (!empty($arr['slug'])){
				if (!class_exists('ReferralLandingCommissions')){
					require_once UAP_PATH . 'public/ReferralLandingCommissions.class.php';
				}
				$object = new ReferralLandingCommissions($arr['slug'], $this->user_id);
			 }
		}

		public function all_new_users_become_affiliates($uid=0){
			/*
			 * @param int
			 * @return none
			 */
			 if (get_option('uap_all_new_users_become_affiliates') && $uid && !defined('UAP_USER_REGISTER_PROCESS')){
				 global $indeed_db;
				 $affiliate_id = $indeed_db->save_affiliate($uid);
				 if (!empty($affiliate_id)){
				 	/// assign default rank
				 	$settings = $indeed_db->return_settings_from_wp_option('register');
					if (!empty($settings['uap_register_new_user_rank'])){
				 		$indeed_db->update_affiliate_rank_by_uid($uid, $settings['uap_register_new_user_rank']);
					}

					/// SET MLM RELATION
					$indeed_db->set_mlm_relation_on_new_affiliate($affiliate_id);
				 }
			 }
		}

		public function do_listing_affiliates($params=array()){
			/*
			 * @param array
			 * @return string
			 */
			$params['current_page'] = (empty($_REQUEST['uapUserList_p'])) ? 1 : $_REQUEST['uapUserList_p'];
			if (!class_exists('Uap_Listing_Affiliates')){
				require_once UAP_PATH . 'classes/Uap_Listing_Affiliates.class.php';
			}
			$object = new Uap_Listing_Affiliates($params);
			$output = $object->run();
			return $output;
		}

		public function uap_shortcode_if_affliate($attr=array(), $content=''){
				global $indeed_db;
				$uid = indeed_get_uid();
				if (empty($uid)) return '';
				$is_affiliate = $indeed_db->is_user_an_active_affiliate($uid);
				if (empty($is_affiliate)) return '';
				return $content;
		}

		public function uap_shortcode_if_not_affliate($attr=array(), $content=''){
				global $indeed_db;
				$uid = indeed_get_uid();
				if (empty($uid)) return $content;
				$is_affiliate = $indeed_db->is_user_an_active_affiliate($uid);
				if (empty($is_affiliate)) return $content;
		}

		public function uap_affiliate_email_was_changed_filter($sent, $user, $user_new_data){
			/*
			 * USE THIS TO UPDATE EMAIL ON uap_reports TABLE
			 * @param boolean, array, array
			 * @return boolean
			 */
			 global $indeed_db;
			 $uid = $user['ID'];
			 $affiliate_id = $indeed_db->get_affiliate_id_by_wpuid($uid);
			 if ($affiliate_id && isset($user_new_data['user_email'])){
			 	$indeed_db->update_affiliate_reports_email_addr($affiliate_id, $user_new_data['user_email']);
			 }
			 return $sent;
		}

		public function hide_admin_bar(){
			/*
			 * Hide the admin bar if user has no privilege
			 * @param none
			 * @return none
			 */
			global $current_user;
			$uid = (isset($current_user->ID)) ? $current_user->ID : 0;
			if ($uid){
				$user = new WP_User($uid);
				if ($user && !empty($user->roles) && !empty($user->roles[0]) && $user->roles[0]!='administrator'){
					$allowed_roles = get_option('uap_dashboard_allowed_roles');
					if ($allowed_roles){
						$roles = explode(',', $allowed_roles);
						if (!empty($roles) && !empty($user->roles[0]) && in_array($user->roles[0], $roles)){
							$show = TRUE;
						} else {
							$show = FALSE;
						}
					} else {
						$show = FALSE;
					}
					show_admin_bar($show);
				}
			}
		}

		/// [visitor_referred]
		public function uap_shortcode_visitor_is_referred($attr=[], $content='')
		{
				$onlyFor = empty($attr['affiliate_id']) ? false : $attr['affiliate_id'];
				$cookieName = 'uap_referral';
				if (empty($_COOKIE[$cookieName])){
						return '';
				}
				$cookieData = unserialize(stripslashes($_COOKIE[$cookieName]));
				if (empty($cookieData)){
						return '';
				}
				$onlyForAffiliates = explode(',', $onlyFor);
				if (in_array($cookieData['affiliate_id'], $onlyForAffiliates)){
						return $content;
				}
				return '';
		}

		/// [visitor_not_referred]
		public function uap_shortcode_visitor_is_not_referred($attr=[], $content='')
		{
				$cookieName = 'uap_referral';
				if (empty($_COOKIE[$cookieName])){
						return $content;
				}
				$cookieData = unserialize(stripslashes($_COOKIE[$cookieName]));
				if (empty($cookieData)){
						return $content;
				}
				return '';
		}


	}//end of class
}//end if
