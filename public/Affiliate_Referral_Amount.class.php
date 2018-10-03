<?php 
if (!class_exists('Affiliate_Referral_Amount')):

class Affiliate_Referral_Amount{
	private static $affiliate_id;
	private static $source;
	private static $rule;
	private static $offer_array;
	private static $rank_settings;
	private static $special_payment_type = FALSE;
	private static $coupon_code = '';
	private $input_amount;
	private $product_id;
	//private $output_amount = 0;
	private $rank_output_amount = FALSE;//0;
	private $offer_output_amount = FALSE;//0;
		
	public function __construct($affiliate_id=0, $source='', $special_payment_type='', $coupon_code=''){
		/*
		 * @param int, float, [string], [string]
		 * @return none
		 */
		if (empty($affiliate_id)){
			return;
		} else {
			self::$affiliate_id = $affiliate_id;
		}
		self::$source = (empty($source)) ? '' : $source;
		self::$special_payment_type = $special_payment_type;
		self::$rule = get_option('uap_referral_offer_type');
		if (empty(self::$rule)){
			self::$rule = 'lowest';
		}
		self::$coupon_code = $coupon_code;
		self::$offer_array = $this->set_offers();
		self::$rank_settings = $this->set_rank_settings();
	}
	
	private function set_offers(){
		/*
		 * @param none
		 * @return array
		 */
		global $indeed_db;
		$data = $indeed_db->get_offer_id_by_affiliate_id_and_source(self::$affiliate_id, self::$source);
		return $data;
	}
	
	private function set_rank_settings(){
		/*
		 * @param none
		 * @return array
		 */
		global $indeed_db;
		$rank = $indeed_db->get_affiliate_rank(self::$affiliate_id);
		if ($rank){
			$data = $indeed_db->get_rank($rank);
			return $data;
		}
		return array();
	}
	
	public function get_result($input_amount=0, $product_id=''){
		/*
		 * @param float, string
		 * @return float
		 */
		$this->input_amount = $input_amount;
		$this->product_id = $product_id;
		
		/// << COUPON >> 
		if (self::$coupon_code){
			$value = $this->calculate_value_from_coupon();
			if ($value){
				$value = round($value, 3);
				return $value;
			}
		}
		/// << COUPON >>		
		
		$this->set_output_amount_by_rank();
		$this->set_output_amount_by_offer();
		
		if ($this->rank_output_amount!==FALSE && $this->offer_output_amount!==FALSE){
			/// compare values
			if (self::$rule=='biggest'){
				$return_value = max($this->rank_output_amount, $this->offer_output_amount);
				$return_value = round($return_value, 3);			
			} else {
				$return_value = min($this->rank_output_amount, $this->offer_output_amount);
				$return_value = round($return_value, 3);			
			}	
			return $return_value;	
		} else if ($this->rank_output_amount!==FALSE){
			/// rank value
			return $this->rank_output_amount;
		} else if ($this->offer_output_amount!==FALSE){
			/// offer value
			return $this->offer_output_amount;
		}
		return 0;
	}
	
	private function set_output_amount_by_rank(){
		/*
		 * @param none
		 * @return none
		 */
		if (self::$rank_settings){			
			if (self::$special_payment_type=='lifetime'){
				/// LIFETIME
				if (isset(self::$rank_settings['lifetime_amount_value']) && self::$rank_settings['lifetime_amount_value']>=0){
					$amount_value = self::$rank_settings['lifetime_amount_value'];
					$amount_type = self::$rank_settings['lifetime_amount_type']; 
				}
			} else if (self::$special_payment_type=='reccuring'){
				/// RECCURING	
				if (isset(self::$rank_settings['reccuring_amount_value']) && self::$rank_settings['reccuring_amount_value']>=0){
					$amount_value = self::$rank_settings['reccuring_amount_value'];
					$amount_type = self::$rank_settings['reccuring_amount_type']; 					
				}		
			}
			
			/// DEFAULT RANK VALUE
			if (empty($amount_value)){
				$amount_value = self::$rank_settings['amount_value'];
				$amount_type = self::$rank_settings['amount_type']; 					
			}
					
			if ($amount_type=='percentage'){
				//rank percentage
				$this->rank_output_amount = $amount_value * $this->input_amount / 100;
			} else {
				//rank flat
				$this->rank_output_amount = $amount_value;
			}
				
		}
	}
	
	private function set_output_amount_by_offer(){
		/*
		 * @param none
		 * @return none
		 */
		if (self::$offer_array && is_array(self::$offer_array) && count(self::$offer_array)>0){
			foreach (self::$offer_array as $offer_array){
				if (!empty($offer_array['products'])){
					$only_products = explode(',', $offer_array['products']);					
				} else {
					continue;
				}
				
				if ( !in_array($this->product_id, $only_products)){
					continue;
				}
				if ($offer_array['amount_type']=='percentage'){
					//rank percentage
					$value = $offer_array['amount_value'] * $this->input_amount / 100;
				} else {
					//offer flat
					$value = $offer_array['amount_value'];
				}	
				if (isset($output_value)){
					if (self::$rule=='biggest'){
						$output_value = max($value, $output_value);
					} else {
						$output_value = min($value, $output_value);
					}
				} else {
					$output_value = $value;
				}
			}//end foreach
			if (isset($output_value)){
				$this->offer_output_amount = $output_value;
			}
			//$this->offer_output_amount = (isset($output_value)) ? $output_value : 0;
		}
	}
	
	public function get_signup_amount(){
		/*
		 * @param none
		 * @return int
		 */
		/// DEFAULT AMOUNT
		$amount = get_option('uap_sign_up_amount_default');
		if ($amount===FALSE){
			$amount = 0;
		}		
		/// AMOUNT BY RANK
		if (self::$rank_settings){
			if (!empty(self::$rank_settings['sign_up_amount_value']) && self::$rank_settings['sign_up_amount_value']>=0){
				$amount	= floatval(self::$rank_settings['sign_up_amount_value']);		
			}
		}
		return $amount;
	}
	
	public function calculate_value_from_coupon(){
		/*
		 * @param none
		 * @return number
		 */
		global $indeed_db;
		$data = $indeed_db->get_coupon_data(self::$coupon_code);
		if ($data){
			if ($data['amount_type']=='percentage'){
				$return_value = $data['amount_value'] * $this->input_amount / 100;
			} else {
				$return_value = $data['amount_value'];
			}			
			return $return_value;
		}
		return 0;
	}
		
}

endif;