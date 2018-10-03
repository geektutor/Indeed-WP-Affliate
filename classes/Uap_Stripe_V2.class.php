<?php
if (!class_exists('Uap_Stripe_V2')):

class Uap_Stripe_V2{
	private $settings = array();
	private $secret_key = '';
	private $errorMessage = '';


	/*
	 * @param none
	 * @return none
	 */
	public function __construct(){
		 global $indeed_db;
		 $this->settings = $indeed_db->return_settings_from_wp_option('stripe_v2');
		 if ($this->settings['uap_stripe_v2_sandbox']){
		 	$this->secret_key = $this->settings['uap_stripe_v2_sandbox_secret_key'];
		 } else {
		 	$this->secret_key = $this->settings['uap_stripe_v2_secret_key'];
		 }
		 require_once UAP_PATH  . 'classes/stripe/vendor/autoload.php';
	}


	/*
	 * @param int
	 * @return boolean
	 */
	public function register_user($uid=0){
		if ($uid){
			$user_data = get_user_meta($uid, 'stripe_v2_meta_data', TRUE);
			\Stripe\Stripe::setApiKey($this->secret_key);
			$the_user_array = $this->make_user_array($user_data);
			if ($the_user_array!==FALSE){
				$acct_id = get_user_meta($uid, 'ihc_stripe_connected_account_id', TRUE);
				if (!empty($acct_id)){
					/// update
					$the_account_object = \Stripe\Account::retrieve($acct_id);
					if (isset($the_user_array['external_account'])){
						$the_account_object->external_account = $this->return_three_level_array_as_object($the_user_array['external_account']);
					}
					if (isset($the_user_array['legal_entity'])){
						$the_account_object->legal_entity = $this->return_three_level_array_as_object($the_user_array['legal_entity']);
					}
					$the_account_object->save();
					return $the_account_object->id;
				} else {
					/// insert

				unset($the_user_array['managed'] );

				global $indeed_db;
				$the_user_array['type'] = "standard";
				$the_user_array['email'] = $indeed_db->get_email_by_uid($uid);

				try {
						$acct = \Stripe\Account::create($the_user_array);
				} catch (exception $e){
						$this->errorMessage = $e->getMessage();
				}

					if (isset($acct->id)){
						/// upload document
						$this->do_upload_document($uid, $acct->id);
						update_user_meta($uid, 'ihc_stripe_connected_account_id', $acct->id);

						return $acct->id;
					}
				}
			}
		}
		return FALSE;
	}


	public function getErrorMessage()
	{
			return $this->errorMessage;
	}


	/*
	 * @param int, string
	 * @return none
	 */
	private function do_upload_document($uid=0, $stripe_account_id='', $photo=''){
		 if (empty($stripe_account_id)){
		 	 $stripe_account_id = get_user_meta($uid, 'stripe_v2_meta_data', TRUE);
		 }
		 if ($stripe_account_id){
		 	 if (empty($photo)){
			 	 $temp = get_user_meta($uid, 'stripe_v2_meta_data', TRUE);
				 if (!empty($temp['verification_document'])){
					 $photo = $temp['verification_document'];
					 $photo = wp_get_attachment_url($photo);
				 }
		 	 }
			 if (!empty($photo)){
			 	try {
					 \Stripe\Stripe::setApiKey($this->secret_key);
					 \Stripe\FileUpload::create(
											  array(
												    "purpose" => "identity_document",
												    "file" => fopen($photo, 'r')
											  ),
											  array("stripe_account" => $stripe_account_id)
					);
			 	} catch (exception $e){}
			 }
		 }
	}


	/*
	 * @param int, int, float, string
	 * @return string
	 */
	public function do_payout($uid=0, $affiliate_id=0, $amount=0, $currency='usd'){
		global $indeed_db;
		$amount = $amount * 100;
		if (empty($this->settings) || empty($this->settings['uap_stripe_v2_enable'])){
		  	return '';
		}

		if (empty($uid)){
			$uid = $indeed_db->get_uid_by_affiliate_id($affiliate_id);
			if (empty($uid)){
				return '';
			}
		}

		$user_stripe_id_account = get_user_meta($uid, 'ihc_stripe_connected_account_id', TRUE);
		if (!$user_stripe_id_account){
			$this->register_user($uid);
		}

		if ($user_stripe_id_account){
			global $indeed_db;
			$site_name = get_option('blogname');
			$username = $indeed_db->get_username_by_wpuid($uid);
			try {
				\Stripe\Stripe::setApiKey($this->secret_key);
				$transfer_details = \Stripe\Transfer::create(array(
				  "amount" => $amount,
				  "currency" => $currency,
				  "destination" => $user_stripe_id_account,
				  "description" => __("From ", 'uap') . $site_name . __(" to ", 'uap') . $username . '.',
				));
			} catch (exception $e){}

			if (isset($transfer_details->id)){
				return $transfer_details->id;
			}
		}
		return '';
	}


