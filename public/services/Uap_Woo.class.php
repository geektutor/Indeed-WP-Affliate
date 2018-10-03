<?php
if (!class_exists('Uap_Woo')) :

class Uap_Woo extends Referral_Main{
	private $source_type = 'woo';
	private static $checkout_referrals_select_settings = array();
	protected static $coupon_code = '';

	public function __construct(){
		/*
		 * @param none
		 * @return none
		 */
		/// THE HOOKS
		add_action('woocommerce_checkout_order_processed', array($this, 'create_referral'));
		add_action('woocommerce_order_status_completed', array($this, 'make_referral_verified'));
		add_action('woocommerce_order_status_pending_to_cancelled', array($this, 'make_referral_refuse'));
		add_action('woocommerce_order_status_completed_to_refunded', array($this, 'make_referral_refuse'));
		add_action('woocommerce_order_status_pending_to_failed', array($this, 'make_referral_refuse'));
		add_action('woocommerce_order_status_on-hold_to_refunded', array($this, 'make_referral_refuse'));
		add_action('woocommerce_order_status_processing_to_refunded', array($this, 'make_referral_refuse'));
		add_action('woocommerce_order_status_processing_to_cancelled', array($this, 'make_referral_refuse'));
		add_action('woocommerce_order_status_completed_to_cancelled', array($this, 'make_referral_refuse'));
		add_action('wc-on-hold_to_trash', array($this, 'make_referral_refuse'));
		add_action('wc-processing_to_trash', array($this, 'make_referral_refuse'));
		add_action('wc-completed_to_trash', array($this, 'make_referral_refuse'));

		/// CHECKOUT REFERRALS SELECT
		add_action('woocommerce_after_order_notes', array($this, 'insert_affiliate_select'));
		add_action('woocommerce_checkout_process', array( $this, 'checking_affiliate_select'));

		//// SUBSCRIPTION
		add_action('woocommerce_customer_changed_subscription_to_active', array($this, 'make_wcs_referral_verified'), 999, 2);
		add_action('woocommerce_customer_changed_subscription_to_cancelled', array($this, 'make_wcs_referral_refuse'), 999, 2);
		add_filter('wcs_new_order_created', array($this, 'create_referral_and_return_renewal_order'), 999, 3);

	}


	public function create_referral($order_id=0){
		/*
		 * @param int (order id)
		 * @return none
		 */
		if (empty($order_id)){
			return; // out
		}
		$order = new WC_Order($order_id);
		self::$user_id = (int)$order->user_id;

		if (empty(self::$affiliate_id)){
			/// let's check the coupon...
			$this->check_coupon($order);
		}

		$this->set_affiliate_id();

		///CHECKOUT REFERRAL SELECT
		$this->check_for_selected_affiliate();
		///CHECKOUT REFERRAL SELECT

		if ($this->valid_referral()){
			// it's valid

			/// tax & shipping settings
			global $indeed_db;
			$temp_data = $indeed_db->return_settings_from_wp_option('general-settings');
			$exclude_shipping = (empty($temp_data['uap_exclude_shipping'])) ? FALSE : TRUE;
			$exclude_tax = (empty($temp_data['uap_exclude_tax'])) ? FALSE : TRUE;

			/// calculate the amount object
			require_once UAP_PATH . 'public/Affiliate_Referral_Amount.class.php';
			$do_math = new Affiliate_Referral_Amount(self::$affiliate_id, $this->source_type, self::$special_payment_type, self::$coupon_code);

			if (!empty(self::$coupon_code)){
				$temp_coupon_data = $indeed_db->get_coupon_data(self::$coupon_code);
				if ($temp_coupon_data['amount_type']=='flat'){
					$run_foreach_line_once = TRUE;
				}
			}

			$items = $order->get_items();
			$shipping = $order->get_total_shipping();
			if ($shipping){
				@$shipping_per_item = $shipping / count($items);
			} else {
				$shipping_per_item = 0;
			}
			$sum = 0;
			$product_price_sum = 0;
			foreach ($items as $item){ /// foreach in lines
				$products_arr[] = $item['product_id'];

				///base price
				$product_price = round($item['line_total'], 3);

				///add shipping if it's case
				if (!empty($shipping_per_item) && !$exclude_shipping){
					$product_price += round($shipping_per_item, 3);
				}

				/// add taxes if it's case
				if (!empty($item['line_tax']) && !$exclude_tax){
					$product_price += round($item['line_tax'], 3);
				}

				$product_price_sum += $product_price;

				/// get amount
				$temp_amount = $do_math->get_result($product_price, $item['product_id']);// input price, product id
				$sum += $temp_amount;

				if (!empty($run_foreach_line_once)){
					/// user for coupon flat amount!
					break;
				}
			}
			if (!empty($products_arr)){
				$product_list = implode(',', $products_arr);
			} else {
				$product_list = '';
			}

			$args = array(
							'refferal_wp_uid' => self::$user_id,
							'campaign' => self::$campaign,
							'affiliate_id' => self::$affiliate_id,
							'visit_id' => self::$visit_id,
							'description' => '',
							'source' => $this->source_type,
							'reference' => $order_id,
							'reference_details' => $product_list,
							'amount' => $sum,
							'currency' => self::$currency,
							'product_price' => $product_price_sum,
			);
			$this->save_referral_unverified($args);
		}
	}

