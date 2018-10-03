<?php
if (!class_exists('Uap_Pushover')):

class Uap_Pushover{

	/*
	 * @param none
	 * @return none
	 */
	public function __construct(){
		require_once UAP_PATH . 'classes/Pushover.class.php';
	}

	/*
	 * @param int, int, string, boolean
	 * @return boolean
	 */
	public function send_notification($uid=0, $rank=-1, $notification_type='', $send_to_admin=FALSE){
		if ($notification_type){
			global $indeed_db;
			$notification_data = $this->get_notification_data($notification_type, $rank);

			if ($notification_data && !empty($notification_data['pushover_status']) && !empty($notification_data['pushover_message'])){
				$meta = $indeed_db->return_settings_from_wp_option('pushover');
				$message = stripslashes($notification_data['pushover_message']);
				$message = uap_replace_constants($message, $uid);
				$title = $notification_data['subject'];
				$title = uap_replace_constants($title, $uid);

				$push = new Pushover();
				$app_token = get_option('uap_pushover_app_token');
				if ($uid && !$send_to_admin){
					$user_token = get_user_meta($uid, 'uap_pushover_token', TRUE);	/// USER
				} else {
					$user_token = $meta['uap_pushover_admin_token'];  /// ADMIN
				}
				$sound = get_option('uap_pushover_sound');
				$sound = empty($meta['uap_pushover_sound']) ? 'bike' : $meta['uap_pushover_sound'];
				$url = empty($meta['uap_pushover_url']) ? '' : $meta['uap_pushover_url'];
				$url_title = empty($meta['uap_pushover_url_title']) ? '' : $meta['uap_pushover_url_title'];

				$push->setToken($app_token);
				$push->setUser($user_token);
				$push->setTitle($title);
				$push->setMessage($message);
				$push->setUrl($url);
				$push->setUrlTitle($url_title);
				$push->setPriority(2); /// 0 || 1 || 2
				$push->setRetry(300); /// five minutes
				$push->setExpire(3600); /// one hour
				$push->setTimestamp(time());
				$push->setDebug(FALSE);
				$push->setSound($sound);
				return $push->send();
			}
		}
		return FALSE;
	}

	/*
	 * @param string
	 * @return array
	 */
	private function get_notification_data($type='', $rank=-1){
		global $wpdb;
		$table = $wpdb->prefix . "uap_notifications";
		$q = $wpdb->prepare("SELECT * FROM $table
									WHERE
									type=%s
									AND rank_id=%d
									ORDER BY id DESC LIMIT 1;", $type, $rank);
		$data = $wpdb->get_row($q);
		if (empty($data)){
			$q = $wpdb->prepare("SELECT * FROM $table
										WHERE
										type=%s
										AND rank_id=-1
										ORDER BY id DESC LIMIT 1;", $type);
			$data = $wpdb->get_row($q);
		}
		return (array)$data;
	}

	public function sendCustom($title='', $message='', $uid=0)
	{
		global $indeed_db;
		$meta = $indeed_db->return_settings_from_wp_option('pushover');
		$message = stripslashes($message);
		$title = stripslashes($title);
		$message = uap_replace_constants($message, $uid);
		$title = uap_replace_constants($title, $uid);

		$push = new Pushover();
		$app_token = get_option('uap_pushover_app_token');
		$user_token = get_user_meta($uid, 'uap_pushover_token', TRUE);	/// USER
		$sound = get_option('uap_pushover_sound');
		$sound = empty($meta['uap_pushover_sound']) ? 'bike' : $meta['uap_pushover_sound'];
		$url = empty($meta['uap_pushover_url']) ? '' : $meta['uap_pushover_url'];
		$url_title = empty($meta['uap_pushover_url_title']) ? '' : $meta['uap_pushover_url_title'];

		$push->setToken($app_token);
		$push->setUser($user_token);
		$push->setTitle($title);
		$push->setMessage($message);
		$push->setUrl($url);
		$push->setUrlTitle($url_title);
		$push->setPriority(2); /// 0 || 1 || 2
		$push->setRetry(300); /// five minutes
		$push->setExpire(3600); /// one hour
		$push->setTimestamp(time());
		$push->setDebug(FALSE);
		$push->setSound($sound);
		return $push->send();
	}

}

endif;
