<?php
if (class_exists('IndeedImport') && !class_exists('UapIndeedImport')):
	
class UapIndeedImport extends IndeedImport{
	
	/*
	 * @param string ($entity_name)
	 * @param string ($entity_opt)
	 * @param object ($xml_object)
	 * @return none
	 */
	protected function do_import_custom_table($entity_name, $entity_opt, &$xml_object){
		global $wpdb;
		$table = $wpdb->prefix . $entity_name;///$wpdb->base_prefix
		
		if (!$xml_object->$entity_name->Count()){
			return;
		}
		
		switch ($entity_name){
			case 'uap_affiliates':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(
											 {$object->id}, 
											'{$object->uid}', 
											'{$object->rank_id}', 
											'{$object->start_data}', 
											'{$object->status}'
					)";
					$this->do_basic_insert($table, $insert_string);
				}				
				break;
			case 'uap_banners':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null, 
											'{$object->name}', 
											'{$object->description}', 
											'{$object->url}', 
											'{$object->image}',
											'{$object->status}',
											'{$object->DATE}'
					)";
					$this->do_basic_insert($table, $insert_string);
				}				
				break;
			case 'uap_notifications':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null, 
											'{$object->type}', 
											'{$object->rank_id}', 
											'{$object->subject}', 
											'{$object->message}',
											'{$object->pushover_message}',
											'{$object->pushover_status}',
											'{$object->status}'
					)";
					$this->do_basic_insert($table, $insert_string);
				}					
				break;
			case 'uap_ranks':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null, 
											'{$object->slug}', 
											'{$object->label}', 
											'{$object->amount_type}', 
											'{$object->amount_value}',
											'{$object->bonus}',
											'{$object->sign_up_amount_value}',
											'{$object->lifetime_amount_type}',
											'{$object->lifetime_amount_value}',
											'{$object->reccuring_amount_type}',
											'{$object->reccuring_amount_value}',
											'{$object->mlm_amount_type}',
											'{$object->mlm_amount_value}',
											'{$object->achieve}',
											'{$object->settings}',
											'{$object->rank_order}',
											'{$object->status}'
					)";
					$this->do_basic_insert($table, $insert_string);
				}					
				break;
			case 'uap_offers':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null, 
											'{$object->name}', 
											'{$object->start_date}', 
											'{$object->end_date}', 
											'{$object->amount_type}',
											'{$object->amount_value}',
											'{$object->settings}',
											'{$object->status}'
					)";
					$this->do_basic_insert($table, $insert_string);
				}					
				break;
			case 'uap_offers_affiliates_reference':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null, 
											'{$object->offer_id}', 
											'{$object->affiliate_id}', 
											'{$object->source}', 
											'{$object->products}'
					)";
					$this->do_basic_insert($table, $insert_string);
				}					
				break;
			case 'uap_mlm_relations':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null, 
											'{$object->affiliate_id}', 
											'{$object->parent_affiliate_id}'
					)";
					$this->do_basic_insert($table, $insert_string);
				}				
				break;
			case 'uap_ranks_history':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null, 
											'{$object->affiliate_id}', 
											'{$object->prev_rank_id}',
											'{$object->rank_id}',
											'{$object->add_date}'
					)";
					$this->do_basic_insert($table, $insert_string);
				}						
				break;
			case 'uap_landing_commissions':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null, 
											'{$object->affiliate_id}', 
											'{$object->prev_rank_id}',
											'{$object->rank_id}',
											'{$object->add_date}'
					)";
					$this->do_basic_insert($table, $insert_string);
				}						
				break;
			case 'uap_coupons_code_affiliates':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null, 
											'{$object->code}', 
											'{$object->affiliate_id}',
											'{$object->type}',
											'{$object->settings}',
											'{$object->status}'
					)";
					$this->do_basic_insert($table, $insert_string);
				}					
				break;
			case 'uap_reports':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES( '{$object->affiliate_id}', 
											'{$object->email}',
											'{$object->period}',
											'{$object->last_sent}'
					)";
					$this->do_basic_insert($table, $insert_string);
				}					
				break;
			case 'uap_ref_links':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$insert_string = "VALUES(null, 
											'{$object->affiliate_id}', 
											'{$object->url}',
											'{$object->status}'
					)";
					$this->do_basic_insert($table, $insert_string);
				}						
				break;
		}
	}	
	
	
	/*
	 * @param string (table name)
	 * @param string (insert values)
	 * @return none
	 */
	private function do_basic_insert($table='', $insert_values=''){
		global $wpdb;
		$wpdb->query("INSERT INTO $table $insert_values;");
	}
}	
	
endif;
