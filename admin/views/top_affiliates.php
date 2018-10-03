<?php
$meta_arr = array(
							'num_of_entries' => 10,
							'entries_per_page' => 5,
							'order_by' => 'earnings',
							'order_type' => 'desc',
							'user_fields' => 'user_login,user_email,first_name,last_name,uap_avatar',	
							'include_fields_label' => 0,						
							'theme' => 'ihc-theme_1',
							'color_scheme' => '0a9fd8',
							'columns' => 5,
							'inside_page' => 0,
							'align_center' => 1,
							'slider_set' => 0,
							'items_per_slide' => 2,
							'speed' => 5000,
							'pagination_speed' => 500,
							'bullets' => 1,
							'nav_button' => 1,
							'autoplay' => 1,
							'stop_hover' => 0,
							'autoplay' => 1,
							'stop_hover' => 0,
							'responsive' => 0,
							'autoheight' => 0,
							'lazy_load' => 0,
							'loop' => 1,
							'pagination_theme' => 'pag-theme1',					
						);
?>
<script>
	var uap_plugin_url = '<?php echo UAP_URL;?>';
	jQuery(document).ready(function(){
		uap_preview_u_list();
	});
</script>	
	<div class="uap-user-list-wrap">
			<div class="uap-page-title">Ultimate Affiliate Pro - 
				<span class="second-text"><?php _e('Top Affiliates List', 'uap');?></span>
			</div>
			<div class="uap-user-list-settings-wrapper">
				<div class="box-title">
		            <h3><i class="fa-uap fa-icon-angle-down-uap"></i><?php _e("ShortCode Generator", 'uap')?></h3>
		            <div class="actions pointer">
					    <a onclick="jQuery('#the_uap_user_list_settings').slideToggle();" class="btn btn-mini content-slideUp">
		                    <i class="fa-uap fa-icon-cogs-uap"></i>
		                </a>		                
					</div>
				 	<div class="clear"></div>
				</div>					
				<div id="the_uap_user_list_settings" class="uap-list-users-settings">
				
					<!-- DISPLAY ENTRIES -->
					<div class="uap-column column-one">
                   		<h4 style="background-color: rgb(66, 66, 66);"><i class="fa-uap fa-icon-dispent-uap"></i><?php _e('Display Entries', 'uap');?></h4>
						<div class="uap-settings-inner">
							<div class="uap-user-list-row">
								<div class="uap-label"><?php _e("Number Of Entries:", 'uap');?></div>
								<div class="uap-field"><input type="number" value="<?php echo $meta_arr['num_of_entries'];?>" id="num_of_entries" onKeyUp="uap_preview_u_list();" onChange="uap_preview_u_list();" style="width: 81px;" min="0" /></div>
							</div>
							<div class="uap-user-list-row">
								<div class="uap-label"><?php _e("Entries Per Page:", 'uap');?></div>
								<div class="uap-field"><input type="number" value="<?php echo $meta_arr['entries_per_page'];?>" id="entries_per_page" onKeyUp="uap_preview_u_list();" onChange="uap_preview_u_list();" style="width: 81px;" min="1" /></div>
							</div>		
							<div class="uap-spacewp_b_divs"></div>					
							<div class="uap-user-list-row">
								<div class="uap-label"><?php _e("Order By:", 'uap');?></div>
								<div class="uap-field">
									<select id="order_by" onChange="uap_preview_u_list();">
										<?php 
											$arr = array( 
														  'referrals' => __('Referrals', 'uap'),
														  'earnings' => __('Earnings', 'uap'), 
														  'visits' => __('Visits', 'uap'),
														  'user_registered' => __('Register Date','uap'), 
														  'user_login' => __("UserName", 'uap'),
														  'user_email' => __("E-mail Address", 'uap'),
														  'random' => __("Random", 'uap'),
											);
											foreach ($arr as $k=>$v){
												$selected = ($meta_arr['order_by']==$k) ? 'selected' : ''; 
												?>
												<option value="<?php echo $k?>" <?php echo $selected;?>><?php echo $v;?></option>	
												<?php 
											}
										?>
									</select>
								</div>
							</div>
							<div class="uap-user-list-row">
								<div class="uap-label"><?php _e("Order Type:", 'uap');?></div>
								<div class="uap-field">
									<select id="order_type" onChange="uap_preview_u_list();">
										<?php 
											foreach (array('asc'=>'ASC', 'desc'=>'DESC') as $k=>$v){
												$selected = ($meta_arr['order_type']==$k) ? 'selected' : ''; 
												?>
												<option value="<?php echo $k?>" <?php echo $selected;?>><?php echo $v;?></option>	
												<?php 
											}
										?>
									</select>
								</div>
							</div>	

							<div class="uap-spacewp_b_divs"></div>	
							<div class="uap-user-list-row">
								<div class="uap-label"><?php _e("Filter By Rank", 'uap');?></div>
								<div class="uap-field">
									<input type="checkbox" id="filter_by_rank" onClick="uap_checkbox_div_relation(this, '#ranks_in__wrap_div');uap_preview_u_list();" />
								</div>
							</div>	
							<div class="uap-user-list-row" id="ranks_in__wrap_div" style="opacity: 0.5;">
								<div class="uap-label"><?php _e("User's Ranks:", 'uap');?></div>
								<div class="uap-field">
									<?php 									
										$ranks = $indeed_db->get_ranks();
										if ($ranks){
											?>
											<select class="uap-form-select " onchange="uap_writeTagValue_list_users(this, '#ranks_in', '#uap-select-ranks-view-values', 'uap-ranks-select-v-');uap_preview_u_list();">
												<option value=""></option>
											<?php 
											foreach ($ranks as $object){
												?>
													<option value="<?php echo $object->id;?>"><?php echo $object->label;?>
												<?php												
											}
											?>
											</select>
											<?php 
										} 
									?>
									
								</div>
								<div id="uap-select-ranks-view-values"></div>
									<input type="hidden" value="" id="ranks_in" />	
							</div>																		
						</div>						
					</div>
					<!-- /DISPLAY ENTRIES -->
					
					
					
					<!-- TEMPLATE -->
					<div class="uap-column column-three">
						<h4 style="background: #1fb5ac;"><i class="fa-uap fa-icon-temp-uap"></i>Template</h4>
						<div class="uap-settings-inner">
							<div class="uap-user-list-row">
								<div class="uap-label"><?php _e("Select Theme", 'uap');?></div>
								<div class="uap-field">
									<select id="theme" onChange="uap_preview_u_list();"><?php 
										$themes = array('uap-theme_1' => __('Theme', 'uap') . ' 1',
														'uap-theme_2' => __('Theme', 'uap') . ' 2',
														'uap-theme_3' => __('Theme', 'uap') . ' 3',
														'uap-theme_4' => __('Theme', 'uap') . ' 4',
														'uap-theme_5' => __('Theme', 'uap') . ' 5',
														'uap-theme_6' => __('Theme', 'uap') . ' 6',
														'uap-theme_7' => __('Theme', 'uap') . ' 7',
														'uap-theme_8' => __('Theme', 'uap') . ' 8',
														'uap-theme_9' => __('Theme', 'uap') . ' 9',
														'uap-theme_10' => __('Theme', 'uap') . ' 10',
												);
										foreach ($themes as $k=>$v){
											$selected = ($meta_arr['theme']==$k) ? 'selected' : '';
											?>
											<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
											<?php 
										}
									?></select>
								</div>
							</div>
							<div class="uap-user-list-row">
								<div class="uap-label"><?php _e("Color Scheme", 'uap');?></div>
								<div class="uap-field">
		                            <ul id="colors_ul" class="colors_ul">
		                                <?php
		                                    $color_scheme = array('0a9fd8', '38cbcb', '27bebe', '0bb586', '94c523', '6a3da3', 'f1505b', 'ee3733', 'f36510', 'f8ba01');
		                                    $i = 0;
		                                    foreach ($color_scheme as $color){
		                                        if( $i==5 ) echo "<div class='clear'></div>";
		                                        $class = ($meta_arr['color_scheme']==$color) ? 'color-scheme-item-selected' : 'color-scheme-item';
		                                        ?>
		                                            <li class="<?php echo $class;?>" onClick="uap_change_color_scheme(this, '<?php echo $color;?>', '#color_scheme');uap_preview_u_list();" style="background-color: #<?php echo $color;?>;"></li>
		                                        <?php
		                                        $i++;
		                                    }
		                                ?>
										<div class='clear'></div>
		                            </ul>
		                            <input type="hidden" id="color_scheme" value="<?php echo $meta_arr['color_scheme'];?>" />								
								</div>
							</div>
							<div class="uap-user-list-row">
								<div class="uap-label"><?php _e("Columns", 'uap');?></div>
								<div class="uap-field">
									<select id="columns" onChange="uap_preview_u_list();"><?php 
										for ($i=1; $i<7; $i++){
											$selected = ($i==$meta_arr['columns']) ? 'selected' : '';
											?>
											<option value="<?php echo $i;?>" <?php echo $selected;?>><?php echo $i . __(" Columns", 'uap')?></option>
											<?php 
										}
									?></select>
								</div>
							</div>	
							<div class="uap-user-list-row" style="padding-top: 10px;">	
								<div class="uap-label"><?php _e("Additional Options", 'uap');?></div>
							</div>	
							<div class="uap-user-list-row">							
								<?php $checked = (empty($meta_arr['align_center'])) ? '' : 'checked';?>
								<input type="checkbox" id="align_center" <?php echo $checked;?> onClick="uap_preview_u_list();"/> <?php _e("Align the Items Centered", 'uap');?>
							</div>	
							
							<div class="uap-user-list-row">	
								<?php $checked = ($meta_arr['include_fields_label']) ? 'checked' : '';?>
								<input type="checkbox" class="" id="include_fields_label" onClick="uap_preview_u_list();" <?php echo $checked;?> />  
								<?php _e('Show Fields Label', 'uap');?> 								
							</div>																	
						</div>
					</div>
					<!-- /TEMPLATE -->
					
					<!-- SLIDER -->
					<div class="uap-column column-four" style="width:50%;">
						<h4 style="background: rgba(240, 80, 80, 1.0);"><i class="fa-uap fa-icon-slider-uap"></i><?php _e("Slider ShowCase", 'uap');?></h4>
						<div class="uap-settings-inner">
							<div class="uap-user-list-row">
								<?php $checked = (empty($meta_arr['slider_set'])) ? '' : 'checked';?>
								<input type="checkbox" <?php echo $checked;?> id="slider_set" onClick="uap_checkbox_div_relation(this, '#slider_options');uap_preview_u_list();"/> <b><?php echo __('Show as Slider', 'uap');?></b>
	                 		 	<div class="extra-info" style="display:block;"><?php echo __('If Slider Showcase is used, Pagination Showcase is disabled.', 'uap');?></div> 
							</div>
							<div style="opacity:0.5" id="slider_options" >
							
						     <div class="splt-1">
								<div class="uap-user-list-row">
									<div class="uap-label"><?php _e('Items per Slide:', 'uap');?></div>
									<div class="uap-field">
										<input type="number" min="1" id="items_per_slide" onChange="uap_preview_u_list();" onKeyup="uap_preview_u_list();" value="<?php echo $meta_arr['items_per_slide'];?>" class=""/>
									</div>
								</div>
								<div class="uap-user-list-row">
									<div class="uap-label"><?php _e('Slider Timeout:', 'uap');?></div>
									<div class="uap-field">
										<input type="number" min="1" id="speed" onChange="uap_preview_u_list();" onKeyup="uap_preview_u_list();" value="<?php echo $meta_arr['speed'];?>" class=""/>
									</div>
								</div>
								<div class="uap-user-list-row">
									<div class="uap-label"><?php _e('Pagination Speed:', 'uap');?></div>
									<div class="uap-field">
										<input type="number" min="1" id="pagination_speed" onChange="uap_preview_u_list();" onKeyup="uap_preview_u_list();" value="<?php echo $meta_arr['pagination_speed'];?>" class=""/>
									</div>
								</div>
								 <div class="uap-user-list-row">
	                          		<div class="uap-label"><?php _e('Pagination Theme:', 'uap');?></div>
	                          		<div class="uap-field">
		                          		<select id="pagination_theme" onChange="uap_preview_u_list();" style="min-width:162px;"><?php 
		                          			$array = array(
		                          								'pag-theme1' => __('Pagination Theme 1', 'uap'),
		                          								'pag-theme2' => __('Pagination Theme 2', 'uap'),
		                          								'pag-theme3' => __('Pagination Theme 3', 'uap'),
		                          							);
		                          			foreach ($array as $k=>$v){
		                          				$selected = ($k==$meta_arr['pagination_theme']) ? 'selected' : '';
		                          				?>
		                          				<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
		                          				<?php 
		                          			}
		                          		?>
		                                </select>
	                          		</div>
	                          </div>
	                          
	                            <div class="uap-user-list-row">
	                          		<div class="uap-label"><?php _e('Animation Slide In', 'uap');?></div>
	                          		<div class="uap-field">
	                                  <select onChange="uap_preview_u_list();" id="animation_in" style="min-width:162px;">
										  <option value="none">None</option>
										  <option value="fadeIn">fadeIn</option>
										  <option value="fadeInDown">fadeInDown</option>
										  <option value="fadeInUp">fadeInUp</option>
										  <option value="slideInDown">slideInDown</option>
										  <option value="slideInUp">slideInUp</option>
										  <option value="flip">flip</option>
										  <option value="flipInX">flipInX</option>
										  <option value="flipInY">flipInY</option>
										  <option value="bounceIn">bounceIn</option>
										  <option value="bounceInDown">bounceInDown</option>
										  <option value="bounceInUp">bounceInUp</option>
										  <option value="rotateIn">rotateIn</option>
										  <option value="rotateInDownLeft">rotateInDownLeft</option>
										  <option value="rotateInDownRight">rotateInDownRight</option>
										  <option value="rollIn">rollIn</option>
										  <option value="zoomIn">zoomIn</option>
										  <option value="zoomInDown">zoomInDown</option>
										  <option value="zoomInUp">zoomInUp</option>
									  </select>                          		
	                          		</div>
	                          	</div>
	                          
	                          
	                          <div class="uap-user-list-row">
	                          		<div class="uap-label"><?php _e('Animation Slide Out', 'uap');?></div>
	                          		<div class="uap-field">
	                                    <select onChange="uap_preview_u_list();" id="animation_out" style="min-width:162px;">
										  <option value="none">None</option>
										  <option value="fadeOut">fadeOut</option>
										  <option value="fadeOutDown">fadeOutDown</option>
										  <option value="fadeOutUp">fadeOutUp</option>
										  <option value="slideOutDown">slideOutDown</option>
										  <option value="slideOutUp">slideOutUp</option>
										  <option value="flip">flip</option>
										  <option value="flipOutX">flipOutX</option>
										  <option value="flipOutY">flipOutY</option>
										  <option value="bounceOut">bounceOut</option>
										  <option value="bounceOutDown">bounceOutDown</option>
										  <option value="bounceOutUp">bounceOutUp</option>
										  <option value="rotateOut">rotateOut</option>
										  <option value="rotateOutUpLeft">rotateOutUpLeft</option>
										  <option value="rotateOutUpRight">rotateOutUpRight</option>
										  <option value="rollOut">rollOut</option>
										  <option value="zoomOut">zoomOut</option>
										  <option value="zoomOutDown">zoomOutDown</option>
										  <option value="zoomOutUp">zoomOutUp</option>
									  </select>                        		
	                          		</div>                          	
	                          </div>	
							</div>
							<div class="splt-2">	
								
								<div class="uap-user-list-row">
	                          		<div class="uap-label"><?php _e('Additional Options', 'uap');?></div>
								</div>
								<div class="uap-user-list-row">
									<?php $checked = (empty($meta_arr['bullets'])) ? '' : 'checked';?>
									<input type="checkbox" id="bullets" onClick="uap_preview_u_list();" <?php echo $checked;?> /> <?php _e("Bullets", 'uap');?>
								</div>
								<div class="uap-user-list-row">
									<?php $checked = (empty($meta_arr['nav_button'])) ? '' : 'checked';?>
									<input type="checkbox" id="nav_button" onClick="uap_preview_u_list();" <?php echo $checked;?> /> <?php _e("Nav Button", 'uap');?>
								</div>	
								<div class="uap-user-list-row">
									<?php $checked = (empty($meta_arr['autoplay'])) ? '' : 'checked';?>
									<input type="checkbox" id="autoplay" onClick="uap_preview_u_list();" <?php echo $checked;?> /> <?php _e("AutoPlay", 'uap');?>
								</div>	
								<div class="uap-user-list-row">
									<?php $checked = (empty($meta_arr['stop_hover'])) ? '' : 'checked';?>
									<input type="checkbox" id="stop_hover" onClick="uap_preview_u_list();" <?php echo $checked;?> /> <?php _e("Stop On Hover", 'uap');?>
								</div>		
								<div class="uap-user-list-row">
									<?php $checked = (empty($meta_arr['responsive'])) ? '' : 'checked';?>
									<input type="checkbox" id="responsive" onClick="uap_preview_u_list();" <?php echo $checked;?> /> <?php _e("Responsive", 'uap');?>
								</div>
								<div class="uap-user-list-row">
									<?php $checked = (empty($meta_arr['autoheight'])) ? '' : 'checked';?>
									<input type="checkbox" id="autoheight" onClick="uap_preview_u_list();" <?php echo $checked;?> /> <?php _e("Auto Height", 'uap');?>
								</div>																	
								<div class="uap-user-list-row">
									<?php $checked = (empty($meta_arr['lazy_load'])) ? '' : 'checked';?>
									<input type="checkbox" id="lazy_load" onClick="uap_preview_u_list();" <?php echo $checked;?> /> <?php _e("Lazy Load", 'uap');?>
								</div>
								<div class="uap-user-list-row">
									<?php $checked = (empty($meta_arr['loop'])) ? '' : 'checked';?>
									<input type="checkbox" id="loop" onClick="uap_preview_u_list();" <?php echo $checked;?> /> <?php _e("Play in Loop", 'uap');?>
								</div>																				
							</div>	
	                         
		        			<div class="clear"></div>																												
							</div>
						</div>
					</div>
					<!-- /SLIDER -->
		        <div class="clear"></div>
					<!-- ENTRY INFO -->
					<div class="uap-column column-two" style="float:none; width:100%;">
                  		<h4 style="background: #9972b5;"><i class="fa-uap fa-icon-entryinfo-uap"></i><?php _e('Displayed User Fields', 'uap');?></h4>
				  		<div class="uap-settings-inner">
				  			<div class="uap-user-list-row">
				  				<?php 
				  					$fields = array('user_login' => 'Username', 
				  									'uap_avatar' => 'Avatar',
				  									'user_email' => 'Email', 
				  									'first_name'=>'First Name',
				  									'last_name' => 'Last Name',
				  									'earnings' => __('Earnings', 'uap'),
				  									'referrals' => __('Referrals', 'uap'),
				  									'visits' => __('Visits', 'uap'),
				  									);
									$green_color = array('earnings', 'referrals', 'visits');
				  					$defaults = explode(',', $meta_arr['user_fields']);
									global $indeed_db;
									$reg_fields = $indeed_db->register_get_custom_fields();

				  					$exclude = array('pass1', 'pass2', 'tos', 'recaptcha', 'confirm_email');
									foreach ($reg_fields as $k=>$v){
										if (!in_array($v['name'], $exclude)){
											if (isset($v['native_wp']) && $v['native_wp']){
												$extra_fields[$v['name']] = __($v['label'], 'uap');
											} else {
												$extra_fields[$v['name']] = $v['label'];
											}
											if (empty($extra_fields[$v['name']])){
												unset($extra_fields[$v['name']]);	
											}
										}										
									}
									
				  					$fields_arr = array_merge($fields, $extra_fields);
				  					
				  					foreach ($fields_arr as $k=>$v){
				  						$checked = (in_array($k, $defaults)) ? 'checked' : '';
				  						$color = (in_array($v, $fields)) ? '#0a9fd8' : '#000';
				  						if (in_array($k, $green_color)){
				  							$color = '#0bb586';
				  						}
				  						?>
				  						<div class="uap-memberslist-fields" style="color: <?php echo $color;?>;">
				  							<input type="checkbox" <?php echo $checked;?> value="<?php echo $k;?>" onClick="uap_make_inputh_string(this, '<?php echo $k;?>', '#user_fields');uap_preview_u_list();" /> <?php echo $v;?>
				  						</div>
				  						<?php 
				  					}
				  				?>
				  				<input type="hidden" value="<?php echo $meta_arr['user_fields'];?>" id="user_fields" />
				  			</div>				  			
				  		</div>	                    				  		
				  	</div>
					<!-- /ENTRY INFO -->
				</div>
		        <div class="clear"></div>
			</div>
			
			<div class="uap-user-list-shortcode-wrapp">
		        <div class="content-shortcode">
		            <div>
		                <span style="font-weight:bolder; color: #fff; font-style:italic; font-size:13px;"><?php echo __('ShortCode :', 'uap');?> </span>
		                <span class="the-shortcode"></span>
		            </div>
		            <div style="margin-top:10px;">
		                <span style="font-weight:bolder; color: #fff; font-style:italic; font-size:13px;"><?php echo __('PHP Code:', 'uap');?> </span>
		                <span class="php-code"></span>
		            </div>
		        </div>
		    </div>
	    
	    	<div class="uap-user-list-preview">
			    <div class="box-title">
			        <h2><i class="fa-uap fa-icon-eyes-uap"></i><?php echo __('Preview', 'uap');?></h2>
			            <div class="actions-preview pointer">
						    <a onclick="jQuery('#preview').slideToggle();" class="btn btn-mini content-slideUp">
			                    <i class="fa-uap fa-icon-cogs-uap"></i>
			                </a>
						</div>
			        <div class="clear"></div>
			    </div>
			    <div id="preview" class="uap-preview"></div>
			</div>

	</div>
	
<?php



