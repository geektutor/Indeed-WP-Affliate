<?php if ($this->error_messages):?>
	<?php foreach ($this->error_messages as $err):?>
		<div class="uap-error-message"><?php echo $err;?></div>
	<?php endforeach;?>
<?php endif;?>