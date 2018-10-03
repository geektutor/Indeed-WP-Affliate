<?php
if (!class_exists('Uap_Custom_Woo_Endpoint')):

class Uap_Custom_Woo_Endpoint{
	private $name = 'uap';
	private $metas = array();
	private $is_affiliate = FALSE;

	public function __construct(){
		/*
		 * @param none
		 * @return none
		 */

		global $indeed_db;
		$this->metas = $indeed_db->return_settings_from_wp_option('woo_account_page');

		if (!uap_is_ump_active()){
			add_action('init',array( $this, 'flush_rules'), 999);
		}
		//add_action('init', array( $this, 'add_endpoints'));
		//add_filter('query_vars', array( $this, 'add_query_vars'), 991, 1);
		add_filter('the_title', array( $this, 'endpoint_title' ));
		add_filter('woocommerce_account_menu_items', array($this, 'new_menu_items'));
		add_action('woocommerce_account_uap_endpoint', array($this, 'content'));
	}

	public function add_endpoints(){
		/*
		 * @param none
		 * @return none
		 */
		add_rewrite_endpoint($this->name, EP_ROOT | EP_PAGES );
	}

	public function flush_rules(){
		/*
		 * @param none
		 * @return none
		 */
		flush_rewrite_rules();
	}

	public function add_query_vars($vars=array()){
		/*
		 * @param array
		 * @return array
		 */
		$vars[] = $this->name;
		return $vars;
	}

	public function endpoint_title($title=''){
		/*
		 * @param string
		 * @return string
		 */
		global $wp_query;
		if (isset($wp_query->query_vars[$this->name]) && !is_admin() && is_main_query() && in_the_loop() && is_account_page()){
			$title = $this->metas['uap_woo_account_page_name'];
		}
		return $title;
	}

	public function new_menu_items($items=array()){
		/*
		 * @param array
		 * @return array
		 */
		 if ($this->name && isset($this->metas['uap_woo_account_page_name']) && $this->check_if_do_print() ){
		 	 $position = $this->metas['uap_woo_account_page_menu_position'];
		 	 $reorder[$position] = array($this->name, $this->metas['uap_woo_account_page_name']);

			 $i = 1;
			 foreach ($items as $key=>$value){
			 	 while (isset($reorder[$i])){
			 	 	$i++;
			 	 }
				 $reorder[$i] = array($key, $value);
			 }

			 ksort($reorder);
			 $return_array = array();
			 foreach ($reorder as $array){
			 	if (isset($array[0]) && isset($array[1])){
				 	$return_array[$array[0]] = $array[1];
			 	}
			 }
			 return $return_array;
		 }
		return $items;
	}

	public function content(){
		/*
		 * @param none
		 * @return string
		 */
		 if ($this->is_affiliate){
			 /// content for affiliates
			 echo do_shortcode('[uap-account-page]');
		 } else {
		 	/// non affiliates
		 	echo stripslashes($this->metas['uap_woo_account_page_non_affiliate_content']);
		 }
	}

	private function check_if_do_print(){
		/*
		 * @param none
		 * @return none
		 */
		global $indeed_db;
		global $current_user;
		if (!empty($current_user) && !empty($current_user->ID)){
			$this->is_affiliate = $indeed_db->is_user_affiliate_by_uid($current_user->ID);
		}
		if (!$this->is_affiliate && !$this->metas['uap_woo_account_page_show_to_everyone']){
			return FALSE;
		}
		return TRUE;
	}

}

endif;