	/*
	 * @param array
	 * @return array || boolean
	 */
	private function make_user_array($user_data=array()){
		if (!$this->check_input_from_user($user_data)){
			return FALSE;
		}
		$currency = get_option('uap_currency');
		$the_user_array = array(
			  "managed" => TRUE,
			  "country" => $user_data['country'],
			  "tos_acceptance" => array(
									    "date" => time(),
									    "ip" => $_SERVER['REMOTE_ADDR'],
			  ),
		);
		switch ($user_data['country']){
			case 'us':
				$the_user_array['external_account'] = array(
					    "object" => "bank_account",
					    "country" => $user_data['country'],
					    "currency" => $currency,/// $user_data['currency']
					    "routing_number" => $user_data['routing_number'],
					    "account_number" => $user_data['account_number'],
			  	);
				$the_user_array['legal_entity'] = array(
						'type' => $user_data['user_type'], /// 'individual' or 'company'
						'dob' => array(
										'day' => $user_data['day'],
										'month' => $user_data['month'],
										'year' => $user_data['year'],
						),
						'first_name' => $user_data['first_name'],
						'last_name' => $user_data['last_name'],
						'address' => array(
										'city' => $user_data['city'],
										'line1' => $user_data['line1'],
										'postal_code' => $user_data['postal_code'],
										'state' => $user_data['state'],
						),
						'ssn_last_4' => $user_data['ssn_last_4'],
						'personal_id_number' => $user_data['personal_id_number'],
				);
				if ($user_data['user_type']=='company'){
					/// INDIVIDUAL
					$the_user_array['legal_entity']['business_name'] = $user_data['business_name'];
					$the_user_array['legal_entity']['business_tax_id'] = $user_data['business_tax_id'];
				}
				break;

			case 'gb':
			case 'dk':
			case 'fr':
			case 'de':
			case 'be':
			case 'it':
			case 'ch':
			case 'at':
			case 'fi':
			case 'nl':
			case 'no':
			case 'se':
			case 'es':
			case 'ie':
			case 'lu':
			case 'pt':
				$the_user_array['external_account'] = array(
				    "object" => "bank_account",
				    "country" => $user_data['country'],
				    "currency" => $currency,
				    "routing_number" => $user_data['routing_number'],
				    "account_number" => $user_data['account_number'],
			  	);
				$the_user_array['legal_entity'] = array(
					'type' => $user_data['user_type'], /// 'individual' or 'company'
					'dob' => array(
									'day' => $user_data['day'],
									'month' => $user_data['month'],
									'year' => $user_data['year'],
					),
					'first_name' => $user_data['first_name'],
					'last_name' => $user_data['last_name'],
					'address' => array(
									'city' => $user_data['city'],
									'line1' => $user_data['line1'],
									'postal_code' => $user_data['postal_code'],
					),
					'additional_owners' => $user_data['additional_owners'],	/// can be null
				);
				if ($user_data['user_type']=='company'){
					/// INDIVIDUAL
					$the_user_array['legal_entity']['business_name'] = $user_data['business_name'];
					$the_user_array['legal_entity']['business_tax_id'] = $user_data['business_tax_id'];
					$the_user_array['legal_entity']['personal_address'] = array(
																'city' => $user_data['personal_address.city'],
																'line1' => $user_data['personal_address.line1'],
																'postal_code' => $user_data['personal_address.postal_code'],
					);
				}
				break;
		}
		return $the_user_array;
	}


	/*
 	 * Check if user has completed all the required fields
	 * @param array
	 * @return boolean
	 */
	private function check_input_from_user($arr=array()){
		 if (isset($arr['country'])){
		 	switch ($arr['country']){
				case 'us':
					$fields = array(
									'individual' => array('routing_number', 'account_number', 'day', 'month', 'year', 'first_name', 'last_name', 'city',
														 'line1', 'postal_code', 'state', 'ssn_last_4', 'personal_id_number'),
									'company' => array('routing_number', 'account_number', 'day', 'month', 'year', 'first_name', 'last_name', 'city',
														 'line1', 'postal_code', 'state', 'ssn_last_4', 'personal_id_number', 'business_name', 'business_tax_id'),
					);
					break;
				case 'gb':
				case 'dk':
				case 'fr':
				case 'de':
				case 'be':
				case 'it':
				case 'ch':
				case 'at':
				case 'fi':
				case 'nl':
				case 'no':
				case 'se':
				case 'es':
				case 'ch':
				case 'ie':
				case 'lu':
				case 'pt':
					$fields = array(
									'individual' => array(
															'routing_number',
															'account_number',
															'day',
															'month',
															'year',
															'first_name',
															'last_name',
															'city',
														 	'line1',
														 	'postal_code'
									),
									'company' => array(
														'routing_number',
														'account_number',
														'day',
														'month',
														'year',
														'first_name',
														'last_name',
														'city',
														'line1',
														'postal_code',
														'business_name',
														'business_tax_id',
														'personal_address.city',
														'personal_address.line1',
														'personal_address.postal_code',
														'additional_owners',
									),
					);
					break;
		 	}
		 }
		 if (isset($fields)){
		 	 $type = isset($arr['user_type']) ? $arr['user_type'] : '';
			 if (isset($fields[$type])){
			 	 foreach ($fields[$type] as $key=>$array_key){
			 	 	 if (!isset($arr[$array_key]) || $arr[$array_key]==''){
			 	 	 	 return FALSE;
			 	 	 }
			 	 }
				 return TRUE;
			 }
		 }
		 return FALSE;
	}


	/*
	 * @param array
	 * @return object
	 */
	private function return_three_level_array_as_object($first_arr=array()){
		foreach ($first_arr as $first_key=>$first_value){
			if (is_array($first_value)){
				foreach ($first_value as $second_key=>$second_value){
					if (is_array($second_value)){
						foreach ($second_value as $third_key=>$third_value){
							if (is_array($third_value)){
								$first_arr[$first_key][$second_key][$third_key] = (object)$first_arr[$first_key][$second_key][$third_key];
							}
						}
						$first_arr[$first_key][$second_key] = (object)$first_arr[$first_key][$second_key];
					}
				}
				$first_arr[$first_key] = (object)$first_arr[$first_key];
			}
		}
		return (object)$first_arr;
	}


	/*
	 * @param int ($uid)
	 * @return string (verified, unverified) || boolean (FALSE)
	 */
	private function get_stripe_user_status($uid=''){
		 if (!empty($this->settings['uap_stripe_v2_enable'])){
			 $user_stripe_id_account = get_user_meta($uid, 'ihc_stripe_connected_account_id', TRUE);
			 if (!empty($user_stripe_id_account)){
				 \Stripe\Stripe::setApiKey($this->secret_key);
				 $the_account_object = \Stripe\Account::retrieve($user_stripe_id_account);
				 if ($the_account_object && isset($the_account_object->legal_entity) && isset($the_account_object->legal_entity->verification) && isset($the_account_object->legal_entity->verification->status)){
				 	return $the_account_object->legal_entity->verification->status;
				 }
			 }
		 }
		 return FALSE;
	}


	/*
	 * @param int (user id)
	 * @return array (user details)
	 */
	 public function get_stripe_user_details_by_uid($uid=0){
	 	 if ($uid){
	 	 	 $user_stripe_id_account = get_user_meta($uid, 'ihc_stripe_connected_account_id', TRUE);
			 if (!empty($user_stripe_id_account)){
				 \Stripe\Stripe::setApiKey($this->secret_key);
				 $the_account_object = \Stripe\Account::retrieve($user_stripe_id_account);
				 if ($the_account_object && isset($the_account_object->legal_entity) && isset($the_account_object->legal_entity->verification) && isset($the_account_object->legal_entity->verification->status)){
				 	return (array)$the_account_object->legal_entity;
				 }
			 }
	 	 }
		 return array();
	 }


}

endif;