	public function make_referral_verified($order_id=0){
		/*
		 * @param int
		 * @return none
		 */
		if ($order_id){
			$this->referral_verified($order_id, $this->source_type);
		}
	}

	public function make_referral_refuse($order_id=0){
		/*
		 * @param int
		 * @return none
		 */
		if ($order_id){
			if (is_object($order_id)){
				$order_id = (isset($order_id->ID)) ? $order_id->ID : 0;
			}
			$this->referral_refuse($order_id, $this->source_type);
		}
	}

	private function check_coupon($order_object){
		/*
		 * check if coupon has a affiliate on it
		 * @param object
		 * @return none
		 */
		 if ($order_object){
		 	 $coupons_arr = $order_object->get_used_coupons();
			 if (!empty($coupons_arr)){
			 	global $indeed_db;
			 	foreach ($coupons_arr as $coupon){
			 		$affiliate = $indeed_db->get_affiliate_for_coupon_code($coupon);
					if ($affiliate){
						self::$affiliate_id = $affiliate;
						self::$special_payment_type = 'coupon';
						self::$coupon_code = $coupon;
					}
			 	}
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

	public function insert_affiliate_select(){
		/*
		 * @param none
		 * @return none
		 */
		 global $indeed_db;
		 if (empty(self::$checkout_referrals_select_settings)){
		 	self::$checkout_referrals_select_settings = $settings = $indeed_db->return_settings_from_wp_option('checkout_select_referral');
		 }
		 /// check it's enable
		 if (self::$checkout_referrals_select_settings['uap_checkout_select_referral_enable']){
		 	$this->set_affiliate_id();
		 	if (self::$affiliate_id && !self::$checkout_referrals_select_settings['uap_checkout_select_referral_rewrite']){
		 		return; /// OUT
		 	}
			$who = self::$checkout_referrals_select_settings['uap_checkout_select_affiliate_list'];
			$type = self::$checkout_referrals_select_settings['uap_checkout_select_referral_name'];
			$data['affiliates'] = $indeed_db->get_affiliates_for_checkout_select($who, $type);
			$data['require'] = (self::$checkout_referrals_select_settings['uap_checkout_select_referral_require']) ? '<abbr class="required" title="required">*</abbr>' : '';
			$data['class'] = 'form-row form-row';
			if ($data['require']){
				$data['class'] .= ' validate-required';
			}
			$data['select_class'] = '';
			$data['input_class'] = '';
			$data['require_on_input'] = '';
			require_once UAP_PATH . 'public/views/checkout_referral_select.php';
		 }
	}

	public function checking_affiliate_select(){
		/*
		 * @param none
		 * @return none
		 */
		 global $indeed_db;
		 if (empty(self::$checkout_referrals_select_settings)){
		 	self::$checkout_referrals_select_settings = $indeed_db->return_settings_from_wp_option('checkout_select_referral');
		 }
		 if (self::$checkout_referrals_select_settings['uap_checkout_select_referral_enable'] && self::$checkout_referrals_select_settings['uap_checkout_select_referral_require']){
		 	if (isset($_POST['uap_affiliate_username']) && $_POST['uap_affiliate_username']==''){
		 		$error = TRUE;
		 	} else if (isset($_POST['uap_affiliate_username_text']) && $_POST['uap_affiliate_username_text']==''){
		 		$error = TRUE;
		 	}
			 if (!empty($error)){
				 wc_add_notice(__('Please complete all required fields!', 'uap'), 'error');
			 }
		 }
	}


	//// WCS
	public function create_referral_and_return_renewal_order($new_order, $subscription, $type){
		if (isset($new_order->id)){
			$this->create_referral($new_order->id);
		}
		return $new_order;
	}

	public function make_wcs_referral_verified($new_status, $subscription){
		$this->make_referral_verified($subscription);
	}

	public function make_wcs_referral_refuse($new_status, $subscription){
		$this->make_referral_refuse($subscription);
	}

}

endif;
