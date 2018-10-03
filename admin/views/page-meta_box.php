	<div class="uap-padding">
		<div><strong><?php _e('Set the Page as:', 'uap');?></strong></div>
		<select class="uap-fullwidth ihc-select" name="uap_set_page_as_default_something">
			<option value="-1">...</option>
			<?php 
				foreach ($data['types'] as $name=>$label):
					$selected = ($name==$data['current_page_type']) ? 'selected' : '';
				?>
					<option <?php echo $selected;?> value="<?php echo $name;?>"><?php echo $label . ' ' . __('Page', 'uap');?></option>
				<?php 
				endforeach;
			?>
		</select>
		<input type="hidden" name="uap_post_id" value="<?php echo $post->ID;?>" />
	</div>	
	
	<div style="margin-top: 13px;">
		<?php if (!empty($data['unset_pages'])): ?>
			<?php foreach ($data['unset_pages'] as $page_name): ?>
				<div class="uap-metabox-not-set"><?php echo __('Default ', 'uap') . $page_name . ' ' . __('Page <b>Not Set!</b>', 'uap');?></div>	
			<?php endforeach;?>
		<?php endif;?>
	</div>