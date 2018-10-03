<div class="uap-wrapper">
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Uploads Settings', 'uap');?></h3>
		<div class="inside">						
			<div class="uap-form-line">
				<span class="uap-labels-special"><?php _e("Upload File Accepted Extensions:", 'uap');?></span>
				<div class="inside">
					<textarea name="uap_upload_extensions" style="width: 300px;"><?php echo $data['metas']['uap_upload_extensions'];?></textarea>
					<div><?php _e("Write the extensions with comma between values! ex: pdf,jpg,mp3", 'uap');?></div>							
				</div>
			</div>
		
			<div class="uap-form-line">
				<span class="uap-labels-special"><?php _e("Upload File Maximum File Size:", 'uap');?></span>
				<div class="inside">
					<input type="number" value="<?php echo $data['metas']['uap_upload_max_size'];?>" name="uap_upload_max_size" min="0.1" step="0.1" /> MB						
				</div>
			</div>				
			
			<div class="uap-form-line">
				<span class="uap-labels-special"><?php _e("Avatar Maximum File Size:", 'uap');?></span>
				<div class="inside">
					<input type="number" value="<?php echo $data['metas']['uap_avatar_max_size'];?>" name="uap_avatar_max_size" min="0.1" step="0.1" /> MB						
				</div>
			</div>								
																							
			<div class="uap-submit-form">
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>	
		</div>
	</div>	
</form>
</div>