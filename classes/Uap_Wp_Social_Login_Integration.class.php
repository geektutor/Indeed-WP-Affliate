<?php
if (!class_exists('Uap_Wp_Social_Login_Integration')):
	
class Uap_Wp_Social_Login_Integration{
	
	public function __construct(){}
	
	public static function run(){
		/*
		 * @param none
		 * @return none
		 */
		 global $indeed_db;		
		 if ($indeed_db->is_magic_feat_enable('wp_social_login')){
		 	 add_action('wsl_hook_process_login_before_wp_safe_redirect', array('Uap_Wp_Social_Login_Integration', 'uap_wp_social_login_do_redirect'), 92, 0);	
		 	 add_action('wsl_hook_process_login_after_wp_insert_user', array('Uap_Wp_Social_Login_Integration', 'uap_wp_social_login_after_register_action'), 90, 3);				 
		 }
	}
	
	public static function uap_wp_social_login_do_redirect(){
		/*
		 * @param none
		 * @return none, will do redirect if it's case
		 */
		$redirect = get_option('uap_wp_social_login_redirect_page');
		if ($redirect && $redirect!=-1){
			$url = get_permalink($redirect);
			if (!empty($url)){
				wp_safe_redirect($url);
				die();		
			}
		}
	}
	
	public static function uap_wp_social_login_after_register_action($user_id=0, $provider='', $hybridauth_user_profile=''){
		/*
		 * @param none
		 * @return none
		 */
		if ($user_id){
			global $indeed_db;
			/// save user to affiliate db
			$indeed_db->save_affiliate($user_id);
			/// rank
			$rank = get_option('uap_wp_social_login_default_rank');
			if ($rank){
				$indeed_db->update_affiliate_rank_by_uid($user_id, $rank);				
			}					
			/// STORE AVATAR
	 		if (!empty($hybridauth_user_profile) && !empty($hybridauth_user_profile->photoURL)){
	 			update_user_meta($user_id, 'uap_avatar', $hybridauth_user_profile->photoURL);
	 		}			
			///ROLE
			$role = get_option('uap_wp_social_login_default_role');
			if ($role){
				$u = new WP_User($user_id);
				$u->set_role($role);
			}  	
		}	 				 
	}
	
}	
	
endif;
