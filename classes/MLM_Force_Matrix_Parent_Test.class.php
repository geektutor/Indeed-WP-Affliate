<?php 
/*
 * input parent_id (int), limit_of_children
 * output new parent id
 */
if (!class_exists('MLM_Force_Matrix_Parent_Test')) :

class MLM_Force_Matrix_Parent_Test{
	private $parents_arr = array();
	private $limit_of_children;
	private $return_value = 0;
	private $max_height_limit = 0;
	private $current_height = 1;

	public function __construct($parent_id=0, $limit_of_children=0, $height_limit=0){
		/*
		 * @oaram int, int
		 * @return none
		 */
		$this->limit_of_children = $limit_of_children;
		$this->max_height_limit = $height_limit;
		if ($this->can_be_parent($parent_id)){
			$this->return_value = $parent_id;
			return;
		} else {
			$this->insert_childs_into_parent_var($parent_id);
			$this->search_parent_for_affiliate();
		}		
	}
	
	public function get_result(){
		/*
		 * @param none
		 * @return number
		 */
		return $this->return_value;
	}

	private function search_parent_for_affiliate(){
		/*
		 * @oaram none
		 * @return int
		 */
		
		/// MATRIX HEIGHT
		$this->current_height++;
		if ($this->current_height>$this->max_height_limit){
			return;
		}
		
		$search_into = $this->parents_arr;		
		if ($search_into){
			unset($this->parents_arr);
			foreach ($search_into as $parent_id){
				if ($this->can_be_parent($parent_id)){
					$this->return_value = $parent_id;
					return;
				} else {
					$this->insert_childs_into_parent_var($parent_id);
				}
			}
			$this->search_parent_for_affiliate();
		} else {
			return;//none (wtf)
		}
	}

	private function insert_childs_into_parent_var($affiliate_id=0){
		/*
		 * @param int
		 * @return none
		 */
		if ($affiliate_id){
			global $indeed_db;
			$children = $indeed_db->mlm_get_children($affiliate_id);
			if ($children){
				foreach ($children as $child){
					$this->parents_arr[] = $child;
				}
			}
		}
	}

	private function can_be_parent($affiliate_id=0){
		/*
		 * @param int
		 * @return boolean
		 */
		if ($affiliate_id){
			global $indeed_db;
			$current_affiliate_children = $indeed_db->mlm_get_count_children_for_parent($affiliate_id);
			if ($this->limit_of_children>$current_affiliate_children){
				return TRUE;
			}
		}
		return FALSE;
	}
}

endif;