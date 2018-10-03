<?php 
if (!class_exists('Uap_Login')){
	class Uap_Login{
		
		public function __construct(){}
		
		public function print_login_form($args=array(), $user_role='unreg', $affiliate_id=0){
			/*
			 * @param array
			 * @return none
			 */
			global $indeed_db;
			$meta_arr = $indeed_db->return_settings_from_wp_option('login');
			foreach ($meta_arr as $key=>$value){
				if (isset($args[$key])){
					$meta_arr[$key] = $args[$key];
				}
			}
						
			///////////// LOGIN FORM
			require_once UAP_PATH . 'public/views/login_form.php';
			$str = '';
			$msg = '';
			if ($affiliate_id){
				if ($user_role!='unreg'){
					////////////REGISTERED USER
					if ($user_role=='pending'){
						//pending user
						$msg = uap_correct_text(get_option('uap_register_pending_user_msg', true));
						if ($msg){
							$str .= '<div class="uap-login-pending">' . $msg . '</div>';
						}
					}
				} else {
					/////////////UNREGISTERED
					$str .= uap_print_form_login($meta_arr);
				}				
			} else {
				/// NOT AFFILIATE
				$str .= uap_print_form_login($meta_arr);
			}

			
			//print the message
			if (!empty($_GET['uap_success_login'])){
				/************************** SUCCESS ***********************/
				$msg .= get_option('uap_login_succes');				
				if (!empty($msg)){
					$str .= '<div class="uap-login-success">' . uap_correct_text($msg) . '</div>';
				}
			} else if (!empty($_GET['uap_pending_email'])){
				/************************ PENDING EMAIL ********************/
				$arr = $indeed_db->return_settings_from_wp_option('login-messages', false, true);
				if (isset($arr['uap_login_error_email_pending']) && $arr['uap_login_error_email_pending']){
					$login_faild = $arr['uap_login_error_email_pending'];
				} else {
					$login_faild = __('Error', 'uap');
				}	
				$str .= '<div class="uap-login-error">' . uap_correct_text($login_faild) . '</div>';
			} else if (!empty($_GET['uap_login_fail'])){
				/************************** FAIL *****************************/
				$login_faild = uap_correct_text(get_option('uap_login_error'));
				if (empty($login_faild)){
					$arr = $indeed_db->return_settings_from_wp_option('login-messages', false, true);
					if (isset($arr['uap_login_error']) && $arr['uap_login_error']){
						$login_faild = $arr['uap_login_error'];
					} else {
						$login_faild = __('Error', 'uap');
					}
				}
				$str .= '<div class="uap-login-error">' . uap_correct_text($login_faild) . '</div>';
			} else if (!empty($_GET['uap_login_pending'])){
				/*********************** PENDING ******************************/
				$str .= '<div class="uap-login-pending">' . uap_correct_text(get_option('uap_login_pending')) . '</div>';
			} else if (!empty($_GET['uap_fail_captcha'])){
				$login_faild = uap_correct_text(get_option('uap_login_error_on_captcha'));
				if (!$login_faild){
					$login_faild = __('Captcha Error', 'uap');
				}
				$str .= '<div class="uap-login-error-wrapper"><div class="uap-login-error">' . $login_faild . '</div></div>';
			}
			return $str;
		}
		
		public function do_login($url=''){
			/*
			 * @param none
			 * @return int (user id)
			 */
			global $indeed_db;
			if (!empty($_POST['log']) && !empty($_POST['pwd'])){
				
				/// CHECK RECAPTCHA
				if (get_option('uap_login_show_recaptcha')){
					$this->check_recaptcha($url);
				}
				
				$arr['user_login'] = sanitize_user($_POST['log']);
				$arr['user_password'] = $_POST['pwd'];
				$arr['remember'] = ( isset( $_POST['rememberme'] ) == 'forever' ) ? true : false;
				$user = wp_signon($arr, true);
				if (!is_wp_error($user)){
						
					//============== Check E-mail verification status
					$this->uap_check_email_verification_status($user->ID, $url);
					
					/// CHECK IF USER IT's AFFILIATE
					$affiliate_id = $indeed_db->affiliate_get_id_by_uid($user->ID);
	
					if (!$affiliate_id){
						/// NOT AFFILIATE ERROR
						wp_clear_auth_cookie();//logout
						do_action( 'wp_logout' );
						nocache_headers();
						$url = add_query_arg( array('uap_login_fail'=>'true'), $url );
						wp_redirect( $url );
						exit();						
					} else if ($affiliate_id && isset($user->roles[0]) && $user->roles[0]=='pending_user'){
						//=================== PENDING USER
						wp_clear_auth_cookie();//logout
						do_action( 'wp_logout' );
						nocache_headers();
						$url = add_query_arg( array('uap_login_pending' => 'true'), $url );
						wp_redirect( $url );
						exit();
					} else {
						//================== LOGIN SUCCESS
						$url = add_query_arg( array( 'uap_success_login' => 'true' ), $url );			
						$redirect_p_id = get_option('uap_general_login_redirect');
						if ($redirect_p_id && $redirect_p_id!=-1){
							$redirect_url = get_permalink($redirect_p_id);
							if ($redirect_url){
								wp_redirect( $redirect_url );
								exit();
							}
						}
						wp_redirect( $url );
						exit();
					}
				}
			}
			if($user->get_error_message() == 'Pending User'){
				wp_clear_auth_cookie();//logout
				do_action( 'wp_logout' );
				nocache_headers();
						
				$url = add_query_arg( array('uap_login_pending' => 'true'), $url );
				wp_redirect( $url );
				exit();
			}
			//===================== LOGIN FAILD
			$url = add_query_arg( array('uap_login_fail'=>'true'), $url );
			wp_redirect( $url );
			exit();
		}
		
		
		public function uap_check_email_verification_status($uid, $redirect_url=''){
			/*
			 * logout and redirect if verification status is -1
			 * @param int, string
			 * @return none
			 */
			if (get_option('uap_register_double_email_verification')){
				$email_verification = get_user_meta($uid, 'uap_verification_status', TRUE);
				if ($email_verification==-1){
					wp_clear_auth_cookie();//logout
					do_action( 'wp_logout' );
					nocache_headers();
					if (!$redirect_url){
						$redirect_url = home_url();
					}
					$redirect_url = add_query_arg(array('uap_pending_email'=>'true'), $redirect_url);
					wp_redirect( $redirect_url );
					exit();
				}				
			}
		}
		
		private function check_recaptcha($url=''){
			/*
			 * REDIRECT IF CAPTCHA IS NOT COMPLETED 
			 * @param string
			 * @return none
			 */
			$secret = get_option('uap_recaptcha_private');
			if ($secret){
				if (isset($_POST['g-recaptcha-response'])){					
					if (!class_exists('ReCaptcha')){
						require_once UAP_PATH . 'classes/recaptcha/autoload.php';								
					}
					$recaptcha = new \ReCaptcha\ReCaptcha($secret);
					$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
					if (!$resp->isSuccess()){
						$captcha_error = TRUE;
					}
				} else {
					$captcha_error = TRUE;
				}		
			}
			if (!empty($captcha_error)){
				$url = add_query_arg( array('uap_fail_captcha'=>'true'), $url );
				wp_redirect( $url );
				exit();
			}			 
		}
		
		
	}//end of class
}//endif