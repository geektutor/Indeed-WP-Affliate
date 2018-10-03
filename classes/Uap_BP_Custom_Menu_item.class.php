<?php
if (!class_exists('Uap_BP_Custom_Menu_item')):
	
	class Uap_BP_Custom_Menu_item{
		private $metas = array();
		private $is_affiliate = FALSE;
		
		public function __construct(){
			/*
			 * ALL THE ACTIONS & FILTERS
			 * @param none
			 * @return none
			 */					
			add_action('bp_setup_nav', array($this, 'do_setup_bp_nav'), 99);
		}
		
		public function do_setup_bp_nav(){
			/*
			 * @param 
			 * @return
			 */
			global $indeed_db;
			global $current_user;
			$this->metas = $indeed_db->return_settings_from_wp_option('bp_account_page');		
			if (!empty($current_user) && !empty($current_user->ID)){
				$this->is_affiliate = $indeed_db->is_user_affiliate_by_uid($current_user->ID);
			}

			if (!$this->is_affiliate && !$this->metas['uap_bp_account_page_show_to_everyone']){
				return;/// OUT
			}
						 
			global $bp;

			bp_core_new_nav_item( array(
					'name' => $this->metas['uap_bp_account_page_name'],
					'slug' => 'uap',
					'position' => $this->metas['uap_bp_account_page_position'],
					'show_for_displayed_user' => false,
					'screen_function' => 'uap_bp_content_action',
					'item_css_id' => 'uap',
					'default_subnav_slug' => 'uap'
				) 
			);
			bp_core_new_subnav_item( array(
					'name' => __('Ultimate Affiliate Pro', 'uap'),
					'slug' => 'uap',
					'show_for_displayed_user' => false, 
					'parent_url' => trailingslashit( bp_displayed_user_domain() . 'uap'),
					'parent_slug' => 'uap',
					'position' => $this->metas['uap_bp_account_page_position'],
					'screen_function' => array($this, 'uap_bp_content_action'),
					'item_css_id' => 'uap',
					'user_has_access' => bp_is_my_profile()					
				)
			);		 
		}
		
		
		public function uap_bp_content_action(){
			/*
			 * @param none
			 * @return none
			 */
			 add_action('bp_template_content', array($this, 'uap_bp_do_the_content'));
			 bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
		}
		
		public function uap_bp_do_the_content(){
			/*
			 * @param none
			 * @return 
			 */
			 if ($this->is_affiliate){
			 	echo do_shortcode('[uap-account-page]');
			 } else {
		 		/// non affiliates
		 		echo stripslashes($this->metas['uap_bp_account_page_non_affiliate_content']);
		 	}			 
		}
		
	}
	
endif;
