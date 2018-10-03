<?php
if (!class_exists('Uap_Cron_Jobs')) :

class Uap_Cron_Jobs{

	public function __construct(){
		/*
		 * @param none
		 * @return none
		 */

		/////////// RANKS
		$repeat = get_option('uap_update_ranks_interval');
		if (empty($repeat)){
			$repeat = 'daily';
		}
		$schedule = wp_next_scheduled('uap_cron_job');
		if (empty($schedule)){
			//create cron
			wp_schedule_event( time(), $repeat, 'uap_cron_job');//modify time
		}
		add_action( 'uap_cron_job', array($this, 'update_affiliates_rank') );

		///////// PAYMENTS
		$repeat = get_option('uap_update_payments_status');
		if (empty($repeat)){
			$repeat = 'daily';
		}
		$schedule = wp_next_scheduled('uap_cron_job_payments');
		if (empty($schedule)){
			//create cron
			wp_schedule_event( time(), $repeat, 'uap_cron_job_payments');//modify time
		}
		add_action( 'uap_cron_job_payments', array($this, 'update_affiliates_payments_status') );

		///////// RANKS
		$repeat = 'daily';
		$schedule = wp_next_scheduled('uap_cron_error_notifications');
		if (empty($schedule)){
			//create cron
			wp_schedule_event( time(), $repeat, 'uap_cron_error_notifications');//modify time
		}
		add_action( 'uap_cron_error_notifications', array($this, 'send_email_notification_for_slug_username_errors') );


		/// REPORTS
		$repeat = 'daily';
		$schedule = wp_next_scheduled('uap_cron_send_reports_to_affiliate');
		if (empty($schedule)){
			//create cron
			$middlenight = strtotime(date('m/d/Y', time()));
			wp_schedule_event($middlenight, $repeat, 'uap_cron_send_reports_to_affiliate');//modify time
		}
		add_action( 'uap_cron_send_reports_to_affiliate', array($this, 'send_reports_to_affiliate') );


		/// DELETE USERS E-MAIL NOT VERIFY
		$repeat = 'daily';
		$schedule = wp_next_scheduled('uap_cron_delete_unverified_affiliates');
		if (empty($schedule)){
			//create cron
			wp_schedule_event(time(), $repeat, 'uap_cron_delete_unverified_affiliates');//modify time
		}
		add_action( 'uap_cron_delete_unverified_affiliates', array($this, 'do_delete_unverified_affiliates') );


		///
		add_action('uapDoRanksReset', array($this, 'doResetRanksAction'));

	}

	public function update_affiliates_rank(){
		/*
		 * @param none
		 * @return none
		 */
		require_once UAP_PATH . 'public/Uap_Change_Ranks.class.php';
		$object = new Uap_Change_Ranks();
	}

	public function update_affiliates_payments_status(){
		/*
		 * @param none
		 * @return none
		 */
		global $indeed_db;
		$indeed_db->update_paypal_transactions();
	}

	public function update_cron_time($new=''){
		/*
		 * @param string
		 * @return none
		 */
		wp_clear_scheduled_hook('uap_cron_job');
		wp_schedule_event( time(), $new, 'uap_cron_job');
	}


	public function send_email_notification_for_slug_username_errors(){
		/*
		 * @param none
		 * @return none
		 */
		global $indeed_db;
		$data = $indeed_db->select_all_same_slugs_with_usernames();
		if ($data){
			$output = '';
			foreach ($data as $arr){
				$owner_of_username = $indeed_db->get_username_by_wpuid($arr['user']);
				$owner_of_slug = $indeed_db->get_username_by_wpuid($arr['slug']);
				$output .= __('User ', 'uap') . $owner_of_slug . __(' has a custom slug that match with nickname of ', 'uap') . $owner_of_username . '. <br/>';
			}
			if ($output){
				$output = __('We inform You that: ', 'uap') . '<br/>' . $output . '<br/>' . __('This could cause some errors into Ultimate Affiliate Pro.', 'uap');
				$admin_email = get_option('admin_email');
				$output = "<html><head></head><body>" . $output . "</body></html>";
				$subject = __('Hello', 'uap');
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
				if ($admin_email){
					wp_mail($admin_email, $subject, $output, $headers);
				}
			}
		}
	}

	public function send_reports_to_affiliate(){
		/*
		 * @param none
		 * @return none
		 */
		 global $indeed_db;
		 if (!get_option('uap_periodically_reports_enable')){
			/// DISABLED BY ADMIN
		  	return;
		 }
		 if (!class_exists('Uap_Affiliate_Notification_Reports')){
		 	 require_once UAP_PATH . 'classes/Uap_Affiliate_Notification_Reports.class.php';
		 }
		 $object = new Uap_Affiliate_Notification_Reports();
		 $data = $indeed_db->get_affiliates_for_reports();
		 if ($data){
		 	foreach ($data as $array){
				$object->report_referrals_message($array['affiliate_id'], $array['email'], $array['period']);
			}
		 }
	}

	public function do_delete_unverified_affiliates(){
		/*
		 * @param none
		 * @return none
		 */
		 global $wpdb, $indeed_db;
		 $settings = $indeed_db->return_settings_from_wp_option('email_verification');
		 if ($settings['uap_register_double_email_verification'] && (int)$settings['uap_double_email_delete_user_not_verified']>-1){
		 	 $time_limit = (int)$settings['uap_double_email_delete_user_not_verified'];
			 $time_limit = $time_limit * 24 * 60 * 60;
			 $table = $wpdb->base_prefix . "usermeta";
			 $data = $wpdb->get_results("SELECT user_id FROM $table	WHERE meta_key='uap_verification_status' AND meta_value='-1';");
			 if ($data){
				foreach ($data as $k=>$v){
					if (!empty($v->user_id)){
						$time_data = $wpdb->get_row("SELECT user_registered FROM " . $wpdb->base_prefix . "users WHERE ID='" . $v->user_id . "';");
						if (!empty($time_data->user_registered)){
							$time_to_delete = strtotime($time_data->user_registered) + $time_limit;
							if ( $time_to_delete < time() ){
								$affiliate_id = $indeed_db->get_affiliate_id_by_wpuid($v->user_id);
								$indeed_db->delete_affiliates($affiliate_id);
							}
						}
					}
				}
			 }
		 }
	}

	public function doResetRanksAction()
	{
			$object = new \Indeed\Uap\ResetRanks();
			$object->doAction()->doSchedule();
	}

}

endif;
