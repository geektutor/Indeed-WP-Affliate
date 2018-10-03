<div class="uap-wrapper">
<div class="uap-stuffbox">
  <form action="<?php echo $data['form_action_url'];?>" method="post">
	<h3 class="uap-h3"><?php _e('Manage Banners', 'uap');?></h3>
	<div class="inside">
	  <div class="uap-inside-item">
		<div class="row">
			<div class="col-xs-4">
			<div class="input-group">
				<span class="input-group-addon" id="basic-addon1"><?php _e('Name', 'uap');?></span>
				<input type="text" class="form-control" placeholder="..."  value="<?php echo $data['name'];?>" name="name"  />
			</div>
			</div>
		</div>
	 </div>	
	 <div class="uap-line-break"></div>	
	 <div class="uap-inside-item">
		<div class="row">
			<div class="col-xs-4">
				<h3><?php _e('Description:', 'uap');?></h3>
				<textarea name="description" class="form-control text-area" cols="30" rows="5" placeholder="<?php _e('Some details...', 'uap');?>"><?php echo $data['description'];?></textarea>
			</div>
		</div>
	</div>
	<div class="uap-inside-item uap-special-line">
		<div class="row">
			<div class="col-xs-4">	
			<h3><?php _e('Banner Options:', 'uap');?></h3>
			<p><?php _e('Predefined URL And Image for your Custom Banner available for Affiliates', 'uap');?></p>
			<div class="input-group">
				<span class="input-group-addon" id="basic-addon1"><?php _e('URL:', 'uap');?></span>
				<input type="text" class="form-control" value="<?php echo $data['url'];?>" name="url" />
			</div>
			<h5><?php _e('Banner Image:', 'uap');?></h5>
			<div class="form-group">
			<input type="text" class="form-control" onClick="open_media_up(this);" value="<?php echo $data['image'];?>" name="image" id="uap_the_image" style="width: 90%;display: inline;"/>
			<i class="fa-uap fa-trash-uap" onclick="jQuery('#uap_the_image').val('');" title="<?php _e('Remove Banner Image', 'uap');?>"></i>
			</div>
			</div>
		</div>
	</div>					
		<input type="hidden" name="status" value="1" />
		<div class="uap-submit-form">
			<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large">
		</div>										
	</div>	
	<input type="hidden" name="id" value="<?php echo $data['id'];?>" />		
  </form>
</div>
	
</div>


</div><!-- end of uap-dashboard-wrap -->