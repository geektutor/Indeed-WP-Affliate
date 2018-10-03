<?php
require_once UAP_PATH . 'public/font_awesome_codes.php';
$font_awesome = uap_return_font_awesome();
?>
<?php foreach ($font_awesome as $base_class => $code):?>
	<div class="uap-font-awesome-popup-item" data-class="<?php echo $base_class;?>" data-code="<?php echo $code;?>"><i class="fa-uap-preview fa-uap <?php echo $base_class;?>"></i></div>
<?php endforeach;?>