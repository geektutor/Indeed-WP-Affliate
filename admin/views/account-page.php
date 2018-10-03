<?php
require_once UAP_PATH . 'public/font_awesome_codes.php';
$font_awesome = uap_return_font_awesome();
?>
<style>
<?php foreach ($font_awesome as $base_class => $code):?>
	<?php echo '.' . $base_class . ':before';?>{
		content: '\<?php echo $code;?>';
	}
<?php endforeach;?>
</style>
<style>
<?php foreach ($data['available_tabs'] as $k=>$v):?>
	<?php echo '.fa-' . $k . '-account-uap:before';?>{
		content: '\<?php echo $v['uap_tab_' . $k . '_icon_code'];?>';
	}
<?php endforeach;?>
</style>


<div class="uap-page-title">Ultimate Affiliates Pro -
	<span class="second-text">
		<?php _e('Accont Page', 'uap');?>
	</span>
</div>
<div class="uap-stuffbox">
	<div class="uap-shortcode-display">
		[uap-account-page]
	</div>
</div>

<div class="metabox-holder indeed">
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Top Section:', 'uap');?></h3>

			<div class="inside">

			<div class="uap-register-select-template" style="padding:20px 0 35px 20px;">
				<?php _e('Select Template:', 'uap');?>
				<select name="uap_ap_top_theme"  style="min-width:300px; margin-left:10px;"><?php
					foreach ($data['top_themes'] as $k=>$v){
						$selected = ($k==$data['metas']['uap_ap_top_theme']) ? 'selected' : '';
						?>
						<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
						<?php
					}
				?></select>
			</div>
			<div class="inside">
			 <div class="input-group">
				<label class="uap_label_shiwtch uap-onbutton">
					<?php $checked = ($data['metas']['uap_ap_edit_show_avatar']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_ap_edit_show_avatar');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" value="<?php echo $data['metas']['uap_ap_edit_show_avatar'];?>" name="uap_ap_edit_show_avatar" id="uap_ap_edit_show_avatar" />
				<label><?php _e('Show Avatar Image', 'uap');?></label>
			</div>
			 <div class="input-group">
				<label class="uap_label_shiwtch uap-onbutton">
					<?php $checked = ($data['metas']['uap_ap_edit_show_earnings']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_ap_edit_show_earnings');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" value="<?php echo $data['metas']['uap_ap_edit_show_earnings'];?>" name="uap_ap_edit_show_earnings" id="uap_ap_edit_show_earnings" />
				<label><?php _e('Show Earning', 'uap');?></label>
			</div>
			 <div class="input-group">
				<label class="uap_label_shiwtch uap-onbutton">
					<?php $checked = ($data['metas']['uap_ap_edit_show_referrals']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_ap_edit_show_referrals');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" value="<?php echo $data['metas']['uap_ap_edit_show_referrals'];?>" name="uap_ap_edit_show_referrals" id="uap_ap_edit_show_referrals" />
				<label><?php _e('Show Referrals', 'uap');?></label>
			</div>
			 <div class="input-group">
				<label class="uap_label_shiwtch uap-onbutton">
					<?php $checked = ($data['metas']['uap_ap_edit_show_achievement']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_ap_edit_show_achievement');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" value="<?php echo $data['metas']['uap_ap_edit_show_achievement'];?>" name="uap_ap_edit_show_achievement" id="uap_ap_edit_show_achievement" />
				<label><?php _e('Show Achievement', 'uap');?></label>
			</div>
			 <div class="input-group">
				<label class="uap_label_shiwtch uap-onbutton">
					<?php $checked = ($data['metas']['uap_ap_edit_show_rank']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_ap_edit_show_rank');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" value="<?php echo $data['metas']['uap_ap_edit_show_rank'];?>" name="uap_ap_edit_show_rank" id="uap_ap_edit_show_rank" />
				<label><?php _e('Show Rank', 'uap');?></label>
			</div>
				<div class="input-group">
					 <label class="uap_label_shiwtch uap-onbutton">
						 <?php $checked = ($data['metas']['uap_ap_edit_show_metrics']) ? 'checked' : '';?>
						 <input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_ap_edit_show_metrics');" <?php echo $checked;?> />
						 <div class="switch" style="display:inline-block;"></div>
					 </label>
					 <input type="hidden" value="<?php echo $data['metas']['uap_ap_edit_show_metrics'];?>" name="uap_ap_edit_show_metrics" id="uap_ap_edit_show_metrics" />
					 <label><?php _e('Show EPC Metrics', 'uap');?></label>
			 	</div>

			</div>

			<div class="inside" style="padding-bottom:30px;">
				<h4><?php _e('Welcome Message:', 'uap');?></h4>
				<div class="uap-wp_editor" style="width:60%; display: inline-block; vertical-align: top;">
				<?php wp_editor(stripslashes($data['metas']['uap_ap_welcome_msg']), 'uap_ap_welcome_msg', array('textarea_name'=>'uap_ap_welcome_msg', 'editor_height'=>200));?>
				</div>
				<div style="width: 19%; display: inline-block; vertical-align: top; margin-left: 10px; color: #333;">
					<h4><?php _e('Regular constants', 'uap');?></h4>
					<?php
						$constants = array(	"{username}",
											"{first_name}",
											"{last_name}",
											"{user_id}",
											"{user_email}",
											"{user_registered}",
											"{flag}",
											"{account_page}",
											"{login_page}",
											"{blogname}",
											"{blogurl}",
											"{siteurl}",
											'{rank_id}',
											'{rank_name}'
							);
						$extra_constants = uap_get_custom_constant_fields();
						foreach ($constants as $v){
							?>
							<div><?php echo $v;?></div>
							<?php
						}
						?>
						</div>
						<div style="width: 19%; display: inline-block; vertical-align: top; margin-left: 10px; color: #333;">
							<h4><?php _e('Custom Fields constants', 'uap');?></h4>
						<?php
						foreach ($extra_constants as $k=>$v){
							?>
							<div><?php echo $k;?></div>
							<?php
						}
					?>
							</div>
				</div>
				<div class="uap-clear"></div>

			<div class="inside">
				<div class="input-group">
				<h3><?php _e('Background/Banner Image:', 'uap');?></h3>
				<p><?php _e('The cover or background image, based on what theme you have chosen', 'uap');?></p>
				<div class="input-group">
				<label class="uap_label_shiwtch uap-onbutton">
					<?php $checked = ($data['metas']['uap_ap_edit_background']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_ap_edit_background');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" value="<?php echo $data['metas']['uap_ap_edit_background'];?>" name="uap_ap_edit_background" id="uap_ap_edit_background" />
				<label></label>
				</div>
				<div class="form-group" style="margin:20px 0 10px 10px">
					<input type="text" class="form-control" onClick="open_media_up(this);" value="<?php  echo $data['metas']['uap_ap_edit_background_image'];?>" name="uap_ap_edit_background_image" id="uap_ap_edit_background_image" style="width: 90%;display: inline; float:none; min-width:500px;"/>
					<i class="fa-uap fa-trash-uap" onclick="jQuery('#uap_ap_edit_background_image').val('');" title="<?php _e('Remove Background Image', 'uap');?>"></i>

				</div>
			</div>
			</div>
			<div class="inside">
				<div class="uap-wrapp-submit-bttn">
						<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large"  style="min-width:50px;" />

					</div>
			</div>
		</div>
		</div>
		<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Content Section:', 'uap');?></h3>

			<div class="inside">
			<div class="uap-register-select-template" style="padding:20px 0 35px 20px;">
				<?php _e('Select Template:', 'uap');?>
				<select name="uap_ap_theme"  style="min-width:300px; margin-left:10px;"><?php
					foreach ($data['themes'] as $k=>$v){
						$selected = ($k==$data['metas']['uap_ap_theme']) ? 'selected' : '';
						?>
						<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
						<?php
					}
				?></select>
			</div>
			</div>
			<div>
			  <div class="inside" style="margin-top: 0px;">
				<h3 style="margin-top: 0px;"><?php _e('Menu Tabs:', 'uap');?></h3>
				<div style="display: inline-block; vertical-align: top">
					<div class="uap-ap-tabs-list">
						<?php foreach ($data['available_tabs'] as $k=>$v):?>
							<div class="uap-ap-tabs-list-item" onClick="uap_ap_make_visible('<?php echo $k;?>', this);" id="<?php echo 'uap_tab-' . $k;?>"><?php echo $v['uap_tab_' . $k . '_menu_label'];?></div>
						<?php endforeach;?>
						<div class="uap-clear"></div>
					</div>
					<div class="uap-ap-tabs-settings">
						<?php

						$tabs = explode(',', $data['metas']['uap_ap_tabs']);
						$i = 0;
						foreach ($data['available_tabs'] as $k=>$v):?>
							<div class="uap-ap-tabs-settings-item" id="<?php echo 'uap_tab_item_' . $k;?>">
								<div class="input-group">
									<h4><?php echo $v['uap_tab_' . $k . '_menu_label'];?></h4>
									<span class="uap-labels-onbutton" style="font-weight:400; min-width:100px;"><?php _e('Activate the Tab:', 'uap');?></span>
									<label class="uap_label_shiwtch  uap-onbutton">
										<?php $checked = (in_array($k, $tabs)) ? 'checked' : '';?>
										<input type="checkbox" class="uap-switch" onClick="uap_make_inputh_string(this, '<?php echo $k;?>', '#uap_ap_tabs');" <?php echo $checked;?> />
										<div class="switch" style="display:inline-block;"></div>
									</label>
								</div>
								<?php if (isset($data['metas']['uap_tab_' . $k . '_menu_label'])) : ?>
									<div class="input-group" style="max-width:40%;">
										<span class="input-group-addon" id="basic-addon1"><?php _e('Menu Label', 'uap');?></span>
										<input type="text" class="form-control" placeholder="" value="<?php echo $data['metas']['uap_tab_' . $k . '_menu_label'];?>" name="<?php echo 'uap_tab_' . $k . '_menu_label';?>">
									</div>
								<?php endif;?>
								<?php if (isset($data['metas']['uap_tab_' . $k . '_title'])) : ?>
									<div class="input-group" style="max-width:40%;">
										<span class="input-group-addon" id="basic-addon1"><?php _e('Title', 'uap');?></span>
										<input type="text" class="form-control" placeholder="" value="<?php echo $data['metas']['uap_tab_' . $k . '_title'];?>" name="<?php echo 'uap_tab_' . $k . '_title';?>">
									</div>
								<?php endif;?>


									<!-- ICON SELECT - SHINY -->
									<div class="row" style="margin-left:0px;">
										<div class="col-xs-4" style="margin-bottom: 10px;">
									   		<div class="input-group" style="margin:0px 0 15px 0;">
												<label><?php _e('Icon', 'uap');?></label>
											<div class="uap-icon-select-wrapper">
												<div class="uap-icon-input">
													<div id="<?php echo 'indeed_shiny_select_' . $k;?>" class="uap-shiny-select-html"></div>
												</div>
								   				<div class="uap-icon-arrow" id="<?php echo 'uap_icon_arrow_' . $k;?>"><i class="fa-uap fa-arrow-uap"></i></div>
												<div class="uap-clear"></div>
											</div>

									   		</div>
										</div>
									</div>
									<script>
									jQuery(document).ready(function(){
										var <?php echo 'uap_shiny_object_' . $i;$i++;?> = new indeed_shiny_select({
													selector: '<?php echo '#indeed_shiny_select_' . $k;?>',
													item_selector: '.uap-font-awesome-popup-item',
													option_name_code: '<?php echo 'uap_tab_' . $k . '_icon_code';?>',
													option_name_icon: '<?php echo 'uap_tab_' . $k . '_icon_class';?>',
													default_icon: '<?php echo 'fa-uap fa-' . $k . '-account-uap';?>',
													default_code: '<?php echo $data['metas']['uap_tab_' . $k . '_icon_code'];?>',
													init_default: true,
													second_selector: '<?php echo '#uap_icon_arrow_' . $k;?>'
										});
									});
									</script>
									<!-- ICON SELECT - SHINY -->


								<?php if (isset($data['metas']['uap_tab_' . $k . '_content'])) : ?>
									<div style="margin-top:20px;">
										<div style="width: 60%; display: inline-block; vertical-align: top; box-sizing:border-box;"><?php
											wp_editor(stripslashes($data['metas']['uap_tab_' . $k . '_content']), 'uap_tab_' . $k . '_content', array('textarea_name' => 'uap_tab_' . $k . '_content', 'editor_height'=>200));
										?></div>
										<div style="width: 19%; display: inline-block; vertical-align: top; padding-left: 10px; box-sizing:border-box; color: #333;">
											<?php
												echo "<h4>" . __('Regular constants', 'uap') . "</h4>";
												foreach ($constants as $v){
													?>
													<div><?php echo $v;?></div>
													<?php
												}
										?>
										</div>
										<div style="width: 19%; display: inline-block; vertical-align: top; padding-left: 10px; box-sizing:border-box; color: #333;">
										<?php
												echo "<h4>" . __('Custom Fields constants', 'uap') . "</h4>";
												foreach ($extra_constants as $k=>$v){
													?>
													<div><?php echo $k;?></div>
													<?php
												}
											?>
										</div>
									</div>
								<?php endif;?>
							</div>
						<?php endforeach;?>
					</div>
				</div>
				<input type="hidden" value="<?php echo $data['metas']['uap_ap_tabs'];?>" id="uap_ap_tabs" name="uap_ap_tabs" />
				<div class="uap-wrapp-submit-bttn">
						<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large"  style="min-width:50px;" />
					</div>
			   </div>
			</div>
		</div>
		<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Footer Section:', 'uap');?></h3>

			<div class="inside" style="padding-bottom:30px;">
				<h4><?php _e('Footer Content:', 'uap');?></h4>
				<div class="uap-wp_editor" style="width:60%; display: inline-block; vertical-align: top;">
				<?php wp_editor(stripslashes($data['metas']['uap_ap_footer_msg']), 'uap_ap_footer_msg', array('textarea_name'=>'uap_ap_footer_msg', 'editor_height'=>200));?>
				</div>
				<div style="width: 19%; display: inline-block; vertical-align: top; margin-left: 10px; color: #333;">
					<h4><?php _e('Regular constants', 'uap');?></h4>
					<?php
						$constants = array(	"{username}",
											"{first_name}",
											"{last_name}",
											"{user_id}",
											"{user_email}",
											"{user_registered}",
											"{account_page}",
											"{login_page}",
											"{blogname}",
											"{blogurl}",
											"{siteurl}",
											'{rank_id}',
											'{rank_name}'
							);
						$extra_constants = uap_get_custom_constant_fields();
						foreach ($constants as $v){
							?>
							<div><?php echo $v;?></div>
							<?php
						}
						?>
						</div>
						<div style="width: 19%; display: inline-block; vertical-align: top; margin-left: 10px; color: #333;">
							<h4><?php _e('Custom Fields constants', 'uap');?></h4>
						<?php
						foreach ($extra_constants as $k=>$v){
							?>
							<div><?php echo $k;?></div>
							<?php
						}
					?>
							</div>
				</div>
				<div class="uap-clear"></div>
				<div class="uap-wrapp-submit-bttn">
						<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large"  style="min-width:50px;" />
					</div>
		</div>
			<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Additional Settings:', 'uap');?></h3>
			<div class="uap-form-line">
				<h2><?php _e('Custom CSS:', 'uap');?></h2>
					<textarea id="uap_account_page_custom_css"  name="uap_account_page_custom_css" class="uap-dashboard-textarea-full"  style="width: 100%; height: 120px;"><?php echo stripslashes($data['metas']['uap_account_page_custom_css']);?></textarea>
					<div class="uap-wrapp-submit-bttn">
						<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large"  style="min-width:50px;" />
					</div>
			</div>

		</div>
</form>
</div>
<script>
jQuery(document).ready(function(){
	uap_ap_make_visible('overview', '#uap_tab-overview');
});
</script>
