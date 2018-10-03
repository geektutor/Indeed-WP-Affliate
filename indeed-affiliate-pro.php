<?php
/*
Plugin Name: Indeed Ultimate Affiliate Pro
Plugin URI: http://www.wpindeed.com/
Description: The most complete and easy to use Affiliate system Plugin that provides you a complete solution for your affiliates.
Version: 4.5
Author: indeed
Author URI: http://www.wpindeed.com
*/

class UAP_Main{
	private static $instance = FALSE;

	public function __construct(){}

	public static function run(){
		/*
		 * @param none
		 * @return none
		 */

		if (self::$instance==TRUE){
			return;
		}
		self::$instance = TRUE;
		/// PATHS
		if (!defined('UAP_PATH')){
			define('UAP_PATH', plugin_dir_path(__FILE__));
		}
		if (!defined('UAP_URL')){
			define('UAP_URL', plugin_dir_url(__FILE__));
		}
		if (!defined('UAP_PROTOCOL')){
			if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&  $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
				define('UAP_PROTOCOL', 'https://');
			} else {
				define('UAP_PROTOCOL', 'http://');
			}
		}

		if (!defined('UAP_PLUGIN_VER')){
			define('UAP_PLUGIN_VER', self::get_plugin_ver() );//used for updates
		}

		/// LANGUAGES
		add_action('init', array('UAP_Main', 'uap_load_language'));
		add_filter('send_password_change_email', array('UAP_Main', 'uap_update_passowrd_filter'), 99, 2);
		add_filter('wp_authenticate_user', array('UAP_Main', 'uap_authenticate_filter'), 9999, 3);

		require_once UAP_PATH . 'autoload.php';
		require_once UAP_PATH . 'utilities.php';
		require_once UAP_PATH . 'classes/Uap_Db.class.php';
		global $indeed_db;
		$indeed_db = new Uap_Db();
		$Uap_GDPR = new Indeed\Uap\Uap_GDPR();

		define('UAP_LICENSE_SET', $indeed_db->envato_check_license() );

		require_once UAP_PATH . 'classes/Uap_Ajax.class.php';
		$uap_ajax = new Uap_Ajax();

		if ( is_admin() && !defined('DOING_AJAX')){ /// current_user_can('administrator')
			/// ADMIN
			require_once UAP_PATH . 'admin/Uap_Main_Admin.class.php';
			$uap_main_object = new Uap_Main_Admin();
		} else {
			/// PUBLIC
			require_once UAP_PATH . 'public/Uap_Main_Public.class.php';
			$uap_main_object = new Uap_Main_Public();
		}

		/// CRON
		require_once UAP_PATH . 'classes/Uap_Cron_Jobs.class.php';
		$uap_cron_object = new Uap_Cron_Jobs();

		/// ADMIN MENU && NOTIFICATIONS
		add_action('admin_bar_menu', array('UAP_Main', 'uap_add_custom_top_menu_dashboard'), 995);
		add_action('admin_bar_menu', array('UAP_Main', 'add_custom_admin_bar_item'), 996);
		add_filter('query_vars', array('UAP_Main', 'edit_query_vars'), 991, 1);
		add_action('init', array('UAP_Main', 'do_add_rewrite_endpoint_uap'), 30);
		add_action('init', array('UAP_Main', 'uap_gate'), 92);

		///other modules
		require_once UAP_PATH . 'classes/Uap_Wp_Social_Login_Integration.class.php';
		Uap_Wp_Social_Login_Integration::run();

