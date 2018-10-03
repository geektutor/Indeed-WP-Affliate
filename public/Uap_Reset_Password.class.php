<?php 
if (!class_exists('Uap_Reset_Password')) : 

class Uap_Reset_Password{
	private static $reset_success = 0;
	public function __construct(){}
	
	public function form(){
		/*
		 * @param none
		 * @return string
		 */
		if (!is_user_logged_in()){
			global $indeed_db;
			$meta_arr = $indeed_db->return_settings_from_wp_option('login');				
			if (!empty(self::$reset_success)){
				if (self::$reset_success==2){
					$data['success_message'] = get_option('uap_reset_msg_pass_ok');
				} else if (self::$reset_success==1) {
					$data['error_message'] = get_option('uap_reset_msg_pass_err');
				}
			}
			require_once UAP_PATH . 'public/views/reset_password.php';
		}
	}
	
	public function do_reset(){
		/*
		 * @param none
		 * @return none
		 */
		self::$reset_success = 1;
		require_once UAP_PATH . 'classes/ResetPassword.class.php';
		$reset_password = new UAP\ResetPassword();
		if ($reset_password->send_mail_with_link($_REQUEST['email_or_userlogin'])){
			self::$reset_success = 2;
		}
	}
	
}

endif;