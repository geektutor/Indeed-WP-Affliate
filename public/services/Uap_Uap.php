<?php
namespace Indeed\Uap\Services;
class Uap_Uap extends \Referral_Main{
	private $source_type = 'uap';
	private static $affiliate_from_coupon = 0;
	private static $checkout_referrals_select_settings = array();
	private static $check_require_error = FALSE;

	public function __construct(){
    $this->check_for_selected_affiliate();
    add_action('uap_register_form_before_submit_button', [$this, 'insert_affiliate_select']);
    add_filter('uap_register_process_filter_errors', [$this, 'check_require'], 1, 1 );

	}


	public function check_for_selected_affiliate(){
		 global $indeed_db;
		 if (empty(self::$checkout_referrals_select_settings)){
		 	self::$checkout_referrals_select_settings = $indeed_db->return_settings_from_wp_option('checkout_select_referral');
		 }
		 if (self::$checkout_referrals_select_settings['uap_checkout_select_referral_enable']){
		 	if (!empty($_POST['uap_affiliate_username'])){
		 		self::$affiliate_id = $_POST['uap_affiliate_username'];
		 	} else if (!empty($_POST['uap_affiliate_username_text'])){
		 		$temp = $indeed_db->get_affiliate_id_by_username($_POST['uap_affiliate_username_text']);
				if ($temp){
					self::$affiliate_id = $temp;
				}
		 	}
		 }
	}

	public function insert_affiliate_select($output='', $is_public=FALSE){
		 global $indeed_db;
		 $string = '';
		 if (empty(self::$checkout_referrals_select_settings)){
		 	self::$checkout_referrals_select_settings = $indeed_db->return_settings_from_wp_option('checkout_select_referral');
		 }
		 /// check it's enable
		 if (self::$checkout_referrals_select_settings['uap_checkout_select_referral_enable']){ ///  && $is_public
		 	$this->set_affiliate_id();
		 	if (self::$affiliate_id && !self::$checkout_referrals_select_settings['uap_checkout_select_referral_rewrite']){
		 		return $output; /// OUT
		 	}
			$who = self::$checkout_referrals_select_settings['uap_checkout_select_affiliate_list'];
			$type = self::$checkout_referrals_select_settings['uap_checkout_select_referral_name'];
			$data['affiliates'] = $indeed_db->get_affiliates_for_checkout_select($who, $type);
			$data['require'] = '';
			$data['class'] = 'iump-form-line-register';
			$data['select_class'] = '';
			$data['input_class'] = '';
			$data['require_on_input'] = '';
			ob_start();
			require_once UAP_PATH . 'public/views/checkout_referral_select.php';
			$string = ob_get_contents();
			ob_end_clean();
		 }
		 if (!empty(self::$check_require_error)){
		 	$string .= '<div class="uap-register-notice">' . __('Please complete all required fields!', 'uap') . '</div>';
		 }
		 echo $output . $string;
	}

	public function check_require($errors){
	 	 global $indeed_db;
		 if (empty(self::$checkout_referrals_select_settings)){
		 	self::$checkout_referrals_select_settings = $indeed_db->return_settings_from_wp_option('checkout_select_referral');
		 }

		 /// REQUIRE
		 if (self::$checkout_referrals_select_settings['uap_checkout_select_referral_require']){
			 /// will not print 1, just to stop the form submiting
			 if (isset($_POST['uap_affiliate_username']) && $_POST['uap_affiliate_username']==''){
			 	$errors['uap_affiliate_username'] = 1;
			 	self::$check_require_error = TRUE;
			 } else if (isset($_POST['uap_affiliate_username_text']) && $_POST['uap_affiliate_username_text']==''){
			 	$errors['uap_affiliate_username_text'] = 1;
			 	self::$check_require_error = TRUE;
			 }
		 }

		 ///
		 if (isset($_POST['uap_affiliate_username_text']) && $_POST['uap_affiliate_username_text']!=''){
		 	 $affiliate_id = $indeed_db->get_affiliate_id_by_username($_POST['uap_affiliate_username_text']);
			 if (!$affiliate_id){
				 $errors['uap_affiliate_username_text'] = 1;
				 self::$check_require_error = TRUE;
			 }
		 }

		 return $errors;
	}

}
