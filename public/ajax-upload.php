<?php
/*
 * Upload files via Ajax
 */
require_once("../../../../wp-load.php");
if (isset($_FILES['avatar'])){
	//========== handle avatar image
	if ($_FILES['avatar']['type']=='image/png' || $_FILES['avatar']['type']=='image/gif' || $_FILES['avatar']['type']=='image/jpeg'){
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		$arr['id'] = media_handle_upload('avatar',0);
		if ($arr['id']){
			$arr['url'] =  wp_get_attachment_url($arr['id']);
			$arr['secret'] = md5($arr['url']);
			echo json_encode($arr);
		} else {
			echo '';
		}
	}
} else if (isset($_FILES['uap_file'])){
	//============= handle upload file
	//debug
	//file_put_contents( "upload_media.log", $_FILES['uap_file']['type'], FILE_APPEND | LOCK_EX );
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	$arr['id'] = media_handle_upload('uap_file',0);
	if ($arr['id']){
		$arr['url'] =  wp_get_attachment_url( $arr['id'] );
		$arr['secret'] = md5($arr['url']);
	}
	$arr['name'] = $_FILES['uap_file']['name'];
	if (in_array($_FILES['uap_file']['type'], array('image/gif','image/jpg','image/jpeg','image/png'))){
		$arr['type'] = 'image';
	} else {
		$arr['type'] = 'other';
	}
	echo json_encode($arr);
}
else if (isset($_FILES['img'])){
		//// upload account page banner
		$cropImage = new Indeed\Uap\CropImage();
		echo $cropImage->saveImage($_FILES)
									 ->getResponse();
}

else if (isset($_POST['imgUrl'])){
		$cropImage = new Indeed\Uap\CropImage();
		echo $cropImage->cropImage($_POST)
									 ->getResponse();
}
