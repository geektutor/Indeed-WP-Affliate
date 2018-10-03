	<div class="uap-user-list-wrap">
		<div class="uap-page-title">Ultimate Affiliates Pro - <span class="second-text"><?php _e('Top Affiliates List', 'uap');?></span>
	</div>		
		<form action="" method="post">
			<div class="uap-stuffbox">
				<h3 class="uap-h3"><?php _e('Responsive Settings', 'uap');?></h3>
				<div class="inside">	
					<div class="uap-form-line">
						<span class="uap-labels-special"><?php _e('Screen Max-Width:', 'uap');?> 479px</span>
						<div class="uap-general-options-link-pages"><select name="uap_listing_users_responsive_small"><?php 
							$arr = array( '1' => 1 . __(' Columns', 'uap'),
										  '2' => 2 . __(' Columns', 'uap'),
										  '3' => 3 . __(' Columns', 'uap'),
										  '4' => 4 . __(' Columns', 'uap'),
									 	  '5' => 5 . __(' Columns', 'uap'),
									 	  '6' => 6 . __(' Columns', 'uap'),
										  '0' => __('Auto', 'uap'),
							);
							foreach ($arr as $k=>$v){
								$selected = ($data['metas']['uap_listing_users_responsive_small']==$k) ? 'selected' : '';
								?>
									<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
								<?php 	
							}
						?>
						</select></div>				
					</div>
					<div class="uap-form-line">
						<span class="uap-labels-special"><?php _e('Screen Min-Width:', 'uap');?> 480px <?php _e(" and Screen Max-Width:");?> 767px</span>
						<div class="uap-general-options-link-pages"><select name="uap_listing_users_responsive_medium"><?php 
							foreach ($arr as $k=>$v){
								$selected = ($data['metas']['uap_listing_users_responsive_medium']==$k) ? 'selected' : '';
								?>
									<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
								<?php 	
							}
						?>
						</select></div>				
					</div>
					<div class="uap-form-line">
						<span class="uap-labels-special"><?php _e('Screen Min-Width:', 'uap');?> 768px <?php _e(" and Screen Max-Width:");?> 959px</span>
						<div class="uap-general-options-link-pages"><select name="uap_listing_users_responsive_large"><?php 
							foreach ($arr as $k=>$v){
								$selected = ($data['metas']['uap_listing_users_responsive_large']==$k) ? 'selected' : '';
								?>
									<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
								<?php 	
							}
						?>
						</select></div>				
					</div>								
					<div class="uap-wrapp-submit-bttn">
		            	<input type="submit" value="<?php _e('Save changes', 'uap');?>" name="save" class="button button-primary button-large">
		            </div>												
				</div>
			</div>				
			
			<div class="uap-stuffbox">
				<h3 class="uap-h3"><?php _e('Custom CSS', 'uap');?></h3>
				<div class="inside">	
					<div class="uap-form-line">
						<span class="uap-labels-special"><?php _e('Add !important;  after each style option and full style path to be sure that it will take effect!', 'uap');?></span>
						<div class="uap-general-options-link-pages"><textarea name="uap_listing_users_custom_css" style="width: 100%; height: 100px;"><?php echo stripslashes($data['metas']['uap_listing_users_custom_css']);?></textarea></div>				
					</div>	
					<div class="uap-wrapp-submit-bttn">
		            	<input type="submit" value="<?php _e('Save changes', 'uap');?>" name="save" class="button button-primary button-large">
		            </div>												
				</div>		
			</div>			
		</form>
	</div>