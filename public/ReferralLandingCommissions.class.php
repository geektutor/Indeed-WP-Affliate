<?php 
if (!class_exists('ReferralLandingCommissions') && class_exists('Referral_Main')) : 

class ReferralLandingCommissions extends Referral_Main{

	public function __construct($slug='', $uid=0){
		/*
		 * @param string, int
		 * @return none
		 */
		 if ($slug){
		 	global $indeed_db;
			self::$user_id = $uid;
			$this->set_affiliate_id();
			$data = $indeed_db->get_landing_commission($slug);			
			if (empty($data['id']) || empty($data['status']) || empty(self::$affiliate_id)){
				return;
			}
			
			/// <<< check cookie >>>
			$cookie_key = 'uaplandingcommission_' . $data['id'];
		 	if (!empty($_COOKIE[$cookie_key])){
				return;
		 	}			
			/// <<< check cookie >>>
			
			if (!$this->valid_referral()){
				return;	
			}
			
			$referrence = 'ref_' . $data['id'] . '_' . self::$user_id . '_' . self::$affiliate_id . '_' . time();
			$source = (empty($data['source'])) ? 'from landing commissions' : $data['source'];	
			require_once UAP_PATH . 'public/Affiliate_Referral_Amount.class.php';
			$do_math = new Affiliate_Referral_Amount(self::$affiliate_id, $source, self::$special_payment_type);
			
			$amount_to_calculate = $data['amount_value'];
			if (isset($_REQUEST['lc_amount']) && is_numeric($_REQUEST['lc_amount'])){
				$amount_to_calculate = $_REQUEST['lc_amount'];
			}

			$sum = $do_math->get_result($amount_to_calculate, '');// input price, product id
			$args = array(
						'refferal_wp_uid' => self::$user_id,
						'campaign' => self::$campaign,
						'affiliate_id' => self::$affiliate_id,
						'visit_id' => self::$visit_id,
						'description' => @$data['description'],
						'source' => $source,
						'reference' => $referrence,
						'reference_details' => '',
						'amount' => $sum,
						'currency' => self::$currency,
			);
			$this->save_referral_unverified($args);
			if ($data['default_referral_status']==2){
				$this->referral_verified($referrence, '', FALSE);
			}
			
			if (!isset($data['cookie_expire'])){ /// for older version
				$data['cookie_expire'] = 0;
			}

			$this->set_cookie($data['cookie_expire'], $data['id']);		
		 }
	}

	private function set_cookie($expire = 0, $shortcode_id = 0){
		/*
		 * @param int (expire time)
		 * @return none (print some javscript)
		 */
		 if ($expire && $shortcode_id){
		 	?>
		 	<script>
		 		var expire = <?php echo $expire;?>;
		 		if (expire) {
       				var date = new Date();
        			date.setTime(date.getTime()+(expire * 60 * 60 * 1000));
        			var e = date.toGMTString();
    			} else {
    				var date = new Date();
    				var e = date.toGMTString();
    			} 
    			document.cookie = '<?php echo 'uaplandingcommission_' . $shortcode_id . '=true';?>; expires=' + e + '; path=/';
		 	</script>
		 	<?php
		 }
	}
	
}
	
endif;