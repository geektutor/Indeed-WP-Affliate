<div class="uap-wrapper">
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3">ReCaptcha</h3>
		<div class="inside">
			<div class="uap-form-line">
				<label class="uap-labels-special"><?php _e('Public Key:', 'uap');?></label> <input type="text" name="uap_recaptcha_public" value="<?php echo $data['metas']['uap_recaptcha_public'];?>" class="uap-deashboard-middle-text-input"/>
			</div>
			<div class="uap-form-line">
				<label class="uap-labels-special"><?php _e('Private Key:', 'uap');?></label> <input type="text" name="uap_recaptcha_private" value="<?php echo $data['metas']['uap_recaptcha_private'];?>" class="uap-deashboard-middle-text-input" />
			</div>		
			<div class=""><?php _e('Get Public and Private Keys from', 'uap');?> <a href="https://www.google.com/recaptcha/admin#list" target="_blank"><?php _e('here', 'uap');?></a>.</div>		
			<div style="margin-top: 15px;">
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" onClick="" class="button button-primary button-large" />
			</div>					
		</div>
	</div>						
</form>
</div>
