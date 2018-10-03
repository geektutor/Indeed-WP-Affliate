<?php
namespace UAP{
	class ResetPassword{
		private $expire_interval = 3600;//one hour
		private $plugin_prefix = 'uap';
		private $base_url = UAP_URL;
		private $notification_function = '';
		
		public function __construct(){
			/*
			 * @param none
			 * @return none
			 */
			$this->notification_function = $this->plugin_prefix . '_send_user_notifications';
		}
		
		public function send_mail_with_link($username_or_email=''){
			/*
			 * @param string
			 * @return boolean
			 */
			$sent = FALSE;
			$user = get_user_by('email', $username_or_email);
			if ($user){		
				$uid = $user->data->ID;
				$email_addr = $username_or_email;
			} else {
				//get user by user_login
				global $wpdb;
				$data = $wpdb->get_row("SELECT ID, user_email FROM " . $wpdb->prefix . "users WHERE `user_login`='$username_or_email';");
				if (isset($data->ID) && isset($data->user_email)){
					$uid = $data->ID;
					$email_addr = $data->user_email;
				}
			}
			
			if (!empty($email_addr) && !empty($uid)){
				$hash = $this->random_string(10);
				$time = time();
				update_user_meta($uid, $this->plugin_prefix . '_reset_password_temp_data', array('code' => $hash, 'time' => $time ));
				///$link = $this->base_url . 'public/arrive.php?do_reset_pass=true&c=' . $hash . '&uid=' . $uid;
				$link = site_url();
				$link = trailingslashit($link);
				$link = add_query_arg('uap_act', 'password_reset', $link);
				$link = add_query_arg('do_reset_pass', 'true', $link);
				$link = add_query_arg('c', $hash, $link);
				$link = add_query_arg('uid', $uid, $link);
								
				$notification_function = $this->notification_function;
				$sent = $notification_function($uid, 'reset_password_process', FALSE, array('{password_reset_link}' => $link));
				if (!$sent){
					$subject = __('Password reset on ', $this->plugin_prefix) . get_option('blogname');
					$msg = __('<p>You or someone else has requested to change password for your account.</p></br><p>To change Your Password click on this URL: </p>', $this->plugin_prefix) . $link;					
					$from_name = get_option('uap_notification_name');
					if (empty($from_name)){
						$from_name = get_option("blogname");
					}	
					$from_email = get_option('uap_notification_email_from');
					if (empty($from_email)){
						$from_email = get_option('admin_email');
					}
					if (!empty($from_email) && !empty($from_name)){
						$headers[] = "From: $from_name <$from_email>";						
					}		
					$headers[] = 'Content-Type: text/html; charset=UTF-8';												
					$sent = wp_mail($email_addr, $subject, $msg, $headers);			
				}
			}	
			return $sent;
		}
		
		public function proceed($uid=0, $code=''){
			/*
			 * @param int, string
			 * @return none
			 */
			 if ($uid && $code){
			 	$time = time();
				$data = get_user_meta($uid, $this->plugin_prefix . '_reset_password_temp_data', TRUE);
				if ($data){
					if ($data['code']==$code && $data['time']+$this->expire_interval>$time){
						$sucess = $this->do_reset_password($uid);
						if ($sucess){
							delete_user_meta($uid, $this->plugin_prefix . '_reset_password_temp_data');
						}
					}
				}
			 }
		}
		
		private function do_reset_password($uid=0){
			/*
			 * @param int
			 * @return boolean
			 */
			 $sent = FALSE;
			 if ($uid){
			 	add_filter( 'send_password_change_email', '__return_false', 1);
			 	$fields['ID'] = $uid;
				$fields['user_pass'] = wp_generate_password(10, TRUE);
				$user_id = wp_update_user($fields);		
				if ($user_id==$fields['ID']){
					$notification_function = $this->notification_function;
					$sent = $notification_function($user_id, 'reset_password', FALSE, array('{NEW_PASSWORD}' => $fields['user_pass']));
					if (!$sent){
						$email_addr = $this->get_mail_by_uid($fields['ID']);
						if ($email_addr){
							$subject = __('Password reset on ', $this->plugin_prefix) . get_option('blogname');
							$msg = __('Your new password it\'s: ', $this->plugin_prefix) . $fields['user_pass'];
							$sent = wp_mail( $email_addr, $subject, $msg );								
						}			
					}						
				}			 	
			 }		
			 return $sent;		 
		}
		
		private function get_mail_by_uid($uid=0){
			/*
			 * @param int
			 * @return string
			 */
			 if ($uid){
			 	$data = get_userdata($uid);
				return (!empty($data) && !empty($data->user_email)) ? $data->user_email : '';
			 }
			 return '';
		}
		
		private function random_string($length=10, $chars='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'){
			/*
			 * @param int, string
			 * @return string
			 */
			$output = '';
			$max = mb_strlen($chars, '8bit') - 1;
			for ($i=0; $i<$length; ++$i) {
				$output .= $chars[rand(0, $max)];
			}
			return $output;
		}
		
	}
}
