			<form action="" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Recurring Referrals', 'uap');?></h3>
					<div class="inside">
					<div class="row">
						<div class="col-xs-5">
							<h3><?php _e('Activate/Hold Reccuring Referrals', 'uap');?></h3>
							<p><?php _e('You can activate this option to take place in your affiliate system.', 'uap');?></p>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_reccuring_referrals_enable']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_reccuring_referrals_enable');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_reccuring_referrals_enable" value="<?php echo $data['metas']['uap_reccuring_referrals_enable'];?>" id="uap_reccuring_referrals_enable" /> 
						</div>
						</div>
						<div class="uap-line-break"></div>
						<div class="row">
						<div class="col-xs-7">			
						<?php if (!empty($data['rank_list'])) :?>
							<h3><?php _e('Recurring Amount For Each Rank', 'uap');?></h3>
							<p><?php _e('Set a special recurring amount for each rank that will replace the default amount rank. This option will also become available in the “Rank Settings” page.', 'uap');?></p>
							<table class="uap-dashboard-inside-table">
								<tr>
									<th><?php _e('Rank Name', 'uap');?></th>
									<th><?php _e('Default Amount Rank', 'uap');?></th>
									<th><?php _e('Recurring Amount', 'uap');?></th>
								</tr>
							<?php foreach ($data['rank_list'] as $id=>$label) :?>
								<tr>
									<td><?php echo $label;?></td>
									<td><?php echo $data['default_rank_amount_value_array'][$id] . ' ' . $data['amount_types'][$data['default_rank_amount_type_array'][$id]];?></td>
									<td>
										<?php $value = ($data['rank_amount_value_array'][$id]>-1) ? $data['rank_amount_value_array'][$id] : 0;?>
										<input type="number" min="0" step="0.01" class="uap-input-number" value="<?php echo $value;?>" name="<?php echo "reccuring_ranks_value[$id]";?>" />
										<select name="<?php echo "reccuring_ranks_amount_type[$id]";?>"><?php 
											foreach ($data['amount_types'] as $k=>$v):
												$selected = ($data['rank_amount_type_array'][$id]==$k) ? 'selected' : '';
												?>
												<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
												<?php 
											endforeach;
										?></select>
									</td>
								</tr>						
							<?php endforeach;?>
						</table>
						<?php endif;?>
						</div>
						</div>
						<div class="uap-submit-form"> 
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>		
					</div>
				</div>
			</form>