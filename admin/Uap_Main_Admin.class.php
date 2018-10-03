<?php
if (!class_exists('Uap_Main_Admin')){
	class Uap_Main_Admin{
		private $version_param_name_db = 'uap_plugin_version';
		private $amount_type_list = array();
		private $admin_view_path = '';
		private $base_admin_url;
		private $error_messages = array();
		private $items_per_page = array(5, 25, 50, 100, 200, 500);
		private $new_affiliates = 0;
		private $new_referrals = 0;
		private $plugin_version = '';

		public function __construct(){
			/*
			 * @param none
			 * @return none
			 */

			$this->plugin_version = UAP_PLUGIN_VER;
			/// INSTALL / UPDATE
			$current_plugin_version = $this->get_current_plugin_version();///get_option($this->version_param_name_db);
			if ($current_plugin_version===FALSE){
				//plugin first activation
				$this->run_install();
				update_option($this->version_param_name_db, UAP_PLUGIN_VER);
			} else if (UAP_PLUGIN_VER!=$current_plugin_version){
				// run updates
				$this->run_updates();
				update_option($this->version_param_name_db, UAP_PLUGIN_VER);
			}

			$this->plugin_version = $current_plugin_version;

			$this->admin_view_path = UAP_PATH . 'admin/views/';
			require_once UAP_PATH . 'admin/utilities.php';

			/// check for cron, curl, etc
			$this->check_system();


			// CREATE plugin menu
			add_action('admin_menu', array($this, 'add_menu'), 72 );

			//SCRIPTS && STYLE
			add_action("admin_enqueue_scripts", array($this, 'add_style_scripts') );

			/// pages & posts editor buttons
			add_action('init', array($this, 'add_custom_bttns'), 23);

			/// check user privilege
			add_action('init', array($this, 'check_user_privilege'), 24);

			/// print column on pages
			add_filter( 'display_post_states', array($this, 'dashboard_print_uap_column'), 999, 2 );

			/// Meta Box
			add_action('add_meta_boxes', array($this, 'create_page_meta_box') );

			/// save meta form values
			add_action('save_post', array($this, 'save_meta_box_values'));

			/// global errors
			add_action('admin_notices', array($this, 'return_global_errors'), 99);

			/// edit wordpress user stuff
			add_action('edit_user_profile', array($this, 'edit_wp_user'), 99);
			add_action('show_user_profile', array($this, 'edit_wp_user'), 99);

			/// DELETE USER FROM WP
			add_action('deleted_user', array($this, 'uap_delete_affiliate_by_uid'), 99, 1);


			$this->referral_action();
		}

		private function get_current_plugin_version(){
			/*
			 * @param none
			 * @return string
			 */
			 if (is_multisite()){
				 global $wpdb;
				 $table = $wpdb->base_prefix . 'options';
				 $data = $wpdb->get_row("SELECT option_value FROM $table WHERE option_name='{$this->version_param_name_db}' ");
				 if ($data && !empty($data->option_value)){
				 	return $data->option_value;
				 }
			 }
		 	 return get_option($this->version_param_name_db);
		}

		public function check_user_privilege(){
			/*
			 * @param none
			 * @return none
			 */
			 $uid = get_current_user_id();
			 $role = '';
			 $user = new WP_User( $uid );
			 $public_home = home_url();
			 if ($user && !empty($user->roles) && !empty($user->roles[0]) && $user->roles[0]!='administrator'){
			 	$allowed_roles = get_option('uap_dashboard_allowed_roles');
			 	if ($allowed_roles){
			 		$roles = explode(',', $allowed_roles);
			 		if ($roles && is_array($roles) && !in_array($user->roles[0], $roles)){
			 			wp_redirect($public_home);
						exit();
					}
				} else {
					wp_redirect($public_home);
					exit();
				}
			}
		}

		private function run_install(){
			/*
			 * Run only @ first activation. Create DB Tables. Default Settings.
			 * @param none
			 * @return none
			 */
			global $indeed_db;
			$indeed_db->create_tables();
			$indeed_db->create_pending_role();
			$indeed_db->create_default_pages();
			$indeed_db->create_demo_banners();
			$indeed_db->create_default_redirects();
			$this->install_default_notifications();
			$this->install_default_ranks();
		}

		private function run_updates(){
			/*
			 * @param none
			 * @return none
			 */
			 global $indeed_db;
			 $indeed_db->create_tables();
			 $indeed_db->modify_tables();

			 /// Register Fields
			 $post_data = array( 'id' => 0, 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'uap_country', 'label'=>'Country', 'type'=>'uap_country', 'native_wp' => 0, 'req' => 0, 'sublabel' => '' );
			 $indeed_db->register_save_custom_field($post_data);
			 $data = $indeed_db->register_get_custom_fields();
			 $pass1_key = uap_get_array_key_for_subarray_element($data, 'name', 'pass1');
			 if ($pass1_key>-1){
			 	 $arr = $data[$pass1_key];
				 $arr['id'] = $pass1_key;
				 $arr['display_public_ap'] = 0;
				 $indeed_db->register_save_custom_field($arr);
			 }
			 if (isset($arr)) unset($arr);
			 $pass2_key = uap_get_array_key_for_subarray_element($data, 'name', 'pass2');
			 if ($pass2_key>-1){
				 $arr = $data[$pass2_key];
				 $arr['id'] = $pass2_key;
				 $arr['display_public_ap'] = 0;
				 $indeed_db->register_save_custom_field($arr);
			 }

			 $indeed_db->check_update_notifications();
		}

		private function check_system(){
			/*
			 * @param none
			 * @return none
			 */
			$wp_cron = ( defined('DISABLE_WP_CRON') && DISABLE_WP_CRON ) ? FALSE : TRUE;
			if (!$wp_cron){
				$this->error_messages[] = __('Cron is disabled!', 'uap');
			}
			global $indeed_db;

			if ($indeed_db->is_magic_feat_enable('paypal')){
				$temp_array = $indeed_db->return_settings_from_wp_option('paypal');
				if (empty($temp_array['uap_paypal_sandbox'])){
					if (empty($temp_array['uap_paypal_client_id']) || empty($temp_array['uap_paypal_client_secret'])){
						$this->error_messages[] = __('PayPal is not set properly!', 'uap');
					}
				} else {
					if (empty($temp_array['uap_paypal_sandbox_client_id']) || empty($temp_array['uap_paypal_sandbox_client_secret'])){
						$this->error_messages[] = __('PayPal is not set properly!', 'uap');
					}
				}
			}
			if ($indeed_db->is_magic_feat_enable('stripe')){
				$temp_array = $indeed_db->return_settings_from_wp_option('stripe');
				if (empty($temp_array['uap_stripe_sandbox'])){
					if (empty($temp_array['uap_stripe_secret_key']) || empty($temp_array['uap_stripe_publishable_key'])){
						$this->error_messages[] = __('Stripe is not set properly!', 'uap');
					}
				} else {
					if (empty($temp_array['uap_stripe_sandbox_secret_key']) || empty($temp_array['uap_stripe_sandbox_publishable_key'])){
						$this->error_messages[] = __('Stripe is not set properly!', 'uap');
					}
				}
			}

			if ($indeed_db->affiliates_with_no_rank_exists()){
				$this->error_messages[] = __('Some Affiliates users do not have assigned a Rank and they may not be rewarded. Check your stage ', 'uap') . ' <a href="' . admin_url('admin.php?page=ultimate_affiliates_pro&tab=affiliates') . '" target="_blank">' . __('here', 'ihc') . '</a>';
			}

		}

		public function return_global_errors(){
			/*
			 * @param none
			 * @return none
			 */
			if (current_user_can('manage_options')){
				echo $this->print_unregistered_notice(TRUE);
			}
		}

		private function print_unregistered_notice($is_global=FALSE){
			/*
			 * @param boolean
			 * @return string
			 */
			 if (defined('UAP_LICENSE_SET') && !UAP_LICENSE_SET){
			 	$data['err_class'] = ($is_global) ? 'error' : 'uap-error-global-dashboard-message';
				$data['url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=help');
				ob_start();
				require $this->admin_view_path . 'global_errors.php';
				$output = ob_get_contents();
				ob_end_clean();
				return $output;
			 }
		}

		private function install_default_notifications(){
			/*
			 * @param none
			 * @return none
			 */
			global $indeed_db;
			$array = array( 'admin_user_register',
							'register',
							'reset_password_process',
							'reset_password',
							'change_password',
							'user_update',
							'rank_change',
							'email_check',
							'email_check_success',
							'register_lite_send_pass_to_user',
			);
			foreach ($array as $type){
				if (!$indeed_db->notification_type_exists($type)){
					$template = uap_return_default_notification_content($type); ///get default notification content
					$data['type'] = $type;
					$data['rank_id'] = -1;
					$data['subject'] = addslashes($template['subject']);
					$data['message'] = addslashes($template['content']);
					$data['status'] = 1;
					$data['pushover_message'] = '';
					$data['pushover_status'] = '';
					$indeed_db->save_notification($data);///and save it
				}
			}
		}

		private function install_default_ranks(){
			/*
			 * @param none
			 * @return none
			 */
			global $indeed_db;
			$rank_1 = array(
								'id' => 1,
								'slug' => 'rank_1',
								'label' => 'Basic',
								'amount_type' => 'percentage',
								'amount_value' => 10,
								'achieve' => '',
								'rank_order' => 1,
								'color' => '0bb586',
								'description' => 'A Demo Rank',
								'bonus' => '',
								'pay_per_click' => '',
								'cpm_commission' => '',
								'sign_up_amount_value' => -1,
								'lifetime_amount_type' => '',
								'lifetime_amount_value' => -1,
								'reccuring_amount_type' => '',
								'reccuring_amount_value' => -1,
								'mlm_amount_type' => '',
								'mlm_amount_value' => '',
								'status' => 1,
			);
			$rank_2 = array(
								'id' => 2,
								'slug' => 'rank_2',
								'label' => 'Premium',
								'amount_type' => 'percentage',
								'amount_value' => 15,
								'achieve' => '{"i":1,"type_1":"referrals_number","value_1":"100"}',
								'rank_order' => 2,
								'color' => 'f8ba01',
								'description' => 'A Demo Rank',
								'bonus' => '',
								'pay_per_click' => '',
								'cpm_commission' => '',
								'sign_up_amount_value' => -1,
								'lifetime_amount_type' => '',
								'lifetime_amount_value' => -1,
								'reccuring_amount_type' => '',
								'reccuring_amount_value' => -1,
								'mlm_amount_type' => '',
								'mlm_amount_value' => '',
								'status' => 1,
			);
			$indeed_db->rank_save_update($rank_1);
			$indeed_db->rank_save_update($rank_2);
		}

		public function add_menu(){
			/*
			 * @param none
			 * @return none
			 */
			add_menu_page('Ultimate Affiliate Pro', '<span>Ultimate Affiliate Pro</span>', 'manage_options',	'ultimate_affiliates_pro', array($this, 'output') , 'dashicons-networking');
		}

		public function output(){
			/*
			 * @param none
			 * @return none (print html)
			 */
			$currency = get_option('uap_currency');
			$this->amount_type_list = array('flat' => $currency, 'percentage'=> '%');

			$tab = (empty($_GET['tab'])) ? 'dashboard' : $_GET['tab'];
			$this->base_admin_url = admin_url('admin.php?page=ultimate_affiliates_pro&tab=' . $tab);
			$this->print_head($tab);
			switch ($tab){
				case 'dashboard':
					$this->print_dashboard();
					break;
				case 'affiliates':
					$this->print_affiliates();
					break;
				case 'ranks':
					$this->print_ranks();
					break;
				case 'offers':
					$this->print_offers();
					break;
				case 'landing_commissions':
					$this->print_landing_commissions();
					break;
				case 'banners':
					$this->print_banners();
					break;
				case 'visits':
					$this->print_visits();
					break;
				case 'referrals':
					$this->print_referrals();
					break;
				case 'payments':
					$this->print_payments();
					break;
				case 'notifications':
					$this->print_notifications();
					break;
				case 'reports':
					$this->print_reports();
					break;
				case 'settings':
					$this->print_settings();
					break;
				case 'showcases':
					$this->print_showcases();
					break;
				case 'register':
					$this->print_register();
					break;
				case 'login':
					$this->print_login();
					break;
				case 'account_page':
					$this->print_account_page();
					break;
				case 'opt_in':
					$this->print_opt_in();
					break;
				case 'magic_features':
					$this->print_magic_features();
					break;
				case 'shortcodes':
					$this->print_shortcodes();
					break;
				case 'help':
					$this->print_help();
					break;
				case 'top_affiliates':
					$this->print_top_affiliates();
					break;
				case 'top_affiliates_settings':
					$this->print_top_affiliates_settings();
					break;
				case 'referral_list_details':
					$this->referral_list_details();
					break;
				case 'view_payment_settings':
					$this->print_view_payment_settings();
					break;
				case 'import_export':
					$this->print_import_export();
					break;
			}
			$this->print_footer();
		}

		private function print_head($tab){
			/*
			 * @param string
			 * @return string
			 */
			global $indeed_db;
			$data['admin_workflow'] = $indeed_db->return_settings_from_wp_option('general-admin_workflow');

			switch ($tab){
				case 'affiliates':
					$indeed_db->reset_dashboard_notification('affiliates');
					break;
				case 'referrals':
					$indeed_db->reset_dashboard_notification('referrals');
					break;
			}

			if ($data['admin_workflow']['uap_admin_workflow_dashboard_notifications']){
				$data['affiliates_notification_count'] = $indeed_db->get_dashboard_notification_value('affiliates');//$this->new_affiliates;
				$data['referrals_notification_count'] = $indeed_db->get_dashboard_notification_value('referrals');;//$this->new_referrals;
			}


			$data['tab'] = $tab;
			$data['base_url'] = admin_url('admin.php?page=ultimate_affiliates_pro');
			$data['menu_items'] = array(
											'affiliates' => __('Affiliates', 'uap'),
											'ranks' => __('Ranks', 'uap'),
											'offers' => __('Offers', 'uap'),
											'landing_commissions' => __('Landing Commissions (CPA)', 'uap'),
											'banners' => __('Creatives', 'uap'),
											'showcases' => __('Showcases', 'uap'),
											'visits' => __('Visits', 'uap'),
											'referrals' => __('Referrals', 'uap'),
											'payments' => __('Payments', 'uap'),
											'magic_features' => __('Magic Features', 'uap'),
											'notifications' => __('Notifications', 'uap'),
											'reports' => __('Reports', 'uap'),
											'settings' => __('General Options', 'uap'),
			);
			$data['right_tabs'] = array(
											'shortcodes' => __('Shortcodes', 'uap'),
											'import_export' => __('Import/Export', 'uap'),
											'help' => __('Help', 'uap'),
			);
			$plugin_vs = $this->plugin_version;

			require_once $this->admin_view_path . 'header.php';
		}

		private function print_top_messages(){
			/*
			 * @param none
			 * @return string
			 */
			echo $this->print_unregistered_notice(FALSE);
			require_once $this->admin_view_path . 'top_messages.php';
		}

		private function print_footer(){

			$plugin_vs = $this->plugin_version;
			require_once $this->admin_view_path . 'footer.php';
		}

		private function print_dashboard(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$data['stats'] = $indeed_db->stats_for_dashboard();
			$data['currency'] = get_option('uap_currency');
			$data['rank_arr'] = $indeed_db->get_affilitated_per_rank();
			$data['last_referrals'] = $indeed_db->get_last_referrals();
			$data['top_affiliates'] = $indeed_db->get_top_affiliates_by_amount();
			$this->print_top_messages();
			require_once $this->admin_view_path . 'dashboard.php';
		}

		private function print_help(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			if (isset($_REQUEST['uap_save_licensing_code']) && isset($_REQUEST['uap_licensing_code'])){
				$submited = $indeed_db->envato_licensing($_REQUEST['uap_licensing_code']);
			}
			$data = $indeed_db->return_settings_from_wp_option('licensing');
			$disabled = ($this->check_curl()) ? '' : 'disabled';
			$this->print_top_messages();
			require_once $this->admin_view_path . 'help.php';
		}

		/*
		 * @param none
		 * @return none
		 */
		private function print_import_export(){
			global $indeed_db;
			if (!empty($_POST['import']) && !empty($_FILES['import_file'])){
				////////////////// IMPORT
				$filename = UAP_PATH . 'import.xml';
				move_uploaded_file($_FILES['import_file']['tmp_name'], $filename);
				require_once UAP_PATH . 'classes/Indeed_Import_Export/IndeedImport.class.php';
				require_once UAP_PATH . 'classes/Indeed_Import_Export/UapIndeedImport.class.php';
				$import = new UapIndeedImport();
				$import->setFile($filename);
				$import->run();
			}
			$this->print_top_messages();
			require_once $this->admin_view_path . 'import_export.php';
		}

		private function check_curl(){
			/*
			 * @param none
			 * @return boolean
			 */
			return (function_exists('curl_version')) ? TRUE : FALSE;
		}

		private function print_affiliates(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			require_once UAP_PATH . 'classes/Uap_Add_Edit_Affiliate.class.php';
			$current_url = UAP_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; /// $_SERVER['SERVER_NAME']
			$current_url = remove_query_arg('uap_list_item', $current_url);
			$currency = get_option('uap_currency');

			if (isset($_POST['Update'])){
				/// UPDATE AFFILIATE
				$args = array(
						'type' => 'edit',
						'tos' => FALSE,
						'captcha' => FALSE,
						'is_public' => FALSE,
						'user_id' => $_POST['user_id'],
				);
				$obj = new Uap_Add_Edit_Affiliate($args);
				$save_err = $obj->save_update_user();
			} else if (isset($_POST['Submit'])){
				/// CREATE AFFILIATE
				$args = array(
						'user_id' => FALSE,
						'type' => 'create',
						'tos' => FALSE,
						'captcha' => FALSE,
						'is_public' => FALSE,
				);
				$obj = new Uap_Add_Edit_Affiliate($args);
				$save_err = $obj->save_update_user();
			} else if (!empty($_POST['delete_affiliate'])){
				/// DELETE AFFILIATE
				$indeed_db->delete_affiliates(array($_POST['delete_affiliate']));
			} else if (!empty($_POST['do_action']) && !empty($_POST['affiliate_id_arr'])){
				if ($_POST['do_action']=='delete'){
					$indeed_db->delete_affiliates($_POST['affiliate_id_arr']);
				} else if ($_POST['do_action']=='update_ranks'){
					require_once UAP_PATH . 'public/Uap_Change_Ranks.class.php';
					$update_rank_object = new Uap_Change_Ranks($_POST['affiliate_id_arr']);
				}
			}

			$data['url-add_edit'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=affiliates&subtab=add_edit');
			$data['url-manage'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=affiliates');
			$data['subtab'] = (empty($_GET['subtab'])) ? 'list' : $_GET['subtab'];
			$data['show_cpm'] = $indeed_db->is_magic_feat_enable('cpm_commission') ? true : false;
			$data['show_ppc'] = $indeed_db->is_magic_feat_enable('pay_per_click') ? true : false;

			/// OUTPUT
			if ($data['subtab']=='list'){
				/// MANAGE AFFILIATES

				$url = admin_url('admin.php?page=ultimate_affiliates_pro&tab=affiliates');
				$limit = (isset($_GET['uap_limit'])) ? $_GET['uap_limit'] : 25;
				$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
				$total_items = $indeed_db->get_affiliates(-1, -1, TRUE, '', '');

				if ($current_page>1){
					$offset = ( $current_page - 1 ) * $limit;
				} else {
					$offset = 0;
				}
				if ($offset + $limit>$total_items){
					$limit = $total_items - $offset;
				}

				require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
				$limit = (isset($_GET['uap_limit'])) ? $_GET['uap_limit'] : 25;
				$pagination = new Indeed_Pagination(array(
						'base_url' => $current_url,
						'param_name' => 'uap_list_item',
						'total_items' => $total_items,
						'items_per_page' => $limit,
						'current_page' => $current_page,
				));

				$order_by = 'a.start_data';
				$order_type = 'DESC';
				if (!empty($_REQUEST['orderby_user'])){
					switch ($_REQUEST['orderby_user']){
						case 'display_name':
							$order_by = 'u.display_name';
							break;
						case 'user_login':
							$order_by = 'u.user_login';
							break;
						case 'user_email':
							$order_by = 'u.user_email';
							break;
						case 'ID':
							$order_by = 'u.ID';
							break;
						case 'user_registered':
							$order_by = 'u.user_registered';
							break;
					}
				}
				if (!empty($_REQUEST['ordertype_user'])){
					$order_type = $_REQUEST['ordertype_user'];
				}

				$data['ranks_list'] = uap_get_wp_roles_list();
				$data['pagination'] = $pagination->output();
				$data['listing_affiliates'] = $indeed_db->get_affiliates($limit, $offset, FALSE, $order_by, $order_type);
				$data['errors'] = uap_return_errors();
				$data['base_list_url'] = $url;
				$data['base_visits_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=visits');
				$data['base_referrals_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=referrals');
				$data['base_paid_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=paid_referrals');
				$data['base_unpaid_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=unpaid');
				$data['base_pay_now'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=payment_form');
				$data['base_reports_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=reports');
				$data['base_transations_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=transactions');
				$data['base_view_payment_settings_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=view_payment_settings&uid=');
				$data['email_verification'] = $indeed_db->is_magic_feat_enable('email_verification');
				$data['mlm_on'] = $indeed_db->is_magic_feat_enable('mlm');
				$data['mlm_matrix_link'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=mlm_view_affiliate_children&affiliate_name=');
				require_once $this->admin_view_path . 'affiliates-list.php';
			} else {
				/// ADD EDIT AFFILIATES
				$id = (empty($_GET['id'])) ? FALSE : $_GET['id'];
				$type = $id ? 'edit' : 'create';
				$args = array(
						'user_id' => $id,
						'type' => $type,
						'tos' => FALSE,
						'captcha' => FALSE,
						'action' => $data['url-manage'],
						'is_public' => FALSE,
				);
				$obj = new Uap_Add_Edit_Affiliate($args);
				$data = $obj->form();
				$data['template'] = '';
				$data['action'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=affiliates');
				ob_start();
				require_once UAP_PATH . 'public/views/register.php';
				$data['output'] = ob_get_contents();
				ob_end_clean();
				require_once $this->admin_view_path . 'affiliates-add_edit.php';
			}

		}

		private function print_ranks(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			$data['url-add_edit'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=ranks&subtab=add_edit');
			$data['url-manage'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=ranks');
			$data['subtab'] = (empty($_GET['subtab'])) ? 'list' : $_GET['subtab'];
			$data['achieve_types'] = array(-1=>'...', 'referrals_number'=>'Number of Referrals', 'total_amount'=>'Total Amount');

			/// OUTPUT
			if ($data['subtab']=='list'){
				if (!empty($_POST['save'])){
					$indeed_db->rank_save_update($_POST);
				} else if (!empty($_POST['delete_rank'])){
					if ($indeed_db->ranks_get_count()>1){
						$indeed_db->delete_rank($_POST['delete_rank']);
					} else {
						$data['alert_message'] = __('You cannot have less than one rank.', 'ihc');
					}
				}

				$data['ranks'] = $indeed_db->get_ranks();
				$data['ranks'] = uap_reorder_ranks($data['ranks']);//reorder
				require_once $this->admin_view_path . 'ranks-list.php';
			} else {
				$id = (empty($_GET['id'])) ? 0 : $_GET['id'];
				$data['ranks'] = $indeed_db->get_ranks();
				$data['graphic'] = uap_create_ranks_graphic($data['ranks'], $id);
				$data['maximum_ranks'] = count($data['ranks']);
				if ($id==0) $data['maximum_ranks']++;
				$data['metas'] = $indeed_db->get_rank($id);
				$data['amount_types'] = $this->amount_type_list;
				$temp_data = $indeed_db->return_settings_from_wp_option('sign_up_referrals');
				$data['display-signup_referrals'] = $indeed_db->is_magic_feat_enable('sign_up_referrals');
				$data['display-lifetime_commissions'] = $indeed_db->is_magic_feat_enable('lifetime_commissions');
				$data['display-reccuring_referrals'] = $indeed_db->is_magic_feat_enable('reccuring_referrals');
				$data['display-mlm'] = $indeed_db->is_magic_feat_enable('mlm');
				$data['mlm_matrix_depth'] = get_option('uap_mlm_matrix_depth');
				$data['bonus_enabled'] = $indeed_db->is_magic_feat_enable('bonus_on_rank');
				$data['pay_per_click_enabled'] = $indeed_db->is_magic_feat_enable('pay_per_click');
				$data['cpm_commission_enabled'] = $indeed_db->is_magic_feat_enable('cpm_commission');
				require_once $this->admin_view_path . 'ranks-add_edit.php';
			}
		}

		private function print_offers(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			$data['url-add_edit'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=offers&subtab=add_edit');
			$data['url-manage'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=offers');
			$data['subtab'] = (empty($_GET['subtab'])) ? 'list' : $_GET['subtab'];
			$currency = get_option('uap_currency');

			if ($data['subtab']=='add_edit'){
				/// ADD EDIT
				$id = (empty($_GET['id'])) ? 0 : $_GET['id'];
				$data['metas'] = $indeed_db->get_offer($id);
				$data['amount_types'] = $this->amount_type_list;
				if (!empty($data['metas']['affiliates'])){
					foreach ($data['metas']['affiliates'] as $id){
						$data['affiliates']['username'][$id] = $indeed_db->get_wp_username_by_affiliate_id($id);
					}
					$data['affiliates']['username'][-1] = 'All';
				}

				if (!empty($data['metas']['products'])){
					$data['metas']['products'] = explode(',', $data['metas']['products']);
					switch ($data['metas']['source']){
						case 'woo':
							foreach ($data['metas']['products'] as $id){
								$data['products']['label'][$id] = $indeed_db->woo_get_product_title_by_id($id);
							}
							break;
						case 'ump':
							foreach ($data['metas']['products'] as $id){
								$data['products']['label'][$id] = $indeed_db->ump_get_level_label_by_id($id);
							}
							break;
						case 'edd':
							foreach ($data['metas']['products'] as $id){
								$data['products']['label'][$id] = $indeed_db->edd_get_label_by_id($id);
							}
							break;
						case 'ulp':
							foreach ($data['metas']['products'] as $id){
								$data['products']['label'][$id] = $indeed_db->ulp_get_label_by_id($id);
							}
							break;
					}
				}
				require_once $this->admin_view_path . 'offers-add_edit.php';
			} else {
				/// LISTING
				if (!empty($_POST['save'])){
					$saved = $indeed_db->save_offer($_POST);
					if ($saved<1){
						$data['errors'] = __('Be sure that you have filled all the reguired fields: Name, Amount, Date Range.', 'uap');
					}
				} else if (!empty($_POST['delete_offers'])){
					$indeed_db->delete_offers($_POST['delete_offers']);
				}
				$data['listing_items'] = $indeed_db->get_offers();
				require_once $this->admin_view_path . 'offers-list.php';
			}
		}

		private function print_landing_commissions(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			$data['url-add_edit'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=landing_commissions&subtab=add_edit');
			$data['url-manage'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=landing_commissions');
			$data['subtab'] = (empty($_GET['subtab'])) ? 'list' : $_GET['subtab'];
			$currency = get_option('uap_currency');

			if ($data['subtab']=='add_edit'){
				/// ADD EDIT
				$id = (empty($_GET['slug'])) ? '' : $_GET['slug'];
				$data['metas'] = $indeed_db->get_landing_commission($id);
				require_once $this->admin_view_path . 'landing_commissions-add_edit.php';
			} else {
				/// LISTING
				if (!empty($_POST['save'])){
					$saved = $indeed_db->save_landing_commission($_POST);
					if ($saved<1){
						$data['errors'] = __('Be sure that you have filled all the required fields: Slug and Amount.', 'uap');
					}
				} else if (!empty($_POST['delete_landing_referral'])){
					$indeed_db->delete_landing_commission($_POST['delete_landing_referral']);
				}
				$data['listing_items'] = $indeed_db->get_landing_commissions();
				require_once $this->admin_view_path . 'landing_commissions-list.php';
			}
		}

		private function print_referrals(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			$data['url-add_edit'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=referrals&subtab=add_edit');
			$data['url-manage'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=referrals');
			$data['subtab'] = (empty($_GET['subtab'])) ? 'list' : $_GET['subtab'];

			if ($data['subtab']=='add_edit'){
				/// ADD EDIT
				$data['affiliates'] = $indeed_db->get_affiliates();
				$id = (empty($_GET['id'])) ? 0 : $_GET['id'];
				$data['metas'] = $indeed_db->get_referral($id);
				$data['status_posible'] = array(0=>'Refuse', 1=>'Unverfied', 2=>'Verified');
				$data['payment_posible'] = array(0 => 'Unpaid', 1 => 'Pending', 2 => 'Complete');
				require_once $this->admin_view_path . 'referrals-add_edit.php';
			} else {
				/// LISTING
				if (!empty($_POST['save'])){
					/// SAVE UPDATE
					/// $indeed_db->save_referral($_POST);
					$save_answer = $indeed_db->save_referral_from_admin($_POST);
					if (!$save_answer){
						$data['error'] = __('Error', 'uap');
					} else {
						/// SAVE affiliate referral relation
						$old_affiliate = $indeed_db->search_affiliate_id_for_current_user($_POST['refferal_wp_uid']);
						if ($old_affiliate){
							$rewrite_referrals = get_option('uap_rewrite_referrals_enable');
							if ($rewrite_referrals){
								/// update user - affiliate relation, use new affiliate
								$indeed_db->update_affiliate_referral_user_relation_by_ids($old_affiliate, $_POST['affiliate_id'], $_POST['refferal_wp_uid']);
							}
						} else {
							/// insert user - affiliate relation
							$indeed_db->insert_affiliate_referral_user_new_relation($_POST['affiliate_id'], $_POST['refferal_wp_uid']);
						}
					}
				} else if (!empty($_POST['change_status'])){
					/// CHANGE STATUS
					if (strpos($_POST['change_status'], '-')!==FALSE){
						$status_data = explode('-', $_POST['change_status']);
						if (isset($status_data[0]) && isset($status_data[1])){
							$indeed_db->change_referral_status($status_data[0], $status_data[1]);
						}
					}
				} else if (isset($_POST['referral_list']) && $_POST['referral_list'] &&
						(isset($_POST['list_action']) && $_POST['list_action']!=-1) || (isset($_POST['list_action_2']) && $_POST['list_action_2']!=-1) ){
					/// CHANGE STATUS && DELETE

					if (isset($_POST['list_action']) && $_POST['list_action']!=-1){
						$action = $_POST['list_action'];
					} else if (isset($_POST['list_action_2']) && $_POST['list_action_2']!=-1){
						$action = $_POST['list_action_2'];
					}

					if ($action=='delete'){
						$data['current_actions'] = 'delete';
						foreach ($_POST['referral_list'] as $id){
							$indeed_db->delete_referrals($id);
						}
					} else {
						switch ($action){
							case 'refuse':
								$data['current_actions'] = 'refuse';
								$status = 0;
								break;
							case 'pending':
								$data['current_actions'] = 'pending';
								$status = 1;
								break;
							case 'complete':
								$data['current_actions'] = 'complete';
								$status = 2;
								break;
						}
						foreach ($_POST['referral_list'] as $id){
							$indeed_db->change_referral_status($id, $status);
						}
					}
				} else if (!empty($_POST['delete_referral'])){
					/// single delete
					foreach ($_POST['delete_referral'] as $id){
						$indeed_db->delete_referrals($id);
					}
				}

				/// VIEW
				$where = array();
				if (!empty($_REQUEST['udf']) && !empty($_REQUEST['udu'])){
					$where[] = " r.date>'" . $_REQUEST['udf'] . "' ";
					$where[] = " r.date<'" . $_REQUEST['udu'] . "' ";
					$data['url-manage'] .= '&udf=' . $_REQUEST['udf'] . '&udu=' . $_REQUEST['udu'];
				}
				if (isset($_REQUEST['u_sts']) && $_REQUEST['u_sts']!=-1){
					$where[] = " r.status='" . $_REQUEST['u_sts'] . "' ";
					$data['url-manage'] .= '&u_sts=' . $_REQUEST['u_sts'];
				}
				if (!empty($_REQUEST['aff_u'])){
					$where[] = "u.user_login LIKE '%" . $_REQUEST['aff_u'] . "%'";
					$data['url-manage'] .= '&aff_u=' . $_REQUEST['aff_u'];
				}
				if (!empty($_GET['affiliate_id'])){
					$where[] = "r.affiliate_id=" . $_GET['affiliate_id'];
					$data['url-manage'] .= '&affiliate_id=' . $_GET['affiliate_id'];
					$wpuid = $indeed_db->get_uid_by_affiliate_id($_GET['affiliate_id']);
					$username = $indeed_db->get_username_by_wpuid($wpuid);
					$full_name = $indeed_db->get_full_name_of_user($_GET['affiliate_id']);
					$data['subtitle'] = __('View Referrals for', 'uap') . " $full_name ($username)";
				}

				$limit = (empty($_GET['uap_limit'])) ? 25 : $_GET['uap_limit'];
				$data['url-manage'] .= '&uap_limit=' . $limit;
				$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
				$total_items = $indeed_db->get_referrals(-1, -1, TRUE, '', '', $where);
				if ($current_page>1){
					$offset = ( $current_page - 1 ) * $limit;
				} else {
					$offset = 0;
				}
				if ($offset + $limit>$total_items){
					$limit = $total_items - $offset;
				}
				require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
				$limit = (empty($_GET['uap_limit'])) ? 25 : $_GET['uap_limit'];
				$pagination = new Indeed_Pagination(array(
						'base_url' => $data['url-manage'],
						'param_name' => 'uap_list_item',
						'total_items' => $total_items,
						'items_per_page' => $limit,
						'current_page' => $current_page,
				));

				$data['base_list_url'] = $data['url-manage'];
				$data['pagination'] = $pagination->output();
				//$data['listing_items'] = $indeed_db->get_referrals($limit, $offset, FALSE, '', '', $where);
				$data['listing_items'] = $indeed_db->get_referrals($limit, $offset, FALSE, 'r.id', 'DESC', $where); /// r.date DESC
				$data['filter'] = uap_return_date_filter($data['url-manage'],
															array(
																0 => __('Refuse', 'uap'),
					 											1 => __('Unverified', 'uap'),
					 											2 => __('Verified', 'uap'),
															),
															TRUE
				);

				$data['actions'] = array(
											-1 => '...',
											'delete' => __('Delete', 'uap'),
											'refuse' => __('Mark as Refuse', 'uap'),
											'pending' => __('Mark as Unverified', 'uap'),
											'complete' => __('Mark as Verified', 'uap'),
				);
				if (empty($data['current_actions'])){
					$data['current_actions'] = -1;
				}
				$available_systems = uap_get_active_services();
				if (!empty($available_systems['woo'])){
					$data['woo_order_base_link'] = admin_url('post.php?post=');// must add &action=edit after id
				}
				if (!empty($available_systems['ulp'])){
					$data['ulp_order_base_link'] = admin_url('post.php?post=');// must add &action=edit after id
				}
				if (!empty($available_systems['edd'])){
					$data['edd_order_base_link'] = admin_url('edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=');
				}
				if (!empty($available_systems['ump'])){
					$data['ump_order_base_link'] = admin_url('admin.php?page=ihc_manage&tab=payments&details_id=');
				}
				$data['mlm_order_base_link'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=referral_list_details&id=');
				$data['user_sign_up_link'] = admin_url('user-edit.php?user_id=');
				require_once $this->admin_view_path . 'referrals-list.php';
			}
		}

		private function referral_list_details(){
			/*
			 * @param none
			 * @return string
			 */
			 global $indeed_db;
			 $data['currency'] = get_option('uap_currency');
			 $data['metas'] = $indeed_db->get_referral(@$_GET['id']);
			 $available_systems = uap_get_active_services();
			 if (in_array('woo', $available_systems)){
				$data['woo_order_base_link'] = admin_url('post.php?post=');// must add &action=edit after id
			 }
			 if (in_array('ulp', $available_systems)){
				$data['ulp_order_base_link'] = admin_url('post.php?post=');// must add &action=edit after id
			 }
			 if (in_array('edd', $available_systems)){
				$data['edd_order_base_link'] = admin_url('edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=');
			 }
			 if (in_array('ump', $available_systems)){
			 	$data['ump_order_base_link'] = admin_url('admin.php?page=ihc_manage&tab=payments&details_id=');
			 }
			 require_once $this->admin_view_path . 'referral_list_details.php';
		}

		private function print_banners(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				/// SAVE
				$indeed_db->save_banner($_POST);
			} else if (!empty($_POST['delete_banner'])){
				/// DELETE
				$indeed_db->delete_banners($_POST);
			}

			/// SET METAS
			$data['subtab'] = (empty($_GET['subtab'])) ? 'list' : $_GET['subtab'];
			$data['url-add_edit'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=banners&subtab=add_edit');
			$data['form_action_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=banners');
			if ($data['subtab']=='add_edit'){
				$banner_id = (empty($_GET['id'])) ? 0 : $_GET['id'];
				$metas = $indeed_db->get_banner($banner_id);
				$data = array_merge($data, $metas);
			} else {
				$data['listing_items'] = $indeed_db->get_banners();
			}

			/// FINAL OUTPUT
			if ($data['subtab']=='add_edit'){
				require_once $this->admin_view_path . 'banners-add_edit.php';
			} else {
				require_once $this->admin_view_path . 'banners-list.php';
			}

		}

		private function print_view_payment_settings(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$uid = (empty($_GET['uid'])) ? 0 : $_GET['uid'];
			$data['metas'] = $indeed_db->get_affiliate_payment_settings($uid);

			$this->print_top_messages();
			require_once $this->admin_view_path . 'affiliate-payment-settings.php';
		}

		private function print_payments(){
			/*
			 * @param none
			 * @return string
			 */
			/// PRINT SUBMENU
			$data['submenu'] = array(
									admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=list') => __('Payments For Affiliates', 'uap'),
									admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=list_all_unpaid') => __('All UnPaid Referrals', 'uap'),
									admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=list_all_paid') => __('All Paid Referrals', 'uap'),
									admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=transactions') => __('All Transactions', 'uap'),
			);
			require_once $this->admin_view_path . 'submenu.php';
			///
			$this->print_top_messages();
			global $indeed_db;
			$subtab = (empty($_GET['subtab'])) ? 'list' : $_GET['subtab'];
			switch ($subtab){
				case 'list':

					/// VIEW
					$limit = 30;
					$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
					$total_items = $indeed_db->get_payments(-1, -1, TRUE);
					$total_items = (empty($total_items[0])) ? 0 : $total_items[0];
					if ($current_page>1){
						$offset = ( $current_page - 1 ) * $limit;
					} else {
						$offset = 0;
					}
					if ($offset + $limit>$total_items){
						$limit = $total_items - $offset;
					}
					$url = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=list');
					require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
					$limit = 30;
					$pagination = new Indeed_Pagination(array(
							'base_url' => $url,
							'param_name' => 'uap_list_item',
							'total_items' => $total_items,
							'items_per_page' => $limit,
							'current_page' => $current_page,
					));
					$data['pagination'] = $pagination->output();

					$data['listing_items'] = $indeed_db->get_payments($limit, $offset, FALSE);
					$data['stats'] = $indeed_db->get_stats_for_payments();
					$data['stats']['currency'] = get_option('uap_currency');
					$data['pay_link'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=payment_form');
					$data['unpaid_link'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=unpaid');
					$data['paid_link'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=transactions');
					$data['paid_referrals'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=paid_referrals');

					if ($data['listing_items']){
						$data['payments_settings'] = array();
						foreach ($data['listing_items'] as $id=>$arr){
							if (empty($data['payments_settings'][$id])){
								$data['payments_settings'][$id] = $indeed_db->get_affiliate_payment_type(0, $id);
							}
						}
					}

					require_once $this->admin_view_path . 'payments.php';
					break;
				case 'list_all_unpaid':
					/// VIEW
					$url = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=list_all_unpaid');
					$limit = 30;
					$where = array();
					if (!empty($_REQUEST['udf']) && !empty($_REQUEST['udu'])){
						$where[] = " date>'" . $_REQUEST['udf'] . "' ";
						$where[] = " date<'" . $_REQUEST['udu'] . "' ";
						$url .= '&udf=' . $_REQUEST['udf'] . '&udu=' . $_REQUEST['udu'];
					}
					$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
					$total_items = $indeed_db->get_all_referral_by_payment_status(0, -1, -1, TRUE, '', '', $where);
					$total_items = (empty($total_items[0])) ? 0 : $total_items[0];
					if ($current_page>1){
						$offset = ( $current_page - 1 ) * $limit;
					} else {
						$offset = 0;
					}
					if ($offset + $limit>$total_items){
						$limit = $total_items - $offset;
					}
					$data['pay_link'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=payment_form');
					$data['listing_items'] = $indeed_db->get_all_referral_by_payment_status(0, $limit, $offset, FALSE, 'date', 'DESC', $where);
					if ($data['listing_items']){
						$data['payments_settings'] = array();
						foreach ($data['listing_items'] as $arr){
							if (empty($data['payments_settings'][$arr['affiliate_id']])){
								$data['payments_settings'][$arr['affiliate_id']] = $indeed_db->get_affiliate_payment_type(0, $arr['affiliate_id']);
							}
						}
					}
					require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
					$limit = 30;
					$pagination = new Indeed_Pagination(array(
							'base_url' => $url,
							'param_name' => 'uap_list_item',
							'total_items' => $total_items,
							'items_per_page' => $limit,
							'current_page' => $current_page,
					));
					$data['pagination'] = $pagination->output();
					$data['filter'] = uap_return_date_filter($url);
					require_once $this->admin_view_path . 'payments_list_all_unpaid.php';
					break;
				case 'list_all_paid':
					$url = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=list_all_paid');
					$limit = 30;
					$where = array();
					if (!empty($_REQUEST['udf']) && !empty($_REQUEST['udu'])){
						$where[] = " date>'" . $_REQUEST['udf'] . "' ";
						$where[] = " date<'" . $_REQUEST['udu'] . "' ";
						$url .= '&udf=' . $_REQUEST['udf'] . '&udu=' . $_REQUEST['udu'];
					}
					$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
					$total_items = $indeed_db->get_all_referral_by_payment_status(2, -1, -1, TRUE, '', '', $where);
					$total_items = (empty($total_items[0])) ? 0 : $total_items[0];
					if ($current_page>1){
						$offset = ( $current_page - 1 ) * $limit;
					} else {
						$offset = 0;
					}
					if ($offset + $limit>$total_items){
						$limit = $total_items - $offset;
					}
					require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
					$limit = 30;
					$data['listing_items'] = $indeed_db->get_all_referral_by_payment_status(2, $limit, $offset, FALSE, 'date', 'DESC', $where);
					$pagination = new Indeed_Pagination(array(
							'base_url' => $url,
							'param_name' => 'uap_list_item',
							'total_items' => $total_items,
							'items_per_page' => $limit,
							'current_page' => $current_page,
					));
					$data['pagination'] = $pagination->output();
					$data['filter'] = uap_return_date_filter($url);
					require_once $this->admin_view_path . 'payments_list_all_paid.php';
					break;
				case 'transactions':
					/// ACTIONS
					if (!empty($_POST['do_payment'])){
						if (empty($_POST['affiliates'])){
							$errors = $this->do_single_payment($_POST);
							if (!empty($errors['error_users'])){
								$data['error_users'] = $errors['error_users'];
							}
						} else {
							$errors = $this->do_multiple_payments($_POST);
							if (!empty($errors['error_users'])){
								$data['error_users'] = $errors['error_users'];
							}
						}
					}

					if (!empty($_POST['transaction_id'])){
						$indeed_db->change_transaction_status($_POST['transaction_id'], $_POST['new_status']);
					} else if (!empty($_POST['delete_transaction'])){
						$indeed_db->cancel_transaction($_POST['delete_transaction']);
					} else if (!empty($_GET['do_update_payments'])){
						$indeed_db->update_paypal_transactions();
					}

					/// VIEW
					$affiliate_id = (empty($_GET['affiliate'])) ? 0 : $_GET['affiliate'];
					if ($affiliate_id){
						$wpuid = $indeed_db->get_uid_by_affiliate_id($affiliate_id);
						$username = $indeed_db->get_username_by_wpuid($wpuid);
						$full_name = $indeed_db->get_full_name_of_user($affiliate_id);
						$data['subtitle'] = __('View Transactions for', 'uap') . " $full_name ($username)";
					}
					$limit = 30;
					$where = array();
					if (!empty($_REQUEST['udf']) && !empty($_REQUEST['udu'])){
						$where[] = " create_date>'" . $_REQUEST['udf'] . "' ";
						$where[] = " create_date<'" . $_REQUEST['udu'] . "' ";
						$url .= '&udf=' . $_REQUEST['udf'] . '&udu=' . $_REQUEST['udu'];
					}
					$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
					$total_items = (int)($indeed_db->get_transactions($affiliate_id, -1, -1, TRUE, '', '', $where));

					if ($current_page>1){
						$offset = ( $current_page - 1 ) * $limit;
					} else {
						$offset = 0;
					}
					if ($offset + $limit>$total_items){
						$limit = $total_items - $offset;
					}
					$url = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=transactions');
					require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
					$limit = 30;
					$pagination = new Indeed_Pagination(array(
							'base_url' => $url,
							'param_name' => 'uap_list_item',
							'total_items' => $total_items,
							'items_per_page' => $limit,
							'current_page' => $current_page,
					));

					$data['listing_items'] = $indeed_db->get_transactions($affiliate_id, $limit, $offset, FALSE, 'create_date', 'DESC', $where);
					$data['pagination'] = $pagination->output();
					$data['filter'] = uap_return_date_filter($url);
					$data['view_transaction_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=view_transaction_details');
					$data['update_payments'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=transactions&do_update_payments=1');
					require_once $this->admin_view_path . 'transactions.php';
					break;
				case 'unpaid':
					$limit = 30;
					$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
					$url = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=unpaid&affiliate=' . $_GET['affiliate']);
					$where = array();
					if (!empty($_REQUEST['udf']) && !empty($_REQUEST['udu'])){
						$where[] = " date>'" . $_REQUEST['udf'] . "' ";
						$where[] = " date<'" . $_REQUEST['udu'] . "' ";
						$url .= '&udf=' . $_REQUEST['udf'] . '&udu=' . $_REQUEST['udu'];
					}
					if (!empty($_GET['affiliate'])){
						$wpuid = $indeed_db->get_uid_by_affiliate_id($_GET['affiliate']);
						$username = $indeed_db->get_username_by_wpuid($wpuid);
						$full_name = $indeed_db->get_full_name_of_user($_GET['affiliate']);
						$data['subtitle'] = __('View UnPaid Referrals for', 'uap') . " $full_name ($username)";
					}
					$total_items = $indeed_db->get_unpaid_payments_for_affiliate($_GET['affiliate'], -1, -1, TRUE, '', '', $where);
					$total_items = (empty($total_items[0])) ? 0 : $total_items[0];
					if ($current_page>1){
						$offset = ( $current_page - 1 ) * $limit;
					} else {
						$offset = 0;
					}
					if ($offset + $limit>$total_items){
						$limit = $total_items - $offset;
					}
					$data['listing_items'] = $indeed_db->get_unpaid_payments_for_affiliate($_GET['affiliate'], $limit, $offset, FALSE, '', '', $where);
					if ($data['listing_items']){
						$data['payments_settings'] = array();
						$data['payments_settings'][@$_GET['affiliate']] = $indeed_db->get_affiliate_payment_type(0, @$_GET['affiliate']);
					}
					require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
					$limit = 30;
					$pagination = new Indeed_Pagination(array(
							'base_url' => $url,
							'param_name' => 'uap_list_item',
							'total_items' => $total_items,
							'items_per_page' => $limit,
							'current_page' => $current_page,
					));
					$data['pagination'] = $pagination->output();
					$data['pay_link'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=payment_form&affiliate=' . $_GET['affiliate']);
					$data['filter'] = uap_return_date_filter($url);
					require_once $this->admin_view_path . 'payments_list_all_unpaid.php';
					break;
				case 'paid_referrals':
					/// VIEW
					$limit = 30;
					$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
					$where = array();
					if (!empty($_REQUEST['udf']) && !empty($_REQUEST['udu'])){
						$where[] = " date>'" . $_REQUEST['udf'] . "' ";
						$where[] = " date<'" . $_REQUEST['udu'] . "' ";
						$url .= '&udf=' . $_REQUEST['udf'] . '&udu=' . $_REQUEST['udu'];
					}
					if (!empty($_GET['affiliate'])){
						$wpuid = $indeed_db->get_uid_by_affiliate_id($_GET['affiliate']);
						$username = $indeed_db->get_username_by_wpuid($wpuid);
						$full_name = $indeed_db->get_full_name_of_user($_GET['affiliate']);
						$data['subtitle'] = __('View Paid Referrals for', 'uap') . " $full_name ($username)";
					}
					$total_items = $indeed_db->get_paid_referrals_for_affiliate($_GET['affiliate'], -1, -1, TRUE, '', '', $where);
					$total_items = (empty($total_items[0])) ? 0 : $total_items[0];
					if ($current_page>1){
						$offset = ( $current_page - 1 ) * $limit;
					} else {
						$offset = 0;
					}
					if ($offset + $limit>$total_items){
						$limit = $total_items - $offset;
					}
					$url = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=paid_referrals&affiliate=' . $_GET['affiliate']);
					$data['listing_items'] = $indeed_db->get_paid_referrals_for_affiliate($_GET['affiliate'], $limit, $offset, FALSE, '', '', $where);
					require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
					$limit = 30;
					$pagination = new Indeed_Pagination(array(
							'base_url' => $url,
							'param_name' => 'uap_list_item',
							'total_items' => $total_items,
							'items_per_page' => $limit,
							'current_page' => $current_page,
					));
					$data['pagination'] = $pagination->output();
					$data['filter'] = uap_return_date_filter($url);
					require_once $this->admin_view_path . 'payments_list_all_paid.php';
					break;
				case 'payment_form':
					/// ACTIONS
					if (!empty($_POST['referrals'])){
						$ids = implode(',', $_POST['referrals']);
						if (empty($_GET['affiliate'])){
							/// get details for affiliate that has the selected referrals
							$data['multiple_affiliates'] = $indeed_db->get_affiliate_payment_details_for_referral_list($ids);
						} else {
							//details for one affiliate
							$data['affiliate_pay'] = $indeed_db->get_affiliate_payment_details($_GET['affiliate'], $ids);
						}
					} else if (!empty($_GET['affiliate'])){
						$data['affiliate_pay'] = $indeed_db->get_affiliate_payment_details($_GET['affiliate']);
					}

					/// VIEW
					$data['currency'] = get_option('uap_currency');
					$data['submit_link'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments&subtab=transactions');
					$data['return_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=payments');
					$data['paypal'] = $indeed_db->is_magic_feat_enable('paypal');
					$data['stripe'] = $indeed_db->is_magic_feat_enable('stripe');
					$data['stripe_v2'] = $indeed_db->is_magic_feat_enable('stripe_v2');
					require_once $this->admin_view_path . 'payment-form.php';
					break;
				case 'view_transaction_details':
					$data['listing_items'] = $indeed_db->get_transation_details(@$_GET['id']);
					if ($data['listing_items'] && isset($data['listing_items'][0]) && isset($data['listing_items'][0]['affiliate_id'])){
						$affiliate_id = $data['listing_items'][0]['affiliate_id'];
						$data['payments_settings'] = $data['payments_settings']= $indeed_db->get_affiliate_payment_type(0, $affiliate_id);
					}
					$data['payment_details_on_transaction'] = $indeed_db->get_payment_details_on_transaction_by_id(@$_GET['id']);
					require_once $this->admin_view_path . 'payments_list_all_paid.php';
					break;
			}

		}

		private function do_single_payment($post_data=array()){
			/*
			 * @param array
			 * @return boolean
			 */
			if (empty($post_data)) return;
			global $indeed_db;
			$ids = (empty($post_data["referrals_in"])) ? array() : explode(',', $post_data["referrals_in"]);
			switch ($post_data['paywith']):
				case 'bank_transfer':
					/// bank transfer
					$indeed_db->change_referrals_status($ids, $post_data['payment_status']);/// set referral payment as complete
					$data = array(
									'payment_type' => 'bank_transfer',
									'transaction_id' => '-',
									'referral_ids' => $post_data["referrals_in"],
									'affiliate_id' => $post_data['affiliate_id'],
									'amount' => $post_data['amount'],
									'currency' => $post_data['currency'],
									'create_date' => date('Y-m-d H:i:s', time()),
									'update_date' => date('Y-m-d H:i:s', time()),
									'status' => $post_data['payment_status'],
					);
					$indeed_db->add_payment($data);
					return TRUE;
					break;
				case 'paypal':
					/// paypal
					require_once UAP_PATH . 'classes/Uap_PayPal.class.php';
					$object = new Uap_PayPal();
					$email = $indeed_db->get_paypal_email_addr($post_data['affiliate_id']);
					$object->add_payment($email, $post_data['amount'], $post_data['currency']);
					$batch_id = $object->do_payout();
					if ($batch_id){
						$indeed_db->change_referrals_status($ids, 1);/// set referral payment status as pending
						$data = array(
								'payment_type' => 'paypal',
								'transaction_id' => $batch_id,
								'referral_ids' => $post_data["referrals_in"],
								'affiliate_id' => $post_data['affiliate_id'],
								'amount' => $post_data['amount'],
								'currency' => $post_data['currency'],
								'create_date' => date('Y-m-d H:i:s', time()),
								'update_date' => date('Y-m-d H:i:s', time()),
								'status' => 1,
						);
						$indeed_db->add_payment($data);
						return TRUE;
					}
					$return['error_users'][] = $indeed_db->get_wp_username_by_affiliate_id($post_data['affiliate_id']);
					return $return;
					break;
				case 'stripe':
					require_once UAP_PATH . 'classes/Uap_Stripe.class.php';
					$object = new Uap_Stripe();
					$transaction_id = $object->do_payout(0, $post_data['affiliate_id'], $post_data['amount'], $post_data['currency']);
					if ($transaction_id){
						$indeed_db->change_referrals_status($ids, 1);/// set referral payment status as pending
						$data = array(
								'payment_type' => 'stripe',
								'transaction_id' => $transaction_id,
								'referral_ids' => $post_data["referrals_in"],
								'affiliate_id' => $post_data['affiliate_id'],
								'amount' => $post_data['amount'],
								'currency' => $post_data['currency'],
								'create_date' => date('Y-m-d H:i:s', time()),
								'update_date' => date('Y-m-d H:i:s', time()),
								'status' => 1,
						);
						$indeed_db->add_payment($data);
						return TRUE;
					}
					$return['error_users'][] = $indeed_db->get_wp_username_by_affiliate_id($post_data['affiliate_id']);
					return $return;
					break;
				case 'stripe_v2':
					require_once UAP_PATH . 'classes/Uap_Stripe_V2.class.php';
					$object = new Uap_Stripe_V2();
					$transaction_id = $object->do_payout(0, $post_data['affiliate_id'], $post_data['amount'], $post_data['currency']);
					if ($transaction_id){
						$indeed_db->change_referrals_status($ids, 1);/// set referral payment status as pending
						$data = array(
								'payment_type' => 'stripe_v2',
								'transaction_id' => $transaction_id,
								'referral_ids' => $post_data["referrals_in"],
								'affiliate_id' => $post_data['affiliate_id'],
								'amount' => $post_data['amount'],
								'currency' => $post_data['currency'],
								'create_date' => date('Y-m-d H:i:s', time()),
								'update_date' => date('Y-m-d H:i:s', time()),
								'status' => 1,
						);
						$indeed_db->add_payment($data);
						return TRUE;
					}
					$return['error_users'][] = $indeed_db->get_wp_username_by_affiliate_id($post_data['affiliate_id']);
					return $return;
					break;
			endswitch;
		}

		private function do_multiple_payments($post_data=array()){
			/*
			 * @param array
			 * @return none
			 */
			if (empty($post_data)) return;
			global $indeed_db;
			switch ($post_data['paywith']):
				case 'bank_transfer':
					/// bank transfer
					$affiliates_arr = (empty($post_data['affiliates'])) ? '' : explode(',', $post_data['affiliates']);
					if ($affiliates_arr){
						foreach ($affiliates_arr as $affiliate_id){
							$ids = (empty($post_data["referrals"][$affiliate_id])) ? array() : explode(',', $post_data["referrals"][$affiliate_id]);
							$indeed_db->change_referrals_status($ids, $post_data['payment_status']);/// set referral payment status as complete
							$data = array(
									'payment_type' => 'bank_transfer',
									'transaction_id' => '-',
									'referral_ids' => $post_data["referrals"][$affiliate_id],
									'affiliate_id' => $affiliate_id,
									'amount' => $post_data["amount"][$affiliate_id],
									'currency' => $post_data['currency'][$affiliate_id],
									'create_date' => date('Y-m-d H:i:s', time()),
									'update_date' => date('Y-m-d H:i:s', time()),
									'status' => $post_data['payment_status'],
							);
							$indeed_db->add_payment($data);
						}
						return TRUE;
					}
					break;
				case 'paypal':
					/// paypal
					$return = array();
					$affiliates_arr = (empty($post_data['affiliates'])) ? '' : explode(',', $post_data['affiliates']);
					if ($affiliates_arr){
						require_once UAP_PATH . 'classes/Uap_PayPal.class.php';
						foreach ($affiliates_arr as $affiliate_id){
							$ids = (empty($post_data["referrals"][$affiliate_id])) ? array() : explode(',', $post_data["referrals"][$affiliate_id]);

							$object = new Uap_PayPal();
							$email = $indeed_db->get_paypal_email_addr($affiliate_id);
							$object->add_payment($email, $post_data["amount"][$affiliate_id], $post_data['currency'][$affiliate_id]);
							$batch_id = $object->do_payout();

							if ($batch_id){
								$indeed_db->change_referrals_status($ids, 1);///set referral payment status as pending
								$data = array(
										'payment_type' => 'paypal',
										'transaction_id' => $batch_id,
										'referral_ids' => $post_data["referrals"][$affiliate_id],
										'affiliate_id' => $affiliate_id,
										'amount' => $post_data["amount"][$affiliate_id],
										'currency' => $post_data['currency'][$affiliate_id],
										'create_date' => date('Y-m-d H:i:s', time()),
										'update_date' => date('Y-m-d H:i:s', time()),
										'status' => 1,
								);
								$indeed_db->add_payment($data);
							} else {
								if (empty($return['error_users'])){
									$return['error_users'] = array();
								}
								$return['error_users'][] = $indeed_db->get_wp_username_by_affiliate_id($affiliate_id);
							}
							unset($object);
						}
					}
					return $return;
					break;
				case 'stripe':
					$return = array();
					$affiliates_arr = (empty($post_data['affiliates'])) ? '' : explode(',', $post_data['affiliates']);
					if ($affiliates_arr){
						require_once UAP_PATH . 'classes/Uap_Stripe.class.php';
						foreach ($affiliates_arr as $affiliate_id){
							$ids = (empty($post_data["referrals"][$affiliate_id])) ? array() : explode(',', $post_data["referrals"][$affiliate_id]);
							$object = new Uap_Stripe();
							$transaction_id = $object->do_payout(0, $affiliate_id, $post_data["amount"][$affiliate_id], $post_data['currency'][$affiliate_id]);
							if ($transaction_id){
								$indeed_db->change_referrals_status($ids, 1);/// set referral payment status as pending
								$data = array(
										'payment_type' => 'stripe',
										'transaction_id' => $transaction_id,
										'referral_ids' => $post_data["referrals"][$affiliate_id],
										'affiliate_id' => $affiliate_id,
										'amount' => $post_data["amount"][$affiliate_id],
										'currency' => $post_data['currency'][$affiliate_id],
										'create_date' => date('Y-m-d H:i:s', time()),
										'update_date' => date('Y-m-d H:i:s', time()),
										'status' => 1,
								);
								$indeed_db->add_payment($data);
							} else {
								if (empty($return['error_users'])){
									$return['error_users'] = array();
								}
								$return['error_users'][] = $indeed_db->get_wp_username_by_affiliate_id($affiliate_id);
							}
							unset($object);
						}
					}
					return $return;
					break;
				case 'stripe_v2':
					$return = array();
					$affiliates_arr = (empty($post_data['affiliates'])) ? '' : explode(',', $post_data['affiliates']);
					if ($affiliates_arr){
						require_once UAP_PATH . 'classes/Uap_Stripe_V2.class.php';
						foreach ($affiliates_arr as $affiliate_id){
							$ids = (empty($post_data["referrals"][$affiliate_id])) ? array() : explode(',', $post_data["referrals"][$affiliate_id]);
							$object = new Uap_Stripe_V2();
							$transaction_id = $object->do_payout(0, $affiliate_id, $post_data["amount"][$affiliate_id], $post_data['currency'][$affiliate_id]);
							if ($transaction_id){
								$indeed_db->change_referrals_status($ids, 1);/// set referral payment status as pending
								$data = array(
										'payment_type' => 'stripe_v2',
										'transaction_id' => $transaction_id,
										'referral_ids' => $post_data["referrals"][$affiliate_id],
										'affiliate_id' => $affiliate_id,
										'amount' => $post_data["amount"][$affiliate_id],
										'currency' => $post_data['currency'][$affiliate_id],
										'create_date' => date('Y-m-d H:i:s', time()),
										'update_date' => date('Y-m-d H:i:s', time()),
										'status' => 1,
								);
								$indeed_db->add_payment($data);
							} else {
								if (empty($return['error_users'])){
									$return['error_users'] = array();
								}
								$return['error_users'][] = $indeed_db->get_wp_username_by_affiliate_id($affiliate_id);
							}
							unset($object);
						}
					}
					return $return;
					break;
			endswitch;
		}

		private function print_notifications(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;

			if (!empty($_POST['save'])){
				/// SAVE
				$indeed_db->save_notification($_POST);
			} else if (!empty($_POST['delete_notification'])){
				/// DELETE
				$indeed_db->delete_notification($_POST['delete_notification']);
			}

			$data['form_action_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=notifications');
			$data['subtab'] = (empty($_GET['subtab'])) ? 'list' : $_GET['subtab'];
			$data['url-add_edit'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=notifications&subtab=add_edit');
			$data['actions_available'] = array(
												'admin_user_register' => __('Affiliate Register - Admin Notification', 'uap'),
												'register' => __('Affiliate Register - User Notification', 'uap'),
												'affiliate_account_approve' => __('Affiliate - Approve Account', 'uap'),
												'affiliate_profile_delete' => __('Affiliate - Delete Account', 'uap'),
												'user_update' => __('User Profile Updates', 'uap'),
												'rank_change' => __('Affiliate get new Rank', 'uap'),
												'reset_password_process' => __('Reset Password - Start Process', 'uap'),
												'reset_password' => __('Reset Password - New Password', 'uap'),
												'change_password' => __('Change Password', 'uap'),
												'admin_on_aff_change_rank' => __('Admin - Affiliate get new Rank', 'uap'),
												'admin_affiliate_update_profile' => __('Admin - Affiliate update profile', 'uap'),
												'affiliate_payment_fail' => __('Affiliate - Payment Inform - Fail', 'uap'),
												'affiliate_payment_pending' => __('Affiliate - Payment Inform - Pending', 'uap'),
												'affiliate_payment_complete' => __('Affiliate - Payment Inform - Complete', 'uap'),
												'register_lite_send_pass_to_user' => __('Affiliate - Send generated password', 'uap'),
												'email_check' => __('Affiliate - Double E-mail Verification Request', 'uap'),
												'email_check_success' => __('Affiliate - Double E-mail Verification Validated', 'uap'),
			);
			$data['email_verification'] = $indeed_db->is_magic_feat_enable('email_verification');
			if (empty($data['email_verification'])){
				unset($data['actions_available']['email_check']);
				unset($data['actions_available']['email_check_success']);
			}
			$data['ranks'] = $indeed_db->get_rank_list();

			if ($data['subtab']=='add_edit'){
				$data['ranks_available'] = array( -1 => __('All', 'uap') ) + $data['ranks'];
				//and the rest of ranks..

				$id = (empty($_GET['id'])) ? 0 : $_GET['id'];
				$metas = $indeed_db->get_notification($id);
				$data = array_merge($data, $metas);
			} else {
				$data['listing_items'] = $indeed_db->get_notifications();
			}

			if ($data['subtab']=='add_edit'){
				require_once $this->admin_view_path . 'notifications-add_edit.php';
			} else {
				require_once $this->admin_view_path . 'notifications-list.php';
			}

		}

		private function print_showcases(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			$data['url_register_settings'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=register');
			$data['url_login'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=login');
			$data['url_account_page'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=account_page');
			$data['url_top_affiliates'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=top_affiliates');
			require_once $this->admin_view_path . 'showcases.php';
		}

		private function print_reports(){
			/*
			 * @param none
			 * @return string
			 */
			$data['submenu'] = array(
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=reports&subtab=reports') => __('Reports', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=reports&subtab=achievements') => __('Achievements', 'uap'),
			);
			require_once $this->admin_view_path . 'submenu.php';
			$this->print_top_messages();

			global $indeed_db;
			$subtab = (empty($_GET['subtab'])) ? 'reports' : $_GET['subtab'];
			if ($subtab=='reports'){
				$data['select_values'] = array(
						'today' => __('Today', 'uap'),
						'yesterday' => __('Yesterday', 'uap'),
						'last_week' => __('Last Week', 'uap'),
						'last_month' => __('Last Month', 'uap'),
						'all_time' => __('All Time', 'uap'),
				);
				$data['selected'] = (isset($_POST['search'])) ? $_POST['search'] : 'all_time';
				$affiliate_id = (empty($_REQUEST['affiliate_id'])) ? 0 : $_REQUEST['affiliate_id'];
				if ($affiliate_id){
					$wpuid = $indeed_db->get_uid_by_affiliate_id($affiliate_id);
					$username = $indeed_db->get_username_by_wpuid($wpuid);
					$full_name = $indeed_db->get_full_name_of_user($affiliate_id);
					$data['subtitle'] = __('View Referrals for') . " $full_name ($username)";
				}

				$data['reports'] = $indeed_db->get_stats_for_reports($data['selected'], $affiliate_id);
				$data['currency'] = get_option('uap_currency');

				/*********** GRAPHS STUFF *******/
				if ($data['selected']=='today' || $data['selected']=='yesterday'){
					$data['tick_type'] = 'hour';
				} else if ($data['selected']=='last_week' || $data['selected']=='last_month'){
					$data['tick_type'] = 'day';
				} else {
					$data['tick_type'] = 'month';
				}
				/// GRAPH VISITS
				$data['visit_graph'] = $indeed_db->get_visits_for_graph($data['selected'], 'all', $affiliate_id);
				$data['visit_graph_success'] = $indeed_db->get_visits_for_graph($data['selected'], 'success', $affiliate_id);
				/// GRAPH REFERRALS
				$data['referrals_graph'] = $indeed_db->get_referrals_for_graph($data['selected'], -1, $affiliate_id);
				$data['referrals_graph-refuse'] = $indeed_db->get_referrals_for_graph($data['selected'], 0, $affiliate_id);
				$data['referrals_graph-unverified'] = $indeed_db->get_referrals_for_graph($data['selected'], 1, $affiliate_id);
				$data['referrals_graph-verified'] = $indeed_db->get_referrals_for_graph($data['selected'], 2, $affiliate_id);

				require_once $this->admin_view_path . 'reports.php';
			} else if ($subtab=='achievements') {
				$search = (empty($_POST['search'])) ? '' : $_POST['affiliate_username'];
				$data['current_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=reports&subtab=achievements_for_affiliate');
				$data['history'] = $indeed_db->get_last_rank_achievements(50, $search);
				require_once $this->admin_view_path . 'achievements.php';
			}
		}

		private function print_settings(){
			/*
			 * @param none
			 * @return string
			 */
			/// PRINT SUBMENU
			$data['submenu'] = array(
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=settings&subtab=general') => __('General Settings', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=settings&subtab=admin_workflow') => __('Admin Workflow', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=settings&subtab=public_workflow') => __('Public Workflow', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=settings&subtab=notification_settings') => __('Notifications', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=settings&subtab=default_pages') => __('Default Pages', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=settings&subtab=redirects') => __('Default Redirects', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=settings&subtab=access') => __('Access', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=settings&subtab=captcha') => __('Captcha', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=settings&subtab=uploads') => __('Upload Settings', 'uap'),
			);
			require_once $this->admin_view_path . 'submenu.php';
			///

			$this->print_top_messages();
			global $indeed_db;
			$data['subtab'] = (empty($_GET['subtab'])) ? 'general' : $_GET['subtab'];

			switch ($data['subtab']){
				case 'general':
					if (!empty($_POST['save'])){
						$indeed_db->save_settings_wp_option('general-settings', $_POST);
					}
					$data['metas'] = $indeed_db->return_settings_from_wp_option('general-settings');
					require_once $this->admin_view_path . 'settings-general.php';
					break;

				case 'redirects':
					if (!empty($_POST['save'])){
						$indeed_db->save_settings_wp_option('general-redirects', $_POST);
					}
					$data['metas'] = $indeed_db->return_settings_from_wp_option('general-redirects');
					require_once $this->admin_view_path . 'settings-redirects.php';
					break;
				case 'default_pages':
					if (!empty($_POST['save'])){
						$indeed_db->save_settings_wp_option('general-default_pages', $_POST);
					}
					$data['metas'] = $indeed_db->return_settings_from_wp_option('general-default_pages');
					require_once $this->admin_view_path . 'settings-default_pages.php';
					break;
				case 'captcha':
					if (!empty($_POST['save'])){
						$indeed_db->save_settings_wp_option('general-captcha', $_POST);
					}
					$data['metas'] = $indeed_db->return_settings_from_wp_option('general-captcha');
					require_once $this->admin_view_path . 'settings-captcha.php';
					break;
				case 'uploads':
					if (!empty($_POST['save'])){
						$indeed_db->save_settings_wp_option('general-uploads', $_POST);
					}
					$data['metas'] = $indeed_db->return_settings_from_wp_option('general-uploads');
					require_once $this->admin_view_path . 'settings-uploads.php';
					break;
				case 'notification_settings':
					if (!empty($_POST['save'])){
						$indeed_db->save_settings_wp_option('general-notification', $_POST);
					}
					$data['metas'] = $indeed_db->return_settings_from_wp_option('general-notification');
					require_once $this->admin_view_path . 'notification_settings.php';
					break;
				case 'access':
					if (!empty($_POST['save'])){
						update_option('uap_dashboard_allowed_roles', $_POST['uap_dashboard_allowed_roles']);
					}
					$meta_value = get_option('uap_dashboard_allowed_roles');
					$meta_values = (empty($meta_value)) ? array() : explode(',', $meta_value);
					require_once $this->admin_view_path . 'access.php';
					break;
				case 'admin_workflow':
					if (!empty($_POST['save'])){
						$data['metas'] = $indeed_db->return_settings_from_wp_option('general-admin_workflow');
						$indeed_db->save_settings_wp_option('general-admin_workflow', $_POST);
						if (!empty($data['metas']['uap_update_ranks_interval']) && strcmp($data['metas']['uap_update_ranks_interval'], $_POST['uap_update_ranks_interval'])<>0){
							/// cron settings has been change
							require_once UAP_PATH . 'classes/Uap_Cron_Jobs.class.php';
							$cron_object = new Uap_Cron_Jobs();
							$cron_object->update_cron_time($_POST['uap_update_ranks_interval']);
						}
					}
					$data['metas'] = $indeed_db->return_settings_from_wp_option('general-admin_workflow');
					require_once $this->admin_view_path . 'settings-admin_workflow.php';
					break;
				case 'public_workflow':
					if (!empty($_POST['save'])){
						$indeed_db->save_settings_wp_option('general-public_workflow', $_POST);
					}
					$data['metas'] = $indeed_db->return_settings_from_wp_option('general-public_workflow');
					$data['payment_types'] = $indeed_db->get_payment_types_available();
					require_once $this->admin_view_path . 'settings-public_workflow.php';
					break;
			}
		}

		private function print_register(){
			/*
			 * @param none
			 * @return string
			 */

			/// PRINT SUBMENU
			$data['submenu'] = array(
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=register&subtab=register_showcase') => __('Register Showcase', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=register&subtab=custom_messages') => __('Custom Messages', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=register&subtab=custom_fields') => __('Custom Fields', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=opt_in') => __('Opt-In Settings', 'uap'),
			);
			require_once $this->admin_view_path . 'submenu.php';
			///

			$this->print_top_messages();
			global $indeed_db;

			$subtab = (empty($_GET['subtab'])) ? 'register_showcase' : $_GET['subtab'];
			switch ($subtab){
				case 'register_showcase':
					/// REGISTER SETTINGS
					if (isset($_POST['save'])){
						$indeed_db->save_settings_wp_option('register', $_POST);
					}
					$data['metas'] = $indeed_db->return_settings_from_wp_option('register', FALSE, FALSE);
					require_once $this->admin_view_path . 'register-settings.php';
					break;
				case 'custom_fields':
					/// SAVE/UPDATE/DELETE
					if (isset($_POST['delete_custom_field']) && $_POST['delete_custom_field']!=''){
						$indeed_db->register_delete_custom_field($_POST['delete_custom_field']);
					} else if (!empty($_POST['save_field'])){
						$indeed_db->register_save_custom_field($_POST);
					} else if (!empty($_POST['save'])){
						//update order of fields...
						$indeed_db->register_update_order($_POST);
					}
					///
					/// MANAGE CUSTOM FIELDS
					$data['register_fields'] = $indeed_db->register_get_custom_fields();
					ksort($data['register_fields']);
					$data['url_edit_custom_fields'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=register&subtab=custom_fields-add_edit');
					require_once $this->admin_view_path . 'register-custom_fields.php';
					break;
				case 'custom_fields-add_edit':
					/// ADD/UPDATE CUSTOM FIELD
					$data['form_submit'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=register&subtab=custom_fields');
					$data['field_types'] = array(
													'text' => __('Text', 'uap'),
													'textarea' => __('Textarea', 'uap'),
													'date' => __('Date Picker', 'uap'),
													'number' => __('Number', 'uap'),
													'select' => __('Select', 'uap'),
													'multi_select' => __('Multiselect Box', 'uap'),
													'checkbox' => __('Checkbox', 'uap'),
													'radio' => __('Radio', 'uap'),
													'file' => __('File Upload', 'uap'),
													'plain_text' => __('Plain Text', 'uap'),
													'conditional_text' => __('Verification Code', 'uap'),
					);
					$data['id'] = (empty($_GET['id'])) ? '' : $_GET['id'];
					$data['metas'] = $indeed_db->register_get_field($data['id']);
					$data['disabled_items'] = array('confirm_email', 'tos', 'name', 'recaptcha', 'uap_avatar', 'uap_country');
					$data['disabled'] = (in_array($data['metas']['name'], $data['disabled_items'])) ? 'disabled' : '';
					$data['register_fields'] = array('-1'=>'...') + $indeed_db->register_get_custom_fields(TRUE, array('social_media', 'upload_image', 'plain_text', 'file', 'capcha'));
					if (empty($data['metas']['conditional_logic_corresp_field'])){
						$data['metas']['conditional_logic_corresp_field'] = -1;
					}
					require_once $this->admin_view_path . 'register-custom_fields_add_edit.php';
					break;
				case 'custom_messages':
					if (isset($_POST['save'])){
						$indeed_db->save_settings_wp_option('register-msg', $_POST);
					}
					$data['metas'] = $indeed_db->return_settings_from_wp_option('register-msg', FALSE, FALSE);
					require_once $this->admin_view_path . 'register-custom_messages.php';
					break;
			}
		}

		private function print_login(){
			/*
			 * @param none
			 * @return string
			 */
			/// PRINT SUBMENU
			$data['submenu'] = array(
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=login') => __('Login Showcase', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=login&subtab=custom_messages') => __('Custom Messages', 'uap')
			);
			require_once $this->admin_view_path . 'submenu.php';
			///

			$this->print_top_messages();
			global $indeed_db;
			$data['subtab'] = (empty($_GET['subtab'])) ? '' : $_GET['subtab'];

			if ($data['subtab']=='custom_messages'){
				if (!empty($_POST['save'])){
					$indeed_db->save_settings_wp_option('login-messages', $_POST);
				}
				$data['metas'] = $indeed_db->return_settings_from_wp_option('login-messages');
				require_once $this->admin_view_path . 'login-custom_messages.php';
			} else {
				if (!empty($_POST['save'])){
					$indeed_db->save_settings_wp_option('login', $_POST);
				}
				$data['login_templates'] = array(

													9 => '(#9) '.__('Radius Gradient Theme', 'uap'),
													8 => '(#8) '.__('Border Pink Theme', 'uap'),
													10 => '(#10) '.__('Simple BootStrap Theme', 'uap'),
													11 => '(#11) '.__('Flat new Style', 'uap'),
													13 => '(#13) '.__('Ultimate Member', 'uap'),
													12 => '(#12) '.__('MegaBox', 'uap'),
													1 => '(#1) '.__('Standard Theme', 'uap'),
													2 =>'(#2) '. __('Basic Theme', 'uap'),
													3 => '(#3) '.__('BlueBox Theme', 'uap'),
													4 =>  '(#4) '.__('Simple Green Theme', 'uap'),
													5 => '(#5) '.__('Labels Theme', 'uap'),
													6 => '(#6) '.__('Premium Theme', 'uap'),
													7 => '(#7) '.__('Double Long Theme', 'uap')
				);
				$data['metas'] = $indeed_db->return_settings_from_wp_option('login');
				require_once $this->admin_view_path . 'login.php';
			}
		}

		private function print_account_page(){
			/*
			 * @param none
		 	 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('account_page', $_POST);
				//indeed_debug_var($_POST);
			}
			$data['top_themes'] = array(
									'uap-ap-top-theme-1' => __('Theme 1', 'uap'),
									'uap-ap-top-theme-2' => __('Theme 2', 'uap'),
									'uap-ap-top-theme-3' => __('Theme 3', 'uap'),
			);
			$data['themes'] = array(
									'uap-ap-theme-1' => __('Theme 1', 'uap'),
									'uap-ap-theme-2' => __('Theme 2', 'uap'),
									'uap-ap-theme-3' => __('Theme 3', 'uap'),
			);
			$data['metas'] = $indeed_db->return_settings_from_wp_option('account_page');
			$data['available_tabs'] = $indeed_db->account_page_get_menu();
			require_once $this->admin_view_path . 'account-page.php';
		}

		private function print_opt_in(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('opt_in', $_POST);
			}
			$email_list = get_option('uap_email_list');
			$data['email_list'] = (empty($email_list)) ? '' : $email_list;
			$data['metas'] = $indeed_db->return_settings_from_wp_option('opt_in');
			require_once UAP_PATH . 'classes/UapMailServices.class.php';
			$obj = new UapMailServices();
			require_once $this->admin_view_path . 'opt_in.php';
		}

		private function print_visits(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['delete_visits'])){
				$indeed_db->delete_visits($_POST['delete_visits']);
			}

			$url = admin_url('admin.php?page=ultimate_affiliates_pro&tab=visits');
			$where = array();
			if (!empty($_REQUEST['udf']) && !empty($_REQUEST['udu'])){
				$where[] = " v.visit_date>'" . $_REQUEST['udf'] . "' ";
				$where[] = " v.visit_date<'" . $_REQUEST['udu'] . "' ";
				$url .= '&udf=' . $_REQUEST['udf'] . '&udu=' . $_REQUEST['udu'];
			}
			if (!empty($_REQUEST['aff_u'])){
				$where[] = "u.user_login LIKE '%" . $_REQUEST['aff_u'] . "%'";
				$url .= '&aff_u=' . $_REQUEST['aff_u'];
			}
			if (!empty($_REQUEST['affiliate_id'])){
				$where[] = "v.affiliate_id=" . $_REQUEST['affiliate_id'];
				$url .= '&affiliate_id=' . $_REQUEST['affiliate_id'];
				$wpuid = $indeed_db->get_uid_by_affiliate_id($_REQUEST['affiliate_id']);
				$username = $indeed_db->get_username_by_wpuid($wpuid);
				$full_name = $indeed_db->get_full_name_of_user($_REQUEST['affiliate_id']);
				$data['subtitle'] = __('View Visits for', 'uap') . " $full_name ($username)";
			}

			$data['base_list_url'] = $url;
			$limit = (empty($_GET['uap_limit'])) ? 25 : $_GET['uap_limit'];
			$url .= '&uap_limit=' . $limit;
			$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
			$total_items = (int)$indeed_db->get_visits(-1, -1, TRUE, '', '', $where );
			if ($current_page>1){
				$offset = ( $current_page - 1 ) * $limit;
			} else {
				$offset = 0;
			}
			if ($offset + $limit>$total_items){
				$limit = $total_items - $offset;
			}
			$data['listing_items'] = $indeed_db->get_visits($limit, $offset, FALSE, 'v.visit_date', 'DESC', $where);
			$data['filter'] = uap_return_date_filter($url, array(), TRUE);

			require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
			$limit = (empty($_GET['uap_limit'])) ? 25 : $_GET['uap_limit'];

			$pagination = new Indeed_Pagination(array(
														'base_url' => $url,
														'param_name' => 'uap_list_item',
														'total_items' => $total_items,
														'items_per_page' => $limit,
														'current_page' => $current_page,
			));
			$data['pagination'] = $pagination->output();

			require_once $this->admin_view_path . 'visits.php';
		}

		private function print_magic_features(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$data['feature_types'] = $indeed_db->get_magic_feat_item_list();
			if (!UAP_LICENSE_SET){
				/// remove some features
				$data['feature_types']['stripe']['enabled'] = FALSE;
				$data['feature_types']['stripe']['link'] = '#';
				$data['feature_types']['stripe']['extra_class'] = 'uap-magic-feat-not-available';
				$data['feature_types']['paypal']['enabled'] = FALSE;
				$data['feature_types']['paypal']['link'] = '#';
				$data['feature_types']['paypal']['extra_class'] = 'uap-magic-feat-not-available';
				$data['feature_types']['lifetime_commissions']['enabled'] = FALSE;
				$data['feature_types']['lifetime_commissions']['link'] = '#';
				$data['feature_types']['lifetime_commissions']['extra_class'] = 'uap-magic-feat-not-available';
				$data['feature_types']['mlm']['enabled'] = FALSE;
				$data['feature_types']['mlm']['link'] = '#';
				$data['feature_types']['mlm']['extra_class'] = 'uap-magic-feat-not-available';
				$data['feature_types']['bonus_on_rank']['enabled'] = FALSE;
				$data['feature_types']['bonus_on_rank']['link'] = '#';
				$data['feature_types']['bonus_on_rank']['extra_class'] = 'uap-magic-feat-not-available';
				$data['feature_types']['wallet']['enabled'] = FALSE;
				$data['feature_types']['wallet']['link'] = '#';
				$data['feature_types']['wallet']['extra_class'] = 'uap-magic-feat-not-available';
				$data['feature_types']['referral_notifications']['enabled'] = FALSE;
				$data['feature_types']['referral_notifications']['link'] = '#';
				$data['feature_types']['referral_notifications']['extra_class'] = 'uap-magic-feat-not-available';
				$data['feature_types']['admin_referral_notifications']['enabled'] = FALSE;
				$data['feature_types']['admin_referral_notifications']['link'] = '#';
				$data['feature_types']['admin_referral_notifications']['extra_class'] = 'uap-magic-feat-not-available';
				$data['feature_types']['periodically_reports']['enabled'] = FALSE;
				$data['feature_types']['periodically_reports']['link'] = '#';
				$data['feature_types']['periodically_reports']['extra_class'] = 'uap-magic-feat-not-available';
				$data['feature_types']['stripe_v2']['enabled'] = FALSE;
				$data['feature_types']['stripe_v2']['link'] = '#';
				$data['feature_types']['stripe_v2']['extra_class'] = 'uap-magic-feat-not-available';
				$data['feature_types']['pay_per_click']['enabled'] = FALSE;
				$data['feature_types']['pay_per_click']['link'] = '#';
				$data['feature_types']['pay_per_click']['extra_class'] = 'uap-magic-feat-not-available';
				$data['feature_types']['cpm_commission']['enabled'] = FALSE;
				$data['feature_types']['cpm_commission']['link'] = '#';
				$data['feature_types']['cpm_commission']['extra_class'] = 'uap-magic-feat-not-available';
			}
			/// PRINT SUBMENU
			foreach ($data['feature_types'] as $k=>$v){
				$data['submenu'][$v['link']] = $v['label'];
			}
			require_once $this->admin_view_path . 'submenu.php';
			///

			if (!empty($_GET['subtab'])){
				switch ($_GET['subtab']){
					case 'sign_up_referrals':
						$this->print_sign_up_referrals();
						break;
					case 'lifetime_commissions':
						$this->print_lifetime_commissions();
						break;
					case 'reccuring_referrals':
						$this->print_reccuring_referrals();
						break;
					case 'social_share':
						$this->print_social_share();
						break;
					case 'paypal':
						$this->print_paypal();
						break;
					case 'allow_one_referrence':
						$this->print_allow_own_referrence();
						break;
					case 'mlm':
						$this->print_mlm();
						break;
					case 'edit_affiliate_referral_relation':
						$this->edit_affiliate_referral_relation();
						break;
					case 'rewrite_referrals':
						$this->print_rewrite_referrals();
						break;
					case 'bonus_on_rank':
						$this->print_bonus_on_rank();
						break;
					case 'opt_in':
						$this->print_opt_in();
						break;
					case 'stripe':
						$this->print_stripe();
						break;
					case 'coupons':
						$this->print_coupons();
						break;
					case 'friendly_links':
						$this->print_friendly_links();
						break;
					case 'custom_affiliate_slug':
						$this->print_custom_affiliate_slug();
						break;
					case 'mlm_view_affiliate_children':
						$this->print_mlm_view_affiliate_children();
						break;
					case 'wallet':
						$this->print_wallet();
						break;
					case 'checkout_select_referral':
						$this->print_checkout_select_referral();
						break;
					case 'woo_account_page':
						$this->print_woo_account_page();
						break;
					case 'bp_account_page':
						$this->print_bp_account_page();
						break;
					case 'referral_notifications':
						$this->print_referral_notifications();
						break;
					case 'admin_referral_notifications':
						$this->print_admin_referral_notifications();
						break;
					case 'periodically_reports':
						$this->print_periodically_reports();
						break;
					case 'qr_code':
						$this->print_qr_code();
						break;
					case 'email_verification':
						$this->print_email_verification();
						break;
					case 'custom_currencies':
						$this->print_custom_currencies();
						break;
					case 'source_details':
						$this->print_source_details();
						break;
					case 'wp_social_login':
						$this->print_wp_social_login();
						break;
					case 'stripe_v2':
						$this->print_stripe_v2();
						break;
					case 'pushover':
						$this->print_pushover();
						break;
					case 'max_amount':
						$this->print_max_amount();
						break;
					case 'simple_links':
						$this->print_simple_links();
						break;
					case 'account_page_menu':
						$this->print_account_page_menu();
						break;
					case 'migrate_affiliates_pro':
						$this->migrate_affiliates_pro();
						break;
					case 'migrate_wp_affiliates':
						$this->migrate_wp_affiliates();
						break;
					case 'migrate_affiliate_wp':
						$this->migrate_affiliate_wp();
						break;
					case 'ranks_pro':
						$this->ranks_pro();
						break;
					case 'landing_pages':
						$this->landing_pages();
						break;
					case 'pay_per_click':
						$this->pay_per_click();
						break;
					case 'cpm_commission':
						$this->cpm_commission();
						break;
					case 'pushover_referral_notifications':
						$this->pushover_referral_notifications();
						break;
					case 'rest_api':
						$this->rest_api();
						break;
				}
			} else {
				/// LIST THE FEATURES
				$this->print_top_messages();
				require_once $this->admin_view_path . 'magic_features.php';
			}
		}

		private function print_wp_social_login(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('wp_social_login', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('wp_social_login');
			$data['pages'] = $indeed_db->uap_get_all_pages();
			$data['ranks'] = $indeed_db->get_ranks();
			$data['ranks'] = uap_reorder_ranks($data['ranks']);//reorder
			require_once $this->admin_view_path . 'wp_social_login.php';
		}

		private function print_sign_up_referrals(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('sign_up_referrals', $_POST);
				if (isset($_POST['signup_ranks_value'])){
					foreach ($_POST['signup_ranks_value'] as $id=>$value){
						if ($value==''){
							$value = -1;
						}
						$indeed_db->update_rank_column('sign_up_amount_value', $id, $value);
					}
				}
			}
			$data['amount_types'] = $this->amount_type_list;
			$data['metas'] = $indeed_db->return_settings_from_wp_option('sign_up_referrals');
			$data['rank_list'] = $indeed_db->get_rank_list();
			$data['rank_value_array'] = $indeed_db->get_column_value_for_each_rank('sign_up_amount_value');
			require_once $this->admin_view_path . 'sign_up_referrals.php';
		}

		private function print_lifetime_commissions(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_GET['delete'])){
				$indeed_db->affiliate_referrals_delete_relation($_GET['delete']);
			} else if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('lifetime_commissions', $_POST);
				if (isset($_POST['lifetime_ranks_amount_type'])){
					foreach ($_POST['lifetime_ranks_amount_type'] as $id=>$value){
						$indeed_db->update_rank_column('lifetime_amount_type', $id, $value);
					}
				}
				if (isset($_POST['lifetime_ranks_value'])){
					foreach ($_POST['lifetime_ranks_value'] as $id=>$value){
						if ($value==''){
							$value = -1;
						}
						$indeed_db->update_rank_column('lifetime_amount_value', $id, $value);
					}
				}
			} else if (!empty($_POST['search'])){
				$data['affiliate_referrals_table_data'] = $indeed_db->get_affiliate_user_relation($_POST['affiliate_username'], $_POST['username']);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('lifetime_commissions');
			$data['rank_list'] = $indeed_db->get_rank_list();
			$data['default_rank_amount_type_array'] = $indeed_db->get_column_value_for_each_rank('amount_type');
			$data['default_rank_amount_value_array'] = $indeed_db->get_column_value_for_each_rank('amount_value');
			$data['rank_amount_type_array'] = $indeed_db->get_column_value_for_each_rank('lifetime_amount_type');
			$data['rank_amount_value_array'] = $indeed_db->get_column_value_for_each_rank('lifetime_amount_value');
			$data['amount_types'] = $this->amount_type_list;
			$data['current_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=lifetime_commissions');
			$data['edit_relation'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=edit_affiliate_referral_relation');
			require_once $this->admin_view_path . 'lifetime_commissions.php';
		}

		private function edit_affiliate_referral_relation(){
			/*
			 * @param none
			 * @return none
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->update_affiliate_referral_user_relation($_POST['id'], $_POST['affiliate']);
			}
			$data['edit_data'] = $indeed_db->get_affiliate_user_relation_by_id(@$_GET['id']);
			$data['affiliates'] = $indeed_db->get_affiliates_username_id_pair();
			require_once $this->admin_view_path . 'edit_affiliate_referral_relation.php';
		}

		private function print_reccuring_referrals(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('reccuring_referrals', $_POST);
				if (isset($_POST['reccuring_ranks_amount_type'])){
					foreach ($_POST['reccuring_ranks_amount_type'] as $id=>$value){
						$indeed_db->update_rank_column('reccuring_amount_type', $id, $value);
					}
				}
				if (isset($_POST['reccuring_ranks_value'])){
					foreach ($_POST['reccuring_ranks_value'] as $id=>$value){
						if ($value==''){
							$value = -1;
						}
						$indeed_db->update_rank_column('reccuring_amount_value', $id, $value);
					}
				}
			}

			$data['metas'] = $indeed_db->return_settings_from_wp_option('reccuring_referrals');
			$data['rank_list'] = $indeed_db->get_rank_list();
			$data['default_rank_amount_type_array'] = $indeed_db->get_column_value_for_each_rank('amount_type');
			$data['default_rank_amount_value_array'] = $indeed_db->get_column_value_for_each_rank('amount_value');
			$data['rank_amount_type_array'] = $indeed_db->get_column_value_for_each_rank('reccuring_amount_type');
			$data['rank_amount_value_array'] = $indeed_db->get_column_value_for_each_rank('reccuring_amount_value');
			$data['amount_types'] = $this->amount_type_list;
			require_once $this->admin_view_path . 'reccuring_referrals.php';
		}

		private function print_social_share(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('social_share', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('social_share');
			$data['social_share_page'] = admin_url('admin.php?page=ism_manage&tab=shortcode');
			require_once $this->admin_view_path . 'social_share.php';
		}

		private function print_paypal(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			$phpversion = phpversion();
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('paypal', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('paypal');
			require_once $this->admin_view_path . 'paypal.php';
		}

		private function print_stripe(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('stripe', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('stripe');
			require_once $this->admin_view_path . 'stripe.php';
		}

		private function print_stripe_v2(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('stripe_v2', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('stripe_v2');
			require_once $this->admin_view_path . 'stripe_v2.php';
		}

		/*
		 * @param none
		 * @return string
		 */
		private function print_pushover(){
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['uap_save'])){
				$indeed_db->save_settings_wp_option('pushover', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('pushover');
			require_once $this->admin_view_path . 'pushover.php';
		}

		private function print_coupons(){
			/*
			 * @param none
			 * @return string
			 */
			 global $indeed_db;
			 if (!empty($_POST['delete_coupons'])){
			 	$indeed_db->delete_coupon_affiliate_pair($_POST['delete_coupons']);
			 }
			 $data['amount_types'] = $this->amount_type_list;
			 $data['url-add_edit'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=coupons');
			 $data['url-manage'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=coupons');
			 if (isset($_GET['add_edit'])){
			 	$data['metas'] = $indeed_db->get_coupon_data($_GET['add_edit']);
				$data['affiliate'] = $indeed_db->get_wp_username_by_affiliate_id($data['metas']['affiliate_id']);
			 	require_once $this->admin_view_path . 'coupons-add_edit.php';
			 } else {
				if (!empty($_POST['save'])){
					$saved = $indeed_db->save_coupon_affiliate_pair($_POST);
					if ($saved<1){
						$data['errors'] = __('Be sure that you have filled all the reguired fields: Code, Affiliate, Amount Value.', 'uap');
					}
				} else if (!empty($_POST['delete_coupon'])){
					$indeed_db->delete_coupon_affiliate_pair($_POST['delete_coupon']);
				}
				if (!empty($_POST['save'])){
					$indeed_db->save_settings_wp_option('coupons', $_POST);
				}
				$data['metas'] = $indeed_db->return_settings_from_wp_option('coupons');
				$data['listing_items'] = $indeed_db->get_coupons_affiliates_pairs();
			 	require_once $this->admin_view_path . 'coupons-list.php';
			 }
		}

		private function print_allow_own_referrence(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('allow_own_referrence', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('allow_own_referrence');
			require_once $this->admin_view_path . 'allow_own_referrence.php';
		}

		private function print_mlm(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('mlm', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('mlm');
			$data['amount_types'] = $this->amount_type_list;
			$data['matrix_types'] = array(
													'unilevel' => __('The Unilevel Plan', 'uap'),
													'force' => __('The Force Matrix Plan', 'uap'),
													'binary' => __('Binary Plan', 'uap'),
			);
			$data['search_submit_url'] = admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=mlm_view_affiliate_children');
			require_once $this->admin_view_path . 'mlm.php';
		}

		private function print_mlm_view_affiliate_children(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			$affiliate_name = (empty($_REQUEST['affiliate_name'])) ? 0 : $_REQUEST['affiliate_name'];

			$affiliate_id = $indeed_db->get_affiliate_id_by_username($affiliate_name);
			require_once UAP_PATH . 'classes/MLM_Get_All_Children.class.php';
			$children_object = new MLM_Get_All_Children($affiliate_id);
			$data['items'] = $children_object->get_results();
			$data['parent'] = $indeed_db->mlm_get_parent($affiliate_id);
			if (empty($data['parent'])){
					$data['parent'] = '';
			} else {
					$parentUid = $indeed_db->get_uid_by_affiliate_id($data['parent']);
					$data['parent'] = $indeed_db->get_username_by_wpuid($parentUid);
			}
			require_once $this->admin_view_path . 'mlm-view_affiliate_children.php';
		}

		private function print_rewrite_referrals(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('rewrite_referrals', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('rewrite_referrals');
			require_once $this->admin_view_path . 'rewrite_referrals.php';
		}

		private function print_bonus_on_rank(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('bonus_on_rank', $_POST);
				if (isset($_POST['bonus_ranks_value'])){
					foreach ($_POST['bonus_ranks_value'] as $id=>$value){
						$indeed_db->update_rank_column_force_empty('bonus', $id, $value);
					}
				}
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('bonus_on_rank');
			$data['rank_list'] = $indeed_db->get_rank_list();
			$data['rank_value_array'] = $indeed_db->get_column_value_for_each_rank('bonus');
			$data['amount_types'] = $this->amount_type_list;

			require_once $this->admin_view_path . 'bonus_on_rank.php';
		}

		private function print_friendly_links(){
			/*
			 * @oaram none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('friendly_links', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('friendly_links');
			require_once $this->admin_view_path . 'friendly_links.php';
		}

		private function print_wallet(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('wallet', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('wallet');
			require_once $this->admin_view_path . 'wallet.php';
		}

		private function print_max_amount(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('max_amount', $_POST);
			}
			$data['ranks'] = $indeed_db->get_ranks();
			$data['metas'] = $indeed_db->return_settings_from_wp_option('max_amount');
			$data['amount_types'] = $this->amount_type_list;
			require_once $this->admin_view_path . 'max_amount.php';
		}

		private function print_simple_links(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('simple_links', $_POST);
				$indeed_db->simple_links_save_link($_POST, 1);
			} else if (!empty($_GET['delete'])){
				$indeed_db->simple_links_delete_link($_GET['delete']);
			} else if (!empty($_GET['approve'])){
				$indeed_db->simple_links_approve_link($_GET['approve']);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('simple_links');

			if (!empty($_GET['affiliate_id'])){
				$where = " affiliate_id=" . $_GET['affiliate_id'] . " ";
			} else {
				$where = '';
			}
			$current_url = UAP_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$url = admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=simple_links');
			$limit = (isset($_GET['uap_limit'])) ? $_GET['uap_limit'] : 25;
			$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
			$total_items = $indeed_db->simple_links_get_counts($where);
			if ($current_page>1){
				$offset = ( $current_page - 1 ) * $limit;
			} else {
				$offset = 0;
			}
			if ($offset + $limit>$total_items){
				$limit = $total_items - $offset;
			}
			require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
			$limit = (isset($_GET['uap_limit'])) ? $_GET['uap_limit'] : 25;
			$pagination = new Indeed_Pagination(array(
					'base_url' => $current_url,
					'param_name' => 'uap_list_item',
					'total_items' => $total_items,
					'items_per_page' => $limit,
					'current_page' => $current_page,
			));
			$data['pagination'] = $pagination->output();
			$limit = 25;
			$data['items'] = $indeed_db->simple_links_get_items($limit, $offset, '', '', $where);

			require_once $this->admin_view_path . 'simple_links.php';
		}


		/*
		 * @param none
		 * @return string
		 */
		private function print_account_page_menu(){
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('account_page_menu', $_POST);
				if (!empty($_POST['slug'])){
					$indeed_db->account_page_save_custom_menu_item($_POST);
				}
			} else if (!empty($_GET['delete'])){
				$indeed_db->account_page_menu_delete_custom_item($_GET['delete']);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('account_page_menu');
			$data['menu'] = $indeed_db->account_page_get_menu(TRUE);
			$data['standard_tabs'] = $indeed_db->account_page_get_menu(TRUE, TRUE);
			$this->print_top_messages();
			require_once $this->admin_view_path . 'account_page_menu.php';
		}


		private function print_top_affiliates(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			$data['submenu'] = array(
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=top_affiliates') => __('Shortcode Generator', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=top_affiliates_settings') => __('Settings', 'uap')
			);
			require_once $this->admin_view_path . 'submenu.php';
			$this->print_top_messages();
			require_once $this->admin_view_path . 'top_affiliates.php';
		}

		private function print_top_affiliates_settings(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('top_affiliate_list', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('top_affiliate_list');

			$data['submenu'] = array(
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=top_affiliates') => __('Shortcode Generator', 'uap'),
					admin_url('admin.php?page=ultimate_affiliates_pro&tab=top_affiliates_settings') => __('Settings', 'uap')
			);
			require_once $this->admin_view_path . 'submenu.php';
			$this->print_top_messages();
			require_once $this->admin_view_path . 'top_affiliates_settings.php';
		}

		private function print_custom_affiliate_slug(){
			/*
			 * @oaram none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('custom_affiliate_slug', $_POST);
			} else if (!empty($_POST['affiliate_id']) && !empty($_POST['slug'])){
				$uid = $indeed_db->get_uid_by_affiliate_id($_POST['affiliate_id']);
				if ($uid){
					$saved = $indeed_db->save_custom_slug_for_uid($uid, $_POST['slug']);
				}
			}

			$url = admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=custom_affiliate_slug');
			$current_url = $url;
			$limit = 25;
			$current_page = (empty($_GET['uap_list_item'])) ? 1 : $_GET['uap_list_item'];
			$total_items = $indeed_db->get_all_affiliates_slug(0, 0, TRUE);

			if ($current_page>1){
				$offset = ( $current_page - 1 ) * $limit;
			} else {
				$offset = 0;
			}
			if ($offset + $limit>$total_items){
				$limit = $total_items - $offset;
			}

			require_once UAP_PATH . 'classes/Indeed_Pagination.class.php';
			$pagination = new Indeed_Pagination(array(
					'base_url' => $current_url,
					'param_name' => 'uap_list_item',
					'total_items' => $total_items,
					'items_per_page' => $limit,
					'current_page' => $current_page,
			));

			$data['pagination'] = $pagination->output();
			$limit = 25;
			$data['items'] = $indeed_db->get_all_affiliates_slug($limit, $offset);
			$data['metas'] = $indeed_db->return_settings_from_wp_option('custom_affiliate_slug');
			require_once $this->admin_view_path . 'custom_affiliate_slug.php';
		}

		private function print_checkout_select_referral(){
			/*
			 * @param none
			 * @return string
			 */
			$this->print_top_messages();
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('checkout_select_referral', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('checkout_select_referral');

			$usernames = array();
			$aff_list = '';
			if (!empty($data['metas']['uap_checkout_select_affiliate_list'])){
				$aff_list = explode(',', $data['metas']['uap_checkout_select_affiliate_list']);
				foreach ($aff_list as $id){
					$usernames[$id] = $indeed_db->get_wp_username_by_affiliate_id($id);
				}
				$usernames[-1] = 'All';
			}

			require_once $this->admin_view_path . 'checkout_select_referral.php';
		}

		private function print_woo_account_page(){
			/*
			 * @param none
			 * @return string
			 */
			 $this->print_top_messages();
			 global $indeed_db;
			 if (!empty($_POST['save'])){
			     $indeed_db->save_settings_wp_option('woo_account_page', $_POST);
			 }
			 $data['metas'] = $indeed_db->return_settings_from_wp_option('woo_account_page');
			 require_once $this->admin_view_path . 'woo_account_page.php';
		}

		private function print_bp_account_page(){
			/*
			 * @param none
			 * @return string
			 */
			 $this->print_top_messages();
			 global $indeed_db;
			 if (!empty($_POST['save'])){
			     $indeed_db->save_settings_wp_option('bp_account_page', $_POST);
			 }
			 $data['metas'] = $indeed_db->return_settings_from_wp_option('bp_account_page');
			 require_once $this->admin_view_path . 'bp_account_page.php';
		}

		private function migrate_affiliates_pro()
		{
				global $indeed_db;
				$this->print_top_messages();
				$data['ranks_available'] = $indeed_db->get_rank_list();
				require_once $this->admin_view_path . 'migrate_affiliates_pro.php';
		}

		private function migrate_wp_affiliates()
		{
				global $indeed_db;
				$this->print_top_messages();
				$data['ranks_available'] = $indeed_db->get_rank_list();
				require_once $this->admin_view_path . 'migrate_wp_affiliates.php';
		}

		private function migrate_affiliate_wp()
		{
				global $indeed_db;
				$this->print_top_messages();
				$data['ranks_available'] = $indeed_db->get_rank_list();
				require_once $this->admin_view_path . 'migrate_affiliate_wp.php';
		}

		private function ranks_pro()
		{
				global $indeed_db;
				$this->print_top_messages();
				if (!empty($_POST['save'])){
						if (get_option('uap_ranks_pro_reset_day')!=$_POST['uap_ranks_pro_reset_day']){
								$object = new \Indeed\Uap\ResetRanks();
								$object->doSchedule();
						}
						$indeed_db->save_settings_wp_option('ranks_pro', $_POST);
				}
				$data['metas'] = $indeed_db->return_settings_from_wp_option('ranks_pro');
				require_once $this->admin_view_path . 'ranks_pro.php';
		}

		private function landing_pages()
		{
				global $indeed_db;
				$this->print_top_messages();
				if (!empty($_GET['delete'])){
						$indeed_db->removeAffiliateLandingPage($_GET['delete']);
				}
				if (!empty($_POST['save'])){
						$indeed_db->save_settings_wp_option('landing_pages', $_POST);
				}
				$data['metas'] = $indeed_db->return_settings_from_wp_option('landing_pages');
				$data['items'] = $indeed_db->getLandingPages();
				require_once $this->admin_view_path . 'landing_pages.php';
		}

		private function pay_per_click()
		{
				global $indeed_db;
				$this->print_top_messages();
				if (!empty($_POST['save'])){
					$indeed_db->save_settings_wp_option('pay_per_click', $_POST);
					if (isset($_POST['pay_per_click_value'])){
						foreach ($_POST['pay_per_click_value'] as $id=>$value){
							$indeed_db->update_rank_column_force_empty('pay_per_click', $id, $value);
						}
					}
				}
				$data['metas'] = $indeed_db->return_settings_from_wp_option('pay_per_click');
				$data['rank_list'] = $indeed_db->get_rank_list();
				$data['rank_value_array'] = $indeed_db->get_column_value_for_each_rank('pay_per_click');
				$data['amount_types'] = $this->amount_type_list;

				require_once $this->admin_view_path . 'pay_per_click.php';
		}

		private function cpm_commission()
		{
				global $indeed_db;
				$this->print_top_messages();
				if (!empty($_POST['save'])){
					$indeed_db->save_settings_wp_option('cpm_commission', $_POST);
					if (isset($_POST['cpm_commission_value'])){
						foreach ($_POST['cpm_commission_value'] as $id=>$value){
							$indeed_db->update_rank_column_force_empty('cpm_commission', $id, $value);
						}
					}
				}
				$data['metas'] = $indeed_db->return_settings_from_wp_option('cpm_commission');
				$data['rank_list'] = $indeed_db->get_rank_list();
				$data['rank_value_array'] = $indeed_db->get_column_value_for_each_rank('cpm_commission');
				$data['amount_types'] = $this->amount_type_list;

				require_once $this->admin_view_path . 'cpm_commission.php';
		}

		private function pushover_referral_notifications()
		{
				global $indeed_db;
				$this->print_top_messages();
				if (!empty($_POST['save'])){
					$indeed_db->save_settings_wp_option('pushover_referral_notifications', $_POST);
				}
				$data['metas'] = $indeed_db->return_settings_from_wp_option('pushover_referral_notifications');
				require_once $this->admin_view_path . 'pushover_referral_notifications.php';
		}

		private function rest_api()
		{
				global $indeed_db;
				$this->print_top_messages();
				if (!empty($_POST['save'])){
					$indeed_db->save_settings_wp_option('rest_api', $_POST);
				}
				$data['metas'] = $indeed_db->return_settings_from_wp_option('rest_api');
				$data['base_url'] = get_option('siteurl');
				require_once $this->admin_view_path . 'rest_api.php';
		}

		private function print_referral_notifications(){
			/*
			 * @param none
			 * @return string
			 */
			 global $indeed_db;
			 if (!empty($_POST['save'])){
			     $indeed_db->save_settings_wp_option('referral_notifications', $_POST);
			 }
			 $data['metas'] = $indeed_db->return_settings_from_wp_option('referral_notifications');

			 if (!class_exists('Uap_Affiliate_Notification_Reports')){
				 require_once UAP_PATH . 'classes/Uap_Affiliate_Notification_Reports.class.php';
			 }
			 $object = new Uap_Affiliate_Notification_Reports();
			 $data['notification_constants'] = $object->notification_constants();

			 $this->print_top_messages();
			 require_once $this->admin_view_path . 'referral_notifications.php';
		}


		/**
		 * @param none
		 * @return none
		 */
		private function print_admin_referral_notifications(){
			 global $indeed_db;
			 if (!empty($_POST['save'])){
			     $indeed_db->save_settings_wp_option('admin_referral_notifications', $_POST);
			 }
			 $data['metas'] = $indeed_db->return_settings_from_wp_option('admin_referral_notifications');

			 if (!class_exists('Uap_Affiliate_Notification_Reports')){
				 require_once UAP_PATH . 'classes/Uap_Affiliate_Notification_Reports.class.php';
			 }
			 $object = new Uap_Affiliate_Notification_Reports();
			 $data['notification_constants'] = $object->notification_constants();

			 $this->print_top_messages();
			 require_once $this->admin_view_path . 'admin_referral_notifications.php';
		}

		private function print_periodically_reports(){
			/*
			 * @param none
			 * @return string
			 */
			 global $indeed_db;
			 if (!empty($_POST['save'])){

				/// CRON
			 	$data['metas'] = $indeed_db->return_settings_from_wp_option('periodically_reports');
			    if ($_POST['uap_periodically_reports_cron_hour']!=$data['metas']['uap_periodically_reports_cron_hour']){
			    	/// fire the CRON
			    	$base_time = strtotime(date('m/d/Y', time()));
					$input_hour = $_POST['uap_periodically_reports_cron_hour'];
					$time = $base_time + ($input_hour*3600);
					wp_schedule_event($time, 'daily', 'uap_cron_send_reports_to_affiliate');
			    }

				/// SAVE THE OPTIONS
			    $indeed_db->save_settings_wp_option('periodically_reports', $_POST);
			 }

			 $schedule = wp_next_scheduled('uap_cron_send_reports_to_affiliate');

			 $data['metas'] = $indeed_db->return_settings_from_wp_option('periodically_reports');
			 if (!class_exists('Uap_Affiliate_Notification_Reports')){
				 require_once UAP_PATH . 'classes/Uap_Affiliate_Notification_Reports.class.php';
			 }
			 $object = new Uap_Affiliate_Notification_Reports();
			 $data['reports_constants'] = $object->report_constants();

			 $this->print_top_messages();
			 require_once $this->admin_view_path . 'periodically_reports.php';
		}

		private function print_qr_code(){
			/*
			 * @param none
			 * @return string
			 */
			 global $indeed_db;
			 if (!empty($_POST['save'])){
			     $indeed_db->save_settings_wp_option('qr_code', $_POST);
			 }
			 $data['metas'] = $indeed_db->return_settings_from_wp_option('qr_code');

			 $this->print_top_messages();
			 require_once $this->admin_view_path . 'qr_code.php';
		}

		private function print_email_verification(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('email_verification', $_POST);
			}
			$data['metas'] = $indeed_db->return_settings_from_wp_option('email_verification');
			$data['payment_types'] = $indeed_db->get_payment_types_available();
			$data['pages'] = $indeed_db->uap_get_all_pages();
			require_once $this->admin_view_path . 'email_verification.php';
		}

		private function print_custom_currencies(){
			/*
			 * @param none
			 * @return none
			 */
			if (!empty($_POST['new_currency_code']) && !empty($_POST['new_currency_name'])){
				$db_data = get_option('uap_currencies_list');
				if (empty($db_data[$_POST['new_currency_code']])){
					$db_data[$_POST['new_currency_code']] = $_POST['new_currency_name'];
				}
				update_option('uap_currencies_list', $db_data);
			}
			$currencies = uap_get_currencies_list('custom');
			require_once $this->admin_view_path . 'settings-custom_currencies.php';
		}

		public function add_custom_bttns(){
			/*
			 * @param none
			 * @return none
			 */
			if (defined('DOING_AJAX') && DOING_AJAX) {
				return;
			}
			if (is_user_logged_in()){
				if (!current_user_can('edit_posts') || !current_user_can('edit_pages')) return;
				if (get_user_option('rich_editing') == 'true') {
					add_filter('mce_buttons', array($this, 'uap_register_button'));
					add_filter("mce_external_plugins", array($this, "uap_js_bttns_return"));
				}
			}
		}

		public function uap_register_button($arr=array()){
			/*
			 * @param array
			 * @return array
			 */
			array_push($arr, 'uap_button_forms');
			return $arr;
		}

		public function uap_js_bttns_return($arr=array()){
			/*
			 * @param array
			 * @return array
			 */
			$arr['uap_button_forms'] =  UAP_URL . 'assets/js/admin-bttns.js';
			return $arr;
		}

		public function add_style_scripts(){
			/*
			 * @param none
			 * @return none
			 */
			$is_plugin_page = FALSE;
			if (!empty($_GET['page']) && $_GET['page']=='ultimate_affiliates_pro'){
				$is_plugin_page = TRUE;
			}
			wp_enqueue_style('uap_font_awesome', UAP_URL . 'assets/css/font-awesome.css');
			wp_enqueue_style('uap_main_admin_style', UAP_URL . 'assets/css/main_admin.css');
			if ($is_plugin_page){
				wp_enqueue_style('uap_bootstrap_style', UAP_URL . 'assets/css/bootstrap.css');
				wp_enqueue_style('uap_bootstrap_theme_style', UAP_URL . 'assets/css/bootstrap-theme.css');
				wp_enqueue_style('uap_main_public_style', UAP_URL . 'assets/css/main_public.css');
				wp_enqueue_style('uap_templates', UAP_URL . 'assets/css/templates.css');
			}
			if (!isset($_GET['page']) || $_GET['page']!='et_divi_options'){
				wp_enqueue_style('uap_jquery-ui.min.css', UAP_URL . 'assets/css/jquery-ui.min.css');
			}

			wp_enqueue_style('uap_select2_style', UAP_URL . 'assets/css/select2.min.css' );
			wp_enqueue_script('jquery');
			wp_enqueue_media();
			wp_register_script('uap_admin_js', UAP_URL . 'assets/js/admin.js', array(), null);
			wp_localize_script('uap_admin_js', 'uap_url', get_site_url());
			wp_enqueue_script('uap_admin_js');

			if ($is_plugin_page){
				wp_enqueue_script('jquery-ui-sortable');
				wp_enqueue_script('jquery-ui-datepicker');
				wp_enqueue_script('jquery-ui-autocomplete');
				wp_enqueue_script('uap-jquery.flot.js', UAP_URL . 'assets/js/jquery.flot.js', array(), null);
				wp_enqueue_script('uap-bootstrap.js', UAP_URL . 'assets/js/bootstrap.js', array(), null);
				wp_enqueue_script('uap-jquery.flot.pie.js', UAP_URL . 'assets/js/jquery.flot.pie.js', array(), null);
				wp_enqueue_script('uap-jquery_form_module', UAP_URL . 'assets/js/jquery.form.js', array(), null );
				wp_enqueue_script('uap-jquery.uploadfile', UAP_URL . 'assets/js/jquery.uploadfile.min.js', array(), null );
				wp_enqueue_script('uap-jquery.flot.time.js', UAP_URL . 'assets/js/jquery.flot.time.js', array(), null);
				wp_register_script('uap_public', UAP_URL . 'assets/js/public.js', array(), null);
				wp_localize_script('uap_public', 'ajax_url', admin_url('admin-ajax.php') );
				wp_enqueue_script('uap_public');
				wp_enqueue_script( 'uap-select2', UAP_URL . 'assets/js/select2.min.js', array(), null );
			}

		}


		public function referral_action(){
			/*
			 * @param none
			 * @return none
			 */
			/// main referral
			require_once UAP_PATH . 'public/Referral_Main.class.php';
			$object = new Referral_Main();

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
		}


		public function dashboard_print_uap_column($states, $post){
			/*
			 * @param string, object
			 * @return none, print a string if it's case
			 */
			if (isset($post->ID) ){
				$str = '';
				//////////// DEFAULT PAGES
				if (get_post_type($post->ID)=='page'){
					global $indeed_db;
					$pages = $indeed_db->return_settings_from_wp_option('general-default_pages');

					switch ($post->ID){
						case $pages['uap_general_login_default_page']:
							$print = __('Affiliates - Login Page', 'uap');
							break;
						case $pages['uap_general_register_default_page']:
							$print = __('Affiliates - Register Page', 'uap');
							break;
						case $pages['uap_general_lost_pass_page']:
							$print = __('Affiliates - Lost Password', 'uap');
							break;
						case $pages['uap_general_logout_page']:
							$print = __('Affiliates - LogOut Page', 'uap');
							break;
						case $pages['uap_general_user_page']:
							$print = __('Affiliates - User Page', 'uap');
							break;
						case $pages['uap_general_tos_page']:
							$print = __('Affiliates - TOS', 'uap');
							break;
					}
					if (!empty($print)){
						$str .= '<div class="uap-dashboard-list-posts-col-default-pages">' . $print . '</div>';
					}
				}
				if (!empty($str)){
					$states[] = $str;
				}
			}
			return $states;
		}

		public function create_page_meta_box(){
			/*
			 * @param
			 * @return
			 */
			global $post, $indeed_db;
			add_meta_box(
						'uap_default_pages',//id
						__('Affiliates Pro - Default Pages', 'uap'),
						array($this, 'print_page_meta_box'),
						'page',
						'side',
						'high'
			);

			$postTypes = $indeed_db->get_all_post_types();
			foreach ($postTypes as $postType){
					add_meta_box(
								'uap_affiliate_landing_page',//id
								__('Affiliates Pro - Affiliate Landing Pages', 'uap'),
								array($this, 'print_affiliate_landing_page_meta_box'),
								$postType,
								'side',
								'high'
					);
			}

		}

		public function print_affiliate_landing_page_meta_box()
		{
				global $post, $indeed_db;
				$data['uap_landing_page_affiliate_id'] = get_post_meta($post->ID, 'uap_landing_page_affiliate_id', true);
				$data['affiliate_uid'] = $indeed_db->get_uid_by_affiliate_id($data['uap_landing_page_affiliate_id']);
				$data['affiliate_usename'] = $indeed_db->get_username_by_wpuid($data['affiliate_uid']);
				require_once $this->admin_view_path . 'landing_pages_meta_box.php';
		}

		public function print_page_meta_box(){
			/*
			 * @param none
			 * @return string
			 */
			global $post;
			global $indeed_db;
			$data['types'] = array(
							'uap_general_login_default_page' => __('Login', 'uap'),
							'uap_general_register_default_page' => __('Register', 'uap'),
							'uap_general_lost_pass_page' =>  __('Lost Password', 'uap'),
							'uap_general_logout_page' =>  __('LogOut', 'uap'),
							'uap_general_user_page' =>  __('User Account', 'uap'),
							'uap_general_tos_page' => __('TOS', 'uap'),
			);
			$data['current_page_type'] = $indeed_db->get_current_page_type($post->ID);
			$data['unset_pages'] = $indeed_db->get_default_unset_pages();
			require_once $this->admin_view_path . 'page-meta_box.php';
		}

		public function save_meta_box_values($post_id=0){
			/*
			 * @param int
			 * @return none
			 */
			if (!empty($_POST['uap_set_page_as_default_something'])){
				global $indeed_db;
				$indeed_db->set_default_page($_POST['uap_set_page_as_default_something'], $_POST['uap_post_id']);
			}
			if (isset($_POST['uap_landing_page_affiliate_id'])){
					update_post_meta($post_id, 'uap_landing_page_affiliate_id', $_POST['uap_landing_page_affiliate_id']);
			}
		}

		public function print_shortcodes(){
			/*
			 * @param none
			 * @return string
			 */
			 require_once $this->admin_view_path . 'shortcodes.php';
		}

		public function edit_wp_user($user_object){
			/*
			 * @param object
			 * @return string
			 */
			if (current_user_can('edit_user') && current_user_can('manage_options') && $user_object && !empty($user_object->data) && !empty($user_object->data->user_login)){
				global $indeed_db;
				$data['is_affiliate'] = $indeed_db->get_affiliate_id_by_username($user_object->data->user_login);
				$data['id'] = $user_object->data->ID;
				ob_start();
				require $this->admin_view_path . 'edit_wp_user.php';
				$output = ob_get_contents();
				ob_end_clean();
				echo $output;
			}
		}

		public function uap_delete_affiliate_by_uid($id=0){
			/*
			 * FIRE WHEN A USER IT's DELETED FROM WP
			 * @param int
			 * @return none
			 */
			 if ($id){
				global $indeed_db;
				$affiliate_id = $indeed_db->get_affiliate_id_by_wpuid($id);
				if ($affiliate_id){
					$indeed_db->delete_affiliate_details($affiliate_id);
				}
			}
		}

		private function print_flag_for_affiliate($user_id=0){
			/*
			 * @param int
			 * @return string
			 */
			 if ($user_id){
			 	 $flag_src = get_user_meta($user_id, 'uap_country', TRUE);
				 if ($flag_src){
				 	$countries = uap_get_countries();
					$country = $countries[strtoupper($flag_src)];
					$title = (empty($country)) ? '' : $country;
 				 	return '<span class="uap-affiliate-flag-wrapp"><img src="' . UAP_URL . 'assets/flags/' . $flag_src . '.svg" title="' . $title . '" class="uap-affiliate-admin-flag" /></span>';
				 }
			 }
			 return '';
		}

		private function print_source_details(){
			/*
			 * @param none
			 * @return string
			 */
			global $indeed_db;
			if (!empty($_POST['save'])){
				$indeed_db->save_settings_wp_option('source_details', $_POST);
			}
			$data['fields_available'] = array(
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
			$data['metas'] = $indeed_db->return_settings_from_wp_option('source_details');
			require_once $this->admin_view_path . 'source_details.php';
		}


	}
}