		$RewriteDefaultWpAvatar = new \Indeed\Uap\RewriteDefaultWpAvatar();
		$LoadTemplates = new \Indeed\Uap\LoadTemplates();
		$uapRestAPI = new \Indeed\Uap\RestAPI();

	}

	public static function uap_gate(){
		/*
		 * @param none
		 * @return none
		 */
		 if (!empty($_GET['uap_act'])){
			$action = $_GET['uap_act'];
		 } else {
		 	global $wp_query;
			if (!empty($wp_query)) $action = get_query_var('uap_act');
		 }
		 if (!empty($action)){
		 	$no_load = TRUE;
		 	switch ($action){
				case 'stripe_payout':
					require_once UAP_PATH . 'public/stripe-webhook.php';
					break;
				case 'password_reset':
					require_once UAP_PATH . 'public/arrive.php';
					break;
				case 'email_verification':
					require_once UAP_PATH . 'public/arrive.php';
					break;
				case 'migrate':
					$params = array(
								'serviceType'   => isset($_GET['service_type']) ? esc_sql($_GET['service_type']) : false,
								'entityType'    => isset($_GET['entity_type']) ? esc_sql($_GET['entity_type']) : false,
								'offset'        => isset($_GET['offset']) ? esc_sql($_GET['offset']) : 0,
								'assignRank'    => isset($_GET['assignRank']) ? esc_sql($_GET['assignRank']) : false
					);
					$object = new \Indeed\Uap\Migration\BaseMigration();
					$object->run($params);
					break;
			case 'tracking':
				$type = isset($_GET['type']) ? $_GET['type'] : '';
				if (empty($type)){
						return;
				}
				switch ($type){
						case 'cpm':
							$object = new \Indeed\Uap\CPM($_GET['affiliate']);
							break;
				}
				break;
			default:
				$home = get_home_url();
				wp_safe_redirect($home);
				exit;
		 	}
		 }
	}

	public static function do_add_rewrite_endpoint_uap(){
		add_rewrite_endpoint('uap', EP_ROOT | EP_PAGES );
	}

	public static function uap_load_language(){
		/*
		 * @param none
		 * @return none
		 */
		load_plugin_textdomain( 'uap', false, dirname(plugin_basename(__FILE__)) . '/languages/' );
	}

	public static function uap_update_passowrd_filter($return, $user_data){
		/*
		 * @param return - boolean, $user_data - array
		 * @return boolean
		 */
		if (isset($user_data['ID']) && $return){
			$sent_mail = uap_send_user_notifications($user_data['ID'], 'change_password');
			if ($sent_mail){
				return FALSE;
			}
		}
		return $return;
	}

	public static function edit_query_vars($vars){
		$vars[] = "uap";
		return $vars;
	}

	public static function uap_add_custom_top_menu_dashboard(){
		/*
		 * =============== DASHBOARD TOP MENU =================
		 * @param none
		 * @return none
		 */

		global $wp_admin_bar;
		if (!is_super_admin() || !is_admin_bar_showing()){
			return;
		}

		/// PARENT
		$wp_admin_bar->add_menu(array(
					'id'    => 'uap_dashboard_menu',
					'title' => 'Ultimate Affiliate Pro',
					'href'  => '#',
					'meta'  => array(),
		));

		///ITEMS
		$wp_admin_bar->add_menu(array('parent'=>'uap_dashboard_menu', 'id'=>'uap_dashboard_menu_pages', 'title'=>__('Affiliate Pages', 'uap'), 'href'=>'#', 'meta'=>array()));
		$wp_admin_bar->add_menu(array('parent'=>'uap_dashboard_menu', 'id'=>'uap_dashboard_menu_showcases', 'title'=>__('Showcases', 'uap'), 'href'=>'#', 'meta'=>array()));
		$wp_admin_bar->add_menu(array('parent'=>'uap_dashboard_menu', 'id'=>'uap_dashboard_menu_magic_feat', 'title'=>__('Magic Features', 'uap'), 'href'=>'#', 'meta'=>array()));
		$wp_admin_bar->add_menu(array('parent'=>'uap_dashboard_menu', 'id'=>'uap_dashboard_menu_ranks', 'title'=>__('Ranks', 'uap'), 'href'=>admin_url('admin.php?page=ultimate_affiliates_pro&tab=ranks'), 'meta'=>array()));
		$wp_admin_bar->add_menu(array('parent'=>'uap_dashboard_menu', 'id'=>'uap_dashboard_menu_notifications', 'title'=>__('Notifications', 'uap'), 'href'=>admin_url('admin.php?page=ultimate_affiliates_pro&tab=notifications'), 'meta'=>array()));
		$wp_admin_bar->add_menu(array('parent'=>'uap_dashboard_menu', 'id'=>'uap_dashboard_menu_shortcodes', 'title'=>__('Shortcodes', 'uap'), 'href'=>admin_url('admin.php?page=ultimate_affiliates_pro&tab=shortcodes'), 'meta'=>array()));

		/// SHOWCASES
		$wp_admin_bar->add_menu(array('parent'=>'uap_dashboard_menu_showcases', 'id'=>'uap_dashboard_menu_showcases_rf', 'title'=>__('Register Form', 'uap'), 'href'=>admin_url('admin.php?page=ultimate_affiliates_pro&tab=register'), 'meta'=>array()));
		$wp_admin_bar->add_menu(array('parent'=>'uap_dashboard_menu_showcases', 'id'=>'uap_dashboard_menu_showcases_lf', 'title'=>__('Login Form', 'uap'), 'href'=>admin_url('admin.php?page=ultimate_affiliates_pro&tab=login'), 'meta'=>array()));
		$wp_admin_bar->add_menu(array('parent'=>'uap_dashboard_menu_showcases', 'id'=>'uap_dashboard_menu_showcases_ta', 'title'=>__('Top Affiliates', 'uap'), 'href'=>admin_url('admin.php?page=ultimate_affiliates_pro&tab=top_affiliates'), 'meta'=>array()));
		$wp_admin_bar->add_menu(array('parent'=>'uap_dashboard_menu_showcases', 'id'=>'uap_dashboard_menu_showcases_ap', 'title'=>__('Account Page', 'uap'), 'href'=>admin_url('admin.php?page=ultimate_affiliates_pro&tab=account_page'), 'meta'=>array()));

		/// DEFAULT PAGES
		$array = array(
							'uap_general_login_default_page' => __('Login', 'uap'),
							'uap_general_register_default_page'=> __('Register', 'uap'),
							'uap_general_lost_pass_page' => __('Lost Password', 'uap'),
							'uap_general_logout_page' => __('LogOut', 'uap'),
							'uap_general_user_page' => __('User Account Page', 'uap'),
							'uap_general_tos_page' => __('TOS', 'uap'),
		);
		foreach ($array as $k=>$v){
			$page = get_option($k);
			$permalink = get_permalink($page);
			if ($permalink){
				$wp_admin_bar->add_menu(array('parent'=>'uap_dashboard_menu_pages', 'id'=>'uap_dashboard_menu_pages_' . $k, 'title'=>$v, 'href'=>$permalink, 'meta'=>array('target'=>'_blank')));
			}
		}

		//. MAGIC FEATURES
		global $indeed_db;
		$array = $indeed_db->get_magic_feat_item_list();
		if ($array){
			foreach ($array as $key=>$item){
				$wp_admin_bar->add_menu(array('parent'=>'uap_dashboard_menu_magic_feat', 'id'=>'uap_dashboard_menu_magic_feat_' . $key, 'title'=>$item['label'], 'href'=>$item['link'], 'meta'=>array()));
			}
		}
	}

	public static function add_custom_admin_bar_item(){
			/*
			 * @param none
			 * @return none
			 */
		global $wp_admin_bar;
		if (!is_super_admin() || !is_admin_bar_showing()){
			return;
		}
		global $wpdb, $indeed_db;
			if (!empty($_GET['page']) && $_GET['page']=='ultimate_affiliates_pro' && !empty($_GET['tab'])){
				switch ($_GET['tab']){
					case 'affiliates':
						$indeed_db->reset_dashboard_notification('affiliates');
						break;
					case 'referrals':
						$indeed_db->reset_dashboard_notification('referrals');
						break;
				}
			}
			?>
			<style>
				.uap-top-bar-count{
				    display: inline-block !important;
				    vertical-align: top !important;
					padding: 2px 7px !important;
				    background-color: #d54e21 !important;
				    color: #fff !important;
				    font-size: 9px !important;
				    line-height: 17px !important;
				    font-weight: 600 !important;
				    margin: 5px !important;
				    vertical-align: top !important;
				    -webkit-border-radius: 10px !important;
				    border-radius: 10px !important;
				    z-index: 26 !important;
				}
			</style>
			<?php


			$admin_workflow = $indeed_db->return_settings_from_wp_option('general-admin_workflow');

			if (!$admin_workflow['uap_admin_workflow_dashboard_notifications']){
				return;
			}

			$new_affiliates = $indeed_db->get_dashboard_notification_value('affiliates');
			$new_referrals = $indeed_db->get_dashboard_notification_value('referrals');

			if (!is_super_admin() || ! is_admin_bar_showing()){
				return;
			}

			$wp_admin_bar->add_menu( array(
				'id'    => 'uap_affiliates',
				'title' => '<span class="uap-top-bar-count">' . $new_affiliates . '</span>New Affiliates',
				'href'  => admin_url('admin.php?page=ultimate_affiliates_pro&tab=affiliates'),
				'meta'  => array ( 'class' => 'uap-top-notf-admin-menu-bar' )
			));

			$wp_admin_bar->add_menu( array(
				'id'    => 'uap_referrals',
				'title' => '<span class="uap-top-bar-count">' . $new_referrals . '</span>New Referrals',
				'href'  => admin_url('admin.php?page=ultimate_affiliates_pro&tab=referrals'),
				'meta'  => array ( 'class' => 'uap-top-notf-admin-menu-bar' )
			));

	}

	/**
	 * @param none
	 * @return float
	 */
	public static function get_plugin_ver(){
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$plugin_data = get_plugin_data( UAP_PATH . 'indeed-affiliate-pro.php', false, false);
		return $plugin_data['Version'];
	}


	public static function uap_authenticate_filter($user_data=null, $username='', $password=''){
			if ($user_data==null) return $user_data;
			if (is_object($user_data) && !empty($user_data->roles) && in_array('pending_user', $user_data->roles)){
				$errors = new WP_Error();
        		$errors->add('title_error', 'Pending User');
        		return $errors;
			}
			return $user_data;
	}


}

UAP_Main::run();
