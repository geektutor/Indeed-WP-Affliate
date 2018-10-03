<form action="" method="post">
	<div class="uap-stuffbox">
		<h3><?php _e('LifeTime Commissions', 'uap');?></h3>
		<div class="inside">			
			<div class="uap-form-line">
				<label class="uap-label"><?php _e('Referral Username:', 'uap');?></label>
				<div style="display:inline-block; font-weight:bolder;"><?php echo $data['edit_data']['referral_username']?></div>
			</div>
			<div class="uap-form-line">
				<label class="uap-label"><?php _e('Affiliate Username:', 'uap');?></label>
				<select name="affiliate"><?php 
					foreach ($data['affiliates'] as $id=>$username){
						$selected = ($id==$data['edit_data']['affiliate_id']) ? 'selected' : '';
						?>
						<option <?php echo $selected;?> value="<?php echo $id;?>"><?php echo $username;?></option>
						<?php 
					}
				?></select>
			</div>
			<input type="hidden" name="id" value="<?php echo $data['edit_data']['relation'];?>" />
			<div class="uap-submit-form"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>				
		</div>
	</div>
</form>