<?php
if (!class_exists('Uap_Tracking')){
	class Uap_Tracking{
		private $cookie_name = 'uap_referral';
		private $settings = array();
		private static $single = FALSE;

		public function __construct(){
			/*
			 * @param string, string
			 * @return none
			 */
			if (self::$single){
				return;
			} else {
				self::$single = TRUE;
			}

			/// SET SETTINGS
			global $indeed_db;
			$this->settings = $indeed_db->return_settings_from_wp_option('general-settings');
			$current_url = UAP_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; /// $_SERVER['SERVER_NAME']

			/// NO Referral Variable
			if (empty($this->settings['uap_referral_variable'])){
				return;
			}

			$get_value = '';
			$campaign = '';
			$affiliateIdFromLandingPage = apply_filters('uap_init_affiliate_id_value', '', $current_url);

			/// SET THE REFERRAL & CAMPAIGN
			if (!empty($_GET[$this->settings['uap_referral_variable']])){
				/// REFERRAL
				$get_value = $_GET[$this->settings['uap_referral_variable']];
				$current_url = remove_query_arg( $this->settings['uap_referral_variable'], $current_url);
				/// CAMPAIGN
				if ($this->settings['uap_campaign_variable'] && !empty($_GET[$this->settings['uap_campaign_variable']])){
					$campaign = $_GET[$this->settings['uap_campaign_variable']];
					$current_url = remove_query_arg( $this->settings['uap_campaign_variable'], $current_url);// remove param from url
				}
			} else if (strpos($current_url, '/' . $this->settings['uap_referral_variable'] . '/')!==FALSE){
				$temp_get = explode('/', $current_url);
				if (is_array($temp_get) && count($temp_get)){
					/// REFERRAL
					$search_key = array_search($this->settings['uap_referral_variable'], $temp_get);
					if ($search_key){
						$key = $search_key + 1;
						if (isset($temp_get[$key])){
							$get_value = $temp_get[$key];
							$current_url = str_replace('/' . $this->settings['uap_referral_variable'] . '/' . $get_value, '', $current_url);
						}
					}
					/// CAMPAIGN
					if (strpos($current_url, '/' . $this->settings['uap_campaign_variable'] . '/')!==FALSE){
						$search_key = array_search($this->settings['uap_campaign_variable'], $temp_get);
						if ($search_key){
							$key = $search_key + 1;
							if (isset($temp_get[$key])){
								$campaign = $temp_get[$key];
								$current_url = str_replace('/' . $this->settings['uap_campaign_variable'] . '/' . $campaign, '', $current_url);
							}
						}
					}
					$force_redirect = TRUE;
				}
			} else if ($indeed_db->is_magic_feat_enable('simple_links')){
				/////////////// CUSTOM LINKS
				$http_ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
				if ($http_ref){
					$get_value = $indeed_db->simple_links_get_uid_by_link($http_ref);
				}
			}

			if (empty($get_value) && empty($affiliateIdFromLandingPage)){
					return; /// OUT
			}

			/************************* GETTING AFFILIATE ID ***************************/

			if (is_string($get_value)){
				/// Search affiliate id by slug
				$get_value = urldecode($get_value);
				$affiliate_id = $indeed_db->get_affiliate_id_by_custom_slug($get_value);

				/// Search affiliate id by username
				if (empty($affiliate_id)){
					$affiliate_id = $indeed_db->get_affiliate_id_by_username($get_value);
				}
			}
			/// Search affiliate id by uid
			if (empty($affiliate_id) && is_numeric($get_value)){
				$affiliate_id = $get_value;
			}

			/// landing pages
			if (empty($affiliate_id) && !empty($affiliateIdFromLandingPage)){
					$affiliate_id = $affiliateIdFromLandingPage;
			}

			$rewrite_referrals_enable = get_option('uap_rewrite_referrals_enable');
			if (!empty($_COOKIE[$this->cookie_name]) && empty($rewrite_referrals_enable)){
					if (!empty($this->settings['uap_redirect_without_param']) || !empty($force_redirect)){
							$this->do_redirect($current_url);
					}
					return;
			}

			if (empty($_COOKIE[$this->cookie_name])){
				$referral_hash = md5($_SERVER['REMOTE_ADDR'] . time());
			} else {
				$cookie_data = unserialize(stripslashes($_COOKIE[$this->cookie_name]));
				if (!empty($cookie_data['referral_hash'])){
					$referral_hash = $cookie_data['referral_hash'];
				}
			}

			$browser = $this->get_browser();
			$device = $this->get_device_type();
			$ip = (empty($_SERVER['REMOTE_ADDR'])) ? '' : $_SERVER['REMOTE_ADDR'];

			if ($indeed_db->is_affiliate_active($affiliate_id)){
				/// STORE DATA IN DB
				$visit_id = $indeed_db->track_the_visit($referral_hash, 0, $affiliate_id, $current_url, $ip, $browser, $device, $campaign);

				/// SET COOKIE
				$this->set_cookie($affiliate_id, $campaign, $referral_hash, $visit_id);

				if (!empty($affiliateIdFromLandingPage) && $affiliateIdFromLandingPage==$affiliate_id){
						return;
				}

				/// REDIRECT
				if (!empty($this->settings['uap_redirect_without_param'])){
						$this->do_redirect($current_url);
				}
			}

			/// FORCE REDIRECT - FOR FRIENDLY LINKS
			if (!empty($force_redirect)){
					$this->do_redirect($current_url);
			}
		}

		private function do_redirect($target_url=''){
				wp_redirect($target_url);
				exit();
		}

		private function set_cookie($affiliate_id=0, $campaign='', $referral_hash='', $visit_id=0){
			/*
			 * @param string
			 * @return int
 		 	 */

			/// For PPC
			if (empty($_COOKIE[$this->cookie_name])){
					do_action('uap_insert_into_cookie_new_affiliate', $affiliate_id);
			} else {
					$cookieData = unserialize(stripslashes($_COOKIE[$this->cookie_name]));
					if ($cookieData['affiliate_id']!=$affiliate_id){
							do_action('uap_insert_into_cookie_new_affiliate', $affiliate_id);
					}
			}

			$data['affiliate_id'] = $affiliate_id;
			$data['campaign'] = $campaign;
			$data['referral_hash'] = $referral_hash;
			$data['visit_id'] = $visit_id;
			$data['timestamp'] = time();
			$data['site_referer'] = (empty($_SERVER['HTTP_REFERER'])) ? '' : $_SERVER['HTTP_REFERER'];
			$cookie_time = $this->settings['uap_cookie_expire'];
			if (empty($cookie_time)){
				$cookie_time = $data['timestamp'] + 360 * 24 * 60 * 60;//one year
			} else {
				$cookie_time = $data['timestamp'] + $cookie_time * 24 * 60 * 60;
			}
			setcookie($this->cookie_name, serialize($data), $cookie_time, '/'); /// name, value, expire, path
			return $data['referral_hash'];
		}

		private function get_browser(){
			/*
			 * @param none
			 * @return string
			 */
			if (!empty($_SERVER['HTTP_USER_AGENT'])){
				if (preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT']) && !preg_match('/Opera/i', $_SERVER['HTTP_USER_AGENT'])){
					return 'Internet Explorer';
				} else if (preg_match('/Firefox/i', $_SERVER['HTTP_USER_AGENT'])){
					return 'Firefox';
				} else if (preg_match('/Chrome/i', $_SERVER['HTTP_USER_AGENT'])) {
					return 'Chrome';
				} else if (preg_match('/Safari/i', $_SERVER['HTTP_USER_AGENT'])){
					return 'Safari';
				} else if (preg_match('/Opera/i', $_SERVER['HTTP_USER_AGENT'])){
					return 'Opera';
				} else {
					return 'Other';
				}
			}
		}

		private function get_device_type(){
			/*
			 * @param none
			 * @return string
			 */
			if (!class_exists('Mobile_Detect')){
				require UAP_PATH . 'classes/Mobile_Detect.class.php';
			}
			$detect = new Mobile_Detect();
			if (($detect->isMobile()) || ($detect->isTablet())) return 'mobile';
			return 'web';
		}

	}
}
