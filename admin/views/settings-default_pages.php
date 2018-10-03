<?php 
$pages = uap_get_all_pages();
//getting pages
?>
<div class="uap-wrapper">		
			<form action="" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Default Pages:', 'uap');?></h3>
					<div class="inside">	
					
						<div class="uap-form-line">
							<label class="uap-labels-special"><?php _e('Register:', 'uap');?></label>
							<select name="uap_general_register_default_page">
								<option value="-1" <?php if($data['metas']['uap_general_register_default_page']==-1)echo 'selected';?> >...</option>
								<?php 
									if ($pages){
										foreach ($pages as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_register_default_page']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_register_default_page']);?>
						</div>		
						
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('Login Page:', 'uap');?></span>
							<select name="uap_general_login_default_page">
								<option value="-1" <?php if($data['metas']['uap_general_login_default_page']==-1)echo 'selected';?> >...</option>
								<?php 
									if ($pages){
										foreach ($pages as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_login_default_page']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_login_default_page']);?>
						</div>
						
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('Logout Page:', 'uap');?></span>
							<select name="uap_general_logout_page">
								<option value="-1" <?php if($data['metas']['uap_general_logout_page']==-1)echo 'selected';?> >...</option>
								<?php 
									if ($pages){
										foreach ($pages as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_logout_page']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>	
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_logout_page']);?>		
						</div>	
		
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('User Account Page:', 'uap');?></span>
							<select name="uap_general_user_page">
								<option value="-1" <?php if($data['metas']['uap_general_user_page']==-1)echo 'selected';?> >...</option>
								<?php 
									if ($pages){
										foreach ($pages as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_user_page']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>	
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_user_page']);?>		
						</div>	
						
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('TOS Page:', 'uap');?></span>
							<select name="uap_general_tos_page">
								<option value="-1" <?php if($data['metas']['uap_general_tos_page']==-1)echo 'selected';?> >...</option>
								<?php 
									if ($pages){
										foreach ($pages as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_tos_page']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>	
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_tos_page']);?>		
						</div>	
									
						<div class="uap-form-line">
							<span class="uap-labels-special"><?php _e('Lost Password:', 'uap');?></span>
							<select name="uap_general_lost_pass_page">
								<option value="-1" <?php if($data['metas']['uap_general_lost_pass_page']==-1)echo 'selected';?> >...</option>
								<?php 
									if ($pages){
										foreach ($pages as $k=>$v){
											?>
												<option value="<?php echo $k;?>" <?php if($data['metas']['uap_general_lost_pass_page']==$k)echo 'selected';?> ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>	
							<?php echo uap_general_options_print_page_links($data['metas']['uap_general_lost_pass_page']);?>		
						</div>									
						
						<div class="uap-submit-form">
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>							
					</div>
				</div>								
			</form>
</div>	
