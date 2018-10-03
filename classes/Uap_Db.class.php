<?php
if (!class_exists('Uap_Db')){
	class Uap_Db{

		private static $mlm_amout_type_per_level;
		private static $mlm_amount_value_per_level;

		public function __construct(){}


		/**
		 * @param none
		 * @return object
		 */
		public function get_all_blog_ids(){
			global $wpdb;
			$data = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs};");
			return $data;
		}

		/**
		 * @param none
		 * @return array
		 */
		public function get_all_prefixes(){
			global $wpdb;
			$data[] = $wpdb->base_prefix;
			if (is_multisite()){
				$ids = $this->get_all_blog_ids();
				if ($ids){
					foreach ($ids as $object){
						$data[] = $wpdb->base_prefix . $object->blog_id . '_';
					}
				}
			}
			return $data;
		}

		public function create_tables(){
			/*
			 * This will run only @install. Until version 3.4, the tables used in their names the base_prefix.
		 	 * @param none
			 * @return none
			 */

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			global $wpdb;

			$prefixes = $this->get_all_prefixes();

			foreach ($prefixes as $the_table_prefix):

			/// VISITS
			$table_name = $the_table_prefix . 'uap_visits';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							ref_hash VARCHAR(200) DEFAULT NULL,
							referral_id INT(11) DEFAULT 0,
							affiliate_id INT(11) DEFAULT NULL,
							campaign_name VARCHAR(100) DEFAULT NULL,
							ip VARCHAR(50) DEFAULT NULL,
							url VARCHAR(200) DEFAULT NULL,
							browser VARCHAR(50) DEFAULT NULL,
							device VARCHAR(50) DEFAULT NULL,
							visit_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
							status TINYINT(1) DEFAULT 0
				);";
				dbDelta($sql);
			}

			/// BANNERS
			$table_name = $the_table_prefix . 'uap_banners';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							name VARCHAR(200),
							description TEXT,
							url VARCHAR(200),
							image VARCHAR(200),
							status TINYINT(1) DEFAULT 0,
							DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
				);";
				dbDelta($sql);
			}

			/// AFFILIATES
			$table_name = $the_table_prefix . 'uap_affiliates';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							uid int(11) NOT NULL,
							rank_id int(11) NOT NULL,
							start_data TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
							status TINYINT(1) DEFAULT 0
				);";
				dbDelta($sql);
			}

			/// NOTIFICATIONS
			$table_name = $the_table_prefix . 'uap_notifications';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							type VARCHAR(200),
							rank_id int(11),
							subject VARCHAR(200),
							message TEXT,
							pushover_message TEXT,
							pushover_status TINYINT(1) NOT NULL DEFAULT 0,
							status TINYINT(1) DEFAULT 0
						)
						COLLATE utf8_general_ci;
				";
				dbDelta($sql);
			}

			/// RANKS
			$table_name = $the_table_prefix . 'uap_ranks';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							slug VARCHAR(200),
							label VARCHAR(200),
							amount_type VARCHAR(200),
							amount_value DECIMAL(12, 2) DEFAULT 0,
							bonus DECIMAL(12, 2) DEFAULT NULL,
							pay_per_click DECIMAL(12, 2) DEFAULT NULL,
							cpm_commission DECIMAL(12, 2) DEFAULT NULL,
							sign_up_amount_value DECIMAL(12, 2) DEFAULT -1,
							lifetime_amount_type VARCHAR(200) DEFAULT NULL,
							lifetime_amount_value DECIMAL(12, 2) DEFAULT -1,
							reccuring_amount_type VARCHAR(200) DEFAULT NULL,
							reccuring_amount_value DECIMAL(12, 2) DEFAULT -1,
							mlm_amount_type TEXT DEFAULT NULL,
							mlm_amount_value TEXT DEFAULT NULL,
							achieve TEXT,
							settings TEXT,
							rank_order TINYINT(3),
							status TINYINT(1) DEFAULT 0
				);";
				dbDelta($sql);
			}

			/// REFERRALS
			/*
			 * status can be 0-refuse, 1-unverified, 2-verified
			 * payment can be 0-unpaid, 1-pending, 2-paid
			 */
			$table_name = $the_table_prefix . 'uap_referrals';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							refferal_wp_uid INT(11) DEFAULT 0,
							campaign VARCHAR(200) DEFAULT NULL,
							affiliate_id INT(11) DEFAULT NULL,
							visit_id INT(11) DEFAULT NULL,
							description VARCHAR(400) DEFAULT NULL,
							source VARCHAR(200) DEFAULT NULL,
							reference VARCHAR(400) DEFAULT NULL,
							reference_details TEXT DEFAULT NULL,
							parent_referral_id INT(11) DEFAULT NULL,
							child_referral_id INT(11) DEFAULT NULL,
							amount DECIMAL(12, 2) DEFAULT 0,
							currency VARCHAR(50) DEFAULT NULL,
							date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
							status TINYINT(1) DEFAULT 0,
							payment TINYINT(1) DEFAULT 0
				);";
				dbDelta($sql);
			}

			/// OFFERS
			$table_name = $the_table_prefix . 'uap_offers';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							name VARCHAR(200) NOT NULL,
							start_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
							end_date TIMESTAMP NOT NULL DEFAULT 0,
							amount_type VARCHAR(200),
							amount_value DECIMAL(12, 2) DEFAULT 0,
							settings TEXT,
							status TINYINT(1) DEFAULT 0
				);"; /// old version: end_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
				dbDelta($sql);
			}

			/// OFFERS - AFFILIATES - REFERENCES
			$table_name = $the_table_prefix . 'uap_offers_affiliates_reference';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							offer_id INT(11) NOT NULL,
							affiliate_id INT(11),
							source VARCHAR(200),
							products TEXT
				);";
				dbDelta($sql);
			}

			/// CAMPAIGNS
			$table_name = $the_table_prefix . 'uap_campaigns';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							name VARCHAR (100) NOT NULL,
							affiliate_id INT(11) NOT NULL DEFAULT 0,
							referrals INT(11) DEFAULT 0,
							visit_count INT(11) DEFAULT 0,
							unique_visits_count INT(11) DEFAULT 0
				);";
				dbDelta($sql);
			}

			/// PAYMENTS
			/*
			 * status = 0-fail, 1-pending, 2-complete
			 */
			$table_name = $the_table_prefix . 'uap_payments';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							payment_type VARCHAR(20),
							transaction_id VARCHAR(50),
							referral_ids TEXT,
							affiliate_id INT(11) NOT NULL DEFAULT 0,
							amount DECIMAL(12, 2) DEFAULT 0,
							currency VARCHAR(20),
							payment_details TEXT DEFAULT NULL,
							create_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
							update_date TIMESTAMP NOT NULL DEFAULT 0,
							payment_special_status VARCHAR(200) DEFAULT NULL,
							status TINYINT(1) DEFAULT 0
				);"; /// old version : update_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				dbDelta($sql);
			}

			/// MLM
			$table_name = $the_table_prefix . 'uap_mlm_relations';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							affiliate_id INT(11) NOT NULL,
							parent_affiliate_id INT(11) NOT NULL
				);";
				dbDelta($sql);
			}

			/// affiliate referral users relations
			$table_name = $the_table_prefix . 'uap_affiliate_referral_users_relations';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							affiliate_id int(11) NOT NULL,
							referral_wp_uid int(11) ,
							DATE TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
				);";
				dbDelta($sql);
			}

			/// ranks
			$table_name = $the_table_prefix . 'uap_ranks_history';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							affiliate_id int(11) NOT NULL,
							prev_rank_id int(11) NOT NULL DEFAULT 0,
							rank_id int(11) NOT NULL,
							add_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
				);";
				dbDelta($sql);
			}

			/// landing_shortcodes_referrals
			$table_name = $the_table_prefix . 'uap_landing_commissions';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							slug varchar(200) NOT NULL,
							settings TEXT,
							create_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
							status TINYINT(1) DEFAULT 1
				);";
				dbDelta($sql);
			}


			/// uap_coupons_code_affiliates
			$table_name = $the_table_prefix . 'uap_coupons_code_affiliates';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
				$sql = "CREATE TABLE " . $table_name . " (
							id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							code VARCHAR(200) NOT NULL,
							affiliate_id INT(11) NOT NULL,
							type VARCHAR(200),
							settings TEXT,
							status TINYINT(1) DEFAULT 1
				);";
				dbDelta($sql);
			}

			/// UAP_REPORTS
			$table_name = $the_table_prefix . 'uap_reports';
			if ($wpdb->get_var("show tables like '$table_name'")!=$table_name){
				$sql = "CREATE TABLE $table_name (
							affiliate_id int(11) NOT NULL,
							email VARCHAR(200) NOT NULL,
							period TINYINT(2),
							last_sent INT(11) DEFAULT NULL
				);";
				dbDelta($sql);
			}

			/// UAP_DASHBOARD_NOTIFICATIONS
			$table_name = $the_table_prefix . 'uap_dashboard_notifications';
			if ($wpdb->get_var("show tables like '$table_name'")!=$table_name){
				$sql = "CREATE TABLE $table_name (
							type VARCHAR(40) NOT NULL,
							value INT(11) DEFAULT 0
				);";
				dbDelta($sql);

				$wpdb->query("INSERT INTO $table_name VALUES('affiliates', 0);");
				$wpdb->query("INSERT INTO $table_name VALUES('referrals', 0);");
			}

			/// UAP_REF_LINKS
			$table_name = $the_table_prefix . 'uap_ref_links';
			//$wpdb->query("DROP TABLE $table_name");
			if ($wpdb->get_var("show tables like '$table_name'")!=$table_name){
				$sql = "CREATE TABLE $table_name (
							id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
							affiliate_id int(11) NOT NULL,
							url VARCHAR(300) NOT NULL,
							status TINYINT(1)
				);";
				dbDelta($sql);
			}

			$table_name = $the_table_prefix . 'uap_cpm';
			if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
					$sql = "CREATE TABLE $table_name (
											id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
											affiliate_id INT(11) NOT NULL,
											count_number INT(11) NOT NULL,
											update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
					);";
					dbDelta($sql);
			}

			endforeach;

			/// set license at 0
			$this->return_settings_from_wp_option('licensing');
			/// set the general-settings
			$this->return_settings_from_wp_option('general-settings');
		}

		/*
		 * @param none
		 * @return none
		 */
		public function modify_tables(){
			global $wpdb;

			/// Notifications
			$table = $wpdb->prefix . 'uap_notifications';
			$data = $wpdb->get_row("SHOW COLUMNS FROM " . $table . " LIKE 'pushover_message';");
			if (!$data){
				$q = "ALTER TABLE $table ADD pushover_message TEXT AFTER message;";
				$wpdb->query($q);
				$q = "ALTER TABLE $table ADD pushover_status TINYINT(1) NOT NULL DEFAULT 0 AFTER pushover_message;";
				$wpdb->query($q);
			}

			/// Payments
			$table = $wpdb->prefix . 'uap_payments';
			$data = $wpdb->get_row("SHOW COLUMNS FROM " . $table . " LIKE 'payment_details';");
			if (!$data){
				$q = "ALTER TABLE $table ADD payment_details TEXT AFTER currency;";
				$wpdb->query($q);
			}

			/// Ranks
			$data = $wpdb->get_row("SHOW COLUMNS FROM {$wpdb->prefix}uap_ranks LIKE 'pay_per_click' ");
			if (!$data){
					$wpdb->query("ALTER TABLE {$wpdb->prefix}uap_ranks ADD pay_per_click DECIMAL(12, 2) DEFAULT NULL AFTER bonus");
			}

			/// Ranks
			$data = $wpdb->get_row("SHOW COLUMNS FROM {$wpdb->prefix}uap_ranks LIKE 'cpm_commission' ");
			if (!$data){
					$wpdb->query("ALTER TABLE {$wpdb->prefix}uap_ranks ADD cpm_commission DECIMAL(12, 2) DEFAULT NULL AFTER bonus");
			}
		}

		public function create_default_pages(){
			/*
			 * @param none
			 * @return none
			 */
			 $insert_array = array(
						'uap_general_register_default_page' => array(
											'title' => __('Affiliate Register Page', 'uap'),
											'content' => '[uap-register]',
						),
						'uap_general_login_default_page' => array(
											'title' => __('Affiliate Login Page', 'uap'),
											'content' => '[uap-login-form]',
						),
						'uap_general_user_page' => array(
											'title' => __('Affiliate Account Page', 'uap'),
											'content' => '[uap-account-page]',
						),
						'uap_general_lost_pass_page' => array(
											'title' => __('Affiliate Lost Password', 'uap'),
											'content' => '[uap-reset-password]',
						),
						'uap_general_tos_page' => array(
											'title' => __('Affiliate TOS Page', 'uap'),
											'content' => '',
						),
			);

			foreach ($insert_array as $key=>$inside_arr){
				$exists = get_option($key);
				if (!$exists){
					$arr = array(
									'post_content' => $inside_arr['content'],
									'post_title' => $inside_arr['title'],
									'post_type' => 'page',
									'post_status' => 'publish',
					);
					$post_id = wp_insert_post($arr);
					update_option($key, $post_id);
				}
			}
		}

		public function create_default_redirects(){
			/*
			 * @param none
			 * @return none
			 */
			 $login = get_option('uap_general_login_default_page');
			 update_option('uap_general_logout_redirect', $login);
			 update_option('uap_general_register_redirect', $login);
			 $account_page = get_option('uap_general_user_page');
			 update_option('uap_general_login_redirect', $account_page);

			 /// extra redirects
			 update_option('uap_general_account_page_no_logged_redirect', $login);
			 update_option('uap_general_logout_page_non_logged_users_redirect', $login);
			 update_option('uap_general_login_page_logged_users_redirect', $account_page);
			 update_option('uap_general_register_page_logged_users_redirect', $account_page);
			 update_option('uap_general_lost_pass_page_logged_users_redirect', $account_page);
		}

		public function create_demo_banners(){
			/*
			 * @param none
			 * @return none
			 */
			 global $wpdb;
			 $home_url = get_home_url();
			 $array = array(
							array(
				 				'name' => __('Demo Banner 1', 'uap'),
				 				'description' => 'Building Awesome Marketing Tool',
				 				'url' => $home_url,
				 				'image' => UAP_URL . 'assets/images/banner_1.jpg',
				 				'status' => 1,
							),
							array(
				 				'name' => __('Demo Banner 2', 'uap'),
				 				'description' => 'Building Awesome Marketing Tool',
				 				'url' => $home_url,
				 				'image' => UAP_URL . 'assets/images/banner_2.jpg',
				 				'status' => 1,
							),
							array(
				 				'name' => __('Demo Banner 3', 'uap'),
				 				'description' => 'Building Awesome Marketing Tool',
				 				'url' => $home_url,
				 				'image' => UAP_URL . 'assets/images/banner_3.jpg',
				 				'status' => 1,
							),
							array(
				 				'name' => __('Demo Banner 4', 'uap'),
				 				'description' => 'Building Awesome Marketing Tool',
				 				'url' => $home_url,
				 				'image' => UAP_URL . 'assets/images/banner_4.jpg',
				 				'status' => 1,
							),
							array(
				 				'name' => __('Demo Banner 5', 'uap'),
				 				'description' => 'Building Awesome Marketing Tool',
				 				'url' => $home_url,
				 				'image' => UAP_URL . 'assets/images/banner_5.jpg',
				 				'status' => 1,
							),
			 );
			 $prefixes = $this->get_all_prefixes();

			 foreach ($prefixes as $the_table_prefix){
				 $table = $the_table_prefix . "uap_banners";
				 $data = $wpdb->get_row("SELECT * FROM $table;");
				 if (empty($data)){
					 foreach ($array as $arr){
					 	$this->save_banner($arr);
					 }
				 }
			 }

		}

		public function check_update_notifications(){
			/*
			 * UPDATE NOTIFICATIONS
			 * @param none
			 * @return none
			 */
			 $notifications = array('register_lite_send_pass_to_user');
			 $prefixes = $this->get_all_prefixes();
			 foreach ($prefixes as $prefix){
			 	 $table = $prefix . 'uap_notifications';
				 foreach ($notifications as $type){
				 	if (!$this->notification_type_exists($type, $table)){
						$template = uap_return_default_notification_content($type); ///get default notification content
						$data['type'] = $type;
						$data['rank_id'] = -1;
						$data['subject'] = addslashes($template['subject']);
						$data['message'] = addslashes($template['content']);
						$data['status'] = 1;
						$this->save_notification($data, $table);///and save it
				 	}
				 }
			 }
		}

		public function create_pending_role(){
			/*
			 * @param none
			 * @return none
			 */
			add_role('pending_user', 'Pending', array('read' => false,'level_0' => true));
			if (is_multisite()){
				global $wpdb;
				$table = $wpdb->base_prefix . 'blogs';
				$data = $wpdb->get_results("SELECT blog_id FROM $table;");
				if ($data){
					foreach ($data as $object){
						if (!empty($object->blog_id) && $object->blog_id>1){
							$prefix = $wpdb->base_prefix . $object->blog_id . '_' ;
							$table = $prefix . 'options';
							$option = $prefix . 'user_roles';
							$temp_data = $wpdb->get_row("SELECT option_value FROM $table WHERE option_name='$option';");
							if ($temp_data && !empty($temp_data->option_value)){
								$array_unserialize = unserialize($temp_data->option_value);
								if (empty($array_unserialize['pending_user'])){
									$array_unserialize['pending_user'] = array(
																				'name' => 'Pending',
																				'capabilities' => array(
																											'read' => FALSE,
																											'level_0' => 1,
																				)
									);
									$array_serialize = serialize($array_unserialize);
									$wpdb->query("UPDATE $table SET option_value='$array_serialize' WHERE option_name='$option'; ");
								}
							}
						}
					}
				}
			}
		}

		public function unistall(){
			/*
			 * REMOVE ALL TABLES AND OPTIONS, USE THIS METHOD ONLY ON UNINSTALL
			 * @param none
			 * @return none
			 */
			global $wpdb;
			$tables = array('uap_visits',
							'uap_banners',
							'uap_affiliates',
							'uap_notifications',
							'uap_ranks',
							'uap_referrals',
							'uap_offers',
							'uap_offers_affiliates_reference',
							'uap_campaigns',
							'uap_payments',
							'uap_mlm_relations',
							'uap_affiliate_referral_users_relations',
							'uap_ranks_history',
							'uap_landing_commissions',
							'uap_coupons_code_affiliates',
							'uap_reports',
							'uap_ref_links',
			);
			$prefixes = $this->get_all_prefixes();

			foreach ($prefixes as $the_table_prefix){
				foreach ($tables as $table){
					$table_name = $the_table_prefix . $table;
					$wpdb->query("DROP TABLE IF EXISTS $table_name;");
				}
			}
			$option_groups = array(
									'login',
									'login-messages',
									'general-settings',
									'general-uploads',
									'general-redirects',
									'general-default_pages',
									'general-captcha',
									'general-msg',
									'register',
									'register-msg',
									'register-custom-fields',
									'opt_in',
									'notifications',
									'account_page',
									'double_email_verification',
									'licensing',
									'listing_users',
									'listing_users_inside_page',
									'sign_up_referrals',
									'lifetime_commissions',
									'reccuring_referrals',
									'social_share',
									'paypal',
									'stripe',
									'bonus_on_rank',
									'pay_per_click',
									'cpm_commission',
									'allow_own_referrence',
									'mlm',
									'rewrite_referrals',
									'licensing',
									'general-notification',
			);
			foreach ($option_groups as $group_name){
				$data = $this->return_settings_from_wp_option($group_name, FALSE, TRUE);
				if ($data && is_array($data)){
					foreach ($data as $key => $value){
						delete_option($key);
					}
				}
			}
			delete_option('uap_plugin_version');
		}

		public function get_banners(){
			/*
			 * @param none
			 * @return array
			 */
			global $wpdb;
			$table = $wpdb->prefix . "uap_banners";
			$data = $wpdb->get_results("SELECT * FROM $table");
			return (array)$data;
		}

		public function get_banner($id=0){
			/*
			 * @param int
			 * @return array
			 */
			if ($id){
				//get banner from db
				global $wpdb;
				$table = $wpdb->prefix . "uap_banners";
				$q = $wpdb->prepare("SELECT * FROM $table WHERE id=%d ", $id);
				$data = $wpdb->get_row($q);
				$array = (array)$data;
				$array['description'] = stripslashes($array['description']);
				return $array;
			} else {
				//get banner meta list
				return array(
								'id' => 0,
								'name' => 'Untitled Banner',
								'description' => 'Building Awesome Marketing Tool',
								'url' => get_site_url(),
								'image'	=> 'http://www.example.com/wp-content/uploads/shooting_stars.jpg',
								'status' => 1
				);
			}
		}

		public function save_banner($post_data=array()){
			/*
			 * @param array (post data)
			 * @return none
			 */
			if (!empty($post_data)){
				if (empty($post_data['image']) || empty($post_data['url'])){
					return;
				}
				$post_data['description'] = addslashes($post_data['description']);
				global $wpdb;
				$table = $wpdb->prefix . "uap_banners";
				if (!empty($post_data['id'])){
					$q = $wpdb->prepare("SELECT * FROM $table WHERE id=%d ", $post_data['id']);
					$data = $wpdb->get_row($q);
					if (!empty($data)){
						/// UPDATE
						$q = $wpdb->prepare("UPDATE $table SET
												name=%s,
												description=%s,
												url=%s,
												image=%s,
												status=%s
										WHERE id=%d
						;", $post_data['name'], stripslashes_deep($post_data['description']), $post_data['url'], $post_data['image'], $post_data['status'], $post_data['id']);
						$wpdb->query($q);
						return;
					}
				}
				/// SAVE
				$q = $wpdb->prepare("INSERT INTO $table VALUES(NULL,
																											%s, %s, %s, %s, %s, NOW());",
							$post_data['name'], $post_data['description'], $post_data['url'], $post_data['image'], $post_data['status']
				);
				$wpdb->query($q);
			}
		}

		public function delete_banners($post_data=array()){
			/*
			 * @param array (post data)
			 * @return none
			 */
			if (!empty($post_data) && !empty($post_data['delete_banner'])){
				global $wpdb;
				$table = $wpdb->prefix . "uap_banners";
				if (!is_array($post_data['delete_banner'])){
					$post_data['delete_banner'] = array($post_data['delete_banner']);
				}
				foreach ($post_data['delete_banner'] as $id){
					$q = $wpdb->prepare("DELETE FROM $table WHERE id=%d ", $id);
					$wpdb->query($q);
				}
			}
		}

		public function affiliate_get_id_by_uid($uid=0){
			/*
			 * @param int
			 * @return int
			 */
			if ($uid){
				global $wpdb;
				$table_name = $wpdb->prefix . 'uap_affiliates';
				$q = $wpdb->prepare("SELECT id FROM $table_name WHERE uid=%d ;", $uid);
				$data = $wpdb->get_row($q);
				if (!empty($data) && !empty($data->id)){
					return $data->id;
				}
			}
			return 0;
		}

		public function get_uid_by_affiliate_id($aff_id=0){
			/*
			 * @param int
			 * @return int
			 */
			if ($aff_id){
				global $wpdb;
				$table_name = $wpdb->prefix . 'uap_affiliates';
				$q = $wpdb->prepare("SELECT uid FROM $table_name WHERE id=%d ;", $aff_id);
				$data = $wpdb->get_row($q);
				if (!empty($data) && !empty($data->uid)){
					return $data->uid;
				}
			}
			return 0;
		}

		public function getUidByEmail($email='')
		{
				global $wpdb;
				return $wpdb->get_var("SELECT ID FROM {$wpdb->users} WHERE user_email='$email';");
		}

		public function getAllAffiliatesIds()
		{
				global $wpdb;
				$table_name = $wpdb->prefix . 'uap_affiliates';
				$data = $wpdb->get_results("SELECT uid FROM $table_name ;");
				return $data;
		}

		public function get_affiliate_id_by_username($user_name=''){
			/*
			 * @param string
			 * @return int
			 */
			if ($user_name){
				global $wpdb;
				$q = $wpdb->prepare("SELECT uap.id FROM " . $wpdb->prefix . "uap_affiliates uap
											INNER JOIN " . $wpdb->base_prefix . "users u
											ON uap.uid=u.ID
											WHERE u.user_login=%s ;", $user_name);
				$data = $wpdb->get_row($q);
				if (!empty($data->id)){
					return $data->id;
				}
			}
			return 0;
		}

		public function get_wp_username_by_affiliate_id($affiliate_id=0){
			/*
			 * @param string
			 * @return int
			 */
			if ($affiliate_id){
				global $wpdb;
				$q = $wpdb->prepare("SELECT u.user_login
											FROM " . $wpdb->prefix . "uap_affiliates uap
											INNER JOIN " . $wpdb->base_prefix . "users u
											ON uap.uid=u.ID
											WHERE uap.id=%d;
				", $affiliate_id);
				$data = $wpdb->get_row($q);
				if (!empty($data->user_login)){
					return $data->user_login;
				}
			}
			return '';
		}

		public function save_affiliate($uid=0){
			/*
			 * @param int
			 * @return int
			 */
		 	if ($this->is_user_admin($uid)){
		 		return 0;
		 	}

			$affiliate_id = 0;
			if ($uid){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_affiliates';
				$q = $wpdb->prepare("SELECT * FROM $table WHERE uid=%d ;", $uid);
				$exists = $wpdb->get_row($q);
				if (!$exists){
					$q = $wpdb->prepare("INSERT INTO $table VALUES(NULL, %d, 0, NOW(), 1);", $uid);
					$wpdb->query($q);
					$affiliate_id = $wpdb->insert_id;

					/// SET DEFAULT PAYMENT SYSTEM
					$this->set_default_payment_on_register_affiliate($uid);

					/// INCREMENT DASHBOARD NOTIFICATION COUNT
					$this->increment_dashboard_notification('affiliates');

					do_action('uap_save_affiliate_action', $uid, $affiliate_id);
				}
			}
			return $affiliate_id;
		}

		public function delete_affiliates($post_data=array()){
			/*
			 * DELETE USER
			 * @param array
			 * @return none
			 */
			if (!empty($post_data)){
				if (!is_array($post_data)){
					$post_data = array(0=>$post_data);
				}
				if (!function_exists('wp_delete_user')){
            		require_once ABSPATH . 'wp-admin/includes/user.php';
        		}
				foreach ($post_data as $id){
					$uid = $this->get_uid_by_affiliate_id($id);
					uap_send_user_notifications($uid, 'affiliate_profile_delete');///send notification to user
					wp_delete_user($uid); /// DELETE WP USER
					$this->delete_affiliate_details($id); /// DELETE UAP AFFILIATE
				}
			}
		}

		public function delete_affiliate_details($id=0){
			/*
			 * @param int (affiliate id)
			 * @return none
			 */
			 if ($id){
			 	global $wpdb;
				$id = esc_sql($id);
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "uap_affiliates WHERE id='" . $id . "';");
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "uap_affiliate_referral_users_relations WHERE affiliate_id='$id' ");
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "uap_campaigns WHERE affiliate_id='$id' ");
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "uap_offers_affiliates_reference WHERE affiliate_id='$id' ");
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "uap_payments WHERE affiliate_id='$id' ");
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "uap_ranks_history WHERE affiliate_id='$id' ");
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "uap_referrals WHERE affiliate_id='$id' ");
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "uap_visits WHERE affiliate_id='$id' ");
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "uap_mlm_relations WHERE affiliate_id='$id' ");/// MLM child
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "uap_mlm_relations WHERE parent_affiliate_id='$id' ");/// MLM parent
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "uap_coupons_code_affiliates WHERE affiliate_id='$id' ");
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "uap_reports WHERE affiliate_id='$id';");
			 }
		}

		public function remove_user_from_affiliate($uid=0){
			/*
			 * DELETE USER
			 * @param int
			 * @return boolean
			 */
			if (!empty($uid)){
				$id = $this->get_affiliate_id_by_wpuid($uid);
				if ($id){
					$this->delete_affiliate_details($id);
					return TRUE;
				}
			}
			return FALSE;
		}

		public function get_affiliate_id_by_wpuid($uid=0){
			/*
			 * @param int
			 * @return int
			 */
			 if ($uid){
			 	global $wpdb;
			 	$table = $wpdb->prefix . 'uap_affiliates';
				$q = $wpdb->prepare("SELECT id FROM $table WHERE uid=%d", $uid);
			 	$data = $wpdb->get_row($q);
				if ($data && !empty($data->id)){
					return $data->id;
				}
			 }
			 return 0;
		}

		public function affiliate_has_childrens($affiliate_id=0){
			/*
			 * @param int
			 * @return boolean
			 */
			 if ($affiliate_id){
			 	 global $wpdb;
				 $table = $wpdb->prefix . 'uap_mlm_relations';
				 $q = $wpdb->prepare("SELECT id FROM $table WHERE parent_affiliate_id=%d ", $affiliate_id);
				 $data = $wpdb->get_row($q);
				 if ($data && !empty($data->id)){
				 	return TRUE;
				 }
			 }
			 return FALSE;
		}

		public function get_affiliates($limit=-1, $offset=-1, $count=FALSE, $order_by='', $order_type='', $where_conditions=array(), $selectByRank=0){
			/*
			 * @param
			 * @return array OR INT
			 */
			global $wpdb;
			$ordertype_rank = (empty($_GET['ordertype_rank'])) ? '' : esc_sql($_GET['ordertype_rank']);
			if (empty($ordertype_rank) && $selectByRank){
					$ordertype_rank = $selectByRank;
			}

			$affiliates_table = $wpdb->prefix . 'uap_affiliates';
			$user_table = $wpdb->base_prefix . 'users';
			$users_meta_table = $wpdb->base_prefix . 'usermeta';
			$search_term = isset($_GET['search_t']) ? esc_sql($_GET['search_t']) : '';

			if ($count){
				$q = "SELECT COUNT(distinct u.ID) as c "; //distinct um.user_id
			} else {
				$q = "SELECT distinct a.id, a.*, u.* ";
			}
			$q .= " FROM $affiliates_table a";
			$q .= " INNER JOIN $user_table u ON a.uid=u.ID";
			if (!empty($search_term)){
				$q .= " INNER JOIN $users_meta_table um ON u.ID=um.user_id";
			}
			$q .= " WHERE 1=1";

			if (!empty($search_term)){
				$q .= " AND (
					(um.meta_key='first_name' AND um.meta_value LIKE '%$search_term%')
						OR
					(um.meta_key='last_name' AND um.meta_value LIKE '%$search_term%')
						OR
					(um.meta_key='nickname' AND um.meta_value LIKE '%$search_term%')
				)";
			}

			if (!empty($ordertype_rank) && $ordertype_rank!=-1){
				$q .= " AND a.rank_id='$ordertype_rank' ";
			}

			if ($count){
				$data = $wpdb->get_row($q);
				if (!empty($data->c)){
					return $data->c;
				}
				return 0;
			} else {
				$return_arr = array();
				if ($order_type && $order_by){
					$order_by = esc_sql($order_by);
					$order_type = esc_sql($order_type);
					$q .= " ORDER BY " . $order_by . " " . $order_type;
				}
				if ($limit>-1 && $offset>-1){
					$limit = esc_sql($limit);
					$offset = esc_sql($offset);
					$q .= " LIMIT " . $limit . " OFFSET " . $offset;
				}
				$data = $wpdb->get_results($q);
				if (!empty($data)){
					foreach ($data as $obj){
						if ($obj->id){
							$rank_label = '-';
							$rank_color = '';
							$rank_id = $this->get_affiliate_rank($obj->id);
							if ($rank_id){

								$rank_data = $this->get_rank($rank_id);
								if (!empty($rank_data['label'])){
									$rank_label = $rank_data['label'];
								}
								if(isset($rank_data['color'])){
									$rank_color = $rank_data['color'];
								}
							}
							$return_arr[$obj->id] = array(
														'uid' => $obj->uid,
														'username' => $obj->user_login,
														'name' => get_user_meta($obj->uid, 'first_name', TRUE) . ' ' .  get_user_meta($obj->uid, 'last_name', TRUE),
														'email' => $obj->user_email,
														'start_data' => $obj->start_data,
														'rank_label' => $rank_label,
														'rank_id' => $rank_id,
														'rank_color' => $rank_color,
														'role' => $this->get_user_first_role($obj->uid),
														'stats' => $this->get_stats_for_payments($obj->id),
														'payment_settings' => $this->get_affiliate_payment_type($obj->uid),
														'email_status' => get_user_meta($obj->uid, 'uap_verification_status', TRUE),
							);

						}
					}
				}
			}
			return $return_arr;
		}

		public function get_affiliates_username_id_pair(){
			/*
			 * @param none
			 * @return array
			 */
			$arr = array();
			global $wpdb;
			$table_affiliates = $wpdb->prefix . 'uap_affiliates';
			$table_users = $wpdb->base_prefix . 'users';
			$data = $wpdb->get_results("SELECT a.id as id, u.user_login as username FROM $table_users as u
											INNER JOIN $table_affiliates as a
											ON u.ID=a.uid
			;");
			if ($data){
				foreach ($data as $object){
					$arr[$object->id] = $object->username;
				}
			}
			return $arr;
		}

		public function get_affiliates_from_referrals($affiliates_ids_in=array(), $minimumDate=''){
			/*
			 * @param array
			 * @return array
			 */
			$return_arr = array();
			global $wpdb;
			$table = $wpdb->prefix . 'uap_referrals';
			$q = "SELECT DISTINCT affiliate_id,
												COUNT(refferal_wp_uid) as total_referrals,
												SUM(amount) as total_amount
												FROM $table
												WHERE
												1=1";
			if (!empty($minimumDate)){
					$q .= " AND `date`>'$minimumDate' ";
			}
			if ($affiliates_ids_in){
				$ids = implode(',', $affiliates_ids_in);
				$ids = esc_sql($ids);
				if ($ids){
					$q .= " AND affiliate_id IN ($ids)";
				}
			}
			$q .= " AND	status='2'
					GROUP BY affiliate_id
			;";
			$data = $wpdb->get_results($q);

			if (!empty($data)){
				foreach ($data as $object){
					$return_arr[$object->affiliate_id]['total_amount'] = (empty($object->total_amount)) ? 0 : $object->total_amount;
					$return_arr[$object->affiliate_id]['total_referrals'] = (empty($object->total_referrals)) ? 0 : $object->total_referrals;
				}
			}
			return $return_arr;
		}

		public function get_affiliate($id=0){
			/*
			 * @param int
			 * @return array
			 */
			if ($id){
				global $wpdb;
				$table_name = $wpdb->prefix . 'uap_affiliates';
				$q = $wpdb->prepare("SELECT * FROM $table_name WHERE id=%d ;", $id);
				$data = $wpdb->get_row($q);
				if (!empty($data)){
					return $data;
				}
			}
			return array();
		}

		public function is_affiliate_active($id=0){
			/*
			 * @param int
			 * @return boolean
			 */
			if ($id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_affiliates';
				$q = $wpdb->prepare("SELECT status FROM $table WHERE id=%d ;", $id);
				$data = $wpdb->get_row($q);
				if (!empty($data->status)){
					return TRUE;
				}
			}
			return FALSE;
		}

		public function is_user_affiliate_by_uid($uid=0){
			/*
			 * @param int
			 * @return boolean
			 */
			 if ($uid){
			 	 global $wpdb;
				 $table = $wpdb->prefix . 'uap_affiliates';
				 $q = $wpdb->prepare("SELECT id FROM $table WHERE uid=%d ", $uid);
				 $data = $wpdb->get_row($q);
				 if (!empty($data->id)){
				 	return TRUE;
				 }
			 }
			 return FALSE;
		}

		public function is_user_an_active_affiliate($uid=0){
				if ($uid){
						global $wpdb;
						$table = $wpdb->prefix . 'uap_affiliates';
						$q = $wpdb->prepare("SELECT status FROM $table WHERE uid=%d ;", $uid);
						$data = $wpdb->get_var($q);
						if ($data){
								return true;
						}
				}
				return false;
		}

		public function delete_notification($id=0){
			/*
			 * @param int
			 * @return none
			 */
			if (!empty($id)){
				global $wpdb;
				$table = $wpdb->prefix . "uap_notifications";
				$q = $wpdb->prepare("DELETE FROM $table WHERE id=%d ", $id);
				$wpdb->query($q);
			}
		}

		public function notification_type_exists($type='', $table=''){
			/**
			 * @param string
			 * @param string
			 * @return boolean
			 */
			if (!empty($type)){
				global $wpdb;
				if (!$table){
					$table = $wpdb->prefix . "uap_notifications";
				}
				$q = $wpdb->prepare("SELECT * FROM $table WHERE type=%d ", $type);
				$data = $wpdb->get_results($q);
				if (!empty($data)){
					return TRUE;
				}
			}
			return FALSE;
		}

		public function get_notifications(){
			/*
			 * @param none
			 * @return array
			 */
			global $wpdb;
			$table = $wpdb->prefix . "uap_notifications";
			$data = $wpdb->get_results("SELECT * FROM $table");
			return (array)$data;
		}

		public function get_notification($id=0){
			/*
			 * @param int
			 * @return array
			 */
			if ($id){
				//get notf from db
				global $wpdb;
				$table = $wpdb->prefix . "uap_notifications";
				$q = $wpdb->prepare("SELECT * FROM $table WHERE id=%d ", $id);
				$data = $wpdb->get_row($q);
				return (array)$data;
			} else {
				//get notf meta list
				return array(
						'id' => 0,
						'type' => '',
						'rank_id' => -1,
						'subject' => '',
						'message' => '',
						'pushover_message' => '',
						'pushover_status' => 0,
						'status' => 1
				);
			}
		}

		public function get_notification_for_rank($rank=-1, $notification_type=''){
			/*
			 * @param int, string
			 * @return array
			 */
			global $wpdb;
			$table = $wpdb->prefix . 'uap_notifications';
			if (!$rank || $rank==-1){
				$q = $wpdb->prepare("SELECT * FROM $table
										WHERE 1=1
										AND type=%s
										AND rank_id='-1'
										ORDER BY id DESC LIMIT 1;", $notification_type);
				$data = $wpdb->get_row($q);
			} else {
				$q = $wpdb->prepare("SELECT * FROM $table
										WHERE 1=1
										AND type=%s
										AND rank_id=%d
										ORDER BY id DESC LIMIT 1;", $notification_type, $rank);
				$data = $wpdb->get_row($q);
			}
			if ($data){
				return (array)$data;
			}
			return array();
		}

		public function save_notification($post_data=array(), $table=''){
			/*
			 * @param array ($_POST)
			 * @param string (for save the notification on custom table - multisite )
			 * @return none
			 */
			if (!empty($post_data)){
				global $wpdb;
				if (!$table){
					$table = $wpdb->prefix . "uap_notifications";
				}
				if (!isset($post_data['pushover_message'])) $post_data['pushover_message'] = '';
				if (!isset($post_data['pushover_status'])) $post_data['pushover_status'] = 0;

				if (!empty($post_data['id'])){
					$q = $wpdb->prepare("SELECT * FROM $table WHERE id=%d ", $post_data['id']);
					$data = $wpdb->get_row($q);
					if (!empty($data)){
						/// UPDATE
						$q = $wpdb->prepare("UPDATE $table SET
											type=%s,
											rank_id=%s,
											subject=%s,
											message=%s,
											pushover_message=%s,
											pushover_status=%s,
											status=%s
										WHERE id=%d
						;", $post_data['type'], $post_data['rank_id'], stripslashes_deep($post_data['subject']), stripslashes_deep($post_data['message']),
						stripslashes_deep($post_data['pushover_message']), $post_data['pushover_status'], $post_data['status'], $post_data['id']);
						$wpdb->query($q);
								return;
					}
				}
				/// SAVE
				$q = $wpdb->prepare("INSERT INTO $table  VALUES(NULL,
																												%s,
																												%s,
																												%s,
																												%s,
																												%s,
																												%s,
																												%s);",
						 $post_data['type'], $post_data['rank_id'], $post_data['subject'], $post_data['message'],
						 $post_data['pushover_message'], $post_data['pushover_status'], $post_data['status']
				);
				$wpdb->query($q);
			}
		}

		public function register_get_custom_fields($only_public=FALSE, $exclude_fields=array()){
			/*
			 * @param boolean, array
			 * @return array
			 */
			$data = get_option('uap_register_fields');
			if ($data===FALSE){
				$defaults = array(
						array( 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'user_login', 'label'=>'Username', 'type'=>'text', 'native_wp' => 1, 'req' => 1, 'sublabel' => '' ),
						array( 'display_admin'=>2, 'display_public_reg'=>2, 'display_public_ap'=>2, 'name'=>'user_email', 'label'=>'Email', 'type'=>'text', 'native_wp' => 1, 'req' => 1, 'sublabel' => '' ),
						array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'confirm_email', 'label'=>'Confirm Email', 'type'=>'text', 'native_wp' => 0, 'req' => 2, 'sublabel' => '' ),
						array( 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'first_name', 'label'=>'First Name', 'type'=>'text', 'native_wp' => 1, 'req' => 1, 'sublabel' => '' ),
						array( 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'last_name', 'label'=>'Last Name', 'type'=>'text', 'native_wp' => 1, 'req' => 1, 'sublabel' => '' ),
						array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'user_url', 'label'=>'Website', 'type'=>'text', 'native_wp' => 1, 'req' => 0, 'sublabel' => '' ),
						array( 'display_admin'=>1, 'display_public_reg'=>2, 'display_public_ap'=>0, 'name'=>'pass1', 'label'=>'Password', 'type'=>'password', 'native_wp' => 1, 'req' => 1, 'sublabel' => '' ),
						array( 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>0, 'name'=>'pass2', 'label'=>'Confirm Password', 'type'=>'password', 'native_wp' => 1, 'req' => 2, 'sublabel' => '' ),
						array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'description', 'label'=>'Biographical Info', 'type'=>'textarea', 'native_wp' => 1, 'req' => 0, 'sublabel' => '' ),
						array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'phone', 'label'=>'Phone', 'type'=>'number', 'native_wp' => 0, 'req' => 0, 'sublabel' => '' ),
						array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'addr1', 'label'=>'Address 1', 'type'=>'textarea', 'native_wp' => 0, 'req' => 0, 'sublabel' => '' ),
						array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'addr2', 'label'=>'Address 2', 'type'=>'textarea', 'native_wp' => 0, 'req' => 0, 'sublabel' => '' ),
						array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'zip', 'label'=>'Zip', 'type'=>'text', 'native_wp' => 0, 'req' => 0, 'sublabel' => '' ),
						array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'city', 'label'=>'City', 'type'=>'text', 'native_wp' => 0, 'req' => 0, 'sublabel' => '' ),
						array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'thestate', 'label'=>'State', 'type'=>'text', 'native_wp' => 0, 'req' => 0, 'sublabel' => '' ),
						array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'country', 'label'=>'Country', 'type'=>'text', 'native_wp' => 0, 'req' => 0, 'sublabel' => '' ),
						array( 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'tos', 'label'=>'Accept', 'type'=>'checkbox', 'native_wp' => 0, 'req' => 2, 'sublabel' => '' ),
						array( 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'recaptcha', 'label'=>'Capcha', 'type'=>'capcha', 'native_wp' => 0, 'req' => 2, 'sublabel' => '' ),
						array( 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'uap_avatar', 'label'=>'Avatar', 'type'=>'upload_image', 'native_wp' => 0, 'req' => 0, 'sublabel' => '' ),
						array( 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'uap_country', 'label'=>'Country', 'type'=>'uap_country', 'native_wp' => 0, 'req' => 0, 'sublabel' => '' ),
				);
				update_option('uap_register_fields', $defaults);
				return $defaults;
			} else {
				if ($only_public){
					// ONLY PUBLIC
					$return = array();
					foreach ($data as $arr){
						if ($arr['display_public_reg']>0 && !in_array($arr['type'], $exclude_fields) && $arr['name']!='tos'){
							$return[$arr['name']] = $arr['name'];
						}
					}
					return $return;
				}
				/// RETURN ALL
				return $data;
			}
		}

		public function register_get_field($id){
			/*
			 * @param int
			 * @return array
			 */
			if ($id!=''){
				$data = $this->register_get_custom_fields();
				if (!empty($data[$id])){
					return $data[$id];
				}
			}
			return array(
							'name' => '',
							'label' => '',
							'type' => 'text',
							'values' => '',
							'sublabel' => '',
							'display_admin' => 0,
							'display_public_ap' => 0,
							'display_public_reg' => 0,
							'class' => '',
							'theme' => '',
							'plain_text_value' => '',
							'conditional_text' => '',
							'error_message' => '',
							'conditional_logic_show' => '',
							'conditional_logic_corresp_field' => '',
							'conditional_logic_corresp_field_value' => '',
							'conditional_logic_cond_type' => '',
							'native_wp' => 0,
							'req' => 0,
			);
		}

		public function register_save_custom_field($post_data=array()){
			/*
			 * @param array($_POST)
			 * @return none
			 */
			if ($post_data){
				$field_id = $post_data['id'];
				unset($post_data['id']);
				if (isset($post_data['save_field'])) unset($post_data['save_field']);

				$defaults = $this->register_get_field($field_id);
				$data = $this->register_get_custom_fields();

				if ($field_id){
					/// UPDATE
					if (isset($post_data['name']) && uap_value_exists_in_another_subarray($data, $post_data['name'], 'name', $field_id)){
						return;
					}
				} else {
					/// CREATE
					if (isset($post_data['name']) && uap_array_value_exists($data, $post_data['name'], 'name')!==FALSE){
						return;
					}
				}

				if ($field_id!=''){
					$data[$field_id] = array_merge($defaults, $post_data);
				} else {
					$data[] = array_merge($defaults, $post_data);
				}

				/// UPDATE PASSWORD FIELD, not required anymore
				$key = uap_array_value_exists($data, 'pass1', 'name');
				if (isset($data[$key])){
					if ($data[$key]['display_admin']==2){
						$data[$key]['display_admin'] = 1;
					}
					if ($data[$key]['display_public_ap']==2){
						$data[$key]['display_public_ap'] = 1;
					}
					if ($data[$key]['display_public_reg']==2){
						$data[$key]['display_public_reg'] = 1;
					}
				}
				/// UPDATE USERNAME FIELD, not required anymore
				$key = uap_array_value_exists($data, 'user_login', 'name');
				if (isset($data[$key])){
					if ($data[$key]['display_admin']==2){
						$data[$key]['display_admin'] = 1;
					}
					if ($data[$key]['display_public_ap']==2){
						$data[$key]['display_public_ap'] = 1;
					}
					if ($data[$key]['display_public_reg']==2){
						$data[$key]['display_public_reg'] = 1;
					}
				}

				update_option('uap_register_fields', $data);
			}
		}

		public function register_delete_custom_field($id){
			/*
			 * @param int
			 * @return none
			 */
			if (isset($id)){
				$data = get_option('uap_register_fields');
				if (!empty($data[$id])){
					unset($data[$id]);
					update_option('uap_register_fields', $data);
				}
			}
		}

		public function register_update_order($post_data){
			/*
			 * @param array
			 * @return none
			 */
			if ($post_data){
				$data = get_option('uap_register_fields');
				$new_data = array();
				foreach ($data as $k=>$v){
					$num = $post_data['uap-order-' . $k];
					$new_data[$num] = $v;
					if (isset($post_data['uap-field-display-admin' . $k])){
						$new_data[$num]['display_admin'] = $post_data['uap-field-display-admin' . $k];
					}
					if (isset($post_data['uap-field-display-public-reg' . $k])){
						$new_data[$num]['display_public_reg'] = $post_data['uap-field-display-public-reg' . $k];
					}
					if (isset($post_data['uap-field-display-public-ap' . $k])){
						$new_data[$num]['display_public_ap'] = $post_data['uap-field-display-public-ap' . $k];
					}
					if (isset($post_data['uap-require-' . $k])){
						$new_data[$num]['req'] = $post_data['uap-require-' . $k];
					}
				}
				update_option('uap_register_fields', $new_data);
			}
		}

		public function return_settings_from_wp_option($type, $only_name=false, $return_default=false){
			/*
			 * @param string, bool, bool
			 * @return array
			 */
			//all metas
			switch ($type){
				case 'login':
					$arr = array(
								   'uap_login_remember_me' => 1,
								   'uap_login_register' => 1,
								   'uap_login_pass_lost' => 1,
								   'uap_login_show_recaptcha' => 0,
								   'uap_login_template' => 'uap-login-template-9',
								   'uap_login_custom_css' => '',
								);
				break;
				case 'login-messages':
					$arr = array(
									'uap_login_succes' => 'Welcome on our Website!',
									'uap_login_pending' => 'Your Affiliate account was not been approved yet. Please, try again later.',
									'uap_login_error' => 'Invalid Affiliate Email Address or Password entered',
									'uap_reset_msg_pass_err' => 'Invalid Affiliate Email Address or Username entered',
									'uap_reset_msg_pass_ok' => 'An e-mail has been sent to You, follow the steps to change Your password.',
									'uap_login_error_email_pending' => 'Your Affiliate E-mail address has not been verified',
									'uap_login_error_on_captcha' => 'You have error on reCaptcha',
								);
				break;
				case 'general-settings':
					$arr = array(
							'uap_redirect_without_param' => 1,
							'uap_referral_variable' => 'ref',
							'uap_referral_custom_base_link' => '',
							'uap_campaign_variable' => 'campaign',
							'uap_default_ref_format' => 'username',
							'uap_cookie_expire' => 360,//value in days
							'uap_currency' => 'USD',
							'uap_currency_position' => 'right',
							'uap_referral_offer_type' => 'biggest',
							'uap_all_new_users_become_affiliates' => 0,
							'uap_exclude_shipping' => 1,
							'uap_exclude_tax' => 1,
							'uap_empty_referrals_enable' => 1,
					);
					break;
				case 'general-admin_workflow':
					$arr = array(
							'uap_update_ranks_interval' => 'daily',
							'uap_update_payments_status' => 'twicedaily',
							'uap_workflow_referral_status_dont_automatically_change' => 0,/// DISABLED OPTION
							'uap_admin_workflow_dashboard_notifications' => 1,
					);
					break;
				case 'general-public_workflow':
					$arr = array(
							'uap_hide_payments_warnings' => 0,
							'uap_default_payment_system' => 'bt',
							'uap_disable_bt_payment_system' => 0,
							'uap_custom_source_name_woo' => 'WooCommerce',
							'uap_custom_source_name_ump' => 'Ultimate Membership Pro',
							'uap_custom_source_name_ulp' => 'Ultimate Learning Pro',
							'uap_custom_source_name_edd' => 'Easy Download Digital',
							'uap_custom_source_name_bonus' => 'Bonus',
							'uap_custom_source_name_mlm' => 'MLM',
							'uap_custom_source_name_landing_commissions' => 'Landing commissions',
							'uap_custom_source_name_user_signup' => 'User SignUp',
							'uap_custom_source_name_ppc' => 'Pay per Click',
							'uap_custom_source_name_cpm' => 'CPM Commission',
					);
					break;
				case 'general-uploads':
					$arr = array(
							'uap_upload_extensions' => 'txt,doc,pdf,jpg,jpeg,png,gif,mp3,zip',
							'uap_upload_max_size' => 5,
							'uap_avatar_max_size' => 1,
					);
					break;
				case 'general-redirects':
					$arr = array(
							'uap_general_logout_redirect' => '',
							'uap_general_register_redirect' => '',
							'uap_general_login_redirect' => '',
							///
							'uap_general_account_page_no_logged_redirect' => '',
							'uap_general_login_page_logged_users_redirect' => '',
							'uap_general_register_page_logged_users_redirect' => '',
							'uap_general_logout_page_non_logged_users_redirect' => '',
							'uap_general_lost_pass_page_logged_users_redirect' => '',
					);
					break;
				case 'general-default_pages':
					$arr = array(
							'uap_general_login_default_page' => '',
							'uap_general_register_default_page'=>'',
							'uap_general_lost_pass_page' => '',
							'uap_general_logout_page' => '',
							'uap_general_user_page' => '',
							'uap_general_tos_page' => '',
					);
					break;
				case 'general-captcha':
					//recapcha
					$arr = array(
									'uap_recaptcha_public' => '',
									'uap_recaptcha_private' => '',
								);
				break;
				case 'general-msg':
					$arr = array(
									'uap_general_update_msg' => 'Successfully Update!',
								);
				break;
				case 'general-notification':
					$arr = array(
									'uap_notification_email_from' => '',
									'uap_notification_name' => '',
								);
				break;
				case 'register':
					$arr = array(
									'uap_register_template' => 'uap-register-9',
									'uap_register_admin_notify' => 1,
									'uap_register_pass_min_length' => 6,
									'uap_register_pass_options' => 1,
									'uap_register_new_user_rank' => 1,
									'uap_register_new_user_role' => 'subscriber',
									'uap_after_approve_role' => 'subscriber',
									'uap_register_custom_css' => '',
									'uap_register_terms_c' => __('Accept our Terms&Conditions', 'uap'),
									'uap_register_auto_login' => 0,
								);
				break;
				case 'register-msg':
					$arr = array(
									//messages
									'uap_register_username_taken_msg' => 'Username is taken',
									'uap_register_error_username_msg' => 'Invalid Username',
									'uap_register_email_is_taken_msg' => 'Email address is taken',
									'uap_register_invalid_email_msg' => 'You must enter a valid email address.',
									'uap_register_emails_not_match_msg' => 'Email Addresses did not match!',
									'uap_register_pass_not_match_msg' => 'Password did not match',
									'uap_register_pass_letter_digits_msg' => 'Password must contain characters and digits!',
									'uap_register_pass_let_dig_up_let_msg' => 'Password must contain characters, digits and minimum one uppercase letter!',
									'uap_register_pass_min_char_msg' => 'Password must contain minimum {X} characters!',
									'uap_register_pending_user_msg' => 'Your account has not been approved yet. Please try again later!',
									'uap_register_err_req_fields' => 'Please complete all required fields!',
									'uap_register_err_recaptcha' => 'Captcha Error',
									'uap_register_err_tos' => 'Error On Terms & Conditions',
									'uap_register_success_meg' => 'Successfully Register!',
									'uap_register_update_msg' => 'Successfully Updated!',
								);
				break;
				case 'register-custom-fields':
					$arr = array(
									'uap_register_fields' => $this->register_get_custom_fields(),
								);
				break;
				case 'opt_in':
					$arr = array(
									'uap_register_opt-in' => 0,
									'uap_register_opt-in-type' => 'email_list',
									'uap_main_email' => '',
									///active campaign
									'uap_active_campaign_apiurl' => '',
									'uap_active_campaign_apikey' => '',
									'uap_active_campaign_listId' => '',
									//aweber
									'uap_aweber_auth_code' => '',
									'uap_aweber_list' => '',
									'uap_aweber_consumer_key' => '',
									'uap_aweber_consumer_secret' => '',
									'uap_aweber_acces_key' => '',
									'uap_aweber_acces_secret' => '',
									//mailchimp
									'uap_mailchimp_api' => '',
									'uap_mailchimp_id_list' => '',
									//get response
									'uap_getResponse_api_key' => '',
									'uap_getResponse_token' => '',
									//campaign monitor
									'uap_cm_api_key' => '',
									'uap_cm_list_id' => '',
									//icontact
									'uap_icontact_user' => '',
									'uap_icontact_appid' => '',
									'uap_icontact_pass' => '',
									'uap_icontact_list_id' => '',
									//constant contact
									'uap_cc_user' => '',
									'uap_cc_pass' => '',
									'uap_cc_list' => '',
									//Wysija Contact
									'uap_wysija_list_id' => '',
									//MyMail
									'uap_mymail_list_id' => '',
									//Mad Mimi
									'uap_madmimi_username' => '',
									'uap_madmimi_apikey' => '',
									'uap_madmimi_listname' => '',
									//indeed email list
									'uap_email_list' => '',
								);
				break;
				case 'notifications':
					$arr = array(
									'uap_notification_email_from' => '',
									'uap_notification_before_time' => 5,
									'uap_notification_name' => '',
								);
				break;
				case 'account_page':
					$arr = array(	'uap_ap_top_theme' => 'uap-ap-top-theme-1',
									'uap_ap_theme' => 'uap-ap-theme-2',
									'uap_ap_edit_show_avatar' => 1,
									'uap_ap_edit_show_earnings' => 1,
									'uap_ap_edit_show_referrals' => 1,
									'uap_ap_edit_show_rank' => 1,
									'uap_ap_edit_background' => 1,
									'uap_ap_edit_show_metrics' => 0,
									'uap_ap_edit_background_image' => '',
									'uap_ap_edit_show_achievement' => 1,
									'uap_ap_tabs' => 'overview,payments_settings,logout,reports,campaign_reports,referrals,visits,banners,campaigns,affiliate_link,payments,edit_account,change_pass,help,referrals_history',
									'uap_ap_welcome_msg' => '<div class="uap-user-page-name"> {last_name} {first_name}</div>
															 <div class="uap-user-page-mess"><span>{flag}</span>Affiliate since {user_registered}</div>',
									'uap_ap_footer_msg' => '',
									'uap_account_page_custom_css' => '',
									/// TABS SETTINGS
									'uap_tab_overview_title' => __('Overview', 'uap'),
									'uap_tab_overview_menu_label' => __('Overview', 'uap'),
									'uap_tab_overview_content' => __('Hey There,
																This is the Overview section.
																&nbsp;
																Enjoy the sun.', 'uap'),
									'uap_tab_edit_account_title' => __('Edit Account', 'uap'),
									'uap_tab_edit_account_menu_label' => __('Edit Account', 'uap'),
									'uap_tab_edit_account_content' => '',
									'uap_tab_change_pass_title' => __('Change Password', 'uap'),
									'uap_tab_change_pass_menu_label' => __('Change Password', 'uap'),
									'uap_tab_change_pass_content' => '',
									'uap_tab_logout_menu_label' => __('LogOut', 'uap'),
									'uap_tab_affiliate_link_title' => __('Affiliate Links', 'uap'),
									'uap_tab_affiliate_link_menu_label' => __('Affiliate Links', 'uap'),
									'uap_tab_affiliate_link_content' => '',
									'uap_tab_campaigns_title' => __('Campaigns', 'uap'),
									'uap_tab_campaigns_menu_label' => __('Campaigns', 'uap'),
									'uap_tab_campaigns_content' => '',
									'uap_tab_banners_title' => __('Banners', 'uap'),
									'uap_tab_banners_menu_label' => __('Banners', 'uap'),
									'uap_tab_banners_content' => '',
									'uap_tab_visits_title' => __('Traffic Log', 'uap'),
									'uap_tab_visits_menu_label' => __('Traffic Log', 'uap'),
									'uap_tab_visits_content' => '',
									'uap_tab_referrals_title' => __('Referrals', 'uap'),
									'uap_tab_referrals_menu_label' => __('Referrals', 'uap'),
									'uap_tab_referrals_content' => '',
									'uap_tab_campaign_reports_title' => __('Campaign Reports', 'uap'),
									'uap_tab_campaign_reports_menu_label' => __('Campaign Reports', 'uap'),
									'uap_tab_campaign_reports_content' => '',
									'uap_tab_reports_title' => __('Overall', 'uap'),
									'uap_tab_reports_menu_label' => __('Overall', 'uap'),
									'uap_tab_reports_content' => '',
									'uap_tab_payments_title' => __('Payments', 'uap'),
									'uap_tab_payments_menu_label' => __('Payments', 'uap'),
									'uap_tab_payments_content' => '',
									'uap_tab_referrals_history_title' => __('Referrals History', 'uap'),
									'uap_tab_referrals_history_menu_label' => __('Referrals History', 'uap'),
									'uap_tab_referrals_history_content' => '',
									'uap_tab_help_title' => __('Help', 'uap'),
									'uap_tab_help_menu_label' => __('Help', 'uap'),
									'uap_tab_help_content' => '',
									'uap_tab_payments_settings_title' => __('Payment Settings', 'uap'),
									'uap_tab_payments_settings_menu_label' => __('Payment Settings', 'uap'),
									'uap_tab_payments_settings_content' => '',
									'uap_tab_coupons_title' => __('Coupons', 'uap'),
									'uap_tab_coupons_menu_label' => __('Coupons', 'uap'),
									'uap_tab_coupons_content' => '',
									'uap_tab_custom_affiliate_slug_title' => __('Custom Affiliate Slug', 'uap'),
									'uap_tab_custom_affiliate_slug_menu_label' => __('Custom Slug', 'uap'),
									'uap_tab_custom_affiliate_slug_content' => '',
									'uap_tab_mlm_title' => __('MLM', 'uap'),
									'uap_tab_mlm_menu_label' => __('MLM', 'uap'),
									'uap_tab_mlm_content' => '',
									'uap_tab_pushover_notifications_title' => __('Pushover Notifications', 'uap'),
									'uap_tab_pushover_notifications_menu_label' => __('Pushover Notifications', 'uap'),
									'uap_tab_pushover_notifications_content' => '',
									'uap_tab_wallet_title' => __('Wallet', 'uap'),
									'uap_tab_wallet_menu_label' => __('Wallet', 'uap'),
									'uap_tab_wallet_content' => '',
									'uap_tab_referral_notifications_title' => __('Notifications', 'uap'),
									'uap_tab_referral_notifications_menu_label' => __('Notifications', 'uap'),
									'uap_tab_referral_notifications_content' => '',
									'uap_tab_simple_links_title' => __('Simple Links', 'uap'),
									'uap_tab_simple_links_menu_label' => __('Simple Links', 'uap'),
									'uap_tab_simple_links_content' => '',
									'uap_tab_landing_pages_title' => __('Landing pages', 'uap'),
									'uap_tab_landing_pages_menu_label' => __('Landing pages', 'uap'),
									'uap_tab_landing_pages_content' => '',

									///icons
									'uap_tab_overview_icon_code' => 'f015',
									'uap_tab_edit_account_icon_code' => 'f007',
									'uap_tab_change_pass_icon_code' => 'f09c',
									'uap_tab_custom_affiliate_slug_icon_code' => 'f09c',
									'uap_tab_payments_settings_icon_code' => 'f0d6',
									'uap_tab_pushover_notifications_icon_code' => 'f0f3',
									'uap_tab_affiliate_link_icon_code' => 'f0c1',
									'uap_tab_simple_links_icon_code' => 'f08e',
									'uap_tab_banners_icon_code' => 'f03e',
									'uap_tab_coupons_icon_code' => 'f145',
									'uap_tab_referrals_icon_code' => 'f0c0',
									'uap_tab_payments_icon_code' => 'f0d6',
									'uap_tab_wallet_icon_code' => 'f260',
									'uap_tab_reports_icon_code' => 'f080',
									'uap_tab_visits_icon_code' => 'f25a',
									'uap_tab_campaign_reports_icon_code' => 'f25a',
									'uap_tab_referrals_history_icon_code' => 'f253',
									'uap_tab_mlm_icon_code' => 'f253',
									'uap_tab_referral_notifications_icon_code' => 'f0f3',
									'uap_tab_help_icon_code' => 'f059',
									'uap_tab_logout_icon_code' => 'f08b',
									'uap_tab_campaigns_icon_code' => 'f0a1',
									'uap_tab_landing_pages_icon_code' => 'f21d',
									'uap_tab_profile_icon_code' => '',
									'uap_tab_marketing_icon_code' => '',
									'uap_tab_reports_icon_code' => '',

							);

							//// get custom
							$custom = $this->account_page_get_custom_menu_items();
							if ($custom){
								foreach ($custom as $key => $temp_array){
									$arr = array_merge($arr, $temp_array);
								}
							}
					break;
				case 'double_email_verification':
					$arr = array(
									'uap_double_email_expire_time' => -1,
									'uap_double_email_redirect_success' => '',
									'uap_double_email_redirect_error' => '',
									'uap_double_email_delete_user_not_verified' => -1,
								);
					break;
				case 'licensing':
					$arr = array(
									'uap_license_set' => 0,
									'uap_envato_code' => '',
								);
					break;
				case 'sign_up_referrals':
					$arr = array(
									'uap_sign_up_referrals_enable' => 1,
									'uap_sign_up_amount_default' => 1,
									'uap_sign_up_default_referral_status' => 2, // 2 - verified , 1 - pending
					);
					break;
				case 'lifetime_commissions':
					$arr = array(
									'uap_lifetime_commissions_enable' => 1,
					);
					break;
				case 'reccuring_referrals':
					$arr = array(
									'uap_reccuring_referrals_enable' => 0,
					);
					break;
				case 'social_share':
					$arr = array(
									'uap_social_share_enable' => 0,
									'uap_social_share_message' => '',
									'uap_social_share_shortcode' => '',
					);
					break;
				case 'paypal':
					$arr = array(
									'uap_paypal_enable' => 0,
									'uap_paypal_sandbox' => 0,
									'uap_paypal_sandbox_client_id' => '',
									'uap_paypal_sandbox_client_secret' => '',
									'uap_paypal_client_id' => '',
									'uap_paypal_client_secret' => '',
					);
					break;
				case 'stripe':
					$arr = array(
									'uap_stripe_enable' => 0,
									'uap_stripe_sandbox' => 0,
									'uap_stripe_sandbox_secret_key' => '',
									'uap_stripe_sandbox_publishable_key' => '',
									'uap_stripe_secret_key' => '',
									'uap_stripe_publishable_key' => '',
				);
					break;
				case 'bonus_on_rank':
					$arr = array(
									'uap_bonus_on_rank_enable' => 0,
									'uap_bonus_on_rank_default_referral_sts' => 2,
					);
					break;
				case 'allow_own_referrence':
					$arr = array(
									'uap_allow_own_referrence_enable' => 0,
					);
					break;
				case 'mlm':
					$arr = array(
									'uap_mlm_enable' => 0,
									'uap_mlm_matrix_type' => 'binary',
									'uap_mlm_child_limit' => 2,
									'uap_mlm_matrix_depth' => 3,
									'uap_mlm_default_amount_value' => '',
									'uap_mlm_default_amount_type' => '',
									'mlm_amount_value_per_level' => '',
									'mlm_amount_type_per_level' => '',
									'uap_mlm_use_amount_from' => 'child_referral',
					);
					break;
				case 'rewrite_referrals':
					$arr = array(
									'uap_rewrite_referrals_enable' => 0,
					);
					break;
				case 'coupons':
					$arr = array(
									'uap_coupons_enable' => 0,
					);
					break;
				case 'friendly_links':
					$arr = array(
									'uap_friendly_links' => 0,
					);
					break;
				case 'custom_affiliate_slug':
					$arr = array(
									'uap_custom_affiliate_slug_on' => 0,
									'uap_custom_affiliate_slug_min_ch' => 4,
									'uap_custom_affiliate_slug_max_ch' => 10,
									'uap_custom_affiliate_slug_rule' => 0,
					);
					break;
				case 'wallet':
					$arr = array(
									'uap_wallet_enable' => 0,
									'uap_wallet_minimum_amount' => '',
									'uap_wallet_exclude_sources' => '',
					);
					break;
				case 'checkout_select_referral':
					$arr = array(
									'uap_checkout_select_referral_enable' => 0,
									'uap_checkout_select_referral_s_type' => 1,
									'uap_checkout_select_referral_label' => '',
									'uap_checkout_select_affiliate_list' => '',
									'uap_checkout_select_referral_rewrite' => 0,
									'uap_checkout_select_referral_require' => 0,
									'uap_checkout_select_referral_name' => 'user_login',
					);
					break;
				case 'top_affiliate_list':
					$arr = array(
									'uap_listing_users_custom_css' => '',
									'uap_listing_users_responsive_small' => 1,
									'uap_listing_users_responsive_medium' => 2,
									'uap_listing_users_responsive_large' => 0,
					);
					break;
				case 'woo_account_page':
					$arr = array(
									'uap_woo_account_page_name' => __('Affiliate', 'uap'),
									'uap_woo_account_page_enable' => 1,
									'uap_woo_account_page_menu_position' => 10,
									'uap_woo_account_page_show_to_everyone' => 1,
									'uap_woo_account_page_non_affiliate_content' => '<p>' . __('How about you become our affiliate?', 'uap') . '</p>[uap-user-become-affiliate]',
					);
					break;
				case 'bp_account_page':
					$arr = array(
									'uap_bp_account_page_name' => __('Affiliate', 'uap'),
									'uap_bp_account_page_enable' => 1,
									'uap_bp_account_page_position' => 10,
									'uap_bp_account_page_show_to_everyone' => 1,
									'uap_bp_account_page_non_affiliate_content' => '<p>' . __('How about you become our affiliate?', 'uap') . '</p>[uap-user-become-affiliate]',
					);
					break;
				case 'referral_notifications':
					$arr = array(
									'uap_referral_notifications_enable' => 0,
									'uap_referral_notification_subject' => '{blogname} New Referral ({referral_source})',
									'uap_referral_notification_content' => '
<div>Hi {first_name} {last_name},</div>
<div></div>
<div>Congratulations! You\’ve  received a new referral for your account ({username}):</div>
<div></div>
<div><strong>Amount :</strong> {referral_amount}</div>
<div><strong>Based on:</strong> {referral_source} ({referral_reference})</div>
<div><strong>Details :</strong> {referral_description}</div>
<div><strong>Status :</strong> {referral_status}</div>
<div></div>
<div></div>
<div>You can follow your referrals in your Account Page:</div>
<div><a href="{account_page}">{account_page}</a></div>
									',
					);
					break;
				case 'admin_referral_notifications':
					$arr = array(
									'uap_admin_referral_notifications_enable' => 0,
									'uap_admin_referral_notification_subject' => '{blogname} New Referral ({referral_source})',
									'uap_admin_referral_notification_content' => '
<div>Hi!</div>
<div></div>
<div>{first_name} {last_name}, ({username}) got a new referral:</div>
<div></div>
<div><strong>Amount :</strong> {referral_amount}</div>
<div><strong>Based on:</strong> {referral_source} ({referral_reference})</div>
<div><strong>Details :</strong> {referral_description}</div>
<div><strong>Status :</strong> {referral_status}</div>
<div></div>
<div></div>
<div>You can follow your referrals in your Account Page:</div>
<div>{account_page}</div>
<div></div>
<div></div>
<div>WooCommerce order details:</div>
<div>{WOOCOMMERCE_ORDER_DETAILS}</div>

									',
					);
					break;
				case 'periodically_reports':
					$arr = array(
									'uap_periodically_reports_enable' => 0,
									'uap_periodically_reports_cron_hour' => 0,
									'uap_periodically_reports_subject' => '{blogname} Periodical Report for {username} Affiliate Account',
									'uap_periodically_reports_content' => '
<div>Hi {first_name} {last_name},</div>
<div></div>
<div>Here\'s a quick overview of your Affiliate Account since your last report:</div>
<div></div>
<div><strong>Visits :</strong> {visits}</div>
<div><strong>Total Referrals :</strong> {total_referrals}</div>
<div><strong>Earnings :</strong> {total_earnings}</div>
<div><strong>Verified Referrals :</strong> {verified_referrals}</div>
<div><strong>Unverified Referrals :</strong> {unverified_referrals}</div>
<div><strong>Refuse Referrals :</strong> {refuse_referrals}</div>

									',
					);
					break;
				case 'qr_code':
					$arr = array(
									'uap_qr_code_enable' => 0,
									'uap_qr_code_size' => 5,
									'uap_qr_code_ecc_level' => 'h',
					);
					break;
				case 'email_verification':
					$arr = array(
									'uap_register_double_email_verification' => 0,
									'uap_double_email_expire_time' => -1,
									'uap_double_email_redirect_success' => '',
									'uap_double_email_redirect_error' => '',
									'uap_double_email_delete_user_not_verified' => -1,
					);
					break;
				case 'source_details':
					$arr = array(
									'uap_source_details_enable' => 0,
									'uap_source_details_woo_fields_list' => '',
									'uap_source_details_edd_fields_list' => '',
									'uap_source_details_ump_fields_list' => '',
									'uap_source_details_signup_fields_list' => '',
					);
					break;
				case 'wp_social_login':
					$arr = array(
									'uap_wp_social_login_on' => 0,
									'uap_wp_social_login_redirect_page' => '',
									'uap_wp_social_login_default_role' => '',
									'uap_wp_social_login_default_rank' => '',
					);
					break;
				case 'stripe_v2' :
					$arr = array(
									'uap_stripe_v2_enable' => 0,
									'uap_stripe_v2_sandbox' => 0,
									'uap_stripe_v2_sandbox_secret_key' => '',
									'uap_stripe_v2_sandbox_publishable_key' => '',
									'uap_stripe_v2_secret_key' => '',
									'uap_stripe_v2_publishable_key' => '',
					);
					break;
				case 'pushover':
					$arr = array(
									'uap_pushover_enabled' => 0,
									'uap_pushover_app_token' => '',
									'uap_pushover_admin_token' => '',
									'uap_pushover_url' => '',
									'uap_pushover_url_title' => '',
									'uap_pushover_sound' => 'bike',
					);
					break;
				case 'max_amount':
					$arr = array(
									'uap_maximum_amount_enabled' => 0,
									'uap_maximum_amount_value' => '',
									'uap_maximum_amount_value_per_rank' => array(),
					);
					break;
				case 'simple_links':
					$arr = array(
									'uap_simple_links_enabled' => 0,
									'uap_simple_links_limit' => 1,
					);
					break;
				case 'account_page_menu':
					$arr = array(
									'uap_account_page_menu_enabled' => 0,
									'uap_account_page_menu_order' => array(),
					);
					break;
				case 'ranks_pro':
					$arr = array(
									'uap_ranks_pro_enabled' => 0,
									'uap_default_achieve_calculation' => 'unlimited',
									'uap_achieve_period' => 30,
									'uap_ranks_pro_reset' => 0,
									'uap_ranks_pro_reset_day' => 1,
					);
					break;
				case 'landing_pages':
					$arr = array(
									'uap_landing_pages_enabled' => 0,
					);
					break;
				case 'pay_per_click':
					$arr = array(
									'uap_pay_per_click_enabled' => 0,
									'uap_pay_per_click_default_referral_sts' => 2,
					);
					break;
				case 'cpm_commission':
					$arr = array(
									'uap_cpm_commission_enabled' => 0,
									'uap_cpm_commission_default_referral_sts' => 2,
					);
					break;
				case 'pushover_referral_notifications':
					$arr = array(
									'uap_pushover_referral_notifications_enabled' => 0,
					);
					break;
				case 'rest_api':
					$arr = array(
									'uap_rest_api_enabled' => 0,
					);
					break;
			}

			if ($return_default){
				//return default values
				return $arr;
			}

			if (isset($arr)){
				if ($only_name){
					return $arr;
				}
				foreach ($arr as $k=>$v){
					$data = get_option($k);
					if ($data!==FALSE){
						$arr[$k] = $data;
					} else {
						add_option($k, $v);
					}
				}
				return $arr;
			}
			return FALSE;
		}

		public function save_settings_wp_option($type='', $post_data=array()){
			/*
			 * @param string, array
			 * @return none
			 */
			if ($type && $post_data){
				$data = $this->return_settings_from_wp_option($type, FALSE, FALSE);
				foreach ($data as $k=>$v){
					if (isset($post_data[$k])){
						update_option($k, $post_data[$k]);
					}
				}
			}
		}


		public function get_ranks($only_active=FALSE){
			/*
			 * @param boolean
			 * @return array
			 */
			global $wpdb;
			$table = $wpdb->prefix . 'uap_ranks';
			$q = "SELECT * FROM $table";
			if ($only_active){
				$q .= " WHERE status=1;";
			}
			$data = $wpdb->get_results($q);
			return $data;
		}

		public function ranks_get_count(){
			/*
			 * @param none
			 * @return int
			 */
			global $wpdb;
			$table = $wpdb->prefix . 'uap_ranks';
			$q = "SELECT COUNT(*) as c FROM $table";
			$data = $wpdb->get_row($q);
			if ($data && isset($data->c)){
				return $data->c;
			}
			return 0;
		}

		public function rank_save_update($post_data=array()){
			/*
			 * @param array
			 * @return array
			 */
			if ($post_data){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_ranks';
				$settings['color'] = (empty($post_data['color'])) ? '' : $post_data['color'];
				$settings['description'] = (empty($post_data['description'])) ? '' : stripslashes_deep($post_data['description']);
				$settings['description'] = str_replace("'", '', $settings['description']);
				$settings['description'] = str_replace('"', '', $settings['description']);

				$settings = serialize($settings);

				/// UPDATE RANK ORDER
				$ranks_arr = $this->get_ranks();
				if (!empty($ranks_arr)){
					foreach ($ranks_arr as $k=>$v){
						if ($v->rank_order==$post_data['rank_order'] && $v->id!=$post_data['id']){
							$swap_id = $v->id;
							break;
						}
					}
					if (!empty($swap_id)){
						//getting older order
						if ($post_data['id']==0){
							//new rank
							$data = $wpdb->get_row("SELECT rank_order FROM $table ORDER BY rank_order DESC LIMIT 1");
							$old_order = (empty($data->rank_order)) ? 1 : $data->rank_order + 1;
						} else {
							foreach ($ranks_arr as $k=>$v){
								if ($v->id==$post_data['id']){
									$old_order = $v->rank_order;
								}
							}
						}
						if (isset($swap_id) && isset($old_order)){
							$q = $wpdb->prepare("UPDATE $table SET rank_order='$old_order' WHERE id=%s ", $swap_id);
							$wpdb->query($q);
						}
					}
				}
				/// END OF UPDATE RANK ORDER

				/// FORCE EMPTY VALUE TO -1 FOR SIGN UP, LIFETIME AND RECCURING
				if ($post_data['sign_up_amount_value']==''){
					$post_data['sign_up_amount_value'] = -1;
				}
				if ($post_data['lifetime_amount_value']==''){
					$post_data['lifetime_amount_value'] = -1;
				}
				if ($post_data['reccuring_amount_value']==''){
					$post_data['reccuring_amount_value'] = -1;
				}

				if (!empty($post_data['id'])){
					$q = $wpdb->prepare("SELECT * FROM $table WHERE id=%d", $post_data['id']);
					$exists = $wpdb->get_row($q);
					if ($exists){
						//update
						$q = $wpdb->prepare("UPDATE $table SET
											slug=%s,
											label=%s,
											amount_type=%s,
											amount_value=%s,
											bonus=%s,
											pay_per_click=%s,
											cpm_commission=%s,
											sign_up_amount_value=%s,
											lifetime_amount_type=%s,
											lifetime_amount_value=%s,
											reccuring_amount_type=%s,
											reccuring_amount_value=%s,
											mlm_amount_type=%s,
											mlm_amount_value=%s,
											achieve=%s,
											rank_order=%s,
											status=%s,
											settings=%s,
											rank_order=%s
											WHERE id=%s
						;", $post_data['slug'], $post_data['label'], $post_data['amount_type'], $post_data['amount_value'],
						$post_data['bonus'],$post_data['pay_per_click'], $post_data['cpm_commission'], $post_data['sign_up_amount_value'], $post_data['lifetime_amount_type'],
						$post_data['lifetime_amount_value'], $post_data['reccuring_amount_type'], $post_data['reccuring_amount_value'],
						serialize($post_data['mlm_amount_type']), serialize($post_data['mlm_amount_value']), stripslashes_deep($post_data['achieve']),
						$post_data['rank_order'], $post_data['status'], $settings, $post_data['rank_order'], $post_data['id']
						);
						$wpdb->query($q);
						return;
					}
				}

					$exists = $wpdb->get_row("SELECT id FROM $table WHERE slug='" . $post_data['slug'] . "';");
					if (!empty($exists) && !empty($exists)){
						/// SAME SLUG NOT ALLOWED
						return;
					}

					$q = $wpdb->prepare("INSERT INTO $table VALUES(NULL,
									%s,
									%s,
									%s,
									%s,
									%s,
									%s,
									%s,
									%s,
									%s,
									%s,
									%s,
									%s,
									%s,
									%s,
									%s,
									%s,
									%s,
									%s)
					;", $post_data['slug'], $post_data['label'], $post_data['amount_type'], $post_data['amount_value'],
					$post_data['bonus'], $post_data['pay_per_click'], $post_data['cpm_commission'], $post_data['sign_up_amount_value'], $post_data['lifetime_amount_type'], $post_data['lifetime_amount_value'],
					$post_data['reccuring_amount_type'], $post_data['reccuring_amount_value'], serialize($post_data['mlm_amount_type']),
					serialize($post_data['mlm_amount_value']), stripslashes_deep($post_data['achieve']), $settings, $post_data['rank_order'], $post_data['status']
					);
					$wpdb->query($q);
			}
		}

		public function get_rank($id=0){
			/*
			 * @param int
			 * @return array
			 */
			global $wpdb;
			$table = $wpdb->prefix . 'uap_ranks';
			if ($id){
				$q = $wpdb->prepare("SELECT * FROM $table WHERE id=%d ;", $id);
				$data = $wpdb->get_row($q);
				if ($data){
					$data = (array)$data;
					$settings = unserialize($data['settings']);
					$data['color'] = (empty($settings['color'])) ? '' : $settings['color'];
					$data['description'] = (empty($settings['description'])) ? '' : $settings['description'];
					@$data['mlm_amount_value'] = (empty($data['mlm_amount_value'])) ? array() : unserialize($data['mlm_amount_value']);
					@$data['mlm_amount_type'] = (empty($data['mlm_amount_type'])) ? array() : unserialize($data['mlm_amount_type']);
					return $data;
				}
			} else {
				/// DEFAULTS
				$data = $wpdb->get_row("SELECT id FROM $table ORDER BY id DESC LIMIT 1");
				$id = (empty($data->id)) ? 1 : $data->id + 1;
				$data = $wpdb->get_row("SELECT rank_order FROM $table ORDER BY rank_order DESC LIMIT 1");
				$rank_order = (empty($data->rank_order)) ? 1 : $data->rank_order + 1;
				return array(
								'id' => 0,
								'slug' => 'rank_' . $id,
								'label' => 'Untitled Rank',
								'amount_type' => '%',
								'amount_value' => 1,
								'achieve' => '',
								'rank_order' => $rank_order,
								'color' => '',//from settings
								'description' => '',//from settings
								'bonus' => '',
								'pay_per_click' => '',
								'cpm_commission' => '',
								'sign_up_amount_value' => '',
								'lifetime_amount_type' => '',
								'lifetime_amount_value' => '',
								'reccuring_amount_type' => '',
								'reccuring_amount_value' => '',
								'mlm_amount_type' => '',
								'mlm_amount_value' => '',
								'status' => 1,
				);
			}
		}

		public function delete_rank($id){
			/*
			 * @param int
			 * @return none
			 */
			if ($id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_ranks';
				$q = $wpdb->prepare("DELETE FROM $table WHERE id=%d ", $id);
				$data = $wpdb->get_results($q);
			}
		}

		public function get_rank_list(){
			/*
			 * @param none
			 * @return array
			 */
			global $wpdb;
			$data = $this->get_ranks();
			$data = uap_reorder_ranks($data);
			$return = array();
			if ($data && is_array($data)){
				foreach ($data as $k=>$obj){
					$return[$obj->id] = $obj->label;
				}
			}
			return $return;
		}

		public function pay_bonus_for_rank($uid=0, $rank_id=0){
			/*
			 * @param int, int
			 * @return none
			 */
			 if ($uid && $rank_id && $this->is_magic_feat_enable('bonus_on_rank')){
			 	$affiliate_id = $this->get_affiliate_id_by_wpuid($uid);
			 	$rank_data = $this->get_rank($rank_id);
				$amount_value = $rank_data['bonus'];
				if ($amount_value===FALSE || $amount_value==''){
					$amount_value = 0;
				}
				$rank_name = $rank_data['label'];
				$currency = get_option('uap_currency');
				$status = get_option('uap_bonus_on_rank_default_referral_sts');
				if ($status===FALSE){
					$status = 2; /// verified
				}

				/// EMPTY REFERRALS
				$general_settings_data = $this->return_settings_from_wp_option('general-settings');
				if (empty($general_settings_data['uap_empty_referrals_enable'])){
					///don't insert referrals with 0$
					$min = 0.01;
					if ($amount_value<$min){
						return;
					}
				}
				/// EMPTY REFERRALS

				$args = array(
						'refferal_wp_uid' => 0,
						'campaign' => '',
						'affiliate_id' => $affiliate_id,
						'visit_id' => '',
						'description' => __('Bonus for reaching rank: ', 'uap') . $rank_name,
						'source' => 'bonus',
						'reference' => 0,
						'reference_details' => 'Bonus',
						'amount' => $amount_value,
						'currency' => $currency,
						'date' => date('Y-m-d H:i:s', time()),
						'status' => $status,
						'payment' => 0,
						'parent_referral_id' => '',
						'child_referral_id' => '',
				);
				$this->save_referral($args);
			 }
		}

		public function update_affiliate_rank_by_uid($uid, $rank_id){
			/*
			 * This will change the affiliate rank and pay the bonus if it's case.
			 * @param int, int
			 * @return none
			 */
			global $wpdb;
			$table = $wpdb->prefix . 'uap_affiliates';
			$q = $wpdb->prepare("SELECT id, rank_id FROM $table WHERE uid=%d ;", $uid);
			$exists = $wpdb->get_row($q);
			if (!empty($exists->id) && $exists->rank_id!=$rank_id){
				$q = $wpdb->prepare("UPDATE $table SET rank_id=%d WHERE uid=%d ;", $rank_id, $uid);
				$wpdb->query($q);

				///rank history
				$this->add_new_rank_to_history($exists->id, $exists->rank_id, $rank_id);

				/// PAY BONUS
				$this->pay_bonus_for_rank($uid, $rank_id);
			}
		}

		public function setAllAffiliateRankAsValue($rankId='')
		{
				global $wpdb;
				$q = $wpdb->prepare("UPDATE {$wpdb->prefix}uap_affiliates SET rank_id=%d ;", $rankId);
				$wpdb->query($q);
		}

		public function update_affiliate_rank($id=0, $rank_id=0){
			/*
			 * Used Only in Change Ranks class, this will not pay the bonus like "update_affiliate_rank_by_uid" do.
			 * @param int, int
			 * @return none
			 */
			if ($id && $rank_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_affiliates';
				$q = $wpdb->prepare("SELECT rank_id FROM $table WHERE id=%d ", $id);
				$exists = $wpdb->get_row($q);
				if (isset($exists->rank_id) && $exists->rank_id!=$rank_id){
					$q = $wpdb->prepare("UPDATE $table SET rank_id=%d WHERE id=%d ", $rank_id, $id);
					$wpdb->query($q);

					///rank history
					$this->add_new_rank_to_history($id, $exists->rank_id, $rank_id);
				}
			}
		}

		public function affiliates_with_no_rank_exists(){
			/*
			 * @param none
			 * @return boolean
			 */
			global $wpdb;
			$affiliates = $wpdb->prefix . 'uap_affiliates';
		    $ranks = $wpdb->prefix . 'uap_ranks';
			$q = "SELECT rank_id
					FROM $affiliates
					WHERE NOT EXISTS
					    (SELECT *
					     FROM $ranks
					     WHERE $ranks.id = $affiliates.rank_id)";
			$data = $wpdb->get_results($q);
			if ($data){
				return TRUE;
			}
			return FALSE;
		}

		public function add_new_rank_to_history($uid=0, $prev_rid=0, $rid=0){
			/*
			 * @param int, int, int
			 * @return none
			 */
			if ($uid && isset($rid)){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_ranks_history';
				$q = $wpdb->prepare("INSERT INTO $table VALUES(null, %d, %d, %d, NOW());", $uid, $prev_rid, $rid);
				$wpdb->query($q);
			}
		}

		public function get_last_rank_achievements($limit=50, $search='', $affiliate_id=0){
			/*
			 * @param int, string, int
			 * @return array
			 */
			global $wpdb;
			$arr = array();
			$table_a = $wpdb->prefix . 'uap_ranks_history';
			$table_b = $wpdb->prefix . 'uap_affiliates';
			$table_c = $wpdb->base_prefix . 'users';
			$q = "SELECT a.* FROM $table_a as a
					INNER JOIN $table_b as b
					ON a.affiliate_id=b.id
					INNER JOIN $table_c as c
					ON b.uid=c.ID
					WHERE 1=1
			";
			if (!empty($search)){
				$search = esc_sql($search);
				$q .= " AND c.user_login LIKE '%$search%'";
			}
			if ($affiliate_id){
				$affiliate_id = esc_sql($affiliate_id);
				$q .= " AND a.affiliate_id=$affiliate_id";
			}
			$q .= " ORDER BY a.add_date DESC";
			if ($limit>-1){
				$limit = esc_sql($limit);
				$q .= " LIMIT $limit";
			}
			$data = $wpdb->get_results($q);
			$ranks = $this->get_rank_list();
			if ($data){
				foreach ($data as $object){
					$inside_data = (array)$object;

					if (!empty($inside_data['rank_id']) && !empty($ranks[$inside_data['rank_id']])){
						$arr[$object->id]['current_rank'] = $ranks[$inside_data['rank_id']];
					} else if ($inside_data['rank_id']==0){
						$arr[$object->id]['current_rank'] = __('None', 'uap');
					}
					if (!empty($inside_data['prev_rank_id']) && !empty($ranks[$inside_data['prev_rank_id']])){
						$arr[$object->id]['prev_rank'] = $ranks[$inside_data['prev_rank_id']];
					} else if (empty($inside_data['prev_rank_id']) && (empty($inside_data['rank_id']) || $inside_data['rank_id']==-1) ){
						continue;
					} else if ($inside_data['prev_rank_id']==0){
						$arr[$object->id]['prev_rank'] = __('None', 'uap');
					}
					$arr[$object->id]['username'] = $this->get_wp_username_by_affiliate_id($inside_data['affiliate_id']);
					$arr[$object->id]['add_date'] = $object->add_date;
				}

			}
			return $arr;
		}

		public function get_achievements_for_affiliate_id($affiliate_id=0){
			/*
			 * @param int
			 * @return array
			 */
			$return = array();
			if ($affiliate_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_ranks_history';
				$q = $wpdb->prepare("SELECT * FROM $table WHERE affiliate_id=%d ORDER BY add_date DESC ;", $affiliate_id);
				$data = $wpdb->get_results($q);
				$ranks = $this->get_rank_list();
				if ($data){
					foreach ($data as $object){
						$inside_data = (array)$object;
						if (!empty($inside_data['rank_id'])){
							$return[$object->id]['current_rank'] = $ranks[$inside_data['rank_id']];
						} else if ($arr[$object->id]['rank_id']==0){
							$return[$object->id]['current_rank'] = __('None', 'uap');
						}
						if (!empty($inside_data['prev_rank_id'])){
							$return[$object->id]['prev_rank'] = $ranks[$inside_data['prev_rank_id']];
						} else if ($inside_data['prev_rank_id']==0){
							$arr[$object->id]['prev_rank'] = __('None', 'uap');
						}
						$return[$object->id]['add_date'] = $object->add_date;
					}
				}
			}
			return $return;
		}

		public function get_affiliate_rank($id=0, $uid=0){
			/*
			 * @param int, int
			 * @return int
			 */
			global $wpdb;
			$table = $wpdb->prefix . 'uap_affiliates';
			$q = "SELECT rank_id FROM $table WHERE ";
			if ($id){
				$id = esc_sql($id);
				$q .= "id='$id' ";
			} else {
				$uid = esc_sql($uid);
				$q .= "uid='$uid' ";
			}
			$data = $wpdb->get_row($q);
			if (!empty($data->rank_id)){
				return $data->rank_id;
			} return 0;
		}


		public function get_visits($limit=-1, $offset=-1, $count=FALSE, $order_by='', $order_type='', $where_conditions=array() ){
			/*
			 * @param none
			 * @return array
			 */
			global $wpdb;
			$return = array();
			$table = $wpdb->prefix . "uap_visits";
			$table_affiliates = $wpdb->prefix . 'uap_affiliates';
			$table_users = $wpdb->base_prefix . 'users';
			if ($count){
				$q = "SELECT COUNT(*) as c FROM $table v";
				$q .= " INNER JOIN $table_affiliates a ON v.affiliate_id=a.id";
				$q .= " INNER JOIN $table_users u ON a.uid=u.ID";
				$q .= " WHERE 1=1";
				if (!empty($where_conditions)){
					foreach ($where_conditions as $condition){
						$q .= " AND " . $condition;
					}
				}

				$data = $wpdb->get_row($q);
				if (isset($data->c)){
					return $data->c;
				}
			} else {
				$q = "SELECT v.* FROM $table v";
				$q .= " INNER JOIN $table_affiliates a ON v.affiliate_id=a.id";
				$q .= " INNER JOIN $table_users u ON a.uid=u.ID";
				$q .= " WHERE 1=1";
				if (!empty($where_conditions)){
					foreach ($where_conditions as $condition){
						$q .= " AND " . $condition ;
					}
				}

				if ($order_type && $order_by){
					$order_by = esc_sql($order_by);
					$order_type = esc_sql($order_type);
					$q .= " ORDER BY " . $order_by . " " . $order_type;
				}
				if ($limit>-1 && $offset>-1){
					$limit = esc_sql($limit);
					$offset = esc_sql($offset);
					$q .= " LIMIT " . $limit . " OFFSET " . $offset;
				}

				$data = $wpdb->get_results($q);
			}
			if (!empty($data)){
				foreach ($data as $object){
					$array = (array)$object;
					$array['username'] = $this->get_wp_username_by_affiliate_id($array['affiliate_id']);
					$return[] = $array;
				}
			}
			return $return;
		}

		public function delete_visits($delete_arr=array()){
			/*
			 * @param array (post data)
			 * @return none
			 */
			if (!empty($delete_arr) && !empty($delete_arr)){
				global $wpdb;
				$db_name = $wpdb->prefix . "uap_visits";
				if (!is_array($delete_arr)){
					$post_data['delete_banner'] = array($delete_arr);
				}
				foreach ($delete_arr as $id){
						$q = $wpdb->prepare("DELETE FROM $db_name WHERE id=%d ", $id);
						$wpdb->query($q);
				}
			}
		}

		public function track_the_visit($visitor_hash='', $referral_id=0, $affiliate_id=0, $url='', $ip='', $browser='', $device='', $campaign_name=''){
			/*
			 * $campaign_name
			 * @param strings
			 * @return int
			 */
			global $wpdb;
			$visit_table = $wpdb->prefix . "uap_visits";
			$campaign_table = $wpdb->prefix . 'uap_campaigns';

			/// CAMPAIGN
			if ($campaign_name){
				/// check if campaign-affiliate exists
				$q = $wpdb->prepare("SELECT id, visit_count, unique_visits_count
												FROM $campaign_table
												WHERE name=%s
												AND affiliate_id=%d;", $campaign_name, $affiliate_id);
				$temp_data = $wpdb->get_row($q);
				if (!empty($temp_data->id) && isset($temp_data->visit_count) && isset($temp_data->unique_visits_count)){
					/// UPDATE
					$unique = (int)$temp_data->unique_visits_count;
					$visits = (int)$temp_data->visit_count;
					$q = $wpdb->prepare("SELECT campaign_name FROM $visit_table WHERE ref_hash=%s AND affiliate_id=%d;", $visitor_hash, $affiliate_id);
					$data = $wpdb->get_row($q);
					if (!empty($data) && !empty($data->campaign_name) && $data->campaign_name==$campaign_name){

					} else {
						$unique++;
					}
					$visits++;
					$q = $wpdb->prepare("UPDATE $campaign_table
																	SET visit_count=%s, unique_visits_count=%s
																	WHERE id=%d;", $visits, $unique, $temp_data->id);
					$wpdb->query($q);
				}
			}

			/// END CAMPAIGN
			$q = $wpdb->prepare("INSERT INTO $visit_table
									VALUES(null, %s, %d, %d, %s, %s, %s, %s, %s, NOW(), null);", $visitor_hash, $referral_id, $affiliate_id, $campaign_name, $ip, $url, $browser, $device);
			$wpdb->query($q);
			return $wpdb->insert_id;
		}

		public function get_campaigns_reports_for_affiliate_id($id=0, $limit=-1, $offset=-1, $only_counts=FALSE, $order_by='', $order_type='', $where_conditions=array()){
			/*
			 * @param int, int, int, boolean
			 * @return array
			 */
			if ($id){
				global $wpdb;
				$table = $wpdb->prefix . "uap_campaigns";
				if ($only_counts){
					$q = $wpdb->prepare("SELECT COUNT(id) as c FROM $table WHERE affiliate_id=%d ", $id);
					if (!empty($where_conditions)){
						foreach ($where_conditions as $condition){
							$q .= " AND " . $condition ;
						}
					}
					$data = $wpdb->get_row($q);
					if (isset($data->c)){
						return $data->c;
					}
				} else {
					$q = "SELECT * FROM $table";
					$q .= " WHERE 1=1 ";
					$id = esc_sql($id);
					$q .= " AND affiliate_id='$id'";
					if (!empty($where_conditions)){
						foreach ($where_conditions as $condition){
							$q .= " AND " . $condition ;
						}
					}
					if ($order_type && $order_by){
						$order_by = esc_sql($order_by);
						$order_type = esc_sql($order_type);
						$q .= " ORDER BY " . $order_by . " " . $order_type;
					}
					if ($limit>-1 && $offset>-1){
						$limit = esc_sql($limit);
						$offset = esc_sql($offset);
						$q .= " LIMIT " . $limit . " OFFSET " . $offset;
					}
					$data = $wpdb->get_results($q);
					if ($data){
						return $data;
					}
				}
			}
			return array();
		}

		public function set_visit_referral_id($visit_id, $referral_id){
			/*
			 * update db, set referral id with wordpress user id
			 * @param int, int
			 * @return none
			 */
			global $wpdb;
			$table = $wpdb->prefix . "uap_referrals";
			$q = $wpdb->prepare("UPDATE $table SET referral_id=%d WHERE id=%d ;", $referral_id, $visit_id);
			$wpdb->query($q);
		}

		public function get_referrals($limit=-1, $offset=-1, $count=FALSE, $order_by='', $order_type='', $where_conditions=array()){
			/*
			 * @param int, int, boolean
			 * @return array/int
			 */
			global $wpdb;
			$return = array();
			$table = $wpdb->prefix . "uap_referrals";
			$table_affiliates = $wpdb->prefix . 'uap_affiliates';
			$table_users = $wpdb->base_prefix . 'users';
			if ($count){
				$q = "SELECT COUNT(*) as c FROM $table r";
				$q .= " INNER JOIN $table_affiliates a ON a.id=r.affiliate_id";
				$q .= " INNER JOIN $table_users u ON u.ID=a.uid";
				$q .= " WHERE 1=1";
				if (!empty($where_conditions)){
					foreach ($where_conditions as $condition){
						$q .= " AND " . $condition ;
					}
				}
				$data = $wpdb->get_row($q);
				if (isset($data->c)){
					return (int)$data->c;
				}
			} else {
				$q = "SELECT r.* FROM $table r";
				$q .= " INNER JOIN $table_affiliates a ON a.id=r.affiliate_id";
				$q .= " INNER JOIN $table_users u ON u.ID=a.uid";
				$q .= " WHERE 1=1";
				if (!empty($where_conditions)){
					foreach ($where_conditions as $condition){
						$q .= " AND " . $condition ;
					}
				}
				if ($order_type && $order_by){
					$order_by = esc_sql($order_by);
					$order_type = esc_sql($order_type);
					$q .= " ORDER BY " . $order_by . " " . $order_type;
				}
				if ($limit>-1 && $offset>-1){
					$limit = esc_sql($limit);
					$offset = esc_sql($offset);
					$q .= " LIMIT " . $limit . " OFFSET " . $offset;
				}
				$data = $wpdb->get_results($q);
			}

			if (!empty($data)){
				foreach ($data as $object){
					$array = (array)$object;
					$array['username'] = $this->get_wp_username_by_affiliate_id($array['affiliate_id']);
					$return[] = $array;
				}
			}
			return $return;
		}

		public function get_referral_report_by_date($affiliate_id=0, $start_time='', $end_time=''){
			/*
			 * @param int, string, string
			 * @return array
			 */
			 $array = array(
			 				'total_earnings' => 0,
			 				'total_referrals' => 0,
			 				'refuse_referrals' => 0,
			 				'unverified_referrals' => 0,
			 				'verified_referrals' => 0,
			 				'visits' => 0,
			 );
			 if ($affiliate_id){
				 global $wpdb;
				 $table = $wpdb->prefix . "uap_referrals";
				 /// referrals total amount
				 $q = $wpdb->prepare("SELECT SUM(amount) as result,
				 														 COUNT(id) as count_referrals
																		 FROM $table
																		 WHERE affiliate_id=%d
																		 AND date>%s
																		 AND date<%s ",
															$affiliate_id, $start_time, $end_time
				 );
				 $temp = $wpdb->get_row($q);
				 if ($temp && isset($temp->result)){
				 	$array['total_earnings'] = $temp->result;
				 }
				 if ($temp && isset($temp->count_referrals)){
				 	$array['total_referrals'] = $temp->count_referrals;
				 }
				 $q = $wpdb->prepare("SELECT COUNT(id) as count_referrals
				 													FROM $table
																	WHERE affiliate_id=%d
																	AND date>%s
																	AND date<%s
																	AND status=0;", $affiliate_id, $start_time, $end_time);
				 $temp = $wpdb->get_row($q);
				 if ($temp && isset($temp->count_referrals)){
				 	$array['refuse_referrals'] = $temp->count_referrals;
				 }
				 $q = $wpdb->prepare("SELECT COUNT(id) as count_referrals
				 													FROM $table
																	WHERE affiliate_id=%d
																	AND date>%s
																	AND date<%s
																	AND status=1;", $affiliate_id, $start_time, $end_time);
				 $temp = $wpdb->get_row($q);
				 if ($temp && isset($temp->count_referrals)){
				 	$array['unverified_referrals'] = $temp->count_referrals;
				 }
				 $q = $wpdb->prepare("SELECT COUNT(id) as count_referrals FROM $table WHERE affiliate_id=%d AND date>%s AND date<%s AND status=2;",
				 													$affiliate_id, $start_time, $end_time);
				 $temp = $wpdb->get_row($q);
				 if ($temp && isset($temp->count_referrals)){
				 	$array['verified_referrals'] = $temp->count_referrals;
				 }

				 $table = $wpdb->base_prefix . 'uap_visits';
				 $q = $wpdb->prepare("SELECT COUNT(id) as visits FROM $table WHERE affiliate_id=%d AND visit_date>%s AND visit_date<%s;", $affiliate_id, $start_time, $end_time);
				 $temp = $wpdb->get_row($q);
				 if ($temp && isset($temp->visits)){
				 	$array['visits'] = $temp->visits;
				 }
			 }
			 return $array;
		}

		public function get_referral($id){
			/*
			 * @param int
			 * @return array
			 */
			if ($id){
				global $wpdb;
				$table = $wpdb->prefix . "uap_referrals";
				$q = $wpdb->prepare("SELECT * FROM $table WHERE id=%d ", $id);
				$data = $wpdb->get_row($q);
				if ($data){
					return (array)$data;
				}
				return array();
			} else {
				return array(
								'id' => 0,
								'refferal_wp_uid' => '',
								'campaign' => '',
								'affiliate_id' => '',
								'visit_id' => '',
								'description' => '',
								'source' => '',
								'reference' => '',
								'reference_details' => '',
								'parent_referral_id' => '',
								'child_referral_id' => '',
								'amount' => 0,
								'currency' => 'USD',
								'date' => '',
								'status' => 1,//unverified
								'payment' => 0,
				);
			}
		}

		public function referral_get_amount_by_id($referral_id=0){
			/*
			 * @param int
			 * @return float
			 */
			if ($referral_id){
				global $wpdb;
				$table = $wpdb->prefix . "uap_referrals";
				$q = $wpdb->prepare("SELECT amount FROM $table WHERE id=%d;", $referral_id);
				$data = $wpdb->get_row($q);
				if (!empty($data->amount)){
					return $data->amount;
				}
			}
			return 0;
		}

		public function save_referral_from_admin($post_data=array()){
			/*
			 * @param array
			 * @return boolean
			 */
			 if (!empty($post_data)){
			 	 global $wpdb;
				 $table = $wpdb->prefix . 'uap_referrals';
				 $table_b = $wpdb->prefix . 'uap_visits';

				 /*
				 if (!empty($post_data['visit_id'])){
				 	 /// CHECK VISIT

					 if ($post_data['id']){
					 	/// UPDATE
					 	$exists = $wpdb->get_row("SELECT * FROM $table WHERE visit_id='" . $post_data['visit_id'] . "'
					 								AND id<>'" . $post_data['id'] . "' ");
						if (!empty($exists)){
							/// NO SAME VISIT ID ALLOWER
							return FALSE;
						}
						$exists = $wpdb->get_row("SELECT referral_id FROM $table_b WHERE id='" . $post_data['visit_id'] . "' ");
						if ($exists && !empty($exists->referral_id) && $exists->referral_id!=$post_data['id']){
							/// visit already exists, out
							return FALSE;
						}
					 } else {
						/// INSERT
						$exists = $wpdb->get_row("SELECT * FROM $table WHERE visit_id='" . $post_data['visit_id'] . "' ");
						if (!empty($exists)){
							return FALSE;
						}
						$exists = $wpdb->get_row("SELECT referral_id FROM $table_b WHERE id='" . $post_data['visit_id'] . "' ");
						if ($exists && !empty($exists->referral_id)){
							/// visit already exists, out
							return FALSE;
						}
					 }

				 }
				 */

				 $referral_id = $this->save_referral($post_data);

				 /// UPDATE VISITS TABLE
				 if (!empty($post_data['visit_id'])){
					 	$q = $wpdb->prepare("UPDATE $table_b SET referral_id='$referral_id' WHERE id=%d ", $post_data['visit_id']);
						$wpdb->query($q);
				 }
				 return TRUE;
			 }
		}

		public function save_referral($post_data=array()){
			/*
			 * @param array
			 * @return int
			 */
			if (!empty($post_data)){
				global $wpdb;
				$table = $wpdb->prefix . "uap_referrals";
				if (empty($post_data['date'])){
					$post_data['date'] = date('Y-m-d H:i:s', time());
				}

				////// MAX AMOUNT
				if ($this->is_magic_feat_enable('max_amount')){
					$rank_id = $this->get_affiliate_rank($post_data['affiliate_id']);
					$max_amount_per_rank = get_option('uap_maximum_amount_value_per_rank');
					if ($max_amount_per_rank && isset($max_amount_per_rank[$rank_id]) && $max_amount_per_rank[$rank_id]!=''){
						if ($max_amount_per_rank[$rank_id]<$post_data['amount']){
							$post_data['amount'] = $max_amount_per_rank[$rank_id];
						}
					} else {
						$max_amount_value = get_option('uap_maximum_amount_value');
						if (!empty($max_amount_value) && $post_data['amount']>$max_amount_value){ ///
							$post_data['amount'] = $max_amount_value;
						}
					}
				}
				////// MAX AMOUNT

				if (!empty($post_data['id'])){
					$q = $wpdb->prepare("SELECT * FROM $table WHERE id=%d ", $post_data['id']);
					$data = $wpdb->get_row($q);
					if (!empty($data)){
								$post_data = apply_filters('uap_update_referral_filter', $post_data);
								/// UPDATE
								$q = $wpdb->prepare("UPDATE $table SET
													refferal_wp_uid=%d,
													campaign=%s,
													affiliate_id=%d,
													visit_id=%d,
													description=%s,
													source=%s,
													reference=%s,
													reference_details=%s,
													parent_referral_id=%d,
													child_referral_id=%d,
													amount=%s,
													currency=%s,
													date=%s,
													status=%s,
													payment=%s
												WHERE id=%d
								;", $post_data['refferal_wp_uid'], $post_data['campaign'], $post_data['affiliate_id'],
										$post_data['visit_id'], $post_data['description'], $post_data['source'], $post_data['reference'],
										$post_data['reference_details'], $post_data['parent_referral_id'], $post_data['child_referral_id'],
										$post_data['amount'], $post_data['currency'], $post_data['date'], $post_data['status'], $post_data['payment'],
										$post_data['id']
								);
								$wpdb->query($q);
								return $post_data['id'];
						}
					}
					$post_data = apply_filters('uap_save_referral_filter', $post_data);
					/// SAVE
					$q = $wpdb->prepare("INSERT INTO $table
										VALUES( NULL,
												%d,
												%s,
												%d,
												%d,
												%s,
												%s,
												%s,
												%s,
												%d,
												%d,
												%s,
												%s,
												%s,
												%s,
												%s );
					", $post_data['refferal_wp_uid'], $post_data['campaign'], $post_data['affiliate_id'],
					$post_data['visit_id'], $post_data['description'], $post_data['source'], $post_data['reference'],
					$post_data['reference_details'], $post_data['parent_referral_id'], $post_data['child_referral_id'],
					$post_data['amount'], $post_data['currency'], $post_data['date'], $post_data['status'], $post_data['payment']
					);
					$wpdb->query($q);

					if ($post_data['campaign'] && isset($wpdb->insert_id)){
						/// save into campaign if is insert and the campaign is set
						$this->increment_campaign_referrals($post_data['campaign'], $post_data['affiliate_id']);
					}

					/// AFFILIATE NOTIFICATION ON EVERY REFERRAL
					if (!class_exists('Uap_Affiliate_Notification_Reports')){
				 		require_once UAP_PATH . 'classes/Uap_Affiliate_Notification_Reports.class.php';
			 		}
			 		$object = new Uap_Affiliate_Notification_Reports();
					$object->send_single_referral_notification($post_data['affiliate_id'], $wpdb->insert_id, $post_data['source']);

					/// INCREMENT DASHBOARD NOTIFICATION COUNT
					$this->increment_dashboard_notification('referrals');

					return $wpdb->insert_id;
			}
		}

		private function increment_campaign_referrals($campaign='', $affiliate_id=0){
			/*
			 * @param string, int
			 * @return none
			 */
			/// CAMPAIGN
			if (!empty($campaign) && !empty($affiliate_id)){
				/// check if campaign-affiliate exists
				global $wpdb;
				$table = $wpdb->prefix . 'uap_campaigns';
				$q = $wpdb->prepare("SELECT id, referrals
											FROM $table
											WHERE name=%s
											AND affiliate_id=%d;", $campaign, $affiliate_id);
				$temp_data = $wpdb->get_row($q);
				if (!empty($temp_data->id) && isset($temp_data->referrals)){
					/// UPDATE
					$referrals = (int)$temp_data->referrals;
					$referrals++;
					$q = $wpdb->prepare("UPDATE $table
										SET referrals=%s
										WHERE id=%d
					", $referrals, $temp_data->id);
					$wpdb->query($q);
				}
			}
		}

		public function referral_update_child($child_referral_id=0, $parent_referral_id=0){
			/*
			 * @param int, int
			 * @return none
			 */
			if ($child_referral_id && $parent_referral_id){
				global $wpdb;
				$table = $wpdb->prefix . "uap_referrals";
				$q = $wpdb->prepare("UPDATE $table SET parent_referral_id=%d WHERE id=%d;", $parent_referral_id, $child_referral_id);
				$wpdb->query($q);
			}
		}

		public function delete_referrals($delete_referrals){
			/*
			 * @param array
			 * @return none
			 */
			if (!empty($delete_referrals)){
				global $wpdb;
				$table = $wpdb->prefix . "uap_referrals";
				if (!is_array($delete_referrals)){
					$delete_referrals = array($delete_referrals);
				}
				foreach ($delete_referrals as $id){
					$this->delete_referral($id);
				}
			}
		}

		public function delete_referral($referral_id=0, $delete_child=TRUE, $delete_parent=TRUE){
			/*
			 * @param int
			 * @return none
			 */
			if ($referral_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_referrals';
				if ($delete_child){
					$child = $this->referral_get_child($referral_id);
					if ($child){
						$this->delete_referral($delete_child, TRUE, FALSE);
					}
				}
				if ($delete_parent){
					$parent = $this->referral_get_parent($referral_id);
					if ($parent){
						$this->delete_referral($delete_child, FALSE, TRUE);
					}
				}
				/// DELETE FROM REFERRAL
				$q = $wpdb->prepare("DELETE FROM $table WHERE id=%d ", $referral_id);
				$wpdb->query($q);

				/// DELETE FROM VISITS
				$q = $wpdb->prepare("DELETE FROM {$wpdb->prefix}uap_visits WHERE referral_id=%d ", $referral_id);
				$wpdb->query($q);
			}
		}

		public function change_referral_status($id, $new_status, $search_parent=TRUE, $search_child=TRUE){
			/*
			 * @param int, int
			 * @return none
			 */

			$data = $this->get_referral($id);
			if (!empty($data) && isset($data['status'])){
				$data['status'] = $new_status;
				$this->save_referral($data);
			}

			/// MLM
			if (get_option('uap_mlm_enable')){
				if ($search_parent){
					$parent = $this->referral_get_parent($id);
					if ($parent){
						$this->change_referral_status($parent, $new_status, TRUE, FALSE);
					}
				}
				if ($search_child){
					$child = $this->referral_get_child($id);
					if ($child){
						$this->change_referral_status($child, $new_status, FALSE, TRUE);
					}
				}
			}
		}

		public function referral_get_child($referral_id=0){
			/*
			 * @param int
			 * @return int
			 */
			if ($referral_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_referrals';
				$q = $wpdb->prepare("SELECT child_referral_id FROM $table WHERE id=%d;", $referral_id);
				$data = $wpdb->get_row($q);
				if (!empty($data->child_referral_id)){
					return $data->child_referral_id;
				} else {
					return 0;
				}
			}
		}

		public function referral_get_parent($referral_id=0){
			/*
			 * @param int
			 * @return int
			 */
			if ($referral_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_referrals';
				$q = $wpdb->prepare("SELECT parent_referral_id FROM $table WHERE id=%d;", $referral_id);
				$data = $wpdb->get_row($q);
				if (!empty($data->parent_referral_id)){
					return $data->parent_referral_id;
				} else {
					return 0;
				}
			}
		}


		public function get_referral_id_for_reference($reference='', $source=''){
			/*
			 * @param string, string
			 * @return int
			 */
			if ($reference){
				global $wpdb;
				$table = $wpdb->prefix . "uap_referrals";
				$q = $wpdb->prepare("SELECT id FROM $table WHERE reference=%s ", $reference);
				if (!empty($source)){
					$q .= $wpdb->prepare(" AND source=%s ", $source);
				}
				$data = $wpdb->get_row($q);
				if (!empty($data->id)){
					return $data->id;
				}
			}
			return 0;
		}

		public function search_affiliate_id_for_current_user($referral_wp_id=0){
			/*
			 * @param int
			 * @return int
			 */
			if ($referral_wp_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
				$q = $wpdb->prepare("SELECT affiliate_id FROM $table WHERE referral_wp_uid=%d;", $referral_wp_id);
				$data = $wpdb->get_row($q);
				if (!empty($data->affiliate_id)){
					return (int)$data->affiliate_id;
				}
			}
			return 0;
		}

		public function insert_affiliate_referral_user_new_relation($affiliate_id=0, $referral_wp_id=0){
			/*
			 * @param int, int
			 * @return none
			 */
			if ($affiliate_id && $referral_wp_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
				$q = $wpdb->prepare("SELECT * FROM $table WHERE affiliate_id=%d AND referral_wp_uid=%d", $affiliate_id, $referral_wp_id);
				$data = $wpdb->get_row($q);
				if (!$data){
					$q = $wpdb->prepare("INSERT INTO $table VALUES(NULL, %d, %d, NOW());", $affiliate_id, $referral_wp_id);
					$wpdb->query($q);
				}
			}
		}

		public function update_affiliate_referral_user_relation($entry_id=0, $new_affiliate_id=0){
			/*
			 * @param int, int
			 * @return none
			 */
			if ($entry_id && $new_affiliate_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
				$q = $wpdb->prepare("UPDATE $table SET affiliate_id=%d WHERE id=%d ", $new_affiliate_id, $entry_id);
				$wpdb->query($q);
			}
		}

		public function update_affiliate_referral_user_relation_by_ids($old_affiliate=0, $new_affiliate=0, $referral_wp_id=0){
			/*
			 * @param int, int, int
			 * @return none
			 */
			if ($old_affiliate && $new_affiliate && $referral_wp_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
				$q = $wpdb->prepare("SELECT * FROM $table WHERE affiliate_id=%d AND referral_wp_uid=%d", $old_affiliate, $referral_wp_id);
				$data = $wpdb->get_row($q);
				if (!empty($data->id)){
					$q = $wpdb->prepare("UPDATE $table SET affiliate_id=%d WHERE id=%d ", $new_affiliate, $data->id);
					$wpdb->query($q);
				}
			}
		}

		public function get_offers(){
			/*
			 * @param none
			 * @return array
			 */
			$return_data = array();
			global $wpdb;
			$table = $wpdb->prefix . "uap_offers";
			$table_b = $wpdb->prefix . "uap_offers_affiliates_reference";
			$data = $wpdb->get_results("SELECT * FROM $table;");
			if (!empty($data)){
				foreach ($data as $object){
					$q = $wpdb->prepare("SELECT affiliate_id FROM $table_b WHERE offer_id=%d ", $object->id);
					$affiliates_data = $wpdb->get_results($q);
					if ($affiliates_data){
						if (!empty($affiliates)){
							unset($affiliates);
						}
						foreach ($affiliates_data as $affiliate_object){
							if ($affiliate_object->affiliate_id==-1){
								$affiliates[] = __('All', 'uap');
							} else {
								$uid = $this->get_uid_by_affiliate_id($affiliate_object->affiliate_id);
								$affiliates[] = $this->get_username_by_wpuid($uid);
							}
						}
						if (!empty($affiliates)){
							$affiliates_str = implode(',', $affiliates);
						} else {
							$affiliates_str = '';
						}
					}
					$temp_arr = (array)$object;
					$temp_arr['affiliates'] = $affiliates_str;
					$return_data[] = $temp_arr;
				}
			}
			return $return_data;
		}

		public function get_offer($id=0){
			/*
			 * @param int
			 * @return array
			 */
			if ($id){
				global $wpdb;
				$table = $wpdb->prefix . "uap_offers";
				$q = $wpdb->prepare("SELECT * FROM $table WHERE id=%d ", $id);
				$data = $wpdb->get_row($q);
				if (!empty($data)){
					if (!empty($data->settings)){
						$settings = unserialize($data->settings);
						if (is_array($settings)){
							$meta['color'] = @$settings['color'];
						}
					}
					$meta['name'] = (empty($data->name)) ? '' : $data->name;
					$meta['start_date'] = (empty($data->start_date)) ? '' : $data->start_date;
					$meta['end_date'] = (empty($data->end_date)) ? '' : $data->end_date;
					$meta['amount_type'] = (empty($data->amount_type)) ? '' : $data->amount_type;
					$meta['amount_value'] = (empty($data->amount_value)) ? '' : $data->amount_value;
					$meta['status'] = (empty($data->status)) ? 0 : $data->status;
					$meta['id'] = $data->id;
					$meta['source'] = $this->get_offers_username_reference_row_value($id, 'source');
					$meta['products'] = $this->get_offers_username_reference_row_value($id, 'products');
					$meta['affiliates'] = $this->get_offers_username_reference_usernames_for_offer($id);
					return $meta;
				}
				return array();
			} else {
				return array(
								'id' => 0,
								'name' => '',
								'amount_type' => '',
								'amount_value' => '',
								'source' => '',
								'products' => '',
								'affiliates' => '',
								'start_date' => '',
								'end_date' => '',
								'color' => '',
								'status' => 1,
				);
			}

		}

		public function delete_offers($ids){
			/*
			 * @param array
			 * @return none
			 */
			if (!empty($ids)){
				global $wpdb;
				$table = $wpdb->prefix . "uap_offers";
				$table_offers_u_r = $wpdb->prefix . 'uap_offers_affiliates_reference';
				if (!is_array($ids)){
					$ids = array($ids);
				}
				foreach ($ids as $id){
					$q = $wpdb->prepare("DELETE FROM $table WHERE id=%d ", $id);
					$wpdb->query($q);
					$q = $wpdb->prepare("DELETE FROM $table_offers_u_r WHERE offer_id=%d ", $id);
					$wpdb->query($q);
				}
			}
		}

		public function save_offer($post_data=array()){
			/*
			 * @param array
			 * @return int
			 */
			if (!empty($post_data)){
				if ($post_data['amount_value']=='' || empty($post_data['start_date']) || empty($post_data['end_date'])){
					return 0;
				}
				global $wpdb;
				$table = $wpdb->prefix . "uap_offers";
				$metas = array(
								'color' => @$post_data['color'],
				);

				$settings = serialize($metas);
				if (!empty($post_data['id'])){
					$q = $wpdb->prepare("SELECT * FROM $table WHERE id=%d ", $post_data['id']);
					$data = $wpdb->get_row($q);
					if (!empty($data)){
						$q = $wpdb->prepare("UPDATE $table SET
											name=%s,
											start_date=%s,
											end_date=%s,
											amount_type=%s,
											amount_value=%s,
											settings=%s,
											status=%s
										WHERE id=%d
						", @$post_data['name'], @$post_data['start_date'], @$post_data['end_date'], @$post_data['amount_type'],
						@$post_data['amount_value'], $settings, $post_data['status'], $post_data['id'] );
						$wpdb->query($q);
						$this->save_offer_affiliate_reference($post_data['id'], $post_data['affiliates'], $post_data['source'], $post_data['products']);
						return $post_data['id'];
					}
				}
				$q = $wpdb->prepare("SELECT id FROM $table WHERE name=%s ", $post_data['name']);
				$check = $wpdb->get_row($q);
				if ($check && !empty($check->id)){
					return 0;
				}
				$q = $wpdb->prepare("INSERT INTO $table VALUES( null,
														 %s,
														 %s,
														 %s,
														 %s,
														 %s,
														 %s,
														 %s
						);", $post_data['name'], $post_data['start_date'], $post_data['end_date'], $post_data['amount_type'],
						$post_data['amount_value'], $settings, $post_data['status']
				);
				$wpdb->query($q);
				$id = $wpdb->insert_id;
				$this->save_offer_affiliate_reference($id, $post_data['affiliates'], $post_data['source'], $post_data['products']);
				return $id;
			}
			return 0;
		}

		public function get_offer_id_by_affiliate_id_and_source($affiliate_id=0, $source=''){
			/*
			 *
			 * @param string
			 * @return array
			 */
			$arr = array();
			if ($affiliate_id){
				$affiliate_id = esc_sql($affiliate_id);
				$source = esc_sql($source);
				global $wpdb;
				$table = $wpdb->prefix . 'uap_offers_affiliates_reference';
				$table_offers = $wpdb->prefix . 'uap_offers';
				$data = $wpdb->get_results("SELECT * FROM $table d
											INNER JOIN $table_offers b
											ON d.offer_id = b.id
											WHERE
											1=1
											AND (d.affiliate_id='$affiliate_id' OR d.affiliate_id='-1')
											AND d.source='$source'
											AND UNIX_TIMESTAMP(b.start_date)<UNIX_TIMESTAMP(NOW())
											AND UNIX_TIMESTAMP(b.end_date)>UNIX_TIMESTAMP(NOW())
											AND b.status=1
											ORDER BY d.offer_id DESC;");
				if ($data){
					foreach ($data as $object){
						if (isset($object->offer_id)){
							$arr[$object->offer_id] = (array)$object;
						}
					}
				}
			}
			return $arr;
		}

		public function save_offer_affiliate_reference($offer_id=0, $affiliates='', $source='', $products=''){
			/*
			 * @param int, string, string
			 * @return none
			 */
			if ($offer_id && $source){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_offers_affiliates_reference';
				$q = $wpdb->prepare("DELETE FROM $table WHERE offer_id=%d ", $offer_id);
				$wpdb->query($q);// REMOVE OLD DATA

				if ($affiliates) $affiliates_arr = explode(',',  $affiliates);
				else $affiliates_arr = array(-1);
				if ($affiliates_arr){
					foreach ($affiliates_arr as $affiliate_id){
							$q = $wpdb->prepare("INSERT INTO $table VALUES(null, %d, %d, %s, %s);", $offer_id, $affiliate_id, $source, $products);
							$wpdb->query($q);
					}
				}
			}
		}

		public function get_offers_username_reference_row_value($offer_id=0, $col=''){
			/*
			 * @param int, string
			 * @return array
			 */
			if ($offer_id && $col){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_offers_affiliates_reference';
				$q = $wpdb->prepare("SELECT $col FROM $table WHERE offer_id=%d ", $offer_id);
				$data = $wpdb->get_row($q);
				if (!empty($data->$col)){
					return $data->$col;
				}
			}
			return array();
		}

		public function get_offers_username_reference_usernames_for_offer($offer_id=0){
			/*
			 * @param int
			 * @return array
			 */
			$arr = array();
			if ($offer_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_offers_affiliates_reference';
				$q = $wpdb->prepare("SELECT affiliate_id FROM $table WHERE offer_id=%d ", $offer_id);
				$data = $wpdb->get_results($q);
				if ($data){
					foreach ($data as $object){
						if (!empty($object->affiliate_id)){
							$arr[] = $object->affiliate_id;
						}
					}
				}
			}
			return $arr;
		}

		public function search_affiliates_by_char($char='', $exclude_user=0){
			/*
			 * @param string, int
			 * @return array
			 */
			$return = array();
			global $wpdb;
			$char = esc_sql($char);
			$q = "SELECT u.user_login, uap.id
											FROM " . $wpdb->base_prefix . "users u
											INNER JOIN " . $wpdb->prefix . "uap_affiliates uap
											ON u.ID=uap.uid
											WHERE u.user_login LIKE '%$char%'
			";
			if (!empty($exclude_user)){
				$exclude_user = esc_sql($exclude_user);
				$q .= " AND u.ID<>$exclude_user;";
			}
			$data = $wpdb->get_results($q);
			if (!empty($data) && is_array($data)){
				foreach ($data as $obj){
					if (!empty($obj->user_login) && !empty($obj->id)){
						$return[$obj->id] = $obj->user_login;
					}
				}
			}
			return $return;
		}

		public function get_all_references(){
			/*
			 * @param none
			 * @return array
			 */
			global $wpdb;
			$return = array();
			$table = $wpdb->prefix . 'uap_referrals';
			$data = $wpdb->get_results("SELECT DISTINCT reference FROM $table;");
			if (!empty($data) && is_array($data)){
				foreach ($data as $object){
					if (!empty($object->reference)){
						$return[] = $object->reference;
					}
				}
			}
			return $return;
		}

		public function is_magic_feat_enable($type=''){
			/*
			 * @param string
			 * @return boolean
			 */
			$temp_data = $this->return_settings_from_wp_option($type);
			switch ($type){
				case 'sign_up_referrals':
					return ($temp_data['uap_sign_up_referrals_enable']) ? TRUE : FALSE;
					break;
				case 'lifetime_commissions':
					return ($temp_data['uap_lifetime_commissions_enable']) ? TRUE : FALSE;
					break;
				case 'reccuring_referrals':
					return ($temp_data['uap_reccuring_referrals_enable']) ? TRUE : FALSE;
					break;
				case 'social_share':
					return ($temp_data['uap_social_share_enable']) ? TRUE : FALSE;
					break;
				case 'paypal':
					return ($temp_data['uap_paypal_enable']) ? TRUE : FALSE;
					break;
				case 'allow_own_referrence':
					return ($temp_data['uap_allow_own_referrence_enable']) ? TRUE : FALSE;
					break;
				case 'mlm':
					return ($temp_data['uap_mlm_enable']) ? TRUE : FALSE;
					break;
				case 'rewrite_referrals':
					return ($temp_data['uap_rewrite_referrals_enable']) ? TRUE : FALSE;
					break;
				case 'bonus_on_rank':
					return ($temp_data['uap_bonus_on_rank_enable']) ? TRUE : FALSE;
					break;
				case 'opt_in':
					return ($temp_data['uap_register_opt-in']) ? TRUE : FALSE;
					break;
				case 'stripe':
					return ($temp_data['uap_stripe_enable']) ? TRUE : FALSE;
					break;
				case 'coupons':
					return ($temp_data['uap_coupons_enable']) ? TRUE : FALSE;
					break;
				case 'friendly_links':
					return ($temp_data['uap_friendly_links']) ? TRUE : FALSE;
					break;
				case 'custom_affiliate_slug':
					return ($temp_data['uap_custom_affiliate_slug_on']) ? TRUE : FALSE;
					break;
				case 'wallet':
					return ($temp_data['uap_wallet_enable']) ? TRUE : FALSE;
					break;
				case 'checkout_select_referral':
					return ($temp_data['uap_checkout_select_referral_enable']) ? TRUE : FALSE;
					break;
				case 'woo_account_page':
					return ($temp_data['uap_woo_account_page_enable']) ? TRUE : FALSE;
					break;
				case 'bp_account_page':
					return ($temp_data['uap_bp_account_page_enable']) ? TRUE : FALSE;
					break;
				case 'referral_notifications':
					return $temp_data['uap_referral_notifications_enable'];
					break;
				case 'admin_referral_notifications':
					return $temp_data['uap_admin_referral_notifications_enable'];
					break;
				case 'periodically_reports':
					return $temp_data['uap_periodically_reports_enable'];
					break;
				case 'qr_code':
					return $temp_data['uap_qr_code_enable'];
					break;
				case 'email_verification':
					return $temp_data['uap_register_double_email_verification'];
					break;
				case 'source_details':
					return $temp_data['uap_source_details_enable'];
					break;
				case 'wp_social_login':
					return $temp_data['uap_wp_social_login_on'];
					break;
				case 'stripe_v2':
					return $temp_data['uap_stripe_v2_enable'];
					break;
				case 'pushover':
					return $temp_data['uap_pushover_enabled'];
					break;
				case 'max_amount':
					return $temp_data['uap_maximum_amount_enabled'];
					break;
				case 'simple_links':
					return $temp_data['uap_simple_links_enabled'];
					break;
				case 'account_page_menu':
					return $temp_data['uap_account_page_menu_enabled'];
					break;
				case 'ranks_pro':
					return $temp_data['uap_ranks_pro_enabled'];
					break;
				case 'landing_pages':
					return $temp_data['uap_landing_pages_enabled'];
					break;
				case 'pay_per_click':
					return $temp_data['uap_pay_per_click_enabled'];
					break;
				case 'cpm_commission':
					return $temp_data['uap_cpm_commission_enabled'];
					break;
				case 'pushover_referral_notifications':
					return $temp_data['uap_pushover_referral_notifications_enabled'];
					break;
				case 'rest_api':
					return $temp_data['uap_rest_api_enabled'];
					break;
			}
			return FALSE;
		}

		public function get_column_value_for_each_rank($col_name=''){
			/*
			 * @param string
			 * @return array
			 */
			$arr = array();
			global $wpdb;
			$table = $wpdb->prefix . 'uap_ranks';
			$col_name = esc_sql($col_name);
			$data = $wpdb->get_results("SELECT id, $col_name FROM $table;");
			if ($data && is_array($data)){
				foreach ($data as $object){
					if (isset($object->id) && isset($object->$col_name)){
						$arr[$object->id] = $object->$col_name;
					}
				}
			}
			return $arr;
		}

		public function update_rank_column($col_name='', $id=0, $value=0){
			/*
			 * @param string, int, float
			 * @return none
			 */
			if ($col_name && $id && $value){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_ranks';
				$col_name = esc_sql($col_name);
				$value = esc_sql($value);
				$id = esc_sql($id);
				$wpdb->query("UPDATE $table SET $col_name='$value' WHERE id='$id';");
			}
		}

		public function update_rank_column_force_empty($col_name='', $id=0, $value=0){
			/*
			 * @param string, int, float
			 * @return none
			 */
			if ($col_name && $id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_ranks';
				$col_name = esc_sql($col_name);
				$value = esc_sql($value);
				$id = esc_sql($id);
				$wpdb->query("UPDATE $table SET $col_name='$value' WHERE id=$id ");
			}
		}

		public function get_campaigns_for_affiliate_id($affiliate_id=0){
			/*
			 * @param int
			 * @return array
			 */
			global $wpdb;
			$return = array();
			$table = $wpdb->prefix . 'uap_campaigns';
			$affiliate_id = esc_sql($affiliate_id);
			$data = $wpdb->get_results("SELECT name FROM $table WHERE affiliate_id=$affiliate_id ");
			if (!empty($data) && is_array($data)){
				foreach ($data as $object){
					$return[] = $object->name;
				}
			}
			return $return;
		}

		public function add_empty_campaign($affiliate_id=0, $name=''){
			/*
			 * @param int, string
			 * @return none
			 */
			if ($affiliate_id && $name){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_campaigns';
				$q = $wpdb->prepare("INSERT INTO $table VALUE(NULL, %s, %d, 0, 0, 0);", $name, $affiliate_id);
				$wpdb->query($q);
			}
		}

		public function delete_campaign($affiliate_id=0, $name=''){
			/*
			 * @param int, string
			 * @return none
			 */
			if ($affiliate_id && $name){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_campaigns';
				$q = $wpdb->prepare("DELETE FROM $table WHERE name=%s AND affiliate_id=%d ", $name, $affiliate_id);
				$wpdb->query($q);
			}
		}

		public function update_visit_referral_id($visit_id=0, $referral_id=0){
			/*
			 * @param int
			 * @return none
			 */
			if ($visit_id && $referral_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_visits';
				$q = $wpdb->prepare("SELECT * FROM $table WHERE id=%d ", $visit_id);
				$exists = $wpdb->get_row($q);
				if ($exists){
					$q = $wpdb->prepare("UPDATE $table
										SET referral_id=%d
										WHERE id=%d ", $referral_id, $visit_id);
					$wpdb->query($q);
				}
			}
		}

		public function get_payments($limit=-1, $offset=-1, $count=FALSE, $order_by='', $order_type=''){
			/*
			 * @param none
			 * @return array
			 */
			$return = array();
			global $wpdb;
			$table = $wpdb->prefix . 'uap_referrals';
			$table_payments = $wpdb->prefix . 'uap_payments';

			if ($count){
				$q = "SELECT count(distinct affiliate_id) as c FROM $table WHERE status='2';";
				$data = $wpdb->get_row($q);
				if (!empty($data->c)){
					return array( 0 => $data->c);
				}
				return array();
			}
			$q = "SELECT DISTINCT affiliate_id, COUNT(id) as c FROM $table WHERE status='2' GROUP BY affiliate_id ";
			if ($order_type && $order_by){
				$order_by = esc_sql($order_by);
				$order_type = esc_sql($order_type);
				$q .= " ORDER BY " . $order_by . " " . $order_type;
			}
			if ($limit>-1 && $offset>-1){
				$limit = esc_sql($limit);
				$offset = esc_sql($offset);
				$q .= " LIMIT " . $limit . " OFFSET " . $offset;
			}
			$data = $wpdb->get_results($q);
			if ($data){
				foreach ($data as $object){
					/// getting unpaid items
					$temp_data = $wpdb->get_row("SELECT COUNT(id) as c, SUM(amount) as s, currency
													FROM $table
													WHERE affiliate_id='" . esc_sql($object->affiliate_id) . "'
													AND status='2'
													AND payment='0';");
					$return[$object->affiliate_id]['count_unpaid'] = (empty($temp_data->c)) ? 0 : $temp_data->c;
					$return[$object->affiliate_id]['total_unpaid'] = (empty($temp_data->s)) ? 0 : $temp_data->s;
					$return[$object->affiliate_id]['unpaid_currency'] = (empty($temp_data->currency)) ? 'USD' : $temp_data->currency;
					/// getting paid items
					$temp_data = $wpdb->get_row("SELECT COUNT(id) as c, SUM(amount) as s, currency
													FROM $table
													WHERE affiliate_id='" . esc_sql($object->affiliate_id) . "'
													AND status='2'
													AND payment='2';");
					$return[$object->affiliate_id]['count_paid'] = (empty($temp_data->c)) ? 0 : $temp_data->c;
					$return[$object->affiliate_id]['total_paid'] = (empty($temp_data->s)) ? 0 : $temp_data->s;
					$return[$object->affiliate_id]['paid_currency'] = (empty($temp_data->currency)) ? 'USD' : $temp_data->currency;

					////
					$temp_data = $wpdb->get_row("SELECT COUNT(id) as c
													FROM $table_payments
													WHERE affiliate_id='" . esc_sql($object->affiliate_id) . "' ;");
					$return[$object->affiliate_id]['has_transactions'] = (empty($temp_data->c)) ? 0 : $temp_data->c;
					///

					$uid = $this->get_uid_by_affiliate_id($object->affiliate_id);
					$user_data = get_userdata($uid);
					$return[$object->affiliate_id]['username'] = (empty($user_data->user_login)) ? '' : $user_data->user_login;
				}
			}
			return $return;
		}

		public function getAllUserData($uid=0)
		{
				global $wpdb;
				if (!$uid){
						return [];
				}
				$query = $wpdb->prepare("SELECT * FROM {$wpdb->users} a INNER JOIN {$wpdb->usermeta} b ON a.ID=b.user_id WHERE a.ID=%d", $uid);
				$data = $wpdb->get_results($query);
				if (empty($data)){
						return [];
				}
				$returnData = [];
				foreach ($data as $object){
						$returnData[] = (array)$object;
				}
				return $returnData;
		}


		public function get_affiliate_payment_details($affiliate_id=0, $ids_in=''){
			/*
			 * @param int, string
			 * @return array
			 */
			$return = array();
			if ($affiliate_id){
				global $wpdb;
				$uid = $this->get_uid_by_affiliate_id($affiliate_id);
				$user_data = get_userdata($uid);
				if (!empty($user_data->user_email)){
					$return['email'] = $user_data->user_email;
				}
				$return['username'] = (empty($user_data->user_login)) ? '' : $user_data->user_login;
				$rank_id = $this->get_affiliate_rank($affiliate_id);
				if ($rank_id){
					$rank_data = $this->get_rank($rank_id);
				}
				$return['rank'] = (empty($rank_data['label'])) ? '' : $rank_data['label'];
				$first_name = get_user_meta($uid, 'first_name', TRUE);
				$last_name = get_user_meta($uid, 'last_name', TRUE);
				$return['name'] = $first_name . ' ' . $last_name;

				$table = $wpdb->base_prefix . 'uap_referrals';
				$q = "SELECT COUNT(id) as c, SUM(amount) as s, currency FROM $table
							WHERE affiliate_id='" . esc_sql($affiliate_id) . "'
							AND status='2'
							AND payment='0' ";
				if ($ids_in){
					$q .= " AND id IN($ids_in)";
				}
				$temp_data = $wpdb->get_row($q);
				$return['amount'] = (empty($temp_data->s)) ? 0 : $temp_data->s;
				$return['currency'] = (empty($temp_data->currency)) ? 'USD' : $temp_data->currency;
				if (!$ids_in){
					$temp_data = $wpdb->get_results("SELECT id FROM $table
														WHERE affiliate_id='" . esc_sql($affiliate_id) . "'
														AND status='2'
														AND payment='0' ");
					if ($temp_data){
						foreach ($temp_data as $key=>$object){
							$ids[] = $object->id;
						}
						if (!empty($ids)){
							$return['referrals_in'] = implode(',', $ids);
						}
					}
				} else {
					$return['referrals_in'] = $ids_in;
				}
				$return['affiliate_id'] = $affiliate_id;
				$return['payment_gateway_data'] = $this->get_affiliate_payment_type(0, $affiliate_id);
			}
			return $return;
		}

		public function get_affiliate_payment_details_for_referral_list($ids_in=''){
			/*
			 * @param  string
			 * @return array
			 */
			$return = array();
			if ($ids_in){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_referrals';
				$ids_in = esc_sql($ids_in);
				$q = "SELECT DISTINCT affiliate_id FROM $table WHERE status='2' AND id IN($ids_in)";
				$data = $wpdb->get_results($q);
				if ($data){
					foreach ($data as $object){
						$temp_data = $wpdb->get_results("SELECT id, amount, currency FROM $table WHERE status='2' AND id IN($ids_in) AND affiliate_id='" . esc_sql($object->affiliate_id) . "';");
						if ($temp_data){
							$return[$object->affiliate_id]['amount'] = 0;
							foreach ($temp_data as $inside_object){
								$return[$object->affiliate_id]['amount'] += $inside_object->amount;
								$return[$object->affiliate_id]['referrals'][] = $inside_object->id;
							}
							if (!empty($return[$object->affiliate_id]['referrals'])) {
								$return[$object->affiliate_id]['referrals'] = implode(',', $return[$object->affiliate_id]['referrals']);
							}
							$uid = $this->get_uid_by_affiliate_id($object->affiliate_id);
							$user_data = get_userdata($uid);
							$return[$object->affiliate_id]['email'] = (empty($user_data->user_email)) ? '' : $user_data->user_email;
							$return[$object->affiliate_id]['username'] = (empty($user_data->user_login)) ? '' : $user_data->user_login;
							$rank_id = $this->get_affiliate_rank($object->affiliate_id);
							if ($rank_id){
								$rank_data = $this->get_rank($rank_id);
							}
							$return[$object->affiliate_id]['rank'] = (empty($rank_data['label'])) ? '' : $rank_data['label'];
							$first_name = get_user_meta($uid, 'first_name', TRUE);
							$last_name = get_user_meta($uid, 'last_name', TRUE);
							$return[$object->affiliate_id]['name'] = $first_name . ' ' . $last_name;
							$return[$object->affiliate_id]['payment_gateway_data'] = $this->get_affiliate_payment_type(0, $object->affiliate_id);
						}
					}
				}
			}
			return $return;
		}

		public function get_unpaid_payments_for_affiliate($affiliate_id=0, $limit=-1, $offset=-1, $count=FALSE, $order_by='date', $order_type='DESC', $where_conditions=array()){
			/*
			 * @param int
			 * @return array
			 */
			$return = array();
			if ($affiliate_id){
				$affiliate_id = esc_sql($affiliate_id);
				global $wpdb;
				$table = $wpdb->prefix . 'uap_referrals';
				if ($count){
					$q = "SELECT COUNT(id) as c FROM $table
								WHERE affiliate_id='$affiliate_id'
								AND status='2'
								AND payment='0'";
					if (!empty($where_conditions)){
						foreach ($where_conditions as $condition){
							$q .= " AND " . $condition ;
						}
					}
					$data = $wpdb->get_row($q);
					if (!empty($data->c)){
						return array(0=>$data->c);
					}
					return array();
				}
				$q = "SELECT *
								FROM $table
								WHERE affiliate_id='$affiliate_id'
								AND status='2'
								AND payment='0'";
				if (!empty($where_conditions)){
					foreach ($where_conditions as $condition){
						$q .= " AND " . $condition ;
					}
				}
				if ($order_type && $order_by){
					$order_by = esc_sql($order_by);
					$order_type = esc_sql($order_type);
					$q .= " ORDER BY " . $order_by . " " . $order_type;
				}
				if ($limit>-1 && $offset>-1){
					$limit = esc_sql($limit);
					$offset = esc_sql($offset);
					$q .= " LIMIT " . $limit . " OFFSET " . $offset;
				}
				$data = $wpdb->get_results($q);
				if ($data){
					foreach ($data  as $object){
						$object = (array)$object;
						$uid = $this->get_uid_by_affiliate_id($object['affiliate_id']);
						$user_data = get_userdata($uid);
						$object['username'] = (empty($user_data->user_login)) ? '' : $user_data->user_login;
						$return[] = $object;
					}
				}
			}
			return $return;
		}

		public function get_transactions($affiliate_id=0, $limit=-1, $offset=-1, $count=FALSE, $order_by='', $order_type='create_date', $where_conditions=array()){
			/*
			 * @param int
			 * @return array / int
			 */
			$return = array();
			global $wpdb;
			$table = $wpdb->prefix . 'uap_payments';
			if ($count){
				$q = "SELECT COUNT(*) as c FROM $table";
				$q .= " WHERE 1=1";
				if ($affiliate_id){
					$affiliate_id = esc_sql($affiliate_id);
					$q .= " AND affiliate_id='$affiliate_id' ";
				}
				if (!empty($where_conditions)){
					foreach ($where_conditions as $condition){
						$q .= " AND " . $condition ;
					}
				}
				$data = $wpdb->get_row($q);
				if (!empty($data->c)){
					return $data->c;
				}
				return 0;
			}
			$q = "SELECT * FROM $table";
			$q .= " WHERE 1=1";
			if ($affiliate_id){
				$affiliate_id = esc_sql($affiliate_id);
				$q .= " AND affiliate_id='$affiliate_id' ";
			}
			if (!empty($where_conditions)){
				foreach ($where_conditions as $condition){
					$q .= " AND " . $condition ;
				}
			}
			if ($order_type && $order_by){
				$order_by = esc_sql($order_by);
				$order_type = esc_sql($order_type);
				$q .= " ORDER BY " . $order_by . " " . $order_type;
			}
			if ($limit>-1 && $offset>-1){
				$limit = esc_sql($limit);
				$offset = esc_sql($offset);
				$q .= " LIMIT " . $limit . " OFFSET " . $offset;
			}
			$data = $wpdb->get_results($q);
			if ($data){
				foreach ($data as $key=>$object){
					$affiliate_wpuid = $this->get_uid_by_affiliate_id($object->affiliate_id);
					$affiliate_username = $this->get_username_by_wpuid($affiliate_wpuid);
					$array = (array)$object;
					$array['username'] = $affiliate_username;
					$return[] = $array;
				}
			}
			return $return;
		}

		public function change_referrals_status($ids=array(), $payment_status=1){
			/*
			 * @param array, int ( payment status 0=unpaid/1=pending/2=paid )
			 * @return none
			 */
			if ($ids){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_referrals';
				foreach ($ids as $id){
					$q = $wpdb->prepare("UPDATE $table SET payment=%s WHERE id=%d ", $payment_status, $id);
					$wpdb->query($q);
				}
			}
		}

		public function add_payment($data=array()){
			/*
			 * @param array
			 * @return none
			 */
			if ($data){
				global $wpdb;

				///get current payment details and store in into db
				$data['payment_details'] = $this->get_current_payment_settings_for_affiliate_id($data['affiliate_id']);

				$table = $wpdb->prefix . 'uap_payments';
				$q = $wpdb->prepare("INSERT INTO $table VALUES( null,
														 %s,
														 %s,
														 %s,
														 %s,
														 %s,
														 %s,
														 %s,
														 %s,
														 %s,
														 '',
														 %s
				);", $data['payment_type'], $data['transaction_id'], $data['referral_ids'],
				$data['affiliate_id'], $data['amount'], $data['currency'], $data['payment_details'],
				$data['create_date'], $data['update_date'], $data['status']
				);
				$wpdb->query($q);

				/// NOTIFICATION TO AFFILIATE
				$id = $wpdb->insert_id;
				$this->payments_send_affiliate_notification_by_status($id, $data['status']);
			}
		}

		public function get_paid_referrals_for_affiliate($affiliate_id=0, $limit=-1, $offset=-1, $count=FALSE, $order_by='date', $order_type='DESC', $where_conditions=array()){
			/*
			 * @param int
			 * @return array
			 */
			$return = array();
			if ($affiliate_id){
				$affiliate_id = esc_sql($affiliate_id);
				global $wpdb;
				$table = $wpdb->prefix . 'uap_referrals';
				if ($count){
					$q = "SELECT COUNT(*) as c FROM $table WHERE affiliate_id='$affiliate_id' AND payment='2'";
					if (!empty($where_conditions)){
						foreach ($where_conditions as $condition){
							$q .= " AND " . $condition ;
						}
					}
					$data = $wpdb->get_row($q);
					if (!empty($data->c)){
						return array(0=>$data->c);
					}
					return array();
				}
				$q = "SELECT * FROM $table WHERE affiliate_id='$affiliate_id' AND payment='2'";
				if (!empty($where_conditions)){
					foreach ($where_conditions as $condition){
						$q .= " AND " . $condition ;
					}
				}
				if ($order_type && $order_by){
					$order_by = esc_sql($order_by);
					$order_type = esc_sql($order_type);
					$q .= " ORDER BY " . $order_by . " " . $order_type;
				}
				if ($limit>-1 && $offset>-1){
					$limit = esc_sql($limit);
					$offset = esc_sql($offset);
					$q .= " LIMIT " . $limit . " OFFSET " . $offset;
				}
				$data = $wpdb->get_results($q);
				if ($data){
					foreach ($data as $key=>$object){
						$object = (array)$object;
						$uid = $this->get_uid_by_affiliate_id($object['affiliate_id']);
						$user_data = get_userdata($uid);
						$object['username'] = (empty($user_data->user_login)) ? '' : $user_data->user_login;
						$return[] = $object;
					}
				}
			}
			return $return;
		}

		public function get_all_referral_by_payment_status($payment=2, $limit=-1, $offset=-1, $count=FALSE, $order_by='date', $order_type='DESC', $where_conditions=array()){
			/*
			 * @param int, 0-unpaid,1-pending,2-paid
			 * @return array
			 */
			$return = array();
			global $wpdb;
			$table = $wpdb->prefix . 'uap_referrals';
			if ($count){
				$q = $wpdb->prepare("SELECT COUNT(*) as c FROM $table WHERE status='2' AND payment=%s ", $payment);
				if (!empty($where_conditions)){
					foreach ($where_conditions as $condition){
						$q .= " AND " . $condition ;
					}
				}
				$data = $wpdb->get_row($q);
				if (!empty($data->c)){
					return array(0 => $data->c);
				}
				return array();
			}
			$q = $wpdb->prepare("SELECT * FROM $table WHERE status='2' AND payment=%s ", $payment);
			if (!empty($where_conditions)){
				foreach ($where_conditions as $condition){
					$q .= " AND " . $condition ;
				}
			}
			if ($order_type && $order_by){
				$order_by = esc_sql($order_by);
				$order_type = esc_sql($order_type);
				$q .= " ORDER BY " . $order_by . " " . $order_type;
			}
			if ($limit>-1 && $offset>-1){
				$limit = esc_sql($limit);
				$offset = esc_sql($offset);
				$q .= " LIMIT " . $limit . " OFFSET " . $offset;
			}
			$data = $wpdb->get_results($q);
			if ($data){
				foreach ($data as $key=>$object){
					$object = (array)$object;
					$uid = $this->get_uid_by_affiliate_id($object['affiliate_id']);
					$user_data = get_userdata($uid);
					$object['username'] = (empty($user_data->user_login)) ? '' : $user_data->user_login;
					$return[] = $object;
				}
			}
			return $return;
		}

		public function add_payment_for_referral_id($referral_id=0, $args=array()){
			/*
			 * @param int, array
			 * @return none
			 */
			if ($referral_id){
				global $wpdb;
				$temp_data = $this->get_referral($referral_id);

				///get current payment details and store in into db
				$data['payment_details'] = $this->get_current_payment_settings_for_affiliate_id($data['affiliate_id']);

				$table = $wpdb->prefix . 'uap_payments';
				$q = $wpdb->prepare("INSERT INTO $table VALUES( null,
														%s,
														%s,
														%s,
														%s,
														%s,
														%s,
														%s,
														%s,
														%s,
														'',
														%s
						);", $args['payment_type'], $args['transaction_id'], $referral_id, $temp_data['affiliate_id'],
						$temp_data['amount'], $temp_data['currency'], $data['payment_details'], $args['create_date'],
						$args['update_date'], $args['status']
				);
				$wpdb->query($q);
			}
		}

		public function get_stats_for_payments($affiliate_id=0, $exclude_sources_from_referrals=''){
			/*
			 * @param int
			 * @return array
			 */
			global $wpdb;

			$table = $wpdb->prefix . 'uap_affiliates';
			$table_b = $wpdb->base_prefix . 'users';
			$temp_data = $wpdb->get_row("SELECT COUNT(*) as c FROM $table as a INNER JOIN $table_b as b ON a.uid=b.ID;");
			$array['affiliates'] = (empty($temp_data->c)) ? 0 : $temp_data->c;

			$table = $wpdb->prefix . 'uap_payments';
			$q = "SELECT COUNT(*) as c FROM $table";
			if ($affiliate_id){
				$q .= " WHERE affiliate_id='$affiliate_id' ";
			}
			$temp_data = $wpdb->get_row($q);
			$array['payments'] = (empty($temp_data->c)) ? 0 : $temp_data->c;

			$q = "SELECT SUM(amount) as a FROM $table";
			if ($affiliate_id){
				$affiliate_id = esc_sql($affiliate_id);
				$q .= " WHERE affiliate_id='$affiliate_id' ";
			}
			$temp_data = $wpdb->get_row($q);
			$array['paid_payments_value'] = (empty($temp_data->a)) ? 0 : $temp_data->a;

			$table = $wpdb->prefix . 'uap_referrals';
			$q = "SELECT SUM(amount) as a FROM $table WHERE payment='0' AND status='2'";
			if ($affiliate_id){
				$affiliate_id = esc_sql($affiliate_id);
				$q .= " AND affiliate_id='$affiliate_id' ";
			}
			if (!empty($exclude_sources_from_referrals)){
				$q .= " AND source NOT IN ('$exclude_sources_from_referrals') ";
			}
			$temp_data = $wpdb->get_row($q);
			$array['unpaid_payments_value'] = (empty($temp_data->a)) ? 0 : $temp_data->a;

			$table = $wpdb->prefix . 'uap_referrals';
			$q = "SELECT COUNT(id) as c FROM $table WHERE payment='0' AND status='2'";
			if ($affiliate_id){
				$affiliate_id = esc_sql($affiliate_id);
				$q .= " AND affiliate_id='$affiliate_id' ";
			}
			if (!empty($exclude_sources_from_referrals)){
				$q .= " AND source NOT IN ('$exclude_sources_from_referrals') ";
			}
			$temp_data = $wpdb->get_row($q);
			$array['unpaid_referrals_count'] = (empty($temp_data->c)) ? 0 : $temp_data->c;

			$q = "SELECT COUNT(id) as c FROM $table WHERE payment='2' AND status='2'";
			if ($affiliate_id){
				$affiliate_id = esc_sql($affiliate_id);
				$q .= " AND affiliate_id='$affiliate_id' ";
			}
			if (!empty($exclude_sources_from_referrals)){
				$q .= " AND source NOT IN ('$exclude_sources_from_referrals') ";
			}
			$temp_data = $wpdb->get_row($q);
			$array['paid_referrals_count'] = (empty($temp_data->c)) ? 0 : $temp_data->c;

			$table = $wpdb->prefix . 'uap_referrals';
			$q = "SELECT COUNT(*) as c FROM $table WHERE 1=1";
			if ($affiliate_id){
				$affiliate_id = esc_sql($affiliate_id);
				$q .= " AND affiliate_id='$affiliate_id' ";
			}
			if (!empty($exclude_sources_from_referrals)){
				$q .= " AND source NOT IN ('$exclude_sources_from_referrals') ";
			}
			$temp_data = $wpdb->get_row($q);
			$array['referrals'] = (empty($temp_data->c)) ? 0 : $temp_data->c;
			//$array['referrals'] = $array['unpaid_referrals_count'] + $array['paid_referrals_count'];

			$temp_table = $wpdb->prefix . 'uap_visits';
			$q = "SELECT COUNT(*) as c, COUNT(IF(referral_id != 0,1,null)) d FROM $temp_table WHERE affiliate_id=$affiliate_id";
			$temp_data = $wpdb->get_row($q);
			$array['visits'] = (isset($temp_data->c)) ? $temp_data->c : 0;
			$array['converted'] = (isset($temp_data->d)) ? $temp_data->d : 0;

			return $array;
		}

		public function change_transaction_status($id=0, $status=1){
			/*
			 * @param int, int (0,1,2)
			 * @return none
			 */
			if ($id){
				global $wpdb;
				/// update payments
				$table = $wpdb->prefix . 'uap_payments';
				$now = date("Y-m-d H:i:s", time());
				$q = $wpdb->prepare("UPDATE $table SET status=%s, update_date='$now' WHERE id=%d ", $status, $id);
				$wpdb->query($q);

				/// NOTIFICATION TO AFFILIATE
				$this->payments_send_affiliate_notification_by_status($id, $status);

				///update referrals status
				$q = $wpdb->prepare("SELECT referral_ids FROM $table WHERE id=%d", $id);
				$data = $wpdb->get_row($q);
				if (!empty($data->referral_ids)){
					$referrals_ids = explode(',', $data->referral_ids);
					$table = $wpdb->prefix . 'uap_referrals';
					foreach ($referrals_ids as $referral_id){
						$q = $wpdb->prepare("UPDATE $table SET payment=%s WHERE id=%d;", $status, $referral_id);
						$wpdb->query($q);
					}
				}
			}
		}

		public function update_transaction_payment_special_status($id=0, $value=''){
			/*
			 * @param int, string
			 * @return none
			 */
			if ($id && $value){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_payments';
				$q = $wpdb->prepare("UPDATE $table SET payment_special_status=%s WHERE id=%d ", $value, $id);
				$wpdb->query($q);
			}
		}

		public function update_transaction_stripe_status($transaction_id='', $status=''){
			/*
			 * @param string, string
			 * @return none
			 */
			 if ($transaction_id && $status){
			 	global $wpdb;
			 	$table = $wpdb->prefix . 'uap_payments';
				$transaction_id = esc_sql($transaction_id);
				$q = "SELECT id FROM $table WHERE transaction_id='$transaction_id';";
			 	$data = $wpdb->get_row($q);
			 	if ($data && !empty($data->id)){
			 		switch ($status){
						case 'failed':
						case 'canceled':
						case 'reversed':
							$value = 0;
							break;
						case 'paid':
							$value = 2;
							break;
						case 'in_transit':
						case 'pending':
							$value = 1;
							break;
			 		}
					if (isset($value)){
						$this->change_transaction_status($data->id, $value);
					}
					$this->update_transaction_payment_special_status($data->id, $status);
			 	}
			 }
		}

		public function cancel_transaction($transaction_id=0){
			/*
			 * @param int
			 * @return none
			 */
			if ($transaction_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_payments';
				$q = $wpdb->prepare("SELECT referral_ids FROM $table WHERE id=%d ", $transaction_id);
				$data = $wpdb->get_row($q);
				if (!empty($data->referral_ids)){
					$ids = explode(',', $data->referral_ids);
					if ($ids){
						$table = $wpdb->prefix . 'uap_referrals';
						foreach ($ids as $id){
								$q = $wpdb->prepare("UPDATE $table SET payment='0' WHERE id=%d ", $id);
								$wpdb->query($q);
						}
						$table = $wpdb->prefix . 'uap_payments';
						$q = $wpdb->prepare("DELETE FROM $table WHERE id=%d ", $transaction_id);
						$wpdb->query($q);
					}
				}
			}
		}

		public function add_new_mlm_relation($aff_id=0, $parent_aff_id=0){
			/*
			 * @param int, int
			 * @return none
			 */
			if ($aff_id && $parent_aff_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_mlm_relations';
				$q = $wpdb->prepare("INSERT INTO $table VALUES(NULL, %d, %d);", $aff_id, $parent_aff_id);
				$wpdb->query($q);
			}
		}

		public function mlm_get_parent($child=0){
			/*
			 * @param int
			 * @return int
			 */
			if ($child){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_mlm_relations';
				$q = $wpdb->prepare("SELECT parent_affiliate_id FROM $table WHERE affiliate_id=%d", $child);
				$data = $wpdb->get_row($q);
				if (!empty($data->parent_affiliate_id)){
					/// check if affiliate user exists
					$uid = $this->get_uid_by_affiliate_id($data->parent_affiliate_id);
					if ($uid){
						$table = $wpdb->base_prefix . 'users';
						$q = $wpdb->prepare("SELECT * FROM $table WHERE ID=%d ", $uid);
						$inside_data = $wpdb->get_row($q);
						if (!empty($inside_data->ID)){
							return $data->parent_affiliate_id;
						}
					}
				}
			}
			return 0;
		}

		public function mlm_get_children($parent_id=0){
			/*
			 * @param int
			 * @return array
			 */
			$return = array();
			if ($parent_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_mlm_relations';
				$q = $wpdb->prepare("SELECT affiliate_id FROM $table WHERE parent_affiliate_id=%d ", $parent_id);
				$data = $wpdb->get_results($q);
				if ($data){
					foreach ($data as $object){
						if (!empty($object->affiliate_id)){
							$return[] = $object->affiliate_id;
						}
					}
				}
			}
			return $return;
		}

		public function set_mlm_relation_on_new_affiliate($affiliate_id=0, $parent_affiliate_id=0){
			/*
			 * USE THIS WHEN AFFILIATE REGISTER FROM ANOTHER SYSTEM OR BECOME AFFILIATE BY SHORTCODE BUTTON
			 * @param int, int
			 * @return none
			 */
			 if (get_option('uap_mlm_enable') && $affiliate_id){
			 	if (isset($_COOKIE) && !empty($_COOKIE['uap_referral']) && empty($parent_affiliate_id)){
			 		/// SERACH INTO COOKIE
				 	$cookie_data = unserialize(stripslashes($_COOKIE['uap_referral']));
					$parent_affiliate_id = (empty($cookie_data['affiliate_id'])) ? 0 : $cookie_data['affiliate_id'];
			 	}

				if (!empty($parent_affiliate_id)){
					global $indeed_db;
					$matrix_type = get_option('uap_mlm_matrix_type');
					$limit = get_option('uap_mlm_child_limit');
					$depth = get_option('uap_mlm_matrix_depth');
					switch ($matrix_type){
						case 'unilevel':
								$indeed_db->add_new_mlm_relation($affiliate_id, $parent_affiliate_id);
							break;
						case 'binary':
							if ($indeed_db->mlm_affiliate_parent_can_get_new_child($parent_affiliate_id, $limit)){
								$indeed_db->add_new_mlm_relation($affiliate_id, $parent_affiliate_id);
							} else {
								require_once UAP_PATH . 'classes/MLM_Force_Matrix_Parent_Test.class.php';
								$object = new MLM_Force_Matrix_Parent_Test($parent_affiliate_id, 2, $depth);
								$new_parent = $object->get_result();
								if ($new_parent){
									$indeed_db->add_new_mlm_relation($affiliate_id, $new_parent);
								}
							}
							break;
						case 'force':
							require_once UAP_PATH . 'classes/MLM_Force_Matrix_Parent_Test.class.php';
							$object = new MLM_Force_Matrix_Parent_Test($parent_affiliate_id, $limit, $depth);
							$new_parent = $object->get_result();
							if ($new_parent){
								$indeed_db->add_new_mlm_relation($affiliate_id, $new_parent);
							}
							break;
					}
				}
			}
		}

		public function mlm_affiliate_parent_can_get_new_child($affiliate_id=0, $limit=0){
			/*
			 * @param int
			 * @return boolean
			 */
			if ($affiliate_id){
				$rank = $this->get_affiliate_rank($affiliate_id);
				global $wpdb;
				$table = $wpdb->prefix . 'uap_mlm_relations';
				$q = $wpdb->prepare("SELECT count(*) as result FROM $table WHERE parent_affiliate_id=%d;", $affiliate_id);
				$data = $wpdb->get_row($q);
				$current_num = (empty($data->result)) ? 0 : (int)$data->result;
				return ($limit>$current_num) ? TRUE : FALSE;
			}
			return FALSE;
		}


		public function mlm_get_amount($affiliate_id=0, $child_amount=0, $level=1){
			/*
			 * @param int, int
			 * @return int
			 */
			/// DEFAULT AMOUNT
			$amount = get_option('uap_mlm_default_amount_value');
			$amount_type = get_option('uap_mlm_default_amount_type');

			/// AMOUNT TYPE & VALUE BY MLM LEVEL
			if (empty(self::$mlm_amount_value_per_level)){
				self::$mlm_amount_value_per_level = get_option('mlm_amount_value_per_level');
			}
			if (empty(self::$mlm_amout_type_per_level)){
				self::$mlm_amout_type_per_level = get_option('mlm_amount_type_per_level');
			}
			/// AMOUNT BY MLM LEVEL
			if (!empty(self::$mlm_amount_value_per_level) && !empty(self::$mlm_amount_value_per_level[$level])){
				$amount = self::$mlm_amount_value_per_level[$level];
				$amount_type = self::$mlm_amout_type_per_level[$level];
			}

			/// AMOUNT TYPE & VALUE BY AFFILIATE RANK
			if ($affiliate_id){
				global $wpdb;
				$rank = $this->get_affiliate_rank($affiliate_id);
				$table = $wpdb->prefix . 'uap_ranks';
				$q = $wpdb->prepare("SELECT mlm_amount_type, mlm_amount_value FROM $table WHERE id=%d", $rank);
				$data = $wpdb->get_row($q);
				if (!empty($data->mlm_amount_type) && !empty($data->mlm_amount_value)){
					@$data->mlm_amount_type = unserialize($data->mlm_amount_type);
					@$data->mlm_amount_value = unserialize($data->mlm_amount_value);
					if (isset($data->mlm_amount_value) && !empty($data->mlm_amount_value[$level]) && !empty($data->mlm_amount_type) && !empty($data->mlm_amount_type[$level])){
						$amount = $data->mlm_amount_value[$level];
						$amount_type = $data->mlm_amount_type[$level];
					}
				}
			}

			if ($amount_type=='flat'){
				return $amount;
			} else {
				return $amount * $child_amount / 100;
			}
		}

		public function get_mlm_amount_value_for_rank_by_aff_id($affiliate_id){
			/*
			 * @param int
			 * @return array
			 */
			 $array = array();
			 $rank = $this->get_affiliate_rank($affiliate_id);
			 if ($rank){
			 	global $wpdb;
				$table = $wpdb->prefix . 'uap_ranks';
				$q = $wpdb->prepare("SELECT mlm_amount_type, mlm_amount_value FROM $table WHERE id=%d", $rank);
				$data = $wpdb->get_row($q);
				if (!empty($data->mlm_amount_type) && !empty($data->mlm_amount_value)){
					@$array['types'] = unserialize($data->mlm_amount_type);
					@$array['values'] = unserialize($data->mlm_amount_value);
				}
			 }
			 return $array;
		}

		public function mlm_get_count_children_for_parent($affiliate_id=0){
			/*
			 * @param int
			 * @return int
			 */
			$count = 0;
			if ($affiliate_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_mlm_relations';
				$q = $wpdb->prepare("SELECT COUNT(affiliate_id) as c FROM $table WHERE parent_affiliate_id=%d ", $affiliate_id);
				$data = $wpdb->get_row($q);
				$count = (empty($data->c)) ? 0 : $data->c;
			}
			return $count;
		}

		public function get_affiliate_user_relation($affiliate_username='', $user_username=''){
			/*
			 * @param string, string
			 * @return
			 */
			global $wpdb;
			$return = array();
			$table_u = $wpdb->base_prefix . 'users';
			$table_a = $wpdb->prefix . 'uap_affiliates';
			$table_au = $wpdb->prefix . 'uap_affiliate_referral_users_relations';

			$affiliate_username = esc_sql($affiliate_username);
			$data = $wpdb->get_results("
										SELECT a.id as id
											FROM $table_u as u
											INNER JOIN $table_a as a
											ON a.uid=u.ID
											INNER JOIN $table_au as au
											ON au.affiliate_id=a.id
											WHERE
											u.user_login LIKE '%$affiliate_username%'
			");
			if (!empty($data)){
				foreach ($data as $object){
					if (isset($object->id)){
						$ids[] = $object->id;
					}
				}
				$id_string = (empty($ids)) ? '' : implode(',',$ids);
				$user_username = esc_sql($user_username);
				$data = $wpdb->get_results("
										SELECT au.*, u.user_login
											FROM $table_u as u
											INNER JOIN $table_au as au
											ON au.referral_wp_uid=u.ID
											WHERE
											u.user_login LIKE '%$user_username%'
											AND au.affiliate_id IN ($id_string)
				");
				if ($data){
					foreach ($data as $object){
						$return[$object->id]['affiliate_username'] = $this->get_wp_username_by_affiliate_id($object->affiliate_id);
						$return[$object->id]['referral_username'] = $object->user_login;
					}
				}
			}
			return $return;
		}

		public function get_affiliate_like_this($affiliate_username=''){
			/*
			 * @param string
			 * @return array
			 */
			global $wpdb;
			$return = array();
			$table_u = $wpdb->base_prefix . 'users';
			$table_a = $wpdb->prefix . 'uap_affiliates';

			$affiliate_username = esc_sql($affiliate_username);
			$data = $wpdb->get_results("
										SELECT a.id as id, u.user_login as username
											FROM $table_u as u
											INNER JOIN $table_a as a
											ON a.uid=u.ID
											WHERE
											u.user_login LIKE '%$affiliate_username%'
					");
			if (!empty($data)){
				foreach ($data as $key=>$object){
					$return[$object->id] = $object->username;
				}
			}
			return $return;
		}

		public function get_affiliate_user_relation_by_id($id){
			/*
			 * @param int
			 * @return none
			 */
			$return = array();
			if ($id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
				$q = $wpdb->prepare("SELECT * FROM $table WHERE id=%d ", $id);
				$data = $wpdb->get_row($q);
				if (!empty($data->affiliate_id) && !empty($data->referral_wp_uid)){
					$return['referral_username'] = $this->get_username_by_wpuid($data->referral_wp_uid);
					$return['affiliate_id'] = $data->affiliate_id;
					$return['relation'] = $id;
				}
			}
			return $return;
		}

		public function get_username_by_wpuid($wpuid=0){
			/*
			 * @param int
			 * @return string
			 */
			if ($wpuid){
				global $wpdb;
				$table = $wpdb->base_prefix . 'users';
				$q = $wpdb->prepare("SELECT user_login FROM $table WHERE ID=%d ", $wpuid);
				$data = $wpdb->get_row($q);
				if (!empty($data->user_login)){
					return $data->user_login;
				}
			}
			return '';
		}

		public function affiliate_referrals_delete_relation($id=0){
			/*
			 * @param int
			 * @return none
			 */
			if ($id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
				$q = $wpdb->prepare("DELETE FROM $table WHERE id=%d ", $id);
				$wpdb->query($q);
			}
		}

		public function search_woo_products($search=''){
			/*
			 * @param string
			 * @return array
			 */
			$arr = array();
			if ($search){
				global $wpdb;
				$table = $wpdb->prefix . 'posts';
				$search = esc_sql($search);
				$data = $wpdb->get_results("SELECT post_title, ID
												FROM $table
												WHERE
												post_title LIKE '%$search%'
												AND post_type='product'
												AND post_status='publish'
				");
				if ($data){
					foreach ($data as $object){
						$arr[$object->ID] = $object->post_title;
					}
				}
			}
			return $arr;
		}

		public function woo_get_product_title_by_id($id=0){
			/*
			 * @param int
			 * @return string
			 */
			if ($id){
				global $wpdb;
				$table = $wpdb->prefix . 'posts';
				$q = $wpdb->prepare("SELECT post_title
												FROM $table
												WHERE ID=%d
				", $id);
				$data = $wpdb->get_row($q);
				if (!empty($data->post_title)){
					return $data->post_title;
				}
			}
			return '';
		}

		public function search_ump_levels($search=''){
			/*
			 * @param string
			 * @return array
			 */
			$arr = array();
			if ($search){
				$search = strtolower($search);
				$levels = get_option('ihc_levels');
				if ($levels){
					foreach ($levels as $k=>$v){
						$value = strtolower($v['label']);
						if (strpos($value, $search)!==FALSE){
							$arr[$k] = $v['label'];
						}
					}
				}
			}
			return $arr;
		}

		public function ump_get_level_label_by_id($id=0){
			/*
			 * @param string
			 * @return array
			 */
			if ($id){
				$levels = get_option('ihc_levels');
				if (!empty($levels[$id]) && !empty($levels[$id]['label'])){
					return $levels[$id]['label'];
				}
			}
			return '';
		}

		public function search_edd_product($search=''){
			/*
			 * @param string
			 * @return array
			 */
			$arr = array();
			if ($search){
				global $wpdb;
				$table = $wpdb->prefix . 'posts';
				$search = esc_sql($search);
				$data = $wpdb->get_results("SELECT post_title, ID
												FROM $table
												WHERE
												post_title LIKE '%$search%'
												AND post_type='download'
												AND post_status='publish'
				");
				if ($data){
					foreach ($data as $object){
						$arr[$object->ID] = $object->post_title;
					}
				}
			}
			return $arr;
		}

		public function search_ulp_product($search='')
		{
				global $wpdb;
				$array = array();
				if (empty($search)) return $array;
				$search = esc_sql($search);
				$data = $wpdb->get_results("SELECT post_title, ID
												FROM {$wpdb->posts}
												WHERE
												post_title LIKE '%$search%'
												AND post_type='ulp_course'
												AND post_status='publish'
				");
				if ($data){
					foreach ($data as $object){
						$array[$object->ID] = $object->post_title;
					}
				}
				return $array;
		}

		public function edd_get_label_by_id($id=0){
			/*
			 * @param int
			 * @return string
			 */
			if ($id){
				global $wpdb;
				$table = $wpdb->prefix . 'posts';
				$q = $wpdb->prepare("SELECT post_title
						FROM $table
						WHERE ID=%d
				", $id);
				$data = $wpdb->get_row($q);
				if (!empty($data->post_title)){
					return $data->post_title;
				}
			}
			return '';
		}

		public function ulp_get_label_by_id($id=0)
		{
				if (empty($id)) return '';
				global $wpdb;
				$query = $wpdb->prepare("SELECT post_title FROM {$wpdb->posts} WHERE ID=%d;", $id);
				return $wpdb->get_var($query);
		}

		public function stats_for_dashboard(){
			/*
			 * @param none
			 * @return array
			 */
			global $wpdb;
			$table = $wpdb->prefix . 'uap_affiliates';
			$table_b = $wpdb->base_prefix . 'users';
			$temp_data = $wpdb->get_row("SELECT COUNT(*) as c FROM $table as a INNER JOIN $table_b as b ON a.uid=b.ID;");
			$array['affiliates'] = (empty($temp_data->c)) ? 0 : $temp_data->c;

			$table = $wpdb->prefix . 'uap_referrals';
			$temp_data = $wpdb->get_row("SELECT SUM(amount) as a FROM $table WHERE payment='0' AND status='2'");
			$array['unpaid_payments_value'] = (empty($temp_data->a)) ? 0 : $temp_data->a;

			$table = $wpdb->prefix . 'uap_referrals';
			$data = $wpdb->get_row("SELECT COUNT(*) as c FROM $table;");
			$array['referrals'] = (isset($data->c)) ? $data->c : 0;

			$table = $wpdb->prefix . 'uap_affiliates';
			$data = $wpdb->get_row("SELECT rank_id,COUNT(rank_id) as c FROM $table WHERE rank_id>0 GROUP BY rank_id ORDER BY c DESC LIMIT 1;");
			$top_rank = (isset($data->rank_id)) ? $data->rank_id : 0;
			$rank_data = $this->get_rank($top_rank);
			$array['top_rank'] = $rank_data['label'];
			return $array;
		}

		public function get_affilitated_per_rank(){
			/*
			 * @param none
			 * @return array
			 */
			global $wpdb;
			$return = array();
			$table = $wpdb->prefix . 'uap_affiliates';
			$table_b = $wpdb->base_prefix . 'users';
			$data = $wpdb->get_results("SELECT a.rank_id,COUNT(a.rank_id) as c FROM $table a INNER JOIN $table_b b ON a.uid=b.ID GROUP BY rank_id;");
			if (!empty($data) && is_array($data)){
				foreach ($data as $object){
					$rank_data = $this->get_rank($object->rank_id);
					$label = $rank_data['label'];
					$return[$label] = $object->c;
				}
			}
			return $return;
		}

		public function get_last_referrals($limit=5){
			/*
			 * @param int
			 * @return array
			 */
			global $wpdb;
			$array = array();
			$table = $wpdb->prefix . 'uap_referrals';
			$limit = esc_sql($limit);
			$data = $wpdb->get_results("SELECT * FROM $table ORDER BY date DESC LIMIT $limit;");
			if ($data){
				foreach ($data as $object){
					$array_element['affiliate_username'] = $this->get_wp_username_by_affiliate_id($object->affiliate_id);
					$array_element['date'] = $object->date;
					$array_element['amount'] = $object->amount;
					$array_element['currency'] = $object->currency;
					$array[] = $array_element;
				}
			}
			return $array;
		}

		public function get_top_affiliates_by_amount($limit=10){
			/*
			 * @param int
			 * @return array
			 */
			global $wpdb;
			$array = array();
			$table = $wpdb->prefix . 'uap_referrals';
			$limit = esc_sql($limit);
			$data = $wpdb->get_results("SELECT affiliate_id,SUM(amount) as s FROM $table GROUP BY affiliate_id ORDER BY s DESC LIMIT $limit;");

			if ($data && is_array($data)){
				foreach ($data as $object){
					$key = $object->affiliate_id;
					$array[$key]['sum'] = round($object->s, 2);
					$name = $this->get_full_name_of_user($object->affiliate_id);
					$array[$key]['name'] = (empty($name)) ? __('Deleted Affiliate', 'uap') : $name;
					if (isset($where)){
						unset($where);
					}
					$where[] = "r.affiliate_id=" . $object->affiliate_id;
					$num_of_referrals = $this->get_referrals( -1, -1, TRUE, '', '', $where);
					$array[$key]['referrals'] = isset($num_of_referrals) ? $num_of_referrals : 0;
				}
			}

			return $array;
		}

		public function get_full_name_of_user($affiliate_id=0){
			/*
			 * @param int
			 * @return string
			 */
			$return = '';
			if ($affiliate_id){
				$uid = $this->get_uid_by_affiliate_id($affiliate_id);
				if ($uid){
					global $wpdb;
					$table = $wpdb->base_prefix . 'usermeta';
					$data = $wpdb->get_row("SELECT meta_value FROM $table WHERE user_id='$uid' AND meta_key='first_name';");
					$return = (empty($data->meta_value)) ? '' : $data->meta_value . ' ';
					$data = $wpdb->get_row("SELECT meta_value FROM $table WHERE user_id='$uid' AND meta_key='last_name';");
					$return .= (empty($data->meta_value)) ? '' : $data->meta_value;
				}
			}
			return $return;
		}

		public function get_stats_for_reports($time='', $affiliate_id=0){
			/*
			 * @param string, int
			 * @return array
			 */
			global $wpdb;
			$array = array();
			$now = time();
			$today = strtotime('00:00:00');
			$affiliate_id = esc_sql($affiliate_id);
			if ($time){
				switch ($time){
					case 'today':
						//$time = date("Y-m-d H:i:s",strtotime("-1 day"));
						$start_time = strtotime('00:00:00');
						$start_time = date('Y-m-d H:i:s', $start_time);
						$start_time = strtotime($start_time);
						$end_now = TRUE;
						break;
					case 'yesterday':
						$start_time = strtotime('-1 day', $today);
						$start_time = date('Y-m-d H:i:s', $start_time);
						$start_time = strtotime($start_time);
						$end_time = date('Y-m-d H:i:s', $today);
						$end_time = strtotime($end_time);
						break;
					case 'last_week':
						$start_time = strtotime('-7 day', $today);
						$end_now = TRUE;
						//$end_time = date('Y-m-d H:i:s', $today);
						break;
					case 'last_month':
						$start_time = strtotime('-30 day', $today);
						//$end_time = date('Y-m-d H:i:s', $today);
						$end_now = TRUE;
						break;
				}
			}

			$table = $wpdb->prefix . 'uap_payments';
			$q = "SELECT SUM(amount) as s FROM $table WHERE status=2";
			if (!empty($start_time)){
				$q .= " AND UNIX_TIMESTAMP(update_date)>$start_time ";
			}
			if (!empty($end_time)){
				$q .= " AND UNIX_TIMESTAMP(update_date)<$end_time ";
			} else if (!empty($end_now)){
				$q .= " AND update_date<NOW()";
			}
			if ($affiliate_id){
				$q .= " AND affiliate_id=$affiliate_id";
			}
			$data = $wpdb->get_row($q);
			$array['total_paid'] = (isset($data->s)) ? $data->s : 0;

			$table = $wpdb->prefix . 'uap_referrals';
			$q = "SELECT SUM(amount) as s FROM $table WHERE payment='0' AND status='2'";

			if (!empty($start_time)){
				$q .= " AND UNIX_TIMESTAMP(date)>$start_time ";
			}
			if (!empty($end_time)){
				$q .= " AND UNIX_TIMESTAMP(date)<$end_time ";
			} else if (!empty($end_now)){
				$q .= " AND date<NOW()";
			}

			if ($affiliate_id){
				$q .= " AND affiliate_id=$affiliate_id";
			}
			$data = $wpdb->get_row($q);
			$array['total_unpaid'] = (isset($data->s)) ? $data->s : 0;

			$table = $wpdb->prefix . 'uap_affiliates';
			$table_b = $wpdb->base_prefix . 'users';
			$q = "SELECT COUNT(*) as c FROM $table as a INNER JOIN $table_b as b ON a.uid=b.ID WHERE 1=1";

			if (!empty($start_time)){
				$q .= " AND UNIX_TIMESTAMP(a.start_data)>$start_time ";
			}
			if (!empty($end_time)){
				$q .= " AND UNIX_TIMESTAMP(a.start_data)<$end_time ";
			} else if (!empty($end_now)){
				$q .= " AND a.start_data<NOW()";
			}

			if ($affiliate_id){
				$q .= " AND a.id=$affiliate_id";
			}
			$data = $wpdb->get_row($q);
			$array['affiliates'] = (empty($data->c)) ? 0 : $data->c;

			$table = $wpdb->prefix . 'uap_referrals';
			$q = "SELECT COUNT(*) as c FROM $table WHERE 1=1";

			if (!empty($start_time)){
				$q .= " AND UNIX_TIMESTAMP(date)>$start_time ";
			}
			if (!empty($end_time)){
				$q .= " AND UNIX_TIMESTAMP(date)<$end_time ";
			} else if (!empty($end_now)){
				$q .= " AND date<NOW()";
			}

			if ($affiliate_id){
				$q .= " AND affiliate_id=$affiliate_id";
			}
			$data = $wpdb->get_row($q);
			$array['referrals'] = (empty($data->c)) ? 0 : $data->c;

			$table = $wpdb->prefix . 'uap_visits';
			$q = "SELECT COUNT(*) as c FROM $table WHERE 1=1";

			if (!empty($start_time)){
				$q .= " AND UNIX_TIMESTAMP(visit_date)>$start_time ";
			}
			if (!empty($end_time)){
				$q .= " AND UNIX_TIMESTAMP(visit_date)<$end_time ";
			} else if (!empty($end_now)){
				$q .= " AND visit_date<NOW() ";
			}

			if ($affiliate_id){
				$q .= " AND affiliate_id=$affiliate_id";
			}
			$data = $wpdb->get_row($q);
			$array['visits'] = (empty($data->c)) ? 0 : $data->c;


			$q = "SELECT COUNT(*) as c FROM $table WHERE 1=1 AND referral_id>0";

			if (!empty($start_time)){
				$q .= " AND UNIX_TIMESTAMP(visit_date)>$start_time ";
			}
			if (!empty($end_time)){
				$q .= " AND UNIX_TIMESTAMP(visit_date)<$end_time ";
			} else if (!empty($end_now)){
				$q .= " AND visit_date<NOW()";
			}

			if ($affiliate_id){
				$q .= " AND affiliate_id=$affiliate_id";
			}
			$data = $wpdb->get_row($q);
			$array['conversions'] = (empty($data->c)) ? 0 : $data->c;

			if (!empty($array['visits']) && !empty($array['conversions'])){
				$array['success_rate'] = ($array['conversions']*100) / $array['visits'];
				$array['success_rate'] = round($array['success_rate'], 2);
			} else {
				$array['success_rate'] = 0;
			}

			return $array;
		}

		public function get_stats_for_referrals($time='', $affiliate_id=0){
			/*
			 * @param string, int
			 * @return array
			 */
			 $arr = array();
			 if ($affiliate_id){
				$now = time();
				$today = strtotime('00:00:00');
				$affiliate_id = esc_sql($affiliate_id);
				if ($time){
					switch ($time){
						case 'today':
							//$time = date("Y-m-d H:i:s",strtotime("-1 day"));
							$start_time = strtotime('00:00:00');
							$start_time = date('Y-m-d H:i:s', $start_time);
							$end_now = TRUE;
							break;
						case 'yesterday':
							$start_time = strtotime('-1 day', $today);
							$start_time = date('Y-m-d H:i:s', $start_time);
							$end_time = date('Y-m-d H:i:s', $today);
							break;
						case 'last_week':
							$start_time = strtotime('-7 day', $today);
							$end_now = TRUE;
							//$end_time = date('Y-m-d H:i:s', $today);
							break;
						case 'last_month':
							$start_time = strtotime('-30 day', $today);
							//$end_time = date('Y-m-d H:i:s', $today);
							$end_now = TRUE;
							break;
					}
				}
			 	global $wpdb;
				$arr['referrals'] = 0;
				$table = $wpdb->prefix . 'uap_referrals';
				$q = "SELECT COUNT(*) as c FROM $table WHERE 1=1";

				if (!empty($start_time)){
					$q .= " AND date>'$start_time'";
				}
				if (!empty($end_time)){
					$q .= " AND date<'$end_time'";
				} else if (!empty($end_now)){
					$q .= " AND date<NOW()";
				}

				if ($affiliate_id){
					$q .= " AND affiliate_id=$affiliate_id";
				}
				$data = $wpdb->get_row($q);
				$arr['referrals'] = (empty($data->c)) ? 0 : $data->c;

				$table = $wpdb->prefix . 'uap_referrals';
				$q = "SELECT SUM(amount) as s FROM $table WHERE payment=0 AND status=2 AND affiliate_id=$affiliate_id";
				$data = $wpdb->get_row($q);
				$arr['verified_referrals_amount'] = (empty($data->s)) ? 0 : $data->s;

				$table = $wpdb->prefix . 'uap_referrals';
				$q = "SELECT SUM(amount) as s FROM $table WHERE payment=0 AND status=1 AND affiliate_id=$affiliate_id";
				$data = $wpdb->get_row($q);
				$arr['unverified_referrals_amount'] = (empty($data->s)) ? 0 : $data->s;
			 }
			 return $arr;
		}

		public function get_email_by_username($username=''){
			/*
			 * @param string
			 * @return string
			 */
			$arr = array();
			if ($username){
				global $wpdb;
				$table = $wpdb->base_prefix . "users";
				$q = $wpdb->prepare("SELECT ID, user_email FROM $table WHERE user_login=%s ", $username);
				$data = $wpdb->get_row($q);
				if (isset($data->ID) && isset($data->user_email)){
					$arr['ID'] = $data->ID;
					$arr['email'] = $data->user_email;
				}
			}
			return $arr;
		}

		public function get_email_by_uid($uid=0){
			/*
			 * @param int
			 * @return string
			 */
			if ($uid){
				global $wpdb;
				$table = $wpdb->base_prefix . "users";
				$q = $wpdb->prepare("SELECT user_email FROM $table WHERE ID=%d ", $uid);
				$data = $wpdb->get_row($q);
				if (isset($data->user_email)){
					return $data->user_email;
				}
			}
			return '';
		}

		public function get_transation_details($id=0){
			/*
			 * @param int
			 * @return array
			 */
			$return = array();
			if ($id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_payments';
				$q = $wpdb->prepare("SELECT * FROM $table WHERE id=%d ", $id);
				$data = $wpdb->get_row($q);
				if ($data){
					$table = $wpdb->prefix . 'uap_referrals';
					$referrals_data = $wpdb->get_results("SELECT * FROM $table WHERE id IN (" . $data->referral_ids . ");");
					if ($referrals_data){
						foreach ($referrals_data as $object){
							$object = (array)$object;
							$uid = $this->get_uid_by_affiliate_id($object['affiliate_id']);
							$user_data = get_userdata($uid);
							$object['username'] = (empty($user_data->user_login)) ? '' : $user_data->user_login;
							$return[] = $object;
						}
					}
				}
			}
			return $return;
		}

		/*
		 * @param int (id)
		 * @return array
		 */
		public function get_payment_details_on_transaction_by_id($transaction_id=0){
			if ($transaction_id){
				global $wpdb;
				$table = $wpdb->prefix . 'uap_payments';
				$q = $wpdb->prepare("SELECT payment_details FROM $table WHERE id=%d ", $transaction_id);
				$data = $wpdb->get_row($q);
				if ($data && !empty($data->payment_details)){
					return unserialize($data->payment_details);
				}
			}
			return array();
		}

		public function get_paypal_email_addr($affiliate_id=0){
			/*
			 * @param int
			 * @return string
			 */
			if ($affiliate_id){
				$uid = $this->get_uid_by_affiliate_id($affiliate_id);
				$mail = get_user_meta($uid, 'uap_affiliate_paypal_email', TRUE);
				if ($mail){
					return $mail;
				} else {
					global $wpdb;
					$table_a = $wpdb->base_prefix . 'users';
					$table_b = $wpdb->prefix . 'uap_affiliates';
					$q = $wpdb->prepare("SELECT a.user_email FROM $table_a as a INNER JOIN $table_b as b ON a.ID=b.uid WHERE b.id=%d", $affiliate_id);
					$data = $wpdb->get_row($q);
					if (!empty($data->user_email)){
						return $data->user_email;
					}
				}
			}
			return '';
		}

		public function update_paypal_transactions(){
			/*
			 * @param none
			 * @return none
			 */
			global $wpdb;
			$table = $wpdb->base_prefix . 'uap_payments';
			$data = $wpdb->get_results("SELECT transaction_id, id FROM $table
											WHERE 1=1
											AND payment_type='paypal'
											AND status=1
											ORDER BY update_date DESC
			");
			if (!empty($data)){
				require_once UAP_PATH . 'classes/Uap_PayPal.class.php';
				foreach ($data as $object){
					$paypal = new Uap_PayPal();
					$status = $paypal->get_status($object->transaction_id);
					$this->update_transaction_payment_special_status($object->id, $status);
					switch ($status){
						case 'SUCCESS':
							$this->change_transaction_status($object->id, 2);
							break;
						case 'DENIED':
						case 'FAILED':
						case 'UNCLAIMED':
						case 'RETURNED':
						case 'ONHOLD':
						case 'BLOCKED':
						case 'CANCELLED':
							$this->change_transaction_status($object->id, 0);
							break;
						case 'PENDING':
						case 'PROCESSIN':
						default:
							$this->change_transaction_status($object->id, 1);
							break;
					}

				}
				unset($paypal);
			}
		}


		public function get_visits_for_graph($interval='today', $succes_filter='all', $affiliate_id=0){
			/*
			 * @param string : today, yesterday, last_week, last_month, all_time ; string : all, success, only_visit; int
			 * @return array
			 */
			$return = array();
			$now = time();
			$today = strtotime('00:00:00');

			$affiliate_id = esc_sql($affiliate_id);
			$last_week = strtotime('-7 day', $today);
			$last_month = strtotime('-30 day', $today);
			switch ($interval){
				case 'today':
					$start = strtotime('00:00:00');
					$start = date('Y-m-d H:i:s', $start);
					//$end = date('Y-m-d H:i:s', $now);
					$end_now = TRUE;
					$div = 3600;// one hour
					break;
				case 'yesterday':
					$start = strtotime('-1 day', $today);
					$start = date('Y-m-d H:i:s', $start);
					$end = date('Y-m-d H:i:s', $today);
					$div = 3600;// one hour
					break;
				case 'last_week':
					$start = strtotime('-7 day', $today);
					$end_now = TRUE;
					//$end = date('Y-m-d H:i:s', $today);
					$div = 3600 * 24;// 24 hours
					break;
				case 'last_month':
					$start = strtotime('-30 day', $today);
					$end_now = TRUE;
					//$end = date('Y-m-d H:i:s', $today);
					$div = 3600 * 24;// 24 hours
					break;
				case 'all_time':
					//$end = date('Y-m-d H:i:s', $today);
					$end_now = TRUE;
					$div = 3600 * 24;// 24 hours
					break;
			}
			global $wpdb;
			$table = $wpdb->prefix . 'uap_visits';
			$q = "SELECT COUNT(*) as value, visit_date FROM $table";
			$q .= " WHERE 1=1";
			if (!empty($start)){
				$q .= " AND visit_date>'$start'";
			}
			if (!empty($end)){
				$q .= " AND visit_date<'$end'";
			} else if (!empty($end_now)){
				$q .= " AND visit_date<NOW()";
			}
			if ($succes_filter=='success'){
				$q .= " AND referral_id>0";
			} else if ($succes_filter=='only_visit'){
				$q .= " AND referral_id=0";
			}
			if (!empty($affiliate_id)){
				$q .= " AND affiliate_id=$affiliate_id";
			}
			$q .= " GROUP BY UNIX_TIMESTAMP(visit_date) ";
			if (!empty($div)){
				$q .= " DIV $div ";
			}
			$q .= " ORDER BY visit_date ASC";
			$data = $wpdb->get_results($q);
			if ($data){
				foreach ($data as $object){
					$return[$object->visit_date] = $object->value;
				}
			}
			return $return;
		}

		public function get_referrals_for_graph($interval='today', $status=-1, $affiliate_id=0){
			/*
			 * @param string : today, yesterday, last_week, last_month, all_time ; int : 0, 1, 2; int
			 * @return array
			 */
			$return = array();
			$now = time();
			$today = strtotime('00:00:00');

			$affiliate_id = esc_sql($affiliate_id);
			$last_week = strtotime('-7 day', $today);
			$last_month = strtotime('-30 day', $today);
			switch ($interval){
				case 'today':
					$start = strtotime('00:00:00');
					$start = date('Y-m-d H:i:s', $start);
					$start = strtotime($start);
					$end_now = TRUE;
					$div = 3600;// one hour
					break;
				case 'yesterday':
					$start = strtotime('-1 day', $today);
					$start = date('Y-m-d H:i:s', $start);
					$start = strtotime($start);
					$end = date('Y-m-d H:i:s', $today);
					$end = strtotime($end);
					$div = 3600;// one hour
					break;
				case 'last_week':
					$start = strtotime('-7 day', $today);
					$end_now = TRUE;
					$div = 3600 * 24;// 24 hours
					break;
				case 'last_month':
					$start = strtotime('-30 day', $today);
					$end_now = TRUE;
					$div = 3600 * 24;// 24 hours
					break;
				case 'all_time':
					$end_now = TRUE;
					$div = 3600 * 24;// 24 hours
					break;
			}

			global $wpdb;
			$table = $wpdb->prefix . 'uap_referrals';
			$q = "SELECT COUNT(*) as value, date FROM $table";
			$q .= " WHERE 1=1";
			if (!empty($start)){
				$q .= " AND UNIX_TIMESTAMP(date)>$start ";
			}
			if (!empty($end)){
				$q .= " AND UNIX_TIMESTAMP(date)<$end ";
			} else if (!empty($end_now)){
				$q .= " AND UNIX_TIMESTAMP(date)<TIMESTAMP(NOW()) ";
			}

			if ($status>-1){
				$q .= " AND status=$status";
			}

			if (!empty($affiliate_id)){
				$q .= " AND affiliate_id=$affiliate_id";
			}

			$q .= " GROUP BY UNIX_TIMESTAMP(date) ";
			if (!empty($div)){
				//$q .= " DIV $div ";
			}
			$q .= " ORDER BY date ASC";

			$data = $wpdb->get_results($q);
			if ($data){
				foreach ($data as $object){
					$return[$object->date] = $object->value;
				}
			}
			return $return;
		}

		public function get_current_page_type($id=0){
			/*
			 * @param int
			 * @return string
			 */
			if ($id){
				$data = $this->return_settings_from_wp_option('general-default_pages');
				if ($key=array_search($id, $data)){
					return $key;
				}
			}
			return '';
		}

		public function set_default_page($type, $post_id){
			/*
			 * @param string, int
			 * @return none
			 */
			if ($type){
				if ($post_id==-1){
					$post_id = '';
				} else {
					$current_type = $this->get_current_page_type($post_id);
					update_option($current_type, -1);
				}
				update_option($type, $post_id);
			}
		}

		public function get_default_unset_pages(){
			/*
			 * @param none
			 * @return array
			 */
			$unset = array();
			$arr = array(
					'uap_general_login_default_page' => __('Login', 'uap'),
					'uap_general_register_default_page' => __('Register', 'uap'),
					'uap_general_lost_pass_page' =>  __('Lost Password', 'uap'),
					'uap_general_logout_page' =>  __('LogOut', 'uap'),
					'uap_general_user_page' =>  __('User Account', 'uap'),
					'uap_general_tos_page' => __('TOS', 'uap'),
			);
			$values = $this->return_settings_from_wp_option('general-default_pages');
			foreach ($arr as $name=>$label){
				if (empty($values[$name]) || $values[$name]==-1){
					$unset[] = $label;
				}
			}
			return $unset;
		}

		public function get_affiliate_payment_settings($uid=0, $affiliate_id=0, $only_keys=FALSE){
			/*
			 * @param int, int
			 * @return array
			 */
			$array = array(
							'uap_affiliate_payment_type' => 'bt',
							///BT
							'uap_affiliate_bank_transfer_data' => '',
							/// PAYPAL
							'uap_affiliate_paypal_email' => '',
							/// STRIPE
							'uap_affiliate_stripe_name' => '',
							'uap_affiliate_stripe_card_number' => '',
							//'uap_affiliate_stripe_cvc' => '',
							'uap_affiliate_stripe_expiration_month' => '',
							'uap_affiliate_stripe_expiration_year' => '',
							'uap_affiliate_stripe_card_type' => 'individual',
							//'uap_affiliate_stripe_tax_id' => '',

			);
			if ($only_keys){
				return $array;
			}
			if (!$uid && $affiliate_id){
				$uid = $this->get_uid_by_affiliate_id($affiliate_id);
			}
			if ($uid){
				foreach ($array as $meta_key=>$meta_value){
					$temp_data = get_user_meta($uid, $meta_key, TRUE);
					if (isset($temp_data)){
						$array[$meta_key] = $temp_data;
					}
				}
			}
			return $array;
		}

		public function get_affiliate_stripe_v2_payment_settings($uid=0, $only_keys=FALSE){
			/*
			 * @param boolean
			 * @return array
			 */
			 $array = array(
			 				'country' => '',
			 				'city' => '',
			 				'user_type' => '',
			 				'routing_number' => '',
			 				'account_number' => '',
			 				'day' => '',
			 				'month' => '',
			 				'year' => '',
			 				'first_name' => '',
			 				'last_name' => '',
			 				'line1' => '',
			 				'postal_code' => '',
			 				'state' => '',
			 				'ssn_last_4' => '',
			 				'personal_id_number' => '',
			 				'business_name' => '',
			 				'business_tax_id' => '',
							'personal_address.city' => '',
							'personal_address.line1' => '',
							'personal_address.postal_code' => '',
							'verification_document' => '',
							'stripe_v2_tos' => 0,
			 );
			 if (!$only_keys){
			 	if ($uid){
			 		$temp = get_user_meta($uid, 'stripe_v2_meta_data', TRUE);
					if ($temp){
						foreach ($array as $key=>$value){
							if (isset($temp[$key])){
								$array[$key] = $temp[$key];
							}
						}
					}
			 	}
			 }
			 return $array;
		}

		public function get_affiliate_payment_type($uid=0, $affiliate_id=0){
			/*
			 * @param int, int
			 * @return string
			 */
			$data = array();
			$temp = $this->get_affiliate_payment_settings($uid, $affiliate_id);
			if (!empty($temp['uap_affiliate_payment_type'])){
				$data['type'] = $temp['uap_affiliate_payment_type'];
				 switch ($temp['uap_affiliate_payment_type']):
				 	case 'paypal':
						$data['is_active'] = (empty($temp['uap_affiliate_paypal_email'])) ? FALSE : TRUE;
						$data['settings'] = $temp['uap_affiliate_paypal_email'];
						break;
					case 'bt':
						$data['is_active'] = (empty($temp['uap_affiliate_bank_transfer_data'])) ? FALSE : TRUE;
						$data['settings'] = $temp['uap_affiliate_bank_transfer_data'];
						break;
					case 'stripe':
						$keys = array(
									'uap_affiliate_stripe_name',
									'uap_affiliate_stripe_card_number',
									//'uap_affiliate_stripe_cvc',
									'uap_affiliate_stripe_expiration_month',
									'uap_affiliate_stripe_expiration_year',
									'uap_affiliate_stripe_card_type',
						);
						$data['is_active'] = TRUE;
						foreach ($keys AS $key){
							if (!isset($temp[$key])){
								$data['is_active'] = FALSE;
							}
							$data['settings'][$key] = $temp[$key];
						}
						break;
					case 'stripe_v2':
						$stripe_account_id = get_user_meta($uid, 'ihc_stripe_connected_account_id', TRUE);
						if ($stripe_account_id){
							$data['is_active'] = TRUE;
						} else {
							$data['is_active'] = FALSE;
						}
						break;
				 endswitch;
			}
			return $data;
		}

		public function save_affiliate_payment_settings($uid=0, $post_data=array()){
			/*
			 * @param int, array
			 * @return none
			 */
			$keys = $this->get_affiliate_payment_settings($uid, 0, TRUE);
			if ($keys){
				foreach ($keys as $meta_key=>$meta_value){
					if (isset($post_data[$meta_key])){
						update_user_meta($uid, $meta_key, $post_data[$meta_key]);
					}
				}
			}
		}

		public function save_stripe_v2_meta_user_data($uid=0, $post_data=array()){
			/*
			 * @param int, array
			 * @return none
			 */
			 $data = $this->get_affiliate_stripe_v2_payment_settings($uid);
			 if ($data && $uid){
			 	 foreach ($data as $meta_key=>$meta_value){
			 	 	if (isset($post_data['stripe_v2_meta_data'][$meta_key])){
			 	 		$data[$meta_key] = $post_data['stripe_v2_meta_data'][$meta_key];
			 	 	}
			 	 }
				 update_user_meta($uid, 'stripe_v2_meta_data', $data);
			 }
		}

		public function get_email_by_affiliate_id($affiliate_id=0){
			/*
			 * @param int
			 * @return string
			 */
			 if ($affiliate_id){
				 global $wpdb;
				 $table_a = $wpdb->base_prefix . 'users';
				 $table_b = $wpdb->prefix . 'uap_affiliates';
				 $affiliate_id = esc_sql($affiliate_id);
				 $data = $wpdb->get_row("SELECT a.user_email FROM $table_a as a INNER JOIN $table_b as b ON a.ID=b.uid WHERE b.id=$affiliate_id");
				 if (!empty($data->user_email)){
					return $data->user_email;
				 }
			 }
			 return '';
		}


		public function check_envato_customer($code=''){
			/*
			 * @param stirng
			 * @return boolean
			 */
			if (!empty($code)){
				if (!class_exists('Envato_marketplace')){
					require_once UAP_PATH . 'classes/Envato_marketplace.class.php';
				}
				$api_key = 'z4dqvsth70g7qsr4f385fxjdt6wz9dfg';
				$user_name = 'azzaroco';
				$item_id = '16527729';
				$envato_object = new Envato_marketplaces($api_key);
				$buyer_verify = $envato_object->verify_purchase($user_name, $code);

				if ( isset($buyer_verify) && isset($buyer_verify->buyer)  && $buyer_verify->item_id==$item_id ){
					return TRUE;
				}
			}
			return FALSE;
		}

		public function envato_licensing($code=''){
			/*
			 * @param string
			 * @return boolean
			 */
			$return = FALSE;
			if ($this->check_envato_customer($code)){
				update_option('uap_license_set', 1);
				$return = TRUE;
			} else {
				update_option('uap_license_set', 0);
				$return = FALSE;
			}
			update_option('uap_envato_code', $code);
			return $return;
		}

		public function envato_check_license(){
			/*
			 * @param none
			 * @return bool
			 */
			$check = get_option('uap_license_set');
			if ($check!==FALSE){
				if ($check==1)
					return TRUE;
				return FALSE;
			}
			return TRUE;
		}

		public function payments_send_affiliate_notification_by_status($id=0, $status=0){
			/*
			 * @param int, int
			 * @return none
			 */
			 global $wpdb;
			 $table_a = $wpdb->prefix . 'uap_payments';
			 $table_b = $wpdb->prefix . 'uap_affiliates';
			 $q = $wpdb->prepare("SELECT a.*, b.uid as uid
											FROM $table_a as a
											INNER JOIN $table_b as b
											ON a.affiliate_id=b.id
											WHERE a.id=%d
			 ", $id);
			 $data = $wpdb->get_row($q);
			 if ($data && isset($data->amount) && isset($data->currency) && isset($data->uid)){
				$payment_data = array('{amount_to_pay}' => $data->amount, '{amount_currency}' => $data->currency);
				switch ($status){
					case 0:
						/// fail
						uap_send_user_notifications($data->uid, 'affiliate_payment_fail', 0, $payment_data);
						break;
					case 1:
						/// pending
		 				uap_send_user_notifications($data->uid, 'affiliate_payment_pending', 0, $payment_data );
						break;
					case 2:
						/// complete
						uap_send_user_notifications($data->uid, 'affiliate_payment_complete', 0, $payment_data );
						break;
				}
			 }
		}

		public function get_payment_types_available(){
			/*
			 * @param none
			 * @return array
			 */
			 $payments = array(
								'bt' => __('Bank Transfer', 'uap'),
								'paypal' => __('PayPal', 'uap'),
								'stripe' => __('Stripe', 'uap'),
								'stripe_v2' => __('Stripe Managed Accounts', 'uap'),
			 );
			 if (!$this->is_magic_feat_enable('paypal') || (defined('UAP_LICENSE_SET') && !UAP_LICENSE_SET)){
			 	unset($payments['paypal']);
			 }
			 if (!$this->is_magic_feat_enable('stripe') || (defined('UAP_LICENSE_SET') && !UAP_LICENSE_SET)){
			 	unset($payments['stripe']);
			 }
			 if (!$this->is_magic_feat_enable('stripe_v2') || (defined('UAP_LICENSE_SET') && !UAP_LICENSE_SET)){
			 	unset($payments['stripe_v2']);
			 }
			 $disable_bt = get_option('uap_disable_bt_payment_system');
			 if (!empty($disable_bt)){
			 	unset($payments['bt']);
			 }
			 return $payments;
		}

		public function get_all_post_types(){
			/*
			 * use this in front-end, returns all the custom post type available in db
			 * @param none
			 * @return array
			 */
			global $wpdb;
			$arr = array();
			$data = $wpdb->get_results('SELECT DISTINCT post_type FROM ' . $wpdb->prefix . 'posts WHERE post_status="publish";');
			if ($data && count($data)){
				foreach ($data as $obj){
					$arr[] = $obj->post_type;
				}
			}
			return $arr;
		}

		public function getPostIdByUrl($url='')
		{
				$postId = url_to_postid($url);
				if ($postId){
						return $postId;
				}

				$cpt_arr = $this->get_all_post_types();
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
						$cpt_id = $this->get_post_id_by_cpt_name($the_cpt, $post_name);
						if ($cpt_id){
							$postId = $cpt_id;
						}
				} else {
						$homepage = get_option('page_on_front');
						if ($url==get_permalink($homepage)){
							$postId = $homepage;
						}
				}
				return $postId;
		}

		public function getLandingPages()
		{
				global $wpdb;
				$query = "
							SELECT DISTINCT(a.ID), a.post_title, b.meta_value, b.meta_id as post_meta_id, d.user_login, c.uid
									FROM {$wpdb->posts} a
									INNER JOIN {$wpdb->postmeta} b
									ON a.ID=b.post_id
									INNER JOIN {$wpdb->prefix}uap_affiliates c
									ON c.id=b.meta_value
									INNER JOIN {$wpdb->users} d
									ON c.uid=d.ID
									WHERE
									b.meta_key='uap_landing_page_affiliate_id'
				";
				$data = $wpdb->get_results($query);
				return $data;
		}

		public function removeAffiliateLandingPage($metaId=0)
		{
				global $wpdb;
				$metaId = esc_sql($metaId);
				$query = "UPDATE {$wpdb->postmeta} SET meta_value='' WHERE meta_id=$metaId ;";
				return $wpdb->query($query);
		}


		public function get_post_id_by_cpt_name($custom_post_type='', $post_name=''){
			/*
			 * @param string, string
			 * @return int (id of post)
			 */
			 if ($custom_post_type && $post_name){
				global $wpdb;
				$table = $wpdb->prefix . 'posts';
				$q = $wpdb->prepare("SELECT ID FROM $table WHERE post_type=%s AND post_name=%s ", $custom_post_type, $post_name);
				$data = $wpdb->get_row($q);
				if (!empty($data->ID)){
					return $data->ID;
				}
			 }
			 return FALSE;
		}

		public function get_landing_commissions(){
			/*
			 * @param none
			 * @return array
			 */
			 global $wpdb;
			 $array = array();
			 $table = $wpdb->prefix . 'uap_landing_commissions';
			 $data = $wpdb->get_results("SELECT * FROM $table");
			 if ($data){
			 	foreach ($data as $object){
			 		$array[] = (array)$object;
			 	}
			 }
			 return $array;
		}

		public function save_landing_commission($post_data=array()){
			/*
			 * @param none
			 * @return boolean
			 */
			 global $wpdb;
			 $table = $wpdb->prefix . 'uap_landing_commissions';
			 $post_data['slug'] = uap_make_string_simple($post_data['slug']); /// slug must not contain strange stuff
			 $settings = array(
			 					'color' => @$post_data['color'],
			 					'amount_value' => @$post_data['amount_value'],
			 					'default_referral_status' => @$post_data['default_referral_status'],
			 					'source' => @$post_data['source'],
			 					'description' => @$post_data['description'],
			 					'cookie_expire' => @$post_data['cookie_expire'],
			 );
			 $settings['description'] = str_replace("'", '', $settings['description']);
			 $settings['description'] = str_replace('"', '', $settings['description']);
			 $settings['description'] = stripslashes_deep($settings['description']);

			 if (!isset($settings['amount_value']) && $settings['amount_value']==''){
			 	return 0;
			 }
			 $settings = serialize($settings);
			 if (empty($post_data['id'])){
			 	/// create
				$q = $wpdb->prepare("SELECT * FROM $table WHERE slug=%s ", $post_data['slug']);
			 	$exists = $wpdb->get_row($q);
				if ($exists && !empty($exists->id)){
					return 0;
				}
				$q = $wpdb->prepare("INSERT INTO $table VALUES(null, %s, %s, NOW(), %s);", $post_data['slug'], $settings, $post_data['status']);
				$wpdb->query($q);
				return 1;
			 } else {
			 	/// update
				$q = $wpdb->prepare("SELECT * FROM $table WHERE slug=%s AND id<>%d;", $post_data['slug'], $post_data['id']);
			 	$exists = $wpdb->get_row($q);
				if ($exists && !empty($exists->id)){
					return 0;
				}
				$q = $wpdb->prepare("UPDATE $table SET slug=%s, settings=%s, status=%s WHERE id=%d ",
						$post_data['slug'], $settings, $post_data['status'], $post_data['id']
				);
				$wpdb->query($q);
			 	return 1;
			 }
		}

		public function get_landing_commission($slug=''){
			/*
			 * @param string
			 * @return array
			 */
			 global $wpdb;
			 $table = $wpdb->prefix . 'uap_landing_commissions';
			 if ($slug){
				 $q = $wpdb->prepare("SELECT * FROM $table WHERE slug=%s ", $slug);
		 		 $data = $wpdb->get_row($q);
				 if ($data){
				 	$settings = (isset($data->settings)) ? unserialize($data->settings) : array();
				 	$array = array(
									'id' => @$data->id,
									'slug' => @$data->slug,
									'status' => @$data->status,
					);

					return array_merge($array, $settings);
				 }
			 }

			  $last = $wpdb->get_row("SELECT id FROM $table ORDER BY id DESC LIMIT 1");
			  if (empty($last) || empty($last->id)){
			  	$slug_identificator = 1;
			  } else {
			  	$slug_identificator = $last->id;
			  }
			  return array(
			 				'id' => 0,
			 				'slug' => 'custom_' . $slug_identificator,
			 				'color' => '0a9fd8',
			 				'amount_value' => 0,
			 				'cookie_expire' => 24,
			 				'source' => 'from landing commissions',
			 				'description' => '',
			 				'default_referral_status' => 1, ///pending
			 				'status' => 1,
			 );
		}

		public function delete_landing_commission($slug=''){
			/*
			 * @param string
			 * @return none
			 */
			 global $wpdb;
			 $table = $wpdb->prefix . 'uap_landing_commissions';
			 $q = $wpdb->prepare("DELETE FROM $table WHERE slug=%s ", $slug);
			 $wpdb->query($q);
		}

		public function get_all_landing_commision_source_type(){
			/*
			 * @param none
			 * @return array
			 */
			 global $wpdb;
			 $array = array();
			 $table = $wpdb->prefix . 'uap_landing_commissions';
			 $data = $wpdb->get_results("SELECT settings, slug FROM $table;");
			 if ($data){
			 	foreach ($data as $object){
			 		$temp_data = unserialize($object->settings);
					if (!empty($temp_data['source']) && !in_array($temp_data['source'], $array)){
						$array[$object->slug]['label'] = $temp_data['source'];
					}
			 	}
			 }
			 return $array;
		}

		public function save_coupon_affiliate_pair($post_data=array()){
			/*
			 * @param array
			 * @return int
			 */
			 global $wpdb;
			 $table = $wpdb->prefix . 'uap_coupons_code_affiliates';
			 $settings = array(
			 					'amount_type' => @$post_data['amount_type'],
								'amount_value' => @$post_data['amount_value'],
			 );
			 if (empty($settings['amount_value']) || empty($post_data['code'])){
			 	return 0;
			 }
			 $settings = serialize($settings);
			 if (empty($post_data['id'])){
			 	/// create
				$q = $wpdb->prepare("SELECT * FROM $table WHERE code=%s ", $post_data['code']);
			 	$exists = $wpdb->get_row($q);
				if ($exists && !empty($exists->id)){
					return 0;
				}
				$q = $wpdb->prepare("INSERT INTO $table VALUES(null, %s, %s, %s, %s, %s);",
						$post_data['code'], $post_data['affiliate_id'], $post_data['type'], $settings, $post_data['status']
				);
				$wpdb->query($q);
				return 1;
			 } else {
			 	/// update
				$q = $wpdb->prepare("SELECT * FROM $table WHERE code=%s AND affiliate_id<>%d ", $post_data['code'], $post_data['affiliate_id']);
			 	$exists = $wpdb->get_row($q);
				if ($exists && !empty($exists->id)){
					return 0;
				}
				$q = $wpdb->prepare("UPDATE $table SET code=%s, settings=%s, status=%s, type=%s WHERE id=%d ",
						$post_data['code'], $settings, $post_data['status'], $post_data['type'], $post_data['id']
				);
				$wpdb->query($q);
			 	return 1;
			 }
		}

		public function delete_coupon_affiliate_pair($id=0){
			/*
			 * @param int
			 * @return none
			 */
			 if ($id){
			 	global $wpdb;
			 	$table = $wpdb->prefix . 'uap_coupons_code_affiliates';
				$q = $wpdb->prepare("DELETE FROM $table WHERE id=%d ", $id);
				$wpdb->query($q);
			 }
		}

		public function get_affiliate_for_coupon_code($code=''){
			/*
			 * @param string
			 * @return int
			 */
			 if ($code){
			 	global $wpdb;
			 	$table = $wpdb->prefix . 'uap_coupons_code_affiliates';
				$q = $wpdb->prepare("SELECT affiliate_id FROM $table WHERE code=%s ", $code);
				$data = $wpdb->get_row($q);
				if ($data && !empty($data->affiliate_id)){
					return $data->affiliate_id;
				}
			 }
			 return 0;
		}

		public function get_coupon_data($code=''){
			/*
			 * @param string
			 * @return array
			 */
			 if ($code){
			 	global $wpdb;
			 	$table = $wpdb->prefix . 'uap_coupons_code_affiliates';
				$q = $wpdb->prepare("SELECT * FROM $table WHERE code=%s ", $code);
				$data = $wpdb->get_row($q);
				if ($data){
				 	$settings = (isset($data->settings)) ? unserialize($data->settings) : array();
				 	$array = array(
									'id' => @$data->id,
									'code' => @$data->code,
									'status' => $data->status,
									'type' => @$data->type,
									'affiliate_id' => $data->affiliate_id,
					);
					return array_merge($array, $settings);
				}
			 }
			  return array(
			 				'id' => 0,
							'code' => '',
							'status' => 1,
							'type' => '',
							'affiliate_id' => '',
							///
							'amount_type' => 'percentage',
							'amount_value' => 1,
			 );
		}

		public function get_coupons_affiliates_pairs(){
			/*
			 * @param none
			 * @return array
			 */
			 $array = array();
			 global $wpdb;
			 $table = $wpdb->prefix . 'uap_coupons_code_affiliates';
			 $data = $wpdb->get_results("SELECT * FROM $table;");
			 if ($data){
			 	foreach ($data as $object){
			 		$array[] = (array)$object;
			 	}
			 }
			 return $array;
		}

		public function search_coupon_code_by_source_and_term($type='', $char=''){
			/*
			 * @param string
			 * @return
			 */
			 $return = array();
			 global $wpdb;
			 $char = esc_sql($char);
			 switch ($type){
			 	case 'woo':
					$data = $wpdb->get_results("SELECT post_title, ID
													FROM " . $wpdb->prefix . "posts
													WHERE post_title LIKE '%$char%'
													AND post_status='publish'
													AND post_type='shop_coupon' ;
					");
					if (!empty($data) && is_array($data)){
						foreach ($data as $obj){
							$return[$obj->ID] = $obj->post_title;
						}
					}
					break;
				case 'edd':
					$table = $wpdb->prefix . 'postmeta';
					$data = $wpdb->get_results("SELECT meta_value FROM $table
												WHERE meta_key='_edd_discount_code' AND meta_value LIKE '%$char%' ");
					if (!empty($data) && is_array($data)){
						foreach ($data as $obj){
							$return[] = $obj->meta_value;
						}
					}
					break;
				case 'ump':
					$table = $wpdb->prefix . 'ihc_coupons';
					$data = $wpdb->get_results("SELECT code FROM $table
												WHERE code LIKE '%$char%' ");
					if (!empty($data) && is_array($data)){
						foreach ($data as $obj){
							$return[] = $obj->code;
						}
					}
			 }
     		 return $return;
		}

		public function get_coupons_for_affiliate($affiliate_id=0){
			/*
			 * @param int
			 * @return array
			 */
			 $array = array();
			 if ($affiliate_id){
				global $wpdb;
			 	$table = $wpdb->prefix . 'uap_coupons_code_affiliates';
				$q = $wpdb->prepare("SELECT * FROM $table WHERE affiliate_id=%d AND status=1;", $affiliate_id);
				$data = $wpdb->get_results($q);
				if ($data){
					foreach ($data as $object){
						$array[] = (array)$object;
					}
				}
			 }
			 return $array;
		}

		public function get_custom_slug_for_uid($uid=0){
			/*
			 * @param int
			 * @return string
			 */
			 if ($uid){
			 	 $data = get_user_meta($uid, 'uap_affiliate_custom_slug', TRUE);
				 if ($data){
				 	return $data;
				 }
			 }
			 return '';
		}

		public function getUserMetaValue($uid=0, $metaKey='')
		{
				global $wpdb;
				if (!$uid){
						return false;
				}
				$query = $wpdb->prepare("SELECT meta_value FROM {$wpdb->usermeta} WHERE user_id=%d AND meta_key=%s ", $uid, $metaKey);
				return $wpdb->get_var($query);
		}

		public function get_affiliate_id_by_custom_slug($slug=''){
			/*
			 * @param string
			 * @return int
			 */
			 if ($slug){
			 	global $wpdb;
				$table = $wpdb->base_prefix . 'usermeta';
				$q = $wpdb->prepare("SELECT user_id FROM $table WHERE meta_key='uap_affiliate_custom_slug' AND meta_value=%s ", $slug);
				$data = $wpdb->get_row($q);
				if ($data && !empty($data->user_id)){
					return $this->get_affiliate_id_by_wpuid($data->user_id);
				}
			 }
			 return 0;
		}

		public function save_custom_slug_for_uid($uid=0, $slug=''){
			/*
			 * @param int, string
			 * @return boolean
			 */
			 if ($uid && $slug){
				 $uid = esc_sql($uid);
				 $slug = esc_sql($slug);
				 /// first test if exists a username with this slug
				 $exists = $this->check_username_into_users_table($slug);
				 if (!$exists){
				 	///check into usermeta
					global $wpdb;
					$table = $wpdb->base_prefix . 'usermeta';
					$data = $wpdb->get_row("SELECT umeta_id FROM $table WHERE meta_key='uap_affiliate_custom_slug' AND meta_value='$slug' AND user_id<>$uid;");
					if (!$data || empty($data->umeta_id)){
						/// slug doesn't exists
						if ($this->uap_is_slug_can_be_saved($slug)){
							update_user_meta($uid, 'uap_affiliate_custom_slug', $slug);
							return TRUE;
						}
					}
				 }
			 }
			 return FALSE;
		}

		public function uap_is_slug_can_be_saved($string=''){
			/*
			 * @param string
			 * @return boolean
			 */
			 if ($string){
			 	if (preg_match('/\s/', $string)){
			 		return FALSE;
			 	}
				if (preg_match("/[^A-Za-z0-9_]/", $string)){
					return FALSE;
				}

				/// extra check
				$length = strlen($string);
				$settings = $this->return_settings_from_wp_option('custom_affiliate_slug');
				if (isset($settings['uap_custom_affiliate_slug_min_ch'])){
					if ($length<$settings['uap_custom_affiliate_slug_min_ch']){
						return FALSE;
					}
				}
				if (isset($settings['uap_custom_affiliate_slug_max_ch'])){
					if ($length>$settings['uap_custom_affiliate_slug_max_ch']){
						return FALSE;
					}
				}
				if (!empty($settings['uap_custom_affiliate_slug_rule'])){
					if ($settings['uap_custom_affiliate_slug_rule']==2){
						//characters, digits and one Uppercase letter
						if (!preg_match('/[a-z]/', $string)){
							return FALSE;
						}
						if (!preg_match('/[0-9]/', $string)){
							return FALSE;
						}
						if (!preg_match('/[A-Z]/', $string)){
							return FALSE;
						}
					} else {
						//characters and digits
						if (!preg_match('/[a-z]/', $string)){
							return FALSE;
						}
						if (!preg_match('/[0-9]/', $string)){
							return FALSE;
						}
					}
				}
				return TRUE;
			 }
			 return FALSE;
		}

		public function check_username_into_users_table($username=''){
			/*
			 * @param string
			 * @return boolean
			 */
			 if ($username){
				global $wpdb;
				$table = $wpdb->base_prefix . 'users';
				$q = $wpdb->prepare("SELECT ID FROM $table WHERE user_login=%s ", $username);
				$data = $wpdb->get_row($q);
				if ($data && !empty($data->ID) ){
				 	return TRUE;
				}
			 }
			 return FALSE;
		}

		public function select_all_same_slugs_with_usernames(){
			/*
			 * @param none
			 * @return array
			 */
			 global $wpdb;
			 $array = array();
			 $table_u = $wpdb->base_prefix . 'users';
			 $table_um = $wpdb->base_prefix . 'usermeta';
			 $data = $wpdb->get_results("SELECT um.user_id as user_id_from_meta, um.meta_value as user_meta_value, u.ID as uid FROM $table_u u
			 								INNER JOIN $table_um um ON um.meta_value=u.user_login
			 								WHERE um.meta_key='uap_affiliate_custom_slug';");
			 if ($data){
			 	foreach ($data as $object){
			 		if ($object->user_id_from_meta != $object->uid){
			 			$array[] = array('user' => $object->uid, 'slug' => $object->user_id_from_meta);
			 		}
			 	}
			 }
			 return $array;
		}

		public function get_all_affiliates_slug($limit=0, $offset=0, $count=FALSE){
			/*
			 * @param none
			 * @return array
			 */
			 global $wpdb;
			 $table = $wpdb->base_prefix . 'usermeta';
			 $array = array();
			 if ($count){
			 	$data = $wpdb->get_row("SELECT COUNT(*) as v FROM $table WHERE meta_key='uap_affiliate_custom_slug' AND meta_value<>'';");
			 	if ($data && !empty($data->v)){
			 		return $data->v;
			 	}
				return 0;
			 } else {
				$q = $wpdb->prepare("SELECT * FROM $table WHERE meta_key='uap_affiliate_custom_slug' AND meta_value<>'' LIMIT %d OFFSET %d ", $limit, $offset);
			 	$data = $wpdb->get_results($q);
				if ($data){
					foreach ($data as $object){
						if (!empty($object->meta_value)){
							$temp = (array)$object;
							$temp['username'] = $this->get_username_by_wpuid($temp['user_id']);
							$array[] = $temp;
						}
					}
				}
				return $array;
			 }
		}


		public function get_amount_for_referrals($referral_list=array()){
			/*
			 * @param array
			 * @return none
			 */
			if (!empty($referral_list)){
				$referral_list_string = implode(',', $referral_list);
				global $wpdb;
				$table = $wpdb->prefix . 'uap_referrals';
				$data = $wpdb->get_row("SELECT SUM(amount) as the_sum FROM $table WHERE id IN ($referral_list_string);");
				if ($data && isset($data->the_sum)){
					return $data->the_sum;
				}
			}
			return 0;
		}


		/// WALLET SECTION
		public function create_wallet_item($type='', $referral_list=array(), $affiliate_id=0){
			/*
			 * @param string (type of coupon), array (referral list), int
			 * @return boolean
			 */
			$code = 'aff' . $affiliate_id . 'c' . uap_random_string(6);
			while ($this->check_if_coupon_code_exists($code, $type)){
				$code = uap_random_string(7);
			}
			$the_amount = $this->get_amount_for_referrals($referral_list);

			///insert the coupon
			$inserted = $this->insert_the_coupon_for_service($type, $the_amount, $code, $affiliate_id);
			if (!$inserted){
				return FALSE;
			}

			$transaction_id = $type . '_' . $affiliate_id . '_' . $code;
			$currency = get_option('uap_currency');
			$this->change_referrals_status($referral_list, 2);/// set each referral as paid
			$referral_list_string = implode(',', $referral_list);
			$data = array(
						'payment_type' => 'wallet',
						'transaction_id' => $transaction_id,
						'referral_ids' => $referral_list_string,
						'affiliate_id' => $affiliate_id,
						'amount' => $the_amount,
						'currency' => $currency,
						'create_date' => date('Y-m-d H:i:s', time()),
						'update_date' => date('Y-m-d H:i:s', time()),
						'status' => 2,
			);
			$this->add_payment($data);/// save payment
		}

		public function delete_wallet_item($type='', $affiliate_id=0, $code=''){
			/*
			 * @param string
			 * @return boolean
			 */
			 if ($code){
			 	 $transaction_id = $type . '_' . $affiliate_id . '_' . $code;
			 	 $payment_id = $this->get_payment_id_by_transaction_id($transaction_id);
				 if ($payment_id){
				 	 $this->cancel_transaction($payment_id);
					 $this->delete_coupon($type, $code);
				 }
			 }
		}

		public function insert_the_coupon_for_service($type='', $amount=0, $code='', $affiliate_id=0){
			/*
			 * @param string, int, string, int
			 * @return none
			 */
			$expire_time = date('Y-m-d', strtotime('+1 year', time()));
			$username = $this->get_wp_username_by_affiliate_id($affiliate_id);
			switch ($type){
				case 'woo':
					$the_coupon = array(
									'post_title'   => $code,
									'post_content' => '',
									'post_status'  => 'publish',
									'post_author'  => 1,
									'post_type'    => 'shop_coupon',
									'post_excerpt' => 'Ultimate Affiliate Pro: Coupon Made For ' . $username,
					);
					$insert_coupon_id = wp_insert_post( $the_coupon );
					if ($insert_coupon_id){
						update_post_meta($insert_coupon_id, 'discount_type', 'fixed_cart');
						update_post_meta($insert_coupon_id, 'coupon_amount', $amount);
						update_post_meta($insert_coupon_id, 'individual_use', 'yes');
						update_post_meta($insert_coupon_id, 'usage_limit', '1');
						update_post_meta($insert_coupon_id, 'expiry_date', $expire_time);
						return $insert_coupon_id;
					}
					break;
				case 'edd':
					if (function_exists('edd_store_discount')){
						$details = array(
											'code' => $code,
											'name' => 'Ultimate Affiliate Pro: Coupon Made For ' . $username,
											'status' => 'active',
											'uses' => 0,
											'max' => 1,
											'amount' => $amount,
											'start' => date("Y-m-d H:i:s"),
											'expiration' => $expire_time,
											'type' => 'flat',
											'is_single_use' => 1,

						);
						return edd_store_discount($details);
					}
					break;
				case 'ump':
					if (function_exists('ihc_create_coupon')){
						$details = array(
											"code" => $code,
											"discount_type" => "price",
											"discount_value" => $amount,
											"period_type" => "date_range",
											"repeat" => 1,
											"target_level" => -1,
											"reccuring" => 0,
											"start_time" => date("Y-m-d H:i:s"),
											"end_time" => $expire_time,
											"box_color" => '#f25a68',
											"description" => 'Ultimate Affiliate Pro: Coupon Made For ' . $username,
						);
						return ihc_create_coupon($details);
					}
					break;

			}
		}

		public function delete_coupon($type='', $code=''){
			/*
			 * @param string, string
			 * @return none
			 */
			global $wpdb;
			$code = esc_sql($code);
			switch ($type){
				case 'woo':
					$table = $wpdb->base_prefix . 'posts';
					$data = $wpdb->get_row("SELECT ID FROM $table WHERE post_title='$code';");
					if ($data && !empty($data->ID)){
						wp_delete_post($data->ID, TRUE);
					}
					break;
				case 'edd':
					if (function_exists('edd_remove_discount')){
						$table = $wpdb->base_prefix . 'postmeta';
						$data = $wpdb->get_row("SELECT post_id FROM $table WHERE meta_key='_edd_discount_code' AND meta_value='$code';");
						if ($data && !empty($data->post_id)){
							edd_remove_discount($data->post_id);
						}
					}
					break;
				case 'ump':
						if (function_exists('ihc_delete_coupon')){
							$table = $wpdb->prefix . 'ihc_coupons';
							$data = $wpdb->get_row("SELECT id FROM $table WHERE code='$code';");
							if ($data && isset($data->id)){
								ihc_delete_coupon($data->id);
							}
						}
					break;

			}
		}

		public function check_if_coupon_code_exists($code='', $type=''){
			/*
			 * @param string, string
			 * @return none
			 */
			 if ($code && $type){
			 	global $wpdb;
				$code = esc_sql($code);
				switch ($type){
					case 'woo':
						$table = $wpdb->base_prefix . 'posts';
						$data = $wpdb->get_row("SELECT ID FROM $table WHERE post_title='$code';");
						if ($data && !empty($data->ID)){
							return TRUE;
						}
						break;
					case 'edd':
						$table = $wpdb->base_prefix . 'postmeta';
						$data = $wpdb->get_row("SELECT meta_id FROM $table WHERE meta_key='_edd_discount_code' AND meta_value='$code';");
						if ($data && !empty($data->meta_id)){
							return TRUE;
						}
						break;
					case 'ump':
						if (function_exists('ihc_get_coupon_by_code')){
							if (ihc_get_coupon_by_code($code)){
								return TRUE;
							}
						}
						break;
				}
			 }
			 return FALSE;
		}

		public function get_payment_id_by_transaction_id($transaction_id=''){
			/*
			 * @param string
			 * @return int
			 */
			 if ($transaction_id){
			 	global $wpdb;
				$table = $wpdb->prefix . 'uap_payments';
				$q = $wpdb->prepare("SELECT id FROM $table WHERE transaction_id=%s ", $transaction_id);
				$data = $wpdb->get_row($q);
				if ($data && !empty($data->id)){
					return $data->id;
				}
			 }
			 return 0;
		}

		public function get_all_wallet_items_for_affiliate($affiliate_id=0){
			/*
			 * @param int
			 * @return array
			 */
			 $array = array();
			 if ($affiliate_id){
			 	global $wpdb;
				$table = $wpdb->prefix . 'uap_payments';
				$q = $wpdb->prepare("SELECT transaction_id, amount FROM $table WHERE affiliate_id=%d AND payment_type='wallet';", $affiliate_id);
				$data = $wpdb->get_results($q);
				if ($data){
					foreach ($data as $object){
						$transaction_id = $object->transaction_id;
						$temp = explode('_', $transaction_id);
						if (isset($temp[0]) && isset($temp[2]) && $this->is_coupon_still_active($temp[0], $temp[2])){
							$inside_temp['type'] = $temp[0];
							$inside_temp['code'] = $temp[2];
							$inside_temp['amount'] = $object->amount;
							$array[] = $inside_temp;
						}
					}
				}
			 }
			 return $array;
		}

		public function is_coupon_still_active($type='', $code=''){
			/*
			 * @param string, string
			 * @return boolean
			 */
			 if ($type && $code){
			 	global $wpdb;
				$code = esc_sql($code);
				switch ($type){
					case 'woo':
						if (class_exists('WC_Coupon')){
							$object = new WC_Coupon($code);
							if($object->is_valid()){
								return TRUE;
							}
							/* //used in < uap 3.9
							if ($object->usage_count==0){
								return TRUE;
							}
							*/
						}
						break;
					case 'edd':
						$table = $wpdb->base_prefix . 'postmeta';
						$data = $wpdb->get_row("SELECT post_id FROM $table WHERE meta_key='_edd_discount_code' AND meta_value='$code';");
						if ($data && !empty($data->post_id)){
							$data = get_post_meta($data->post_id, '_edd_discount_uses', TRUE);
							if (!$data){
								return TRUE;
							}
						}
						break;
					case 'ump':
						$table = $wpdb->prefix . 'ihc_coupons';
						$data = $wpdb->get_row("SELECT submited_coupons_count FROM $table WHERE code='$code';");
						if ($data && isset($data->submited_coupons_count) && $data->submited_coupons_count<1){
							return TRUE;
						}
						break;

				}
			 }
			 return FALSE;
		}

		public function get_affiliates_for_checkout_select($who='', $type_of_name=''){
			/*
			 * @param string, string
			 * @return array
			 */
			 $array = array();
			 if ($type_of_name){
			 	 global $wpdb;
				 $table_users = $wpdb->base_prefix . 'users';
				 $table_affiliates = $wpdb->prefix . 'uap_affiliates';
				 $q = "SELECT a.id as id, u.$type_of_name as name FROM
				 			$table_users u
				 			INNER JOIN $table_affiliates a ON u.ID=a.uid
				 			WHERE 1=1
				 ";
				 if ($who!='' && strpos($who, '-1')===FALSE){ /// IF NOT ALL AFFILIATES
				 	$q .= " AND a.id IN($who)";
				 }
				 $data = $wpdb->get_results($q);
				 if ($data){
				 	foreach ($data as $object){
				 		$array[$object->id] = $object->name;
				 	}
				 }
			 }
			 return $array;
		}

		public function set_default_payment_on_register_affiliate($uid=0){
			/*
			 * @param int
			 * @return none
			 */
			if ($uid){
				$value = '';
				$available_systems = $this->get_payment_types_available();
				if ($available_systems){
					$temp = $this->return_settings_from_wp_option('general-public_workflow');
					$default_value = $temp['uap_default_payment_system'];
					if ($default_value && !empty($available_systems[$default_value])){
						$value = $default_value;
					}
					$this->save_affiliate_payment_settings($uid, array('uap_affiliate_payment_type' => $value));
				}
			}
		}

		public function uap_get_meta_user_options($type='', $uid=0){
			/*
			 * @param string, int
			 * @return array
			 */
			 $array = array();
			 if ($uid){
			 	switch ($type){
					case 'user_notifications':
						$array = array(
										'uap_notifications_on_every_referral_types' => '',
						);
						break;
			 	}
				if (!empty($array)){
					foreach ($array as $k => $v){
						$temp = get_user_meta($uid, $k, TRUE);
						if ($temp!==''){
							$array[$k] = $temp;
						}
					}
				}
			 }
			 return $array;
		}

		public function save_meta_user_options($type='', $uid=0, $new_vals=array()){
			/*
			 * @param string, int, array
			 * @return none
			 */
			 if ($uid){
			 	 $keys = $this->uap_get_meta_user_options($type, $uid);
				 if ($keys){
				 	foreach ($keys as $k=>$v){
				 		if (isset($new_vals[$k])){
				 			update_user_meta($uid, $k, $new_vals[$k]);
				 		}
				 	}
				 }
			 }
		}

		public function save_affiliate_report_settings($affiliate_id=0, $post_data=array()){
			/*
			 * @param int, array
			 * @return boolean
			 */
			 if ($affiliate_id && $post_data){
				 global $wpdb;
				 $table = $wpdb->prefix . 'uap_reports';
				 $q = $wpdb->prepare("SELECT email FROM $table WHERE affiliate_id=%d ", $affiliate_id);
				 $exists = $wpdb->get_row($q);
				 if ($exists && !empty($exists->email)){
				 	 /// UPDATE
					 $q = $wpdb->prepare("UPDATE $table SET email=%s, period=%s WHERE affiliate_id=%d ", $post_data['email'], $post_data['period'], $affiliate_id);
				 	 $wpdb->query($q);
				 } else {
				 	 /// SELECT
					 $q = $wpdb->prepare("INSERT INTO $table VALUES(%d, %s, %s, UNIX_TIMESTAMP());", $affiliate_id, $post_data['email'], $post_data['period']);
				 	 $wpdb->query($q);
				 }
				 return TRUE;
			 }
			 return FALSE;
		}

		public function update_affiliate_reports_last_sent($affiliate_id=0){
			/*
			 * @param int
			 * @return boolean
			 */
			 if ($affiliate_id){
				 global $wpdb;
				 $table = $wpdb->prefix . 'uap_reports';
				 $q = $wpdb->prepare("SELECT email FROM $table WHERE affiliate_id=%d ", $affiliate_id);
				 $exists = $wpdb->get_row($q);
				 if ($exists && !empty($exists->email)){
				 	 /// UPDATE
					 $q = $wpdb->prepare("UPDATE $table SET last_sent=UNIX_TIMESTAMP() WHERE affiliate_id=%d ", $affiliate_id);
				 	 $wpdb->query($q);
					 return TRUE;
				 }
			 }
			 return FALSE;
		}

		public function delete_affiliate_report_settings($affiliate_id=0){
			/*
			 * @param int
			 * @return boolean
			 */
			 if ($affiliate_id){
				 global $wpdb;
				 $table = $wpdb->prefix . 'uap_reports';
				 $q = $wpdb->prepare("DELETE FROM $table WHERE affiliate_id=%d ", $affiliate_id);
				 $wpdb->query($q);
			 }
		}

		public function affiliate_get_report_settings($affiliate_id=0){
			/*
			 * @param int
			 * @return array
			 */
			 if ($affiliate_id){
				 global $wpdb;
				 $table = $wpdb->prefix . 'uap_reports';
				 $q = $wpdb->prepare("SELECT * FROM $table WHERE affiliate_id=%d ", $affiliate_id);
				 $data = $wpdb->get_row($q);
				 if ($data){
				 	$array = (array)$data;
				 	return $array;
				 }
			 }
			 return array(
			 				'period' => '',
			 );
		}

		public function update_affiliate_reports_email_addr($affiliate_id=0, $new_value=''){
			/*
			 * @param int, string
			 * @return boolean
			 */
			 if ($affiliate_id){
				 global $wpdb;
				 $affiliate_id = esc_sql($affiliate_id);
				 $table = $wpdb->prefix . 'uap_reports';
				 $exists = $wpdb->get_row("SELECT email FROM $table WHERE affiliate_id=$affiliate_id;");
				 if ($exists && !empty($exists->email)){
					 	/// we must do update
	 				  $new_value = esc_sql($new_value);
					 	$wpdb->query("UPDATE $table SET email='$new_value' WHERE affiliate_id=$affiliate_id; ");
					 	return TRUE;
				 }
			 }
			 return FALSE;
		}

		public function get_affiliates_for_reports(){
			/*
			 * @oaran none
			 * @return array
			 */
			 $array = array();
			 global $wpdb;
			 $table = $wpdb->prefix . 'uap_reports';
			 $q = "SELECT period, affiliate_id, email, last_sent
						FROM $table
						WHERE last_sent+(period*24*3600)<UNIX_TIMESTAMP();";
			 $data = $wpdb->get_results($q);
			 if ($data){
			 	foreach ($data as $object){
			 		$array[] = (array)$object;
			 	}
			 }
			 return $array;
		}

		public function uap_get_all_pages(){
			/*
			 * @param none
			 * @return array
			 */
			$arr = array();
			$args = array(
					'sort_order' => 'ASC',
					'sort_column' => 'post_title',
					'hierarchical' => 1,
					'child_of' => 0,
					'parent' => -1,
					'number' => '',
					'offset' => 0,
					'post_type' => 'page',
					'post_status' => 'publish'
			);
			$pages = get_pages($args);
			if (isset($pages) && count($pages)>0){
				foreach ($pages as $page){
					if ($page->post_title=='') $page->post_title = '(no title)';
					$arr[$page->ID] = $page->post_title;
				}
			}
			return $arr;
		}

		public function increment_dashboard_notification($type=''){
			/*
			 * @param string ( affiliates || referrals )
			 * @return none
			 */
			global $wpdb;
			$table = $wpdb->prefix . 'uap_dashboard_notifications';
			$q = $wpdb->prepare("UPDATE $table SET value=value+1 WHERE type=%s ", $type);
			$wpdb->query($q);
		}

		public function reset_dashboard_notification($type=''){
			/*
			 * @param string ( affiliates || referrals )
			 * @return none
			 */
			global $wpdb;
			$table = $wpdb->prefix . 'uap_dashboard_notifications';
			$q = $wpdb->prepare("UPDATE $table SET value=0 WHERE type=%s ", $type);
			$wpdb->query($q);
		}

		public function get_dashboard_notification_value($type=''){
			/*
			 * @param string ( affiliates || referrals )
			 * @return none
			 */
			global $wpdb;
			$table = $wpdb->prefix . 'uap_dashboard_notifications';
			$q = $wpdb->prepare("SELECT value FROM $table WHERE type=%s ", $type);
			$data = $wpdb->get_row($q);
			return (empty($data->value)) ? 0 : $data->value;
		}

		public function get_next_rank_achieved_percetage($affiliate_id=0){
			/*
			 * @param int
			 * @return int/float
			 */
			 $return_value = 0;
			 if ($affiliate_id){
			 	$ranks = $this->get_ranks();
				$ranks = uap_reorder_ranks($ranks);//reorder
				$affiliate_rank = $this->get_affiliate_rank($affiliate_id);
				$affiliate_data = $this->get_affiliates_from_referrals(array($affiliate_id));
				if ($affiliate_data){
					$affiliate_data = $affiliate_data[$affiliate_id];
					$affiliate_data['referrals_number'] = $affiliate_data['total_referrals'];
					foreach ($ranks as $k => $rank){
						if ($rank->id==$affiliate_rank){
							$key = $k;
						}
					}
					if (isset($key)){
						$key++;
						if (isset($ranks[$key]) && isset($ranks[$key]->achieve)){
							$achieve_ruls = $ranks[$key]->achieve;
							$achieve_ruls = json_decode($achieve_ruls, TRUE);
							if ($achieve_ruls['i']==1){
								$type = $achieve_ruls['type_1'];///type can be referrals_numbers or total_amount
								@$return_value = 100 / $achieve_ruls['value_1'] * $affiliate_data[$type];
							} else {
								$type = $achieve_ruls['type_1'];
								@$value_1 =  100 / $achieve_ruls['value_1'] * $affiliate_data[$type];
								$type = $achieve_ruls['type_2'];
								@$value_2 =  100 / $achieve_ruls['value_2'] * $affiliate_data[$type];
								if ($achieve_ruls['relation_2']=='and'){
									///smaller value
									$return_value = min($value_1, $value_2);
								} else {
									///biggets value
									$return_value = max($value_1, $value_2);
								}
							}
						} else {
							return -1; //last rank has been achieved
						}
					}
				}
			 }
			 return round($return_value, 1);
		}

		public function get_magic_feat_item_list(){
			/*
			 * Use this function carefully, preffered only in admin section.
			 * @param none
			 * @return array
			 */
			return array(
					'sign_up_referrals' => array(
							'label' => __('SignUp Referrals (CPL)', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=sign_up_referrals'),
							'icon' => 'fa-sign-in-ref-uap',
							'description' => __('Available for membership system, awarding commission when referred user signs up', 'uap'),
							'enabled' => $this->is_magic_feat_enable('sign_up_referrals'),
							'extra_class' => '',
					),
					'pay_per_click' => array(
							'label' => __('Pay Per Click  (CPC)', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=pay_per_click'),
							'icon' => 'fa-pay_per_click-uap',
							'extra_class' => '',
							'description' => __('PPC Campaign for affiliate users', 'uap'),
							'enabled' => $this->is_magic_feat_enable('pay_per_click'),
					),
					'cpm_commission' => array(
							'label' => __('CPM Commission', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=cpm_commission'),
							'icon' => 'fa-cpm_commission-uap',
							'extra_class' => '',
							'description' => __('Cost Per Mile (CPM) Campaign for affiliate users', 'uap'),
							'enabled' => $this->is_magic_feat_enable('cpm_commission'),
					),
					'lifetime_commissions' => array(
							'label' => __('LifeTime Commissions', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=lifetime_commissions'),
							'icon' => 'fa-hourglass-start-uap',
							'description' => __('Allow for your affiliate to receive commission for all lifetime referrals', 'uap'),
							'enabled' => $this->is_magic_feat_enable('lifetime_commissions'),
							'extra_class' => '',
					),
					'reccuring_referrals' => array(
							'label' => __('Recurring Referrals', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=reccuring_referrals'),
							'icon' => 'fa-history-uap',
							'description' => __('Award commissions for recurring subscriptions into membership systems', 'uap'),
							'enabled' => $this->is_magic_feat_enable('reccuring_referrals'),
							'extra_class' => '',
					),
					'social_share' => array(
							'label' => __('Social Share', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=social_share'),
							'icon' => 'fa-share-alt-uap',
							'description' => __('Provides social share buttons for affiliate links using “Social Share & Locker”', 'uap'),
							'enabled' => $this->is_magic_feat_enable('social_share'),
							'extra_class' => '',
					),

					'paypal' => array(
							'label' => __('PayPal', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=paypal'),
							'icon' => 'fa-paypal-uap',
							'description' => __('Pay your affiliates via PayPal', 'uap'),
							'enabled' => $this->is_magic_feat_enable('paypal'),
							'extra_class' => '',
					),

					'stripe' => array(
							'label' => __('Stripe', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=stripe'),
							'icon' => 'fa-stripe-uap',
							'description' => __('Pay your affiliates via Stripe', 'uap'),
							'enabled' => $this->is_magic_feat_enable('stripe'),
							'extra_class' => '',
					),
					'stripe_v2' => array(
							'label' => __('Stripe v2', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=stripe_v2'),
							'icon' => 'fa-stripe-uap',
							'description' => __('Managed accounts Stripe service', 'uap'),
							'enabled' => $this->is_magic_feat_enable('stripe_v2'),
							'extra_class' => '',
					),
					'allow_own_referrence' => array(
							'label' => __('Allow Own Reference', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=allow_one_referrence'),
							'icon' => 'fa-university-uap',
							'description' => __('Allow for your affiliate to earn commissions from their own referrals', 'uap'),
							'enabled' => $this->is_magic_feat_enable('allow_own_referrence'),
							'extra_class' => '',
					),
					'mlm' => array(
							'label' => __('MLM', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=mlm'),
							'icon' => 'fa-referrals-uap',
							'description' => __('Set a multi-level marketing system for your affiliates', 'uap'),
							'enabled' => $this->is_magic_feat_enable('mlm'),
							'extra_class' => '',
					),
					'rewrite_referral' => array(
							'label' => __('ReAssign Referral', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=rewrite_referrals'),
							'icon' => 'fa-life-ring-uap',
							'description' => __('Decides if a new customer is re-assigned to the first or last linked affiliate', 'uap'),
							'enabled' => $this->is_magic_feat_enable('rewrite_referrals'),
							'extra_class' => '',
					),
					'bonus_on_rank' => array(
							'label' => __('Bonus On Ranks', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=bonus_on_rank'),
							'icon' => 'fa-money-uap',
							'description' => __('Set bonuses when one of your affiliates reach a specific rank', 'uap'),
							'enabled' => $this->is_magic_feat_enable('bonus_on_rank'),
							'extra_class' => '',
					),
					'opt_in' => array(
							'label' => __('Opt-In', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=opt_in'),
							'icon' => 'fa-opt_in-uap',
							'description' => __('Send affiliate email addresses to your Opt-In destination', 'uap'),
							'enabled' => $this->is_magic_feat_enable('opt_in'),
							'extra_class' => '',
					),
					'coupons' => array(
							'label' => __('Coupons', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=coupons'),
							'icon' => 'fa-coupons-uap',
							'description' => __('Correlate an affiliate with a WooCommerce, Easy Digital Download or Ultimate Membership Pro coupon code', 'uap'),
							'enabled' => $this->is_magic_feat_enable('coupons'),
							'extra_class' => '',
					),
					'friendly_links' => array(
							'label' => __('Friendly Affiliate Links', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=friendly_links'),
							'icon' => 'fa-friendly_links-uap',
							'description' => __('Affiliate URLs receive improved looks and a better structure', 'uap'),
							'enabled' => $this->is_magic_feat_enable('friendly_links'),
							'extra_class' => '',
					),
					'custom_affiliate_slug' => array(
							'label' => __('Custom Affiliate Slug', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=custom_affiliate_slug'),
							'icon' => 'fa-custom_affiliate_slug-uap',
							'description' => __('Provides personal slugs besides the default username or ID', 'uap'),
							'enabled' => $this->is_magic_feat_enable('custom_affiliate_slug'),
							'extra_class' => '',
					),
					'wallet' => array(
							'label' => __('Wallet', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=wallet'),
							'icon' => 'fa-wallet-uap',
							'description' => __('Affiliates will have the option to spend their earnings directly in the website', 'uap'),
							'enabled' => $this->is_magic_feat_enable('wallet'),
							'extra_class' => '',
					),
					'checkout_select_referral' => array(
							'label' => __('Fair Checkout Reward', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=checkout_select_referral'),
							'icon' => 'fa-checkout_select_referral-uap',
							'description' => __('Customers decide which affiliate will be rewarded during the checkout process', 'uap'),
							'enabled' => $this->is_magic_feat_enable('checkout_select_referral'),
							'extra_class' => '',
					),
					'woo_account_page' => array(
							'label' => __('WooCommerce Account Page Integration', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=woo_account_page'),
							'icon' => 'fa-woo-uap',
							'description' => '',
							'enabled' => $this->is_magic_feat_enable('woo_account_page'),
							'extra_class' => '',
					),
					'bp_account_page' => array(
							'label' => __('BuddyPress Account Page Integration', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=bp_account_page'),
							'icon' => 'fa-bp-uap',
							'description' => '',
							'enabled' => $this->is_magic_feat_enable('bp_account_page'),
							'extra_class' => '',
					),
					'referral_notifications' => array(
							'label' => __('Referral Notifications', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=referral_notifications'),
							'icon' => 'fa-referral_notifications-uap',
							'description' => __('Notify the affiliates via email when they get any new referrals', 'uap'),
							'enabled' => $this->is_magic_feat_enable('referral_notifications'),
							'extra_class' => '',
					),
					'admin_referral_notifications' => array(
							'label' => __('Admin Referral Notifications', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=admin_referral_notifications'),
							'icon' => 'fa-referral_notifications-uap',
							'description' => '',
							'enabled' => $this->is_magic_feat_enable('admin_referral_notifications'),
							'extra_class' => '',
					),
					'periodically_reports' => array(
							'label' => __('Periodically Reports', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=periodically_reports'),
							'icon' => 'fa-periodically_reports-uap',
							'description' => __('Affiliates will receive periodical email reports about their stats', 'uap'),
							'enabled' => $this->is_magic_feat_enable('periodically_reports'),
							'extra_class' => '',
					),
					'qr_code' => array(
							'label' => __('QR Codes', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=qr_code'),
							'icon' => 'fa-qr-uap',
							'description' => __('The Affiliate Links can be provided as QR Codes', 'uap'),
							'enabled' => $this->is_magic_feat_enable('qr_code'),
							'extra_class' => '',
					),
					'email_verification' => array(
							'label' => __('E-mail Verification', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=email_verification'),
							'icon' => 'fa-email_verification-uap',
							'description' => __('Requires the email address of new affiliates to be verified before they can log in', 'uap'),
							'enabled' => $this->is_magic_feat_enable('email_verification'),
							'extra_class' => '',
					),
					'custom_currencies' => array(
							'label' => __('Custom Currencies', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=custom_currencies'),
							'icon' => 'fa-custom_currencies-uap',
							'description' => '',
							'enabled' => 1,
							'extra_class' => '',
					),
					'source_details' => array(
							'label' => __('Source Details', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=source_details'),
							'icon' => 'fa-source_details-uap',
							'description' => '',
							'enabled' => $this->is_magic_feat_enable('source_details'),
							'extra_class' => '',
					),
					'wp_social_login' => array(
							'label' => __('Wp Social Login Integration', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=wp_social_login'),
							'icon' => 'fa-wp_social_login-uap',
							'description' => __('Integrated for a lite register / login with social accounts', 'uap'),
							'enabled' => $this->is_magic_feat_enable('wp_social_login'),
							'extra_class' => '',
					),
					'pushover' => array(
							'label' => __('Pushover Notifications', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=pushover'),
							'icon' => 'fa-pushover-uap',
							'extra_class' => '',
							'description' => __('Users receive notifications on mobile via Pushover', 'uap'),
							'enabled' => $this->is_magic_feat_enable('pushover'),
					),
					'max_amount' => array(
							'label' => __('Maximum Amount', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=max_amount'),
							'icon' => 'fa-max_amount-uap',
							'extra_class' => '',
							'description' => __('Set a maximum amount that can not be passed for a referral.', 'uap'),
							'enabled' => $this->is_magic_feat_enable('max_amount'),
					),
					'simple_links' => array(
							'label' => __('Referrer Links', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=simple_links'),
							'icon' => 'fa-simple_links-uap',
							'extra_class' => '',
							'description' => __('Directly links without any visible affiliate links listed', 'uap'),
							'enabled' => $this->is_magic_feat_enable('simple_links'),
					),
					'account_page_menu' => array(
							'label' => __('Account Custom Tabs', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=account_page_menu'),
							'icon' => 'fa-account_page_menu-uap',
							'extra_class' => '',
							'description' => __('Create and reorder account page menu items', 'uap'),
							'enabled' => $this->is_magic_feat_enable('account_page_menu'),
					),
					'migrate_affiliate_wp' => array(
						'label' => __('Migrate AffiliateWP', 'uap'),
						'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=migrate_affiliate_wp'),
						'icon' => 'fa-migrate_affiliate_wp-uap',
						'extra_class' => '',
						'description' => __('Migrate data from AffiliateWP plugin', 'uap'),
						'enabled' => 1,
					),
					'migrate_affiliates_pro' => array(
							'label' => __('Migrate Affiliates Pro', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=migrate_affiliates_pro'),
							'icon' => 'fa-migrate_affiliates_pro-uap',
							'extra_class' => '',
							'description' => __('Migrate data from Affiliates Pro plugin', 'uap'),
							'enabled' => 1,
					),
					'migrate_wp_affiliates' => array(
							'label' => __('Migrate WP Affiliate', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=migrate_wp_affiliates'),
							'icon' => 'fa-migrate_wp_affiliates-uap',
							'extra_class' => '',
							'description' => __('Migrate data from WP Affiliate plugin', 'uap'),
							'enabled' => 1,
					),
					'ranks_pro' => array(
							'label' => __('Ranks PRO', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=ranks_pro'),
							'icon' => 'fa-ranks_pro-uap',
							'extra_class' => '',
							'description' => __('Dynamic Ranks Achievements workflow', 'uap'),
							'enabled' => $this->is_magic_feat_enable('ranks_pro'),
					),
					'landing_pages' => array(
							'label' => __('Affiliate Landing Pages', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=landing_pages'),
							'icon' => 'fa-landing_pages-uap',
							'extra_class' => '',
							'description' => __('Affiliate Landing Pages instead of affiliate links', 'uap'),
							'enabled' => $this->is_magic_feat_enable('landing_pages'),
					),
					'pushover_referral_notifications' => array(
							'label' => __('Pushover Notifications', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=pushover_referral_notifications'),
							'icon' => 'fa-pushover_referral_notifications-uap',
							'extra_class' => '',
							'description' => __('Users receive notifications on mobile via Pushover', 'uap'),
							'enabled' => $this->is_magic_feat_enable('pushover_referral_notifications'),
					),
					'rest_api' => array(
							'label' => __('REST API', 'uap'),
							'link' => admin_url('admin.php?page=ultimate_affiliates_pro&tab=magic_features&subtab=rest_api'),
							'icon' => 'fa-rest_api-uap',
							'extra_class' => '',
							'description' => __('CRUD actions for main affiliate system data', 'uap'),
							'enabled' => $this->is_magic_feat_enable('rest_api'),
					),
			);
		}

		public function referral_has_source_details($referral_id=0){
			/*
			 * @param int
			 * @return boolean
			 */
			 $value = FALSE;
			 if ($referral_id){
			 	global $wpdb;
				$table = $wpdb->prefix . 'uap_referrals';
				$q = $wpdb->prepare("SELECT reference, source FROM $table WHERE id=%d ", $referral_id);
				$data = $wpdb->get_row($q);
				if (!empty($data->reference) && !empty($data->source)){
				 	$check = uap_get_active_services();
				 	switch ($data->source){
						case 'woo':
							if (!empty($check['woo']) && class_exists('WC_Order')){
								try {
										$exists = new WC_Order($data->reference);
								    if ($exists){
								    		$value = TRUE;
								    }
								 } catch (exception $e){}
							}
							break;
						case 'edd':
							if (!empty($check['edd']) && function_exists('edd_get_payment_meta')){
								$exists = edd_get_payment_meta($data->reference);
								if ($exists){
									$value = TRUE;
								}
							}
							break;
						case 'ump':
							if (!empty($check['ump'])){
								$table = $wpdb->base_prefix . 'users';
								require_once IHC_PATH . 'classes/Orders.class.php';
								$object = new Ump\Orders();
								$exists = $object->get_data($data->reference);
								if ($exists){
									$value = TRUE;
								}
							}
							break;
						case 'User SignUp':
							if ($this->is_magic_feat_enable('sign_up_referrals')){
								$uid = str_replace('user_id_', '', $data->reference);
								$table = $wpdb->base_prefix . 'users';
								$exists = $wpdb->get_row("SELECT * FROM $table WHERE ID=$uid;");
								if (!empty($exists)){
									$value = TRUE;
								}
							}
							break;
				 	}
				}
			 }
			 return $value;
		}

		public function get_source_details_for_reference($referral_id){
			/*
			 * @param int
			 * @return array
			 */
			 $array = array();
			 if ($referral_id){
			 	global $wpdb;
				$table = $wpdb->prefix . 'uap_referrals';
				$referral_id = esc_sql($referral_id);
			 	$data = $wpdb->get_row("SELECT reference, source FROM $table WHERE id=$referral_id;");
				if (!empty($data->reference) && !empty($data->source)){
					$reference = $data->reference;
				 	$check = uap_get_active_services();
					$fields_in_arr = $this->return_settings_from_wp_option('source_details');
				 	switch ($data->source){
						case 'woo':
							if (!empty($check['woo']) && class_exists('WC_Order')){
								$fields = explode(',', $fields_in_arr['uap_source_details_woo_fields_list']);
								$woo = new WC_Order($reference);
								if (in_array('email', $fields)){
										if (method_exists($woo, 'get_billing_email')){
												$array['email'] = $woo->get_billing_email();
										} else {
												$array['email'] = $woo->billing_email;
										}

								}
								if (in_array('first_name', $fields)){
										if (method_exists($woo, 'get_billing_first_name')){
												$array['first_name'] = $woo->get_billing_first_name();
										} else {
												$array['first_name'] = $woo->billing_first_name;
										}
								}
								if (in_array('last_name', $fields)){
										if (method_exists($woo, 'get_billing_last_name')){
												$array['last_name'] = $woo->get_billing_last_name();
										} else {
												$array['last_name'] = $woo->billing_last_name;
										}
								}
								if (in_array('order_date', $fields)){
										if (method_exists($woo, 'get_date_created')){
												$array['order_date'] = $woo->get_date_created();
										} else {
												$array['order_date'] = $woo->order_date;
										}
								}
								if (in_array('order_amount', $fields)){
										if (method_exists($woo, 'get_formatted_order_total')){
												$array['order_amount'] = $woo->get_formatted_order_total();
										}
								}
								if (in_array('phone', $fields)){
										if (method_exists($woo, 'get_billing_phone')){
												$array['phone'] = $woo->get_billing_phone();
										} else {
												$array['phone'] = $woo->billing_phone;
										}
								}
								if (in_array('shipping_address', $fields)){
										$array['shipping_address'] = $woo->get_formatted_shipping_address();
								}
								if (in_array('billing_address', $fields)){
										$array['billing_address'] = $woo->get_formatted_billing_address();
								}

								if (in_array('cart_items', $fields)){
										$array['cart_items'] = '';
										$temp_arr = $woo->get_items();
										if ($temp_arr){
											foreach ($temp_arr as $item){
												$cart_arr[] = $item['name'];
											}
											if (!empty($cart_arr)){
												$array['cart_items'] = implode(', ', $cart_arr);
											}
										}
								}
							}
							break;
						case 'edd':
							if (!empty($check['edd']) && function_exists('edd_get_payment_meta')){
								$fields = explode(',', $fields_in_arr['uap_source_details_edd_fields_list']);
								$payment_meta = edd_get_payment_meta($reference);
								$customer_meta = edd_get_payment_meta_user_info($reference);
								if (in_array('email', $fields)){
									$array['email'] = $customer_meta['email'];
								}
								if (in_array('first_name', $fields)){
									$array['first_name'] = $customer_meta['first_name'];
								}
								if (in_array('last_name', $fields)){
									$array['last_name'] = $customer_meta['last_name'];
								}
								if (in_array('order_date', $fields)){
									$array['order_date'] = uap_convert_date_to_us_format($payment_meta['date']);
								}
								if (in_array('order_amount', $fields)){
									$array['order_amount'] = edd_currency_filter(edd_format_amount(edd_get_payment_amount($reference)));
								}
								if (in_array('billing_address', $fields)){
									$temp_data = isset($payment_meta['address']) ? $payment_meta['address'] : '';
									if (!empty($temp_data['line1'])){
										$temp_arr[] = $temp_data['line1'];
									}
									if (!empty($temp_data['line2'])){
										$temp_arr[] = $temp_data['line2'];
									}
									if (!empty($temp_data['city'])){
										$temp_arr[] = $temp_data['city'];
									}
									if (!empty($temp_data['zip'])){
										$temp_arr[] = $temp_data['zip'];
									}
									if (!empty($temp_data['state'])){
										$temp_arr[] = $temp_data['state'];
									}
									if (!empty($temp_data['country'])){
										$temp_arr[] = $temp_data['country'];
									}
									if (!empty($temp_arr)){
										$temp_data['line1'] = implode(', ', $temp_arr);
									}
								}
								/*
								if (in_array('user_login', $fields) && !empty($customer_meta['id'])){
									$array['user_login'] = $this->get_username_by_wpuid($customer_meta['id']);
								}
								*/

								if (in_array('cart_items', $fields)){
									$products = edd_get_payment_meta_cart_details($reference);
									if ($products){
										$array['cart_items'] = '';
										foreach ($products as $item){
											$cart_arr[] = $item['name'];
										}
										if (!empty($cart_arr)){
											$array['cart_items'] = implode(', ', $cart_arr);
										}
									}
								}

							}
							break;
						case 'ump':
							if (!empty($check['ump'])){
								$fields = explode(',', $fields_in_arr['uap_source_details_ump_fields_list']);
								require_once IHC_PATH . 'classes/Orders.class.php';
								$temp_obj = new Ump\Orders();
								$temp_data = $temp_obj->get_data($reference);

								if (in_array('order_amount', $fields) && !empty($temp_data['amount_value']) && !empty($temp_data['amount_type'])){
									$array['order_amount'] = $temp_data['amount_type'] . ' ' .$temp_data['amount_value'];
								}
								if (in_array('order_date', $fields) && !empty($temp_data['create_date'])){
									$array['order_date'] = uap_convert_date_to_us_format($temp_data['create_date']);
								}

								if (!empty($temp_data['uid'])){
									$user = get_userdata($temp_data['uid']);
									if (in_array('email', $fields)){
										$array['email'] = $user->user_email;
									}
									if (in_array('first_name', $fields)){
										$array['first_name'] = $user->first_name;
									}
									if (in_array('last_name', $fields)){
										$array['last_name'] = $user->last_name;
									}
									if (in_array('user_login', $fields) && !empty($user->user_login)){
										$array['user_login'] = $user->user_login;
									}
								}
								if (in_array('cart_items', $fields) && !empty($temp_data['lid'])){
									$array['cart_items'] = '';
									$levels = get_option('ihc_levels');
									if ($levels){
										if (!empty($levels[$temp_data['lid']])){
											$label = $levels[$temp_data['lid']]['label'];
											$array['cart_items'] = $label;
										}
									}
								}
							}
							break;
						case 'User SignUp':
							if ($this->is_magic_feat_enable('sign_up_referrals')){
								$fields = explode(',', $fields_in_arr['uap_source_details_signup_fields_list']);
								$uid = str_replace('user_id_', '', $reference);
								$user = get_userdata($uid);
								if (in_array('email', $fields)){
									$array['email'] = $user->user_email;
								}
								if (in_array('first_name', $fields)){
									$array['first_name'] = $user->first_name;
								}
								if (in_array('last_name', $fields)){
									$array['last_name'] = $user->last_name;
								}
								if (in_array('order_date', $fields) && !empty($user->data->user_registered)){
									$array['order_date'] = uap_convert_date_to_us_format($user->data->user_registered);
								}
								if (in_array('user_login', $fields) && !empty($user->user_login)){
									$array['user_login'] = $user->user_login;
								}
							}
							break;
				 	}
				}
			}
			return $array;
		}


		/*
		 * @param int
		 * @return string
		 */
		public function get_current_payment_settings_for_affiliate_id($affiliate_id=0){
			$return = array();
			if ($affiliate_id){
				$uid = $this->get_uid_by_affiliate_id($affiliate_id);
				if ($uid){
					$temp_meta = $this->get_affiliate_payment_settings($uid);
					switch ($temp_meta['uap_affiliate_payment_type']){
						case 'paypal':
							$return['uap_affiliate_paypal_email'] = array(
																					'label' => __('PayPal E-mail Address', 'uap'),
												 									'value' => $temp_meta['uap_affiliate_paypal_email'],
							);
							break;
						case 'stripe':
							$return = array(
											'uap_affiliate_stripe_name' => array(
																					'label' => __('Name on Card', 'uap'),
																					'value' => $temp_meta['uap_affiliate_stripe_name'],
											),
											'uap_affiliate_stripe_card_number' => array(
																					'label' => __('Card Number', 'uap'),
																					'value' => $temp_meta['uap_affiliate_stripe_card_number'],
											),
											/*
											'uap_affiliate_stripe_cvc' => array(
																					'label' => 'CVC',
																					'value' => $temp_meta['uap_affiliate_stripe_cvc'],
											),
											*/
											'uap_affiliate_stripe_expiration_month' => array(
																					'label' => __('Expiration Month', 'uap'),
																					'value' => $temp_meta['uap_affiliate_stripe_expiration_month'],
											),
											'uap_affiliate_stripe_expiration_year' => array(
																					'label' => __('Expiration Year', 'uap'),
																					'value' => $temp_meta['uap_affiliate_stripe_expiration_year'],
											),
											'uap_affiliate_stripe_card_type' => array(
																					'label' => __('Card Type', 'uap'),
																					'value' => $temp_meta['uap_affiliate_stripe_card_type'],
											),
							);
							break;
						case 'bt':
							$return['uap_affiliate_bank_transfer_data'] = array(
																					'label' => __('Bank Transfer Details', 'uap'),
												 									'value' => $temp_meta['uap_affiliate_bank_transfer_data'],
							);
							break;
						case 'stripe_v2':
							$stripe_v2_data = $this->get_affiliate_stripe_v2_payment_settings($affiliate_id);
							if ($stripe_v2_data){
								$possible = array(
													'first_name' => __('First Name', 'uap'),
													'last_name' => __('Last Name', 'uap'),
													'first_name' => __('First Name', 'uap'),
													'day' => __('Birth day', 'uap'),
													'month' => __('Month', 'uap'),
													'year' => __('Year', 'uap'),
													'country' => __('Country', 'uap'),
													'state' => __('State', 'uap'),
													'city' => __('City', 'uap'),
													'line1' => __('Line1', 'uap'),
													'postal_code' => __('Postal Code', 'uap'),
													'user_type' => __('User Type', 'uap'),
													'routing_number' => __('Routing Number', 'uap'),
													'account_number' => __('Account Number', 'uap'),
													'ssn_last_4' => __('SSN last 4', 'uap'),
													'personal_id_number' => __('Personal id number', 'uap'),
													'business_name' => __('Business name', 'uap'),
													'business_tax_id' => __('Business tax id', 'uap'),
													'personal_address.city' => __('Personal Address City', 'uap'),
													'personal_address.line1' => __('Personal Address Line1', 'uap'),
													'personal_address.postal_code' => __('Personal Address Postal Code', 'uap'),
								);
								foreach ($possible as $key=>$value){
									$return[$key] = array(
															'label' => $value,
											 				'value' => @$stripe_v2_data[$key]
									);
								}
							}
							break;
					}
				}
			}
			if (!empty($return)){
				return serialize($return);
			}
			return '';
		}


		/*
		 * @param  int (user id), int (affiliate id)
		 * @return string (user full name or username)
		 */
		public function get_wp_full_name($uid=0, $affiliate_id=0){
		 	if (empty($uid)){
		 		$uid = $this->get_uid_by_affiliate_id($affiliate_id);
		 	}
			if ($uid){
				$first_name = get_user_meta($uid, 'first_name', TRUE);
				$last_name = get_user_meta($uid, 'last_name', TRUE);
				if (empty($first_name) && empty($last_name)){
					return $first_name . ' ' . $last_name;
				} else {
					return $this->get_username_by_wpuid($uid);
				}
			}
			return '';
		}


		/*
		 * @param array, int
		 * @return bool
		 */
		public function simple_links_save_link($post_data=array(), $status=0){
			global $wpdb;
			if ($post_data && !empty($post_data['affiliate_id']) && !empty($post_data['url'])){
				if (strpos($post_data['url'], '.')===FALSE){
					return 0;
				}
				/// first must check if link already exists
				$table = $wpdb->prefix . 'uap_ref_links';
				$q = $wpdb->prepare("SELECT id FROM $table WHERE url LIKE %s;", $post_data['url']);
				$data = $wpdb->get_row($q);
				if (!empty($data) && !empty($data->id)){
					return -1;
				} else {
					/// check the limit
					$q = $wpdb->prepare("SELECT COUNT(id) as c FROM $table WHERE affiliate_id=%d ", $post_data['affiliate_id']);
					$data = $wpdb->get_row($q);
					$the_limit = get_option('uap_simple_links_limit');
					if (!empty($data) && !empty($data->c) && $data->c>=$the_limit){
						return 0;
					}
					$q = $wpdb->prepare("INSERT INTO $table VALUES(NULL, %d, %s, %d);", $post_data['affiliate_id'], $post_data['url'], $status);
					$wpdb->query($q);
					return 1;
				}
			}
			return 0;
		}


		/*
		 * @param int
		 * @return bool
		 */
		public function simple_links_approve_link($id=0){
			global $wpdb;
			if ($id){
				$table = $wpdb->prefix . 'uap_ref_links';
				$q = $wpdb->prepare("UPDATE $table SET status=1 WHERE id=%d ", $id);
				$q = $wpdb->query($q);
				return TRUE;
			}
			return FALSE;
		}


		/*
		 * @param string
		 * @return int
		 */
		public function simple_links_get_uid_by_link($link=''){
			global $wpdb;
			if ($link){
				$table = $wpdb->prefix . 'uap_ref_links';
				$q = $wpdb->prepare("SELECT affiliate_id FROM $table WHERE status=1 AND url LIKE %s;", $link);
				$data = $wpdb->get_row($q);
				if ($data && !empty($data->affiliate_id)){
					return $data->affiliate_id;
				}
			}
			return 0;
		}


		/*
		 * @param int, string
		 * @return bool
		 */
		public function simple_links_delete_link($id=0){
			global $wpdb;
			if ($id){
				$table = $wpdb->prefix . 'uap_ref_links';
				$q = $wpdb->prepare("DELETE FROM $table WHERE id=%d", $id);
				$wpdb->query($q);
				return TRUE;
			}
			return FALSE;
		}


		/*
		 * @param string
		 * @return int
		 */
		public function simple_links_get_counts($where=''){
			global $wpdb;
			$table = $wpdb->prefix . 'uap_ref_links';
			$q = "SELECT COUNT(id) as c FROM $table WHERE 1=1 ";
			if ($where){
				$q .= " AND " . $where;
			}
			$data = $wpdb->get_row($q);
			if ($data && !empty($data->c)){
				return $data->c;
			}
			return 0;
		}


		/*
		 * @param int, int, string, string, string
		 * @return array
		 */
		public function simple_links_get_items($limit=-1, $offset=-1, $order_type='', $order_by='', $where=''){
			global $wpdb;
			$array = array();
			$table = $wpdb->prefix . 'uap_ref_links';

			$q = "SELECT id, affiliate_id, url, status FROM $table ";
			$q .= " WHERE 1=1 ";
			if ($where){
				$q .= " AND " . $where;
			}
			if ($order_type && $order_by){
				$order_by = esc_sql($order_by);
				$order_type = esc_sql($order_type);
				$q .= " ORDER BY " . $order_by . " " . $order_type;
			}
			if ($limit>-1 && $offset>-1){
				$limit = esc_sql($limit);
				$offset = esc_sql($offset);
				$q .= " LIMIT " . $limit . " OFFSET " . $offset;
			}
			$data = $wpdb->get_results($q);
			if ($data){
				foreach ($data as $object){
					$object = (array)$object;
					$object['username'] = $this->get_wp_username_by_affiliate_id($object['affiliate_id']);
					$array[] = $object;
				}
			}
			return $array;
		}


		/*
		 * @param int
		 * @return array
		 */
		public function simple_links_get_items_for_affiliate($affiliate_id=0){
			global $wpdb;
			$array = array();
			$table = $wpdb->prefix . 'uap_ref_links';
			$q = $wpdb->prepare("SELECT id, affiliate_id, url, status FROM $table WHERE affiliate_id=%d ", $affiliate_id);
			$data = $wpdb->get_results($q);
			if ($data){
				foreach ($data as $object){
					$object = (array)$object;
					$array[] = $object;
				}
			}
			return $array;
		}


		/*
		 * @param none
		 * @return array
		 */
		public function account_page_get_custom_menu_items(){
			$data = get_option('uap_account_page_custom_menu_items');
			if ($data){
				foreach ($data as $slug=>$array){
					$key = 'uap_tab_' . $slug . '_title';
					$data[$slug] = array();
					$data[$slug][$key] = get_option($key);
					$key = 'uap_tab_' . $slug . '_menu_label';
					$data[$slug][$key] = get_option($key);
					$key = 'uap_tab_' . $slug . '_content';
					$data[$slug][$key] = get_option($key);
					$key = 'uap_tab_' . $slug . '_icon_code';
					$data[$slug][$key] = get_option($key);
				}
				return $data;
			}
			return array();
		}


		/*
		 * @param array
		 * @return bool
		 */
		public function account_page_save_custom_menu_item($post_data=array()){
			if ($post_data && !empty($post_data['slug'])){
				$data = get_option('uap_account_page_custom_menu_items');
				if (isset($data[$post_data['slug']])){
					return FALSE;
				} else {
					$data[$post_data['slug']] = TRUE;
					update_option('uap_account_page_custom_menu_items', $data);
					$key = 'uap_tab_' . $post_data['slug'] . '_menu_label';
					update_option($key, @$post_data['label']);
					$key = 'uap_tab_' . $post_data['slug'] . '_title';
					update_option($key, @$post_data['label']);
					$key = 'uap_tab_' . $post_data['slug'] . '_content';
					update_option($key, '');
					$key = 'uap_tab_' . $post_data['slug'] . '_icon_code';
					update_option($key, @$post_data['icon_code']);
				}
			}
			return FALSE;
		}


		/*
		 * @param string
		 * @return bool
		 */
		public function account_page_menu_delete_custom_item($slug=''){
			if ($slug){
				$data = get_option('uap_account_page_custom_menu_items');
				if (isset($data[$slug])){
					unset($data[$slug]);
					update_option('uap_account_page_custom_menu_items', $data);
					return TRUE;
				}
			}
			return FALSE;
		}


		/*
		 * @param bool, bool
		 * @return array
		 */
		public function account_page_get_menu($exclude_children=FALSE, $only_standard=FALSE){
			$tabs = array(
							'overview' => array(
												'label' => __('Overview', 'uap'),
							),
							'edit_account' => array(
												'label' => __('Edit Account', 'uap'),
							),
							'change_pass' => array(
												'label' => __('Change Password', 'uap'),
							),
							'custom_affiliate_slug' => array(
												'label' => __('Custom Affiliate Slug', 'uap'),
							),
							'payments_settings' => array(
												'label' => __('Payment Settings', 'uap'),
							),
							'pushover_notifications' => array(
												'label' => __('Pushover Notification', 'uap'),
							),
							'affiliate_link' => array(
												'label' => __('Affiliate Links', 'uap'),
							),
							'simple_links' => array(
												'label' => __('Simple Links', 'uap'),
							),
							'banners' => array(
												'label' => __('Banners', 'uap'),
							),
							'coupons' => array(
												'label' => __('Coupons', 'uap'),
							),
							'referrals' => array(
												'label' => __('Referrals', 'uap'),
							),
							'payments' => array(
												'label' => __('Payments', 'uap'),
							),
							'wallet' => array(
												'label' => __('Wallet', 'uap'),
							),
							'reports' => array(
												'label' => __('Reports', 'uap'),
							),
							'visits' => array(
												'label' => __('Visits', 'uap'),
							),
							'campaign_reports' => array(
												'label' => __('Campaign Reports', 'uap'),
							),
							'referrals_history' => array(
												'label' => __('Referrals History', 'uap'),
							),
							'mlm' => array(
												'label' => __('MLM', 'uap'),
							),
							'referral_notifications' => array(
												'label' => __('Notifications', 'uap'),
							),
							'help' => array(
												'label' => __('Help', 'uap'),
							),
							'logout' => array(
												'label' => __('LogOut', 'uap'),
							),
							'campaigns' => array(
												'label' => __('Campaigns', 'uap'),
							),
							'landing_pages' => array(
												'label' => __('Landing pages', 'uap'),
							),
			);

			if (!$this->is_magic_feat_enable('coupons')){
				unset($tabs['coupons']);
			}
			if (!$this->is_magic_feat_enable('custom_affiliate_slug')){
				unset($tabs['custom_affiliate_slug']);
			}
			if (!$this->is_magic_feat_enable('mlm')){
				unset($tabs['mlm']);
			}
			if (!$this->is_magic_feat_enable('pushover')){
				unset($tabs['pushover']);
			}
			if (!$this->is_magic_feat_enable('wallet')){
				unset($tabs['wallet']);
			}
			if (!$this->is_magic_feat_enable('referral_notifications') && !$this->is_magic_feat_enable('periodically_reports')){
				unset($tabs['referral_notifications']);
			}
			if (!$this->is_magic_feat_enable('simple_links')){
				unset($tabs['simple_links']);
			}

			if ($exclude_children){
				$children = array(
									'reports',
									'visits',
									'campaign_reports',
									'referrals_history',
									'edit_account',
									'change_pass',
									'payments_settings',
									'affiliate_link',
									'simple_links',
									'campaigns',
									'banners',
									'coupons',
				);
				foreach ($tabs as $k=>$v){
					if (in_array($k, $children)){
						unset($tabs[$k]);
					}
				}
				$tabs['profile'] = array(
												'label' => __('Profile', 'uap'),
				);
				$tabs['marketing'] = array(
												'label' => __('Marketing', 'uap'),
				);
				$tabs['reports'] = array(
												'label' => __('Reports', 'uap'),
				);
			}

			$temp = $this->return_settings_from_wp_option('account_page');
			foreach ($tabs as $slug=>$array){
				$key = 'uap_tab_' . $slug . '_title';
				$tabs[$slug][$key] = @$temp[$key];
				$key = 'uap_tab_' . $slug . '_menu_label';
				$tabs[$slug][$key] = @$temp[$key];
				$key = 'uap_tab_' . $slug . '_content';
				$tabs[$slug][$key] = @$temp[$key];
				$key = 'uap_tab_' . $slug . '_icon_code';
				$tabs[$slug][$key] = @$temp[$key];
			}

			if ($only_standard){
				return $tabs;
			}
			$tabs = array_merge($tabs, $this->account_page_get_custom_menu_items());
			return $tabs;
		}


		/*
		 * @param array
		 * @return bool
		 */
		public function custom_insert_user_with_ID($userdata=array()){
			global $wpdb;
			$table = $wpdb->prefix . 'users';
			foreach ($userdata as $key=>$check_data){
				if (empty($userdata[$key]) || is_object($userdata[$key])){
					$userdata[$key] = '';
				}
			}
			$q = $wpdb->prepare("INSERT INTO $table VALUES(
															%d,
															%s,
															%s,
															%s,
															%s,
															%s,
															%s,
															%s,
															%s,
															%s
			);", $userdata['ID'], $userdata['user_login'], $userdata['user_pass'],
			$userdata['user_nicename'], $userdata['user_email'], $userdata['user_url'],
			$userdata['user_registered'], $userdata['user_activation_key'], $userdata['user_status'],
			$userdata['display_name']
			);
			return $wpdb->query($q);
		}


		/*
		 * @param none
		 * @return array
		 */
		public function get_all_ump_wp_options($except=array('general-redirects', 'general-default_pages')){
		 	$array = array();
	 	 	$search_groups = array(
									'login',
									'login-messages',
									'general-settings',
									'general-admin_workflow',
									'general-public_workflow',
									'general-uploads',
									'general-redirects',
									'general-default_pages',
									'general-captcha',
									'general-msg',
									'general-notification',
									'register',
									'register-msg',
									'register-custom-fields',
									'opt_in',
									'notifications',
									'account_page',
									'double_email_verification',
									'licensing',
									'sign_up_referrals',
									'lifetime_commissions',
									'reccuring_referrals',
									'social_share',
									'paypal',
									'stripe',
									'bonus_on_rank',
									'allow_own_referrence',
									'mlm',
									'rewrite_referrals',
									'coupons',
									'friendly_links',
									'custom_affiliate_slug',
									'wallet',
									'checkout_select_referral',
									'top_affiliate_list',
									'woo_account_page',
									'bp_account_page',
									'referral_notifications',
									'periodically_reports',
									'qr_code',
									'email_verification',
									'source_details',
									'wp_social_login',
									'stripe_v2',
									'pushover',
									'max_amount',
									'simple_links',
									'account_page_menu',
									'ranks_pro',
									'landing_pages',
									'pay_per_click',
									'cpm_commission'
		    );
		 	if ($except){
				foreach ($except as $value){
			 		$key = array_search($value, $search_groups);
					if ($key!==FALSE){
				 		unset($search_groups[$key]);
					}
			 	}
		 	}
		 	foreach ($search_groups as $key_group){
		 		$temp = $this->return_settings_from_wp_option($key_group);
			 	$array = array_merge($array, $temp);
		 	}
		 	return $array;
		}


		/*
		 * @param int
		 * @return array
		 */
		public function get_user_roles($uid=0){
			global $wpdb;
			if ($uid){
				$role_key = $wpdb->prefix . 'capabilities';
				$data = get_user_meta($uid, $role_key, TRUE);
				return $data;
			}
			return array();
		}


		/*
		 * @param int (user id
		 * @return bool)
		 */
		public function is_user_admin($uid=0){
			if ($uid){
				$data = $this->get_user_roles($uid);
				if (isset($data['administrator']) && $data['administrator']==1){
					return TRUE;
				}
			}
			return FALSE;
		}


		/**
		 * @param int
		 * @return string
		 */
		public function get_user_first_role($uid=0){
			global $wpdb;
			if ($uid){
				$role_key = $wpdb->prefix . 'capabilities';
				$data = get_user_meta($uid, $role_key, TRUE);
				$first = key($data);
			}
			return isset($first) ? $first : '';
		}

		public function does_post_exists($id=0){
				global $wpdb;
				$q = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE ID=%d", $id);
				return $wpdb->get_var($q);
		}

		public function getPPCValueForRank($rankId=0)
		{
				global $wpdb;
				if (empty($rankId)){
						return 0;
				}
				$rankId = esc_sql($rankId);
				return $wpdb->get_var("SELECT pay_per_click FROM {$wpdb->prefix}uap_ranks WHERE id=$rankId");
		}

		public function getCPMValueForRank($rankId=0)
		{
				global $wpdb;
				if (empty($rankId)){
						return 0;
				}
				$rankId = esc_sql($rankId);
				return $wpdb->get_var("SELECT cpm_commission FROM {$wpdb->prefix}uap_ranks WHERE id=$rankId");
		}

		public function getLandingPagesForAffiliate($affiliateId=0)
    {
        global $wpdb;
        if (empty($affiliateId)){
            return false;
        }
        $affiliateId = esc_sql($affiliateId);
        $query = "SELECT a.ID, a.post_title
                    FROM {$wpdb->posts} a
                    INNER JOIN {$wpdb->postmeta} b
                    ON a.ID=b.post_id
                    WHERE
                    b.meta_key='uap_landing_page_affiliate_id'
                    AND
                    b.meta_value=$affiliateId
        ";
				return $wpdb->get_results($query);
    }

		public function getCPMForAffiliate($affiliateId=0)
		{
				if (empty($affiliateId)) return 0;
				global $wpdb;
				$countNumber = $wpdb->get_var("SELECT count_number FROM {$wpdb->prefix}uap_cpm WHERE affiliate_id=$affiliateId");
				if ($countNumber===null){
						$countNumber = 0;
				}
				return $countNumber;
		}

		public function getReferralsBySourceAndAffiliate($type='', $affiliateId=0)
		{
				global $wpdb;
				$query = "SELECT Count(*) FROM {$wpdb->prefix}uap_referrals
										WHERE
										1=1
				";
				if ($type){
						$type = esc_sql($type);
						$query .= " AND source='$type' ";
				}
				if ($affiliateId){
						$affiliateId = esc_sql($affiliateId);
						$query .= " AND affiliate_id=$affiliateId ";
				}
				$data = $wpdb->get_var($query);
				if ($data===null){
						$data = 0;
				}
				return $data;
		}

		public function getEPCdata($type='', $affiliateId=0)
		{
				global $wpdb;

				$type = esc_sql($type);
				$today = strtotime('00:00:00');
				switch($type){
					case '3months':
						$start_time = strtotime('-90 day', $today);
						break;
					case '7days':
						$start_time = strtotime('-7 day', $today);
						break;
					case '30days':
						$start_time = strtotime('-30 day', $today);
						break;
					default:
						$start_time = strtotime('-90 day', $today);
					break;
				}

				//$start_time = date('Y-m-d H:i:s', $start_time);
				//$today = date('Y-m-d H:i:s', $today);

				$table = $wpdb->prefix . 'uap_visits';
				$q = "SELECT COUNT(*)  FROM $table WHERE affiliate_id=$affiliateId  AND UNIX_TIMESTAMP(visit_date)<=$today";
				if (!empty($start_time)){
					$q .= " AND UNIX_TIMESTAMP(visit_date)>$start_time ";
				}
				$data_visits = $wpdb->get_var($q);
					if ($data_visits===null || $data_visits == 0){
							return 0;
					}


				$table = $wpdb->prefix . 'uap_referrals';
				$q = "SELECT SUM(amount)  FROM $table WHERE affiliate_id=$affiliateId
															AND visit_id > 0
															AND status = 2
															AND UNIX_TIMESTAMP(date)<=$today";
				if (!empty($start_time)){
					$q .= " AND UNIX_TIMESTAMP(date)>$start_time ";
				}
				$data_earnings = $wpdb->get_var($q);
					if ($data_earnings===null){
							$data_earnings = 0;
					}

				$epc_data = $data_earnings/	$data_visits *100;

				return round($epc_data,2);

		}

		public function get_user_col_value($uid=0, $col_name=''){
			if ($uid && $col_name){
				global $wpdb;
				$table = $wpdb->base_prefix . 'users';
				$col_name = esc_sql($col_name);
				$q = $wpdb->prepare("SELECT $col_name FROM $table WHERE ID=%d;", $uid);
				$data = $wpdb->get_row($q);
				if (!empty($data->$col_name)){
					return $data->$col_name;
				}
			}
		}

		public function getUserFullName($uid=0){
				if (empty($uid)) return '';
				$uid = esc_sql($uid);
				$first = get_user_meta($uid, 'first_name', TRUE);
				$last = get_user_meta($uid, 'last_name', TRUE);
				if($first != '' || $last != '')
					return $first . ' ' . $last;

				$nickname = get_user_meta($uid, 'nickname', TRUE);
				return $nickname;
		}

		public function doApproveAffiliate($affiliateId=0)
		{
				if (empty($affiliateId)){
					return false;
				}
				$uid = $this->get_uid_by_affiliate_id($affiliateId);
				if (empty($uid)){
					return false;
				}
				$role = get_option('uap_after_approve_role');
				if (empty($role)){
						$role = get_option('default_role');
				}
				$new_role = empty($role) ? 'subscriber' : $role;
				$uid = wp_update_user(array( 'ID' => $uid, 'role' => $new_role));
				uap_send_user_notifications($uid, 'affiliate_account_approve');
		}

	}//end of class
}//end if
