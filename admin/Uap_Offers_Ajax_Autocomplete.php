<?php
require_once '../../../../wp-load.php';
require_once '../utilities.php';

if (!empty($_GET['term'])){
	global $indeed_db;

	if (!empty($_GET['source'])){
		/// SEARCH FOR PRODUCTS
		switch ($_GET['source']){
			case 'woo':
				$data = $indeed_db->search_woo_products($_GET['term']);
				break;
			case 'ump':
				$data = $indeed_db->search_ump_levels($_GET['term']);
				break;
			case 'edd':
				$data = $indeed_db->search_edd_product($_GET['term']);
				break;
			case 'ulp':
				$data = $indeed_db->search_ulp_product($_GET['term']);
				break;
		}
	} else if (!empty($_GET['users'])){
		/// SEARCH FOR USERS
		$exclude_user = (empty($_GET['exclude_user'])) ? 0 : $_GET['exclude_user'];
		$data = $indeed_db->search_affiliates_by_char($_GET['term'], $exclude_user);
		if (empty($_GET['without_all'])){
			$data[-1] = 'All';
		}
	}

	if (!empty($data)){
		$i = 0;
		foreach ($data as $k=>$v){
			$return[$i]['id'] = $k;
			$return[$i]['label'] = $v;
			$i++;
		}
		echo json_encode($return);
	}
}

die;
