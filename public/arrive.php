<?php
if (empty($no_load)){
	require_once '../../../../wp-load.php';	
}
require_once UAP_PATH . 'classes/ResetPassword.class.php';

if (!empty($_GET['do_reset_pass']) && !empty($_GET['uid']) && !empty($_GET['c'])){
	/// DO RESET PASSWORD
	$object = new UAP\ResetPassword();
	$object->proceed($_GET['uid'], $_GET['c']);
} else if (!empty($_GET['do_uap_code']) && !empty($_GET['uid'])){
	/// E-mail Verification
	require_once UAP_PATH . 'classes/Uap_Double_Email_Verification.class.php';
	$object = new Uap_Double_Email_Verification($_GET['uid'], $_GET['do_uap_code']);
}

/// AND OUT
$redirect_url = get_home_url();
wp_redirect($redirect_url);
exit();