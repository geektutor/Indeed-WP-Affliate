<?php
if (!class_exists('Uap_Add_Edit_Affiliate')):
	include_once UAP_PATH . 'classes/Uap_Add_Edit_Affiliate.class.php';
endif;

if (!class_exists('Uap_Register_Lite')):
	
class Uap_Register_Lite extends Uap_Add_Edit_Affiliate{
	private $lite_register_metas = array();
	protected $shortcodes_attr = array();
	
	public function __construct(){}
	
	public function setVariable($arr=array()){
		/*
		 * set the input variables
		 * @param array
		 * @return none
		 */
		if(count($arr)){
			foreach($arr as $k=>$v){
				$this->$k = $v;
			}
		}
	}

	private function set_register_fields(){
		/*
		 * @param none
		 * @return none
		 */
		$this->register_fields = uap_get_user_reg_fields();//register fields	
	}
		
	public function form(){
		/*
		 * @param none
		 * @return string
		 */
		$this->set_register_fields();	
		$key = uap_array_value_exists($this->register_fields, 'user_email', 'name');
		$this->register_template = $this->lite_register_metas['uap_register_lite_template'];
		if (isset($this->shortcodes_attr['template']) && $this->shortcodes_attr['template']!==FALSE){
			$this->register_template = $this->shortcodes_attr['template'];
		}
		
		$data['email_fields'] = $this->print_fields($this->register_fields[$key]); 
		$data['submit_button'] = indeed_create_form_element(array('type'=>'submit', 'name'=>'Submit', 'value' => __('Register', 'uap'), 'class' => 'button button-primary button-large',));
		$data['hidden_fields'][] = indeed_create_form_element(array('type'=>'hidden', 'name'=>'uapaction', 'value' => 'register_lite' ));
		$data['css'] = $this->lite_register_metas['uap_register_lite_custom_css'];
		$data['js'] = '';
		$data['template'] = $this->lite_register_metas['uap_register_lite_template'];
		require UAP_PATH . 'public/views/register_lite_form.php';
	}
	
	public function save_update_user(){
		/*
		 * @param none
		 * @return none
		 */
		 $this->register_metas = array_merge(uap_return_meta_arr('register'), uap_return_meta_arr('register-msg'), uap_return_meta_arr('register-custom-fields'));			 
		 $this->set_register_fields();
		 $this->check_email();
		 $this->errors = apply_filters('ump_before_printing_errors', $this->errors);	
		 if ($this->errors){
			 //print the error and exit
			 $this->return_errors();
			 return FALSE;
		 }
		 
		 $this->fields['user_login'] = @$_POST['user_email'];
		 $this->fields['user_email'] = @$_POST['user_email'];
		 $this->fields['user_pass'] = wp_generate_password(10);
		 $this->set_roles();
		 $this->user_id = wp_insert_user($this->fields);
		 do_action('ump_on_register_action', $this->user_id);
		 do_action('ump_on_register_lite_action', $this->user_id);
		 uap_Db::increment_dashboard_notification('users');
		 $this->do_individual_page();	
		 $this->notify_user_send_password();	 
		 $this->do_opt_in();
		 $this->double_email_verification();	
		 $this->do_autologin();
		 $this->notify_admin();
		 $this->notify_user();
		 if ($this->is_public){
			$this->succes_message();//this will redirect
		 }			 	 
	}
	
	private function do_autologin(){
		/*
		 * @param none
		 * @return none
		 */
		if (isset($this->shortcodes_attr['autologin']) && $this->shortcodes_attr['autologin']!==FALSE){
			$this->lite_register_metas['uap_register_lite_auto_login'] = $this->shortcodes_attr['autologin'];
		}
		if (!empty($this->lite_register_metas['uap_register_lite_auto_login']) && !empty($this->lite_register_metas['uap_register_lite_user_role']) && $this->lite_register_metas['uap_register_lite_user_role']!='pending_user'){
			wp_set_auth_cookie($this->user_id);
		}		 
	}

	protected function do_opt_in(){
		/*
		 * @param none
		 * @return none
		 */
		if ($this->lite_register_metas['uap_register_lite_opt_in'] && $this->type=='create' && empty($this->lite_register_metas['uap_register_lite_double_email_verification'])){
			uap_run_opt_in($_POST['user_email'], get_option('uap_register_lite_opt_in_type'));
		}
	}

	protected function double_email_verification(){
		/*
		 * @param none
		 * @return none
		 */
		if (isset($this->shortcodes_attr['double_email']) && $this->shortcodes_attr['double_email']!==FALSE){
			$this->lite_register_metas['uap_register_lite_double_email_verification'] = $this->shortcodes_attr['double_email'];
		}
		
		if ($this->is_public && $this->type=='create' && !empty($this->lite_register_metas['uap_register_lite_double_email_verification']) ){
			$hash = uap_random_str(10);
			//put the hash into user option
			update_user_meta($this->user_id, 'uap_activation_code', $hash);
			//set uap_verification_status @ -1
			update_user_meta($this->user_id, 'uap_verification_status', -1);
			///$activation_url_w_hash = uap_URL . 'user_activation.php?uid=' . $this->user_id . '&uap_code=' . $hash;
			$activation_url_w_hash = site_url();
			$activation_url_w_hash = add_query_arg('uap_action', 'user_activation', $activation_url_w_hash);	
			$activation_url_w_hash = add_query_arg('uid', $this->user_id, $activation_url_w_hash);	
			$activation_url_w_hash = add_query_arg('uap_code', $hash, $activation_url_w_hash);
				
			//send a nice notification
			uap_send_user_notifications($this->user_id, 'email_check', @$_POST['lid'], array('{verify_email_address_link}'=>$activation_url_w_hash));
		}
	}	

	private function set_roles(){
		/*
		 * @param none
		 * @return none
		 */
		if ($this->is_public && $this->type=='create'){
			if (isset($this->lite_register_metas['uap_register_lite_user_role'])){
				$this->fields['role'] = $this->lite_register_metas['uap_register_lite_user_role'];
			} else {
				$this->fields['role'] = 'subscriber';
			}	
			if (isset($this->shortcodes_attr['role']) && $this->shortcodes_attr['role']!==FALSE){
				$this->fields['role'] = $this->shortcodes_attr['role'];
			}
		}
	}

	protected function succes_message(){
		/*
		 * @param none
		 * @return none
		 */			
		if ($this->type=='create'){
			$q_arg = 'create_message';				
		} else {
			$q_arg = 'update_message';						
		}		
		$redirect = get_option('uap_register_lite_redirect');
		if (empty($redirect) || $redirect==-1){
			$redirect = get_option('uap_general_register_redirect');			
		}
		if ($redirect && $redirect!=-1 && $this->type=='create'){
			//custom redirect
			$url = get_permalink($redirect);
			if (!$url){				
				$url = uap_get_redirect_link_by_label($redirect, $this->user_id);
				if (strpos($url, UAP_PROTOCOL . $_SERVER['HTTP_HOST'] )!==0){ /// $_SERVER['SERVER_NAME'] 
					//if it's a external custom redirect we don't want to add extra params in url, so let's redirect from here
					wp_safe_redirect($url);
					exit();						
				}
			}		
		}
		if (empty($url)){
			$url = uap_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; /// $_SERVER['SERVER_NAME'] 			
		}
		if ($this->bank_transfer_message){
			/// bt redirect only to same page
			$bt_params = array( 'uap_register' => $q_arg,
								'uapbt' => 'true',
								'uap_lid' => $_POST['lid'],
								'uap_uid' => $this->user_id,
			);
			if ($this->coupon){
				$coupon_data = uap_check_coupon($this->coupon, $_POST['lid']);
				if ($coupon_data){
					if ($coupon_data['discount_type']=='percentage'){
						$bt_params['cp'] = $coupon_data['discount_value'];
					} else {
						$bt_params['cc'] = $coupon_data['discount_value'];
					}
					uap_submit_coupon($this->coupon);
				}
			}				
			//country
			if (!empty($_POST['uap_country'])){
				$bt_params['uap_country'] = $_POST['uap_country']; 
			}					
			$url = add_query_arg($bt_params, $url);
		} else {
			$url = add_query_arg(array('uap_register'=>$q_arg), $url);
		}
		wp_safe_redirect($url);
		exit();
	}	
}
	
endif;