<?php
if (!defined('ABSPATH')) exit();
if (class_exists('Uap_Ulp')) return;

class Uap_Ulp extends Referral_Main
{
    private $source_type = 'ulp';
    private static $checkout_referrals_select_settings = array();
    private static $check_require_error = FALSE;

    public function __construct()
    {
        add_action('ulp_new_order', array($this, 'create_referral'), 999, 3);
        add_action('ulp_order_update_the_status', array($this, 'update_referral'), 999, 2 );
        add_action('ulp_checkout_page_print_string_before_pay_bttn', array($this, 'insert_affiliate_select'), 999);
        add_filter('ulp_do_direct_payment_processing', array($this, 'check_require'), 999, 1);
        add_filter('ulp_payment_submited_filter_html', array($this, 'payment_submit'), 999, 1);
    }

    public function create_referral($orderId=0, $uid=0, $courseId=0)
    {
        if (empty($orderId)) return;

        self::$user_id = $uid;
    		$this->set_affiliate_id();

        $this->check_for_selected_affiliate();

        if (!$this->valid_referral()) return;

        ///get order details
        require_once ULP_PATH . 'classes/Db/DbUlpOrdersMeta.class.php';
        $DbUlpOrdersMeta = new DbUlpOrdersMeta();
        $data = $DbUlpOrdersMeta->getAllMetasAsArray($orderId);

        require_once UAP_PATH . 'public/Affiliate_Referral_Amount.class.php';
        $do_math = new Affiliate_Referral_Amount(self::$affiliate_id, $this->source_type);
        $sum = $do_math->get_result($data['amount'], $courseId);// input price, product id

        // insert referral
        $args = array(
            'refferal_wp_uid' => $data['user_id'],
            'campaign' => self::$campaign,
            'affiliate_id' => self::$affiliate_id,
            'visit_id' => self::$visit_id,
            'description' => '',
            'source' => $this->source_type,
            'reference' => $orderId,
            'reference_details' => '',
            'amount' => $sum,
            'currency' => self::$currency,
            'product_price' => $data['amount'],
        );
        $this->save_referral_unverified($args);
    }

    public function update_referral($orderId=0, $status='')
    {
      switch ($status){
        case 'complete':
        case 'ulp_complete':
            $this->referral_verified($orderId, $this->source_type);
            break;
        case 'pending':
        case 'ulp_pending':
        case 'fail':
        case 'ulp_fail':
        default:
            $this->referral_refuse($orderId, $this->source_type);
            break;
        }
    }

    //////////////// CHECKOUT REFERRAL SELECT

  	public function check_for_selected_affiliate()
    {
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

  	public function insert_affiliate_select()
    {
  		 global $indeed_db;
  		 $string = '';
  		 if (empty(self::$checkout_referrals_select_settings)){
  		 	self::$checkout_referrals_select_settings = $indeed_db->return_settings_from_wp_option('checkout_select_referral');
  		 }
  		 /// check it's enable
  		 if (self::$checkout_referrals_select_settings['uap_checkout_select_referral_enable']){
  		 	$this->set_affiliate_id();
  		 	if (self::$affiliate_id && !self::$checkout_referrals_select_settings['uap_checkout_select_referral_rewrite']){
  		 		return ''; /// OUT
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
  		 echo $string;
  	}

  	public function check_require($doIt=true)
    {
  		 if (empty(self::$checkout_referrals_select_settings)){
    		 	global $indeed_db;
    		 	self::$checkout_referrals_select_settings = $indeed_db->return_settings_from_wp_option('checkout_select_referral');
  		 }

  		 /// REQUIRE
  		 if (self::$checkout_referrals_select_settings['uap_checkout_select_referral_require']){
    			 /// will not print 1, just to stop the form submiting
    			 if (isset($_POST['uap_affiliate_username']) && $_POST['uap_affiliate_username']==''){
      			 	self::$check_require_error = true;
              $doIt = false;
    			 } else if (isset($_POST['uap_affiliate_username_text']) && $_POST['uap_affiliate_username_text']==''){
      			 	self::$check_require_error = true;
              $doIt = false;
    			 }
  		 }

  		 if (isset($_POST['uap_affiliate_username_text']) && $_POST['uap_affiliate_username_text']!=''){
  		 	 $affiliate_id = $indeed_db->get_affiliate_id_by_username($_POST['uap_affiliate_username_text']);
  			 if (!$affiliate_id){
    				 self::$check_require_error = true;
             $doIt = false;
  			 }
  		 }

       return $doIt;

  	}


    public function payment_submit($made=true)
    {
        if (!empty(self::$check_require_error)){
            return false;
        }
        return $made;
    }

}
