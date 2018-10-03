<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Notifications Settings', 'uap');?></h3>
		<div class="inside">
			<div class="uap-form-line">
				<label class="uap-labels-special"><?php _e("'From' E-mail Address:", 'uap');?></label> <input type="text" name="uap_notification_email_from" value="<?php echo $data['metas']['uap_notification_email_from'];?>" class="uap-deashboard-middle-text-input"/>
			</div>
			<div class="uap-form-line">
				<label class="uap-labels-special"><?php _e("'From' Name:", 'uap');?></label> <input type="text" name="uap_notification_name" value="<?php echo $data['metas']['uap_notification_name'];?>" class="uap-deashboard-middle-text-input" />
			</div>		
			<div style="margin-top: 15px;">
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" onClick="" class="button button-primary button-large" />
			</div>					
		</div>
	</div>						
</form>
