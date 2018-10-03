<?php
if (!class_exists('Uap_UMP')) :

class Uap_UMP extends Referral_Main{
	private $source_type = 'ump';
	private static $affiliate_from_coupon = 0;
	private static $checkout_referrals_select_settings = array();
	private static $check_require_error = FALSE;
	protected static $coupon_code = '';

	public function __construct(){
		/*
		 * @param none
		 * @return none
		 */
		/// THE HOOKS
		add_action('ump_payment_check', array($this, 'check_referral'), 1, 2);
		add_action('ump_coupon_code_submited', array($this, 'check_coupon_code'), 1, 1);

		/// CHECKOUT REFERRALS SELECT
		add_filter('ump_before_submit_form', array($this, 'insert_affiliate_select'), 1, 2);
		add_filter('ump_before_printing_errors', array($this, 'check_require'), 1, 1);

		///Refunded
		add_action('ump_paypal_user_do_refund', array($this, 'make_referral_refuse'), 12, 3);
	}


	/**
	 * @param int
 	 * @param int
	 * @return none
	 */
	public function make_referral_refuse($uid=0, $lid=-1, $transaction_id=''){
		if ($uid && $lid>-1 && $transaction_id){
				try {
					 $order_id = Ihc_Db::get_order_id_by_meta_value_and_meta_type('txn_id', $transaction_id);
				} catch (Exception $e){}

				if (!empty($order_id)){
						$this->referral_refuse($order_id, $this->source_type);
				}
		}
	}


	public function check_referral($order_id=0, $type=''){
		/*
		 * @param int (order id)
		 * @return none
		 */
		if ($order_id){
			require_once IHC_PATH . 'classes/Orders.class.php';
			$object = new Ump\Orders();
			$data = $object->get_data($order_id);
			if ($type=='insert'){
				/// INSERT
				if (isset($data['automated_payment']) && $data['automated_payment']==2){
					self::$special_payment_type = 'reccuring';
				}
				$this->create_referral_affiliate_relation($data['uid'], $data['lid'], $data['amount_value'], $order_id);
			}

			/// UPDATE
			if (isset($data['status']) && $data['status']!='pending'){
				if ($data['status']=='Completed'){
					$this->referral_verified($order_id, $this->source_type);
				} else {
					$this->referral_refuse($order_id, $this->source_type);
				}
			}
		}
	}

	public function create_referral_affiliate_relation($uid=0, $lid=0, $amount=0, $referrence=''){
		/*
		 * @param int, int, double
		 * @return none
		 */
		if ($uid && isset($lid) && isset($amount)){
			self::$user_id = $uid;
			$this->set_affiliate_id();

			/// COUPON
			if (empty(self::$affiliate_id) && !empty(self::$affiliate_from_coupon)){
				self::$affiliate_id = self::$affiliate_from_coupon;
			}
			/// COUPON

			///CHECKOUT REFERRAL SELECT
			$this->check_for_selected_affiliate();
			///CHECKOUT REFERRAL SELECT

			if ($this->valid_referral()){
				require_once UAP_PATH . 'public/Affiliate_Referral_Amount.class.php';
				$do_math = new Affiliate_Referral_Amount(self::$affiliate_id, $this->source_type, self::$special_payment_type, self::$coupon_code);
				$sum = $do_math->get_result($amount, $lid);// input price, product id
				$args = array(
						'refferal_wp_uid' => self::$user_id,
						'campaign' => self::$campaign,
						'affiliate_id' => self::$affiliate_id,
						'visit_id' => self::$visit_id,
						'description' => '',
						'source' => $this->source_type,
						'reference' => $referrence,
						'reference_details' => 'From UMP',
						'amount' => $sum,
						'currency' => self::$currency,
						'product_price' => $amount,
				);
				$this->save_referral_unverified($args);
			}
		}
	}

	public function check_coupon_code($code=''){
		/*
		 * it will set the affiliate_id
		 * @param string
		 * @return none
		 */
		 if (!empty($code)){
		 	global $indeed_db;
	 		$affiliate = $indeed_db->get_affiliate_for_coupon_code($code);

			if ($affiliate){
				self::$affiliate_from_coupon = $affiliate;
				self::$special_payment_type = 'coupon';
				self::$coupon_code = $code;
			}
		 }
	}


	//////////////// CHECKOUT REFERRAL SELECT

	public function check_for_selected_affiliate(){
		/*
		 * @param none
		 * @return none
		 */
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
		/*
		 * @param string, bool
		 * @return string
		 */
		 global $indeed_db;
		 $string = '';
		 if (empty(self::$checkout_referrals_select_settings)){
		 	self::$checkout_referrals_select_settings = $indeed_db->return_settings_from_wp_option('checkout_select_referral');
		 }
		 /// check it's enable
		 if (self::$checkout_referrals_select_settings['uap_checkout_select_referral_enable'] && $is_public){
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
		 	$string .= '<div class="ihc-register-notice">' . __('Please complete all required fields!', 'ihc') . '</div>';
		 }
		 return $output . $string;
	}

	public function check_require($errors){
		/*
		 * @param array
		 * @return array
		 */
		 if (empty(self::$checkout_referrals_select_settings)){
		 	global $indeed_db;
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

endif;
