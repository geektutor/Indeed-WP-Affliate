<?php
if (!class_exists('Uap_Payments_Export')):
	
class Uap_Payments_Export{
	
	/*
	 * @var array
	 */
	 private $ids = array();
	 
	/*
	 * @var string
	 */
	private $min_date = '';
	
	/*
	 * @var string
	 */	
	private $max_date = '';
	
	/*
	 * @var string
	 */	
	private $payment_type = '';
	
	/*
	 * @var string
	 */
	private $new_status = '';
	
	/*
	 * @param none
	 * @return none
	 */
	 public function __construct(){}
	 
	 
	 /*
	  * @param string
	  * @return string
	  */
	 public function set_min_date($min_date=''){
	 	$this->min_date = $min_date;
	 }
	 
	 
	 /*
	  * @param string
	  * @return string
	  */
	 public function set_max_date($max_date=''){
	 	$this->max_date = $max_date;
	 }
	 
	 
	 /*
	  * @param string
	  * @return string
	  */
	 public function set_payment_type($payment_type=''){
	 	$this->payment_type = $payment_type;
	 }
	 
	 
	 /*
	  * @param string
	  * @return string
	  */
	 public function set_new_status($new_status=''){
	 	$this->new_status = $new_status;
	 }
	 	 	 
	 	 
	 /*
	  * @param 
	  * @return string (link to csv file)
	  */
	 public function generate_csv(){
	 	$data = $this->get_payments();
		if ($data){
			
			/// CREATE CSV FILE
			
			/// remove old files
			try {
				$this->remove_folter_content(UAP_PATH . 'temp_files', 'index.php');
			} catch (exception $e){}
			
			$file_name = 'temp_files/uap_transactions_' . date('Y_m_d') . '.csv';
			$file_path = UAP_PATH . $file_name;
			$file_link = UAP_URL . $file_name;
			if (file_exists($file_path)){
				unlink($file_path);
			}
			$file_resource = fopen($file_path, 'w');
			
			/// WRITE TOP
			$write[] = __('Name', 'uap');
			$write[] = __('E-mail', 'uap');
			$write[] = __('Amount', 'uap');
			$write[] = __('Currency', 'uap');
			$write[] = __('Payment Type', 'uap');
			$write[] = __('Payment Details', 'uap');
			fputcsv($file_resource, $write, ",");
			unset($write);
			
			/// WRITE CONTENT
			foreach ($data as $key=>$temp_array){
				$write[] = $temp_array['name'];
				$write[] = $temp_array['email'];
				$write[] = $temp_array['amount'];
				$write[] = $temp_array['currency'];
				$write[] = $temp_array['payment_type'];
				$write[] = $temp_array['payment_details'];
				$this->ids[] = $temp_array['id'];
				fputcsv($file_resource, $write, ",");
				unset($write);
			}
			
			/// CLOSE FILE
			fclose($file_resource);
			
			/// SET STATUS
			$this->set_payment_special_status();			
			if ($this->new_status){
				$this->set_status_at_complete();
			}
			
			return $file_link;					
		}
		return '';
	 }
	 
	 
	 /*
	  * @param none
	  * @return array
	  */
	 private function get_payments(){
	 	global $wpdb, $indeed_db;
		$payment_types = array(
								'paypal' => 'Paypal',
								'stripe' => 'Stripe',
								'stripe_v2' => 'Stripe v2',
								'bank_transfer' => __('Bank Transfer', 'uap'),
		);
		$return_data = array();
		$table = $wpdb->prefix . 'uap_payments';
		$q = "SELECT id, payment_type, affiliate_id, amount, currency, payment_details FROM $table WHERE ";
		$q .= " (status=0 OR status=1) ";
		if ($this->min_date!=''){
			$this->min_date = date('Y-m-d H:i:s', strtotime($this->min_date));
			$q .= " AND create_date>='$this->min_date' ";
		}
		if ($this->max_date!=''){
			$this->max_date = date('Y-m-d H:i:s', strtotime($this->max_date));
			$q .= " AND create_date<='$this->max_date' ";
		}
		if ($this->payment_type!=''){
			$q .= " AND payment_type='$this->payment_type' ";
		}
		$data = $wpdb->get_results($q);
		if ($data){
			foreach ($data as $object){
				$temp = (array)$object;
				$temp['payment_details'] = $this->edit_payment_details($temp['payment_details']);
				$temp['payment_type'] = $payment_types[$temp['payment_type']];
				$temp['email'] = $indeed_db->get_email_by_affiliate_id($object->affiliate_id);
				$temp['name'] = $indeed_db->get_wp_full_name(0, $object->affiliate_id);
				$return_data[] = $temp;
				unset($temp);
			}
		}
		return $return_data;
	 }
	 
	 
	 /*
	  * @param string
	  * @return string
	  */
	  private function edit_payment_details($string=''){
	  		$return = '';
	  	 	if ($string){
	  	 		$temp_return = array();
	  	 		$temp_array = unserialize($string);
				foreach ($temp_array as $array){
					$temp_return[] = $array['label'] . ': ' . $array['value'];
				}
				$return = implode(',', $temp_return);
	  	 	}
			return $return;
	  }
	  
	  
	  /*
	   * @param string
	   * @return none
	   */
	   private function set_status_at_complete(){
	   		if ($this->ids){
		   		global $wpdb, $indeed_db;
				$table = $wpdb->prefix . 'uap_payments';
				$the_id_list = implode(',', $this->ids);
				$wpdb->query("UPDATE $table SET status=2 WHERE id IN ($the_id_list);");		   			
	   		}
	   }
	   
	   
	   /*
	    * @param none
	    * @return none
	    */
	    private function set_payment_special_status(){
	   		if ($this->ids){
		   		global $wpdb, $indeed_db;
				$table = $wpdb->prefix . 'uap_payments';
				$the_id_list = implode(',', $this->ids);
				$wpdb->query("UPDATE $table SET payment_special_status='exported' WHERE id IN ($the_id_list);");		   			
	   		}	    	
	    }
		
		
		/*
		 * @param string, string
		 * @return none
		 */
		private function remove_folter_content($dir='', $exclude=''){
			$files = glob( $dir . "/*.*" );
			foreach ($files as $filename) {
			    if (is_file($filename) && strpos($filename, $exclude)===FALSE) {
			        unlink($filename);
			    }
			}
		}
	   
			
}
	
	
endif;
