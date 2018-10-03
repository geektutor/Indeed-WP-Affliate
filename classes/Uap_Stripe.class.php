<?php
if (!class_exists('Uap_Stripe')):
	
class Uap_Stripe{
	private $settings = array();
	
	public function __construct(){
		/*
		 * @param none
		 * @return none
		 */
		 global $indeed_db;
		 $this->settings = $indeed_db->return_settings_from_wp_option('stripe');
		 require_once UAP_PATH  . 'classes/stripe/vendor/autoload.php';
	}
	
	public function do_payout($uid=0, $affiliate_id=0, $amount=0, $currency='usd'){
		/*
		 * @param int, int, float, string
		 * @return string 
		 */
		 global $indeed_db;
		 $amount = $amount * 100;
		 
		 if (empty($this->settings) || empty($this->settings['uap_stripe_enable'])){
		  	return '';	
		 }
		 
		 if ($this->settings['uap_stripe_sandbox']){
		 	$key = $this->settings['uap_stripe_sandbox_secret_key'];
		 } else {
		 	$key = $this->settings['uap_stripe_secret_key']; 
		 }
		 
		\Stripe\Stripe::setApiKey($key);
		
		$metas = $indeed_db->get_affiliate_payment_settings($uid, $affiliate_id, FALSE);		
		$user_email = $indeed_db->get_email_by_affiliate_id($affiliate_id);
		
		if (empty($metas['uap_affiliate_stripe_name']) || empty($metas['uap_affiliate_stripe_card_number'])
			|| empty($metas['uap_affiliate_stripe_expiration_month'])
			|| empty($metas['uap_affiliate_stripe_expiration_year']) || empty($metas['uap_affiliate_stripe_card_type']) //|| empty($metas['uap_affiliate_stripe_cvc'])
		) return '';
		
		/// card info
		try {
			$token = \Stripe\Token::create(
	            array(
		            	"card" => array(
			                "number" => $metas['uap_affiliate_stripe_card_number'],
			                "exp_month" => $metas['uap_affiliate_stripe_expiration_month'],
			                "exp_year" => $metas['uap_affiliate_stripe_expiration_year'],
			                //"cvc" => '',//$metas['uap_affiliate_stripe_cvc'],
	            		)
				)
	        );		
			indeed_debug_var($token->id);
		} catch (exception $e){}
		
		if (!empty($token) && !empty($token->id)){
			try {						
				$recipient = \Stripe\Recipient::create(
					 array(
						  "name" => $metas['uap_affiliate_stripe_name'],
						  "type" => $metas['uap_affiliate_stripe_card_type'],
						  "card" => $token->id,
						  "email" => $user_email
					  )
				);													
			} catch (exception $e){}

			if (!empty($recipient) && !empty($recipient->id) && !empty($recipient->default_card)){
				try {
					$transfer = \Stripe\Transfer::create(
						array(
							  "amount" => $amount, // amount in cents
							  "currency" => $currency,
							  "recipient" => $recipient->id,
							  "card" => $recipient->default_card,
							  //"statement_descriptor" => ''
						  )
					);		
				} catch (exception $e){}
				return (isset($transfer) && isset($transfer->id)) ? $transfer->id : '';
			}				
		}
		return '';
	}

	public function do_webhook(){
		/*
		 * @param none
		 * @return none
		 */
		$body = @file_get_contents('php://input');
		@$data = json_decode($body, TRUE);		
		if (!empty($data) && !empty($data['data']) && !empty($data['data']['object']) 
		&& !empty($data['data']['object']['id']) && !empty($data['data']['object']['status']) ){				
			$tr_id = $data['data']['object']['id'];
			$status = $data['data']['object']['status'];
			if (!empty($data['data']['object']['reversed'])){
				$status = 'reversed';
			}
			global $indeed_db;
			$indeed_db->update_transaction_stripe_status($tr_id, $status);				
		}
	}
	
}
	
endif;


