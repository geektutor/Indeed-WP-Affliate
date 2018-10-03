<?php

if (!defined('WP_UNINSTALL_PLUGIN')){
	exit();
}
include plugin_dir_path(__FILE__) . 'classes/Uap_Db.class.php';
$uap_uninstall_object = new Uap_Db();
$uap_uninstall_object->unistall();