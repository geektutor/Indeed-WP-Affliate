<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Friendly Links', 'uap');?></h3>
		<div class="inside">
			<div class="row">
				<div class="col-xs-8">
					<h3><?php _e('Activate/Hold Friendly Affiliate Links', 'uap');?></h3>
					<p><?php _e('Affiliates will be able to use friendly links instead of the default one. They have a better looking structure and are easier to read. Ex: www.yourwebsite.com/ref/smith', 'uap');?></p>
					<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<?php $checked = ($data['metas']['uap_friendly_links']) ? 'checked' : '';?>
						<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_friendly_links');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="uap_friendly_links" value="<?php echo $data['metas']['uap_friendly_links'];?>" id="uap_friendly_links" /> 
				</div>
			</div>
			<br/>
			<p><?php _e('This feature is recommended only when the referral format is based on username. Affiliates will not be able to build custom links into a friendly structure with variables inside. Ex: ?order=true', 'uap');?></p>
			<p><?php _e('Refreshing the WP permalinks may be required.', 'uap');?></p>
			<div class="uap-line-break"></div>	
			<div class="uap-submit-form"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>								
		</div>
	</div>
</form>					