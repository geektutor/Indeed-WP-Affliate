<div class="uap-wrapper">
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Export', 'uap');?></h3>
		<div class="inside">
			<div class="uap-form-line">
				<span class="uap-labels-special"></span>
				<div class="uap-form-line">
					<span style="font-weight:bold; display:inline-block; width: 25%;"><?php _e('Users', 'uap');?></span>
					<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<input type="checkbox" class="iump-switch" onClick="uap_check_and_h(this, '#import_users');" />
						<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="import_users" value=0 id="import_users"/>			
				</div>	
				<div class="uap-form-line">
					<span style="font-weight:bold; display:inline-block; width: 25%;"><?php _e('Settings', 'uap');?></span>
					<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#import_settings');" />
						<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="import_settings" value=0 id="import_settings"/>			
				</div>							
			</div>	
				
			<div class="uap-hidden-download-link" style="display: none;"><a href="" target="_blank" download>export.xml</a></div>	
			
			<div class="uap-wrapp-submit-bttn">
				<div style="display: inline-block; vertical-align: top;" class="button button-primary button-large" onClick="uap_make_export_file();"><?php _e('Export', 'uap');?></div>
				<div style="display: inline-block; vertical-align: top;" id="ihc_loading_gif" ><span class="spinner"></span></div>
			</div>				
						
		</div>
	</div>	
</form>

<form action="" method="post" enctype="multipart/form-data">
	<div class="ihc-stuffbox">
		<h3><?php _e('Import', 'ihc');?></h3>
		<div class="inside">				
			<div class="iump-form-line">
				<span class="iump-labels-special"><?php _e('File', 'ihc');?></span>
				<input type="file" name="import_file" />
			</div>	
					
			<div class="ihc-wrapp-submit-bttn">
				<input type="submit" value="<?php _e('Import', 'ihc');?>" name="import" class="button button-primary button-large">
			</div>							
		</div>
	</div>			
</form>

</div>
