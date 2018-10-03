<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Maximum Amount', 'uap');?></h3>
		<div class="inside">
		<div class="row">
		  <div class="col-xs-5">	
			<div class="uap-form-line">
				<h2><?php _e('Activate/Hold Maximum Amount', 'uap');?></h2>
				<p><?php _e('Set a maximum amount that can not be passed for a referral. It is a safety limit decided by the Admin for avoiding big referrals that have to be paid.', 'uap'); ?></p>
				<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_maximum_amount_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_maximum_amount_enabled');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="uap_maximum_amount_enabled" value="<?php echo $data['metas']['uap_maximum_amount_enabled'];?>" id="uap_maximum_amount_enabled" /> 												
			</div>
		 </div>		
		</div>			
		<div class="uap-line-break"></div>	
				
			<div class="uap-inside-item">	
				<div class="row" style="margin-left:0px;">
					<div class="col-xs-5" style="margin-bottom: 10px;">
						<h3><?php _e('Default Amount Limit', 'uap');?></h3>
						<p><?php _e('Set the default flat amount limit that will be used when no special limit is set for a certain rank.', 'uap');?></p>
						<div class="input-group" style="margin:0px 0 15px 0;">
							<span class="input-group-addon" id="basic-addon1"><?php _e('Max Amount Limit', 'uap');?></span>										
							<input type="number" min="0" step="0.01" class="uap-field-text-with-padding form-control" name="uap_maximum_amount_value" value="<?php echo $data['metas']['uap_maximum_amount_value'];?>" />
							<div class="input-group-addon"><?php echo $data['amount_types']['flat'];?></div>	
						</div>	
					</div>
				</div>
			</div>	
			<div class="uap-line-break"></div>	
			<?php if (!empty($data['ranks'])):?>
				<div class="uap-inside-item">
				<div class="row" style="margin-left:0px;">
					<div class="col-xs-4" style="margin-bottom: 10px;">
						<h3><?php _e('Max Amount Limit for Each Rank', 'uap');?></h3>
						<p><?php _e('Set a special max amount limit for each rank. This option will also become available in the “Rank Settings” page.', 'uap');?></p>
						
				<?php foreach ($data['ranks'] as $rank_data):?>		
								<div class="input-group" style="margin:0px 0 15px 0;">
									<span class="input-group-addon" id="basic-addon1"><?php echo $rank_data->label;?></span>										
									<input type="number" min="0" step="0.01" class="uap-field-text-with-padding form-control" name="uap_maximum_amount_value_per_rank[<?php echo $rank_data->id;?>]" value="<?php echo $data['metas']['uap_maximum_amount_value_per_rank'][$rank_data->id];?>" />
									<div class="input-group-addon"><?php echo $data['amount_types']['flat'];?></div>
								</div>		
				<?php endforeach;?>	
					</div>
				</div>	
			</div>					
			<?php endif;?>	
																							
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>		
					
		</div>
	</div>	

</form>