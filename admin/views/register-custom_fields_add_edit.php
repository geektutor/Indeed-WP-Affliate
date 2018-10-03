<form method="post" action="<?php echo $data['form_submit'];?>">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('User Custom Fields', 'uap');?></h3>
		<div class="inside">
			<div class="uap-form-line">
				<label class="uap-label"><?php _e('Slug:', 'uap');?></label>
				<input type="text" name="name" value="<?php echo $data['metas']['name'];?>" <?php echo $data['disabled'];?> />
			</div>
			<div class="uap-form-line">
				<label class="uap-label"><?php _e('Field Type:', 'uap');?></label>
				<select id="field_type" <?php if ($data['disabled']) echo 'disabled'; else echo 'name="type"';?> onChange="uap_register_fields(this.value);">
					<?php foreach ($data['field_types'] as $k=>$v): ?>
						<?php $selected = ($data['metas']['type']==$k) ? 'selected' : '';?>
						<option value="<?php echo $k?>" <?php echo $selected;?>><?php echo $v?></option>
					<?php endforeach;?>
				</select>
			</div>	
			
			<?php 
				$display = 'none';
				if (($data['metas']['type']=='select' || $data['metas']['type']=='checkbox' || $data['metas']['type']=='radio' 
					|| $data['metas']['type']=='multi_select') && ($data['metas']['name']!='tos')){
						$display = 'block';
				}
			?>
			<div class="uap-form-line" id="uap-register-field-values" style="display: <?php echo $display;?>">
				<label class="uap-label" style="vertical-align: top;"><?php _e('Values', 'uap');?></label>
					<div style="display: inline-block;" class="uap-register-the-values">
					<?php 
						if (isset($data['metas']['values']) && $data['metas']['values']){
							foreach ($data['metas']['values'] as $value){
							?>
								<div style="display: block; margin-bottom: 5px;" class="uap-custom-field-item-wrapp">
									<input type="text" name="values[]" value="<?php echo uap_correct_text($value);?>" style="min-width:250px;"/>
									<i class="fa-uap fa-remove-uap" style="cursor: pointer;" onclick="jQuery(this).parent().remove();"></i>
									<i class="fa-uap fa-arrows-uap"></i>
								</div>
							<?php 
							}
						} else {
						?>
							<div style="display: block; margin-bottom: 5px;" class="uap-custom-field-item-wrapp">
								<input type="text" name="values[]" value=""/>
								<i class="fa-uap uap-icon-remove-e" style="cursor: pointer;" onclick="jQuery(this).parent().remove();"></i>
								<i class="fa-uap fa-arrows-uap"></i>
							</div>
						<?php 
						}
						?>														 
					</div>
				<div class="uap-clear"></div>
				<div style="display: inline-block; margin-left: 140px; margin-top: 10px; padding: 5px; background-color: #27bebe; color: #fff; cursor: pointer;" onclick="uap_add_new_register_field_value();"><?php _e('Add New Value', 'uap');?></div>
			</div>
						
			<div id="uap-register-field-conditional-text" style="display: <?php if ($data['metas']['type']=='conditional_text') echo 'block'; else echo 'none';?>">
				<div class="uap-form-line">
					<label class="uap-labels" style="vertical-align: top;"><?php _e('Right Answer:', 'uap');?></label>
					<input type="text" value="<?php echo uap_correct_text(@$data['metas']['conditional_text']);?>" name="conditional_text" /> 
				</div>
				<div class="uap-form-line">
					<label class="uap-labels" style="vertical-align: top;"><?php _e('Error Message:', 'uap');?></label>
					<textarea name="error_message" style="min-width: 250px;"><?php echo uap_correct_text(@$data['metas']['error_message']);?></textarea> 														
				</div>
			</div>
						
			<div class="uap-no-border" id="uap-register-field-plain-text" style="display: <?php if ($data['metas']['type']=='plain_text') echo 'block'; else echo 'none';?>">
				<label class="uap-labels" style="vertical-align: top;"><?php _e('Content:', 'uap');?> </label>
				<div style="display: inline-block; max-width: 85%;">
				<?php 
					$settings = array(
									'media_buttons' => true,
									'textarea_name'=>'plain_text_value',
									'textarea_rows' => 5,
									'tinymce' => true,
									'quicktags' => true,
									'teeny' => true,
					);
					wp_editor(uap_correct_text(@$data['metas']['plain_text_value']), 'plain_text_value', $settings);
				?>
				</div>
			</div>
			
			
			<h2><?php _e('Labels', 'uap');?></h2>						
			<div class="uap-form-line">
				<label class="uap-label"><?php _e('Field Label:', 'uap');?> </label> <input type="text" name="label" value="<?php echo uap_correct_text($data['metas']['label']);?>"/>
			</div>							
			<div class="uap-form-line">
				<label class="uap-label"><?php _e('SubLabel:', 'uap');?></label>
				<input type="text" value="<?php echo uap_correct_text(@$data['metas']['sublabel']);?>" name="sublabel" style="width: 400px;" />
			</div>
			<div class="uap-form-line">
				<label class="uap-label"><?php _e('Style Class:', 'uap');?></label>
				<input type="text" value="<?php echo uap_correct_text(@$data['metas']['class']);?>" name="class" style="width: 400px;" />
			</div>								
			
			<?php if (!in_array($data['metas']['name'], $data['disabled_items'])):?>
				<div class="uap-special-line">
					<h2><?php _e("Conditional Logic", 'uap');?></h2>
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Show:', 'uap');?></label>
						<select name="conditional_logic_show">
							<option <?php if (@$data['metas']['conditional_logic_show']=='yes') echo 'selected';?> value="yes"><?php _e("Yes", 'uap');?></option>
							<option <?php if (@$data['metas']['conditional_logic_show']=='no') echo 'selected';?> value="no"><?php _e("No", 'uap');?></option>
						</select>								
					</div>	
					<div class="uap-form-line" style="text-align: left;">
						<div style="display: inline-block;">
							<label class="uap-label"><?php _e('If Field:', 'uap');?></label>
							<select name="conditional_logic_corresp_field">
							<?php foreach ($data['register_fields'] as $k => $v):?>
								<?php $selected = ($data['metas']['conditional_logic_corresp_field']==$k) ? 'selected' : '';?>
								<option value="<?php echo $k?>" <?php echo $selected;?>><?php echo $v?></option>
							<?php endforeach;?>
							</select>
						</div>
						<div style="display: inline-block;margin-left: 20px;">									
							<select name="conditional_logic_cond_type" style="min-width: 70px;"> 
								<option <?php if (@$data['metas']['conditional_logic_cond_type']=='has') echo 'selected';?> value="has"><?php _e("Is", 'uap');?></option>
								<option <?php if (@$data['metas']['conditional_logic_cond_type']=='contain') echo 'selected';?> value="contain"><?php _e("Contains", 'uap');?></option>
							</select>
						</div>		
						<div style="display: inline-block;margin-left: 10px">
							<label style="display: inline-block;margin-right:10px;"> : </label>
							<input type="text" name="conditional_logic_corresp_field_value" value="<?php echo uap_correct_text(@$data['metas']['conditional_logic_corresp_field_value']);?>" style="vertical-align: middle; min-width: 250px;" />
						</div>																
					</div>											
				</div>	
			<?php endif; ?>			
			
			<input type="hidden" name="id" value="<?php echo $data['id'];?>" />			
			<div class="uap-submit-form">
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save_field" class="button button-primary button-large">
			</div>	
		</div>
	</div>
</form>

</div>

<script>
	jQuery(document).ready(function(){
		jQuery('.uap-register-the-values').sortable({
			cursor: 'move'
		});
	});
</script>	