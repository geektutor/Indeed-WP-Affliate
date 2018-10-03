<?php
if (!class_exists('IndeedExport')):
class IndeedExport{
	/*
	 * @var array
	 */
	protected $entities = array();	
	/*
	 * @var string
	 */
	protected $file = '';
	/*
	 * @var boolean
	 */
	protected $getUsers = FALSE;
	/*
	 * @var array
	 */
	protected $affiliate_ids = array();
	
	
	/*
	 * @param none
	 * @return none
	 */
	public function __construct(){}
	
	
	/*
	 * @param array
	 * @return none
	 */
	public function setEntity($params=array()){
		if (!empty($params['table_name'])){
			$table_name = $params['table_name'];
			if (empty($this->entities[$table_name])){
				$this->entities[$table_name] = $params;
			}
		}
	}
	
	
	/*
	 * @param bool
	 * @return none
	 */
	public function setGetUsers($value=FALSE){
		$this->getUsers = $value;
	}
	
	
	/*
	 * @param string
	 * @return none
	 */
	public function setFile($filename=''){
		$this->file = $filename;
	}
	
	
	/*
	 * @param none
	 * @return boolean
	 */
	public function run(){
		global $wpdb;
		if ($this->entities || $this->getUsers){
			$xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
			///write info
			$temp_entity = $this->entities;
			foreach ($temp_entity as &$temp_arr){
				if (isset($temp_arr['values'])){
					unset($temp_arr['values']);
				} else if (isset($temp_arr['keys_to_select'])){
					unset($temp_arr['keys_to_select']);
				}
			}
			if ($this->getUsers){
				$temp_entity['users'] = '';	
				$temp_entity['uap_affiliates'] = '';	
				$temp_entity['usermeta'] = '';	
			}
			if (!empty($temp_entity['usermeta'])){
				$temp_entity['indeed_wp_capabilities'] = '';
			}
			$this->array_to_xml(array('import_info'=>$temp_entity), $xml_data);
			if ($this->getUsers){
				$db_data = $this->get_affiliate_users();
				if ($db_data){
					/// users that are affiliates
					$this->array_to_xml(array('users' => $db_data), $xml_data);
					/// getting uap_affiliates data
					$temp_data = $this->get_db_data_for_entity(array('full_table_name' => $wpdb->prefix . 'uap_affiliates'));
					if ($temp_data){
						$this->array_to_xml(array('uap_affiliates' => $temp_data), $xml_data);
					}
					/// usermeta
					$users_ids = implode(',', $this->affiliate_ids);
					$options['selected_cols'] = " user_id, meta_key, meta_value ";
					$options['full_table_name'] = $wpdb->base_prefix . 'usermeta';
					$cap = $wpdb->get_blog_prefix() . 'capabilities';
					$options['where_clause'] = " AND meta_key NOT LIKE '$cap' AND user_id IN ($users_ids) ";
					$db_data = $this->get_db_data_for_entity($options);
					if ($db_data){
						$this->array_to_xml(array('usermeta'=>$db_data), $xml_data);						
						/// write capabilities like a table
						$options['where_clause'] = " AND meta_key LIKE '$cap' AND user_id IN ($users_ids) ";
						$options['selected_cols'] = " user_id, meta_value ";
						$capabilities = $this->get_db_data_for_entity($options);
						if ($capabilities){
							$this->array_to_xml(array('indeed_wp_capabilities'=>$capabilities), $xml_data);						
						}							
					}						
				}				
			}
			if ($this->entities){
				foreach ($this->entities as $table => $options){
					switch ($table){
						case 'options':
							$db_data = $options['values'];
							foreach ($db_data as $db_data_key=>$db_data_value){
								if (is_array($db_data_value)){
									$db_data[$db_data_key] = serialize($db_data_value);
								}
							}
							break;
						default:
							$db_data = $this->get_db_data_for_entity($options);
							break;
					}				
					if ($db_data){
						$this->array_to_xml(array($table=>$db_data), $xml_data);
						unset($db_data);
					}
				}				
			}
			$result = $xml_data->asXML($this->file);
			return TRUE;
		}
		return FALSE;
	}
	
	
	/*
	 * @param array, object
	 * @return none
	 */
	protected function array_to_xml($data=array(), &$xml_data){
		if (!empty($data)){
			foreach ($data as $key => $value){
				if (is_numeric($key)){
					$key = 'item' . $key;
				}
				if (is_array($value)){
					$subnode = $xml_data->addChild($key);
					$this->array_to_xml($value, $subnode);
				} else {
					$xml_data->addChild("$key", htmlspecialchars("$value")); ///htmlspecialchars("$value")
				}
			}			
		}
	}
	
	
	/*
	 * @param array (options for query)
	 * @param bool (return data as object)
	 * @return array || object
	 */
	protected function get_db_data_for_entity($options=array()){
		global $wpdb;
		$array = array();
		if (empty($options['selected_cols'])){
			$options['selected_cols'] = '*';
		}
		if (empty($options['where_clause'])){
			$options['where_clause'] = '';
		}
		if (empty($options['limit'])){
			$options['limit'] = '';
		}
		$q = "SELECT {$options['selected_cols']} 
					FROM {$options['full_table_name']}
					WHERE 1=1
					{$options['where_clause']}
					{$options['limit']}
		";
		$data = $wpdb->get_results($q);
		if ($data){
			foreach ($data as $object){
				$array[] = (array)$object;
			}
		}
		return $array;
	}


	/*
	 * @param array
	 * @return array
	 */
	protected function get_affiliate_users($options=array()){
		global $wpdb;
		$array = array();
		if (empty($options['selected_cols'])){
			$options['selected_cols'] = 'u.*';
		}
		if (empty($options['where_clause'])){
			$options['where_clause'] = '';
		}
		if (empty($options['limit'])){
			$options['limit'] = '';
		}
		$u = $wpdb->base_prefix . 'users';
		$a = $wpdb->prefix . 'uap_affiliates';
		$q = "SELECT {$options['selected_cols']} 
					FROM $u u
					INNER JOIN $a a
					ON u.ID=a.uid
					WHERE 1=1
					{$options['where_clause']}
					{$options['limit']}
		";
		$data = $wpdb->get_results($q);
		
		if ($data){
			foreach ($data as $object){
				$array[] = (array)$object;
				$this->affiliate_ids[] = $object->ID;
			}
		}
		return $array;		
	}
	
	
}
endif;