<?php
require_once UAP_PATH . 'classes/paypal/vendor/autoload.php';
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

if (!class_exists('Uap_PayPal')):

class Uap_PayPal{
	private $apiContext;
	private $is_sandbox = 1;
	private $senders = array();
	private $client_id;
	private $client_secret;

	public function __construct(){
		/*
		 * @param none
		 * @return none
		 */
		date_default_timezone_set(@date_default_timezone_get());
		$this->is_sandbox = get_option('uap_paypal_sandbox');
		if ($this->is_sandbox){
			$this->client_id = get_option('uap_paypal_sandbox_client_id');
			$this->client_secret = get_option('uap_paypal_sandbox_client_secret');
		} else {
			$this->client_id = get_option('uap_paypal_client_id');
			$this->client_secret = get_option('uap_paypal_client_secret');
		}
		$this->set_api_context();
	}

	private function set_api_context($client_id='', $client_secret=''){
		/*
		 * @param string, string
		 * @return none
		 */
		 if (!$this->client_id || !$this->client_secret){
		 	return FALSE;
		 }
		$this->apiContext = new ApiContext(
				new OAuthTokenCredential(
						$this->client_id,
						$this->client_secret
				)
		);
		$config = array(
				//'log.LogEnabled' => true,
				//'log.FileName' => '../PayPal.log',
				//'cache.enabled' => true,
				'log.LogEnabled' => false,
				'log.FileName' => '',
				'log.LogLevel' => 'DEBUG',
				'cache.enabled' => false,
		);
		if ($this->is_sandbox){
			$config['mode'] = 'sandbox';
		} else {
			$config['mode'] = 'live';
		}
		$this->apiContext->setConfig($config);
	}

	public function add_payment($email='', $amount=0, $currency='USD'){
		/*
		 * @param string, double, string
		 * @return none
		 */
		$new_sender = new \PayPal\Api\PayoutItem();
		$new_sender->setRecipientType('Email')
							->setNote('Affiliate Payment')
							->setReceiver($email)
							->setSenderItemId(uniqid())
							->setAmount(new \PayPal\Api\Currency('{
									"value":"' . $amount . '",
									"currency":"' . $currency . '"
							}'));
		$this->senders[] = $new_sender;
	}

	public function do_payout(){
		/*
		 * @param none
		 * @return string
		 */
		if ($this->senders && $this->apiContext){
			try {
				$payouts = new \PayPal\Api\Payout();
				$senderBatchHeader = new \PayPal\Api\PayoutSenderBatchHeader();
				$senderBatchHeader->setSenderBatchId(uniqid())
				->setEmailSubject("You have a payment");
				$payouts->setSenderBatchHeader($senderBatchHeader);
				foreach ($this->senders as $sender){
					$payouts->addItem($sender);
				}
				$output = $payouts->create(null, $this->apiContext);

				if (!empty($output) && !empty($output->batch_header) && !empty($output->batch_header->payout_batch_id)){
					return $output->batch_header->payout_batch_id;
				}
			} catch (Exception $e){}
			return '';
		}
		return '';
	}

	public function get_status($payout_batch_id=''){
		/*
		 * @param string
		 * @return string
		 */
		$status = \PayPal\Api\Payout::get($payout_batch_id, $this->apiContext);
		if (!empty($status->items) && !empty($status->items[0]) && !empty($status->items[0]->transaction_status)){
			return $status->items[0]->transaction_status;
		}
		return '';
	}

}

endif;
