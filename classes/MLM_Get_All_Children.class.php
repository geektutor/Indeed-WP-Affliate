<?php
if (!class_exists('MLM_Get_All_Children')):
	
	class MLM_Get_All_Children{
		private $children = array();
		private $max_depth = 1;
		private $amount_per_level_type = array();
		private $amount_per_level_value = array();
		private $amount_per_rank_type = array();
		private $amount_per_rank_value = array();
		private $general_amount_type = '';
		private $general_amount_value = '';
		
		public function __construct($affiliate_id=0){
			/*
			 * @param int
			 * @return none
			 */
			if ($affiliate_id){
				global $indeed_db;
				$this->max_depth = get_option('uap_mlm_matrix_depth');	///getting max depth
				$children = $indeed_db->mlm_get_children($affiliate_id); ///getting first line of children
				$currency = get_option('uap_currency');				
				
				///AMOUNT PER RANK
				$temp_data = $indeed_db->get_mlm_amount_value_for_rank_by_aff_id($affiliate_id);
				if ($temp_data && isset($temp_data['types']) && isset($temp_data['values'])){
					$this->amount_per_rank_type = $temp_data['types'];
					$this->amount_per_rank_value = $temp_data['values'];	
					if ($this->amount_per_rank_type){
						foreach ($this->amount_per_rank_type as $key=>$amount_type){
							if ($amount_type=='flat'){
								$this->amount_per_rank_type[$key] = $currency;
							} else {
								$this->amount_per_rank_type[$key] = '%';
							}
						}															
					}
				}
				
				/// AMOUNT PER LEVEL
				$this->amount_per_level_type = get_option('mlm_amount_type_per_level');
				$this->amount_per_level_value = get_option('mlm_amount_value_per_level');
				if ($this->amount_per_level_type){
					foreach ($this->amount_per_level_type as $key=>$amount_type){
						if ($amount_type=='flat'){
							$this->amount_per_level_type[$key] = $currency;
						} else {
							$this->amount_per_level_type[$key] = '%';
						}
					}
				}			
				
				/// GENERAL AMOUNT VALUE & AMOUNT TYPE
				$general_settings = $indeed_db->return_settings_from_wp_option('mlm');
				$this->general_amount_value = $general_settings['uap_mlm_default_amount_value'];
				$this->general_amount_type = $general_settings['uap_mlm_default_amount_type'];
				if ($this->general_amount_type=='flat'){
					$this->general_amount_type = $currency;
				} else {
					$this->general_amount_type = '%';
				}
				unset($general_settings);
				
				
				if ($this->max_depth>0 && $children){
					$current_depth = 2;
					foreach ($children as $child_id){
						$level = 1;
						$temp['parent']= $indeed_db->get_wp_username_by_affiliate_id($affiliate_id);
						$temp['level'] = $level;
						$temp['username'] = $indeed_db->get_wp_username_by_affiliate_id($child_id);
						$temp['email'] = $indeed_db->get_email_by_affiliate_id($child_id);
						$temp['amount_value'] = '';
						$amount_type = '';
						
						/// BY USER RANK
						if (isset($this->amount_per_rank_type[$level]) && isset($this->amount_per_rank_value[$level])){
							$temp['amount_value'] = $this->amount_per_rank_value[$level];
							if (!empty($temp['amount_value'])){
								$amount_type = $this->amount_per_rank_type[$level];
							}								
						}					
					
						/// BY MLM LEVEL
						if (empty($temp['amount_value']) && isset($this->amount_per_level_type[$level]) && isset($this->amount_per_level_value[$level])){
							$temp['amount_value'] = $this->amount_per_level_value[$level];
							if (!empty($temp['amount_value'])){
								$amount_type = $this->amount_per_level_type[$level];
							}								
						}	
								
						/// DEFAULT MLM AMOUNT
						if (empty($temp['amount_value'])){
							$temp['amount_value'] = $this->general_amount_value;
							if (!empty($temp['amount_value'])){
								$amount_type = $this->general_amount_type;
							}								
						}
						
						$temp['amount_value'] .= ' ' . $amount_type;
														
						$this->children[$child_id] = $temp;						
						$this->get_childs_for_child($child_id, $current_depth);
					}
				}		
			}
		}
		
		public function get_results(){
			/*
			 * @param none
			 * @return array
			 */		
			return $this->children;
		}
	
		private function get_childs_for_child($parent_id=0, $current_depth){
			/*
			 * RECURSIVE FUNCTION
			 * @param int, int
			 * @return none
			 */
			if ($parent_id && $current_depth<=$this->max_depth){
				$current_depth++;
				global $indeed_db;
				$children = $indeed_db->mlm_get_children($parent_id);
				foreach ($children as $child_id){
					$level = $current_depth - 1;
					$temp['parent']= $indeed_db->get_wp_username_by_affiliate_id($parent_id);
					$temp['level'] = $level;
					$temp['username'] = $indeed_db->get_wp_username_by_affiliate_id($child_id);
					$temp['amount_value'] = '';
					$amount_type = '';
					
					/// BY USER RANK
					if (isset($this->amount_per_rank_type[$level]) && isset($this->amount_per_rank_value[$level])){
						$temp['amount_value'] = $this->amount_per_rank_value[$level];
						if (!empty($temp['amount_value'])){
							$amount_type = $this->amount_per_rank_type[$level];
						}								
					}	
					
					/// BY MLM LEVEL
					if (empty($temp['amount_value']) && isset($this->amount_per_level_type[$level]) && isset($this->amount_per_level_value[$level])){
						$temp['amount_value'] = $this->amount_per_level_value[$level];
						if (!empty($temp['amount_value'])){
							$amount_type = $this->amount_per_level_type[$level];
						}						
					}
					
					/// DEFAULT MLM AMOUNT
					if (empty($temp['amount_value'])){
						$temp['amount_value'] = $this->general_amount_value;
						if (!empty($temp['amount_value'])){
							$amount_type = $this->general_amount_type;
						}								
					}
													
					$temp['amount_value'] .= ' ' . $amount_type;								
																
					$this->children[$child_id] = $temp;						
					$this->get_childs_for_child($child_id, $current_depth);
				}		
			}
		}		
	}
	
endif;
