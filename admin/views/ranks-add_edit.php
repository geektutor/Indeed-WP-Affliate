			<form action="<?php echo $data['url-manage'];?>" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Manage Rank', 'uap');?></h3>
					<div class="inside">	
						
						
					<div class="uap-inside-item">
						<div class="row">
							<div class="col-xs-6">
							<h4><?php _e('Activate/Hold Rank', 'uap');?></h4>
								<p><?php _e('Activate or deactivate a specific rank without needing to delete it.', 'uap');?></p>
								<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
									<?php $checked = ($data['metas']['status']) ? 'checked' : '';?>
									<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#rank_status');" <?php echo $checked;?> />
									<div class="switch" style="display:inline-block;"></div>
								</label>
								<input type="hidden" name="status" value="<?php echo $data['metas']['status'];?>" id="rank_status" /> 
							</div>
						</div>	
					</div>	
				<div class="uap-line-break"></div>	
				<div class="uap-inside-item">
					<div class="row">
						<div class="col-xs-6">
							<h3><?php _e('Rank Settings', 'uap');?></h3>
							<p><?php _e('The slug needs to be unique and set only with lowercase characters.', 'uap');?></p>
							<div class="input-group">
								<span class="input-group-addon" id="basic-addon1"><?php _e('Slug', 'uap');?></span>
								<input type="text" class="form-control" placeholder="<?php _e('unique rank name', 'uap');?>" value="<?php echo $data['metas']['slug'];?>" name="slug" />
							</div>
						</div>
					</div>	
				</div>
				<div class="uap-inside-item">	
					<div class="row">
						<div class="col-xs-4">
							<div class="input-group">
								<span class="input-group-addon" id="basic-addon1"><?php _e('Label', 'uap')?></span>
								<input type="text" class="form-control"  value="<?php echo $data['metas']['label'];?>" name="label" id="rank_label" />
							</div>
						</div>
					</div>	
				</div>	
				<div class="uap-inside-item">	
					<div class="row">
						<div class="col-xs-4">			
							<div class="form-group">
								<label class="control-label"><?php _e('Description', 'uap')?></label>
								<textarea name="description" class="form-control text-area" cols="30" rows="5" placeholder="<?php _e('Some details...', 'uap');?>"><?php echo $data['metas']['description'];?></textarea>
							</div>
						</div>
					</div>	
				</div>
				<div class="uap-line-break"></div>	
				<div class="uap-inside-item">
					<div class="row">	
						<div class="col-xs-8">
							<h3><?php _e('Position', 'uap');?></h3>
							<p><?php _e('Based on rank position an affiliate may jump to the next rank if the achievement conditions are met.', 'uap');?></p>	
							<div class="uap-rank-graphic"><?php echo $data['graphic']; ?></div>
							<div style="margin-top:15px;" class="col-xs-2">
								<div class="input-group">
									<span class="input-group-addon" id="basic-addon1"><?php _e('Order', 'uap');?></span>	
									<input type="number" min="1" style="min-width: 70px;" class="form-control" onChange="uap_rank_change_order_preview(<?php echo $data['metas']['id'];?>, this.value);" onKeyUp="uap_rank_change_order_preview(<?php echo $data['metas']['id'];?>, this.value);" max="<?php echo $data['maximum_ranks'];?>" value="<?php echo $data['metas']['rank_order'];?>" name="rank_order" />
								</div>
							 </div>			
						</div>
					</div>
				</div>
				<div class="uap-line-break"></div>	
				<div class="uap-inside-item">
					<div class="row">	
						<div class="col-xs-4">
							<h3><?php _e('Rank Amount', 'uap');?></h3>
							<p><?php _e('The default rank amount may be overwritten by specific offers or other special settings.', 'uap');?></p>	
							<div style="margin-bottom:15px;">
									
									<select name="amount_type" class="form-control m-bot15"><?php 
										foreach ($data['amount_types'] as $k=>$v):
											$selected = ($data['metas']['amount_type']==$k) ? 'selected' : '';
											?>
											<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
											<?php 
										endforeach;
									?></select>
							 </div>
							<div class="input-group">
								<span class="input-group-addon" id="basic-addon1"><?php _e('Value', 'uap');?></span>
								<input type="number" min="0" step="0.01" class="form-control" value="<?php echo $data['metas']['amount_value'];?>" name="amount_value" aria-describedby="basic-addon1">
							</div>
								
						</div>
					</div>
				</div> 
				<div class="uap-line-break"></div>	
				<div class="uap-inside-item">
					<div class="row">	
						<div class="col-xs-4">
							<h3><?php _e('Achievement', 'uap');?></h3>
							<p><?php _e('To jump into current rank, affiliates need to accomplish the required achievements.', 'uap');?></p>	
							<div style="" id="achieve_rules">
								
								<?php 
								$is_edit = FALSE;
								$excluded_achieve_type = array();
								if (!empty($data['metas']['achieve'])){
									$is_edit = TRUE;
									$arr = json_decode($data['metas']['achieve'], true);
								}								
								if (!empty($arr) && !empty($arr['i'])){
									for ($i=1; $i<=$arr['i']; $i++){
										$excluded_achieve_type[] = $arr['type_' . $i];	
									}
								}
								?>								
								
								<div id="achieve_type_div" style="margin-bottom:15px;">
									<select id="achieve_type" class="form-control m-bot15">
										<?php foreach ($data['achieve_types'] as $k=>$v):
												if (in_array($k, $excluded_achieve_type)){
													continue;
												}
											?>
											<option value="<?php echo $k;?>"><?php echo $v;?></option>
										<?php endforeach;?>
									</select>
								</div>
								<div class="input-group" id="achieve_value_div">
									<span class="input-group-addon" id="basic-addon1"><?php _e('Value', 'uap');?></span>
									<input  id="achieve_value" type="number" min="1" class="form-control" aria-describedby="basic-addon1">
								</div>
								<div id="achieve_relation_div" style="display: <?php if ($is_edit) echo 'block'; else echo 'none';?>;">
									<select id="achieve_relation" class="form-control m-bot15" style="width:50%; margin-top:15px;">
										<option value="and">AND</option>
										<option value="or">OR</option>
									</select>
								</div>								
								
								<div onClick="uap_add_new_achieve_rule();" id="add_new_achieve" class="button button-primary button-large" ><i class="fa-uap fa-add-new-item-uap" style="margin-right: 5px;"></i>Add</div>
							
								<div id="achieve_rules_view"><?php 									
										if (!empty($arr) && is_array($arr)){
											$display_reset = 'inline-block';
											if ($arr['i']>1){
												for ($i=1; $i<=$arr['i']; $i++){
													if (isset($arr['relation_' . $i])){
														?>
														<div class="achieve-item-relation"><?php echo $arr['relation_' . $i];?></div>
														<?php 
													}
													?>
													<div class="achieve-item" id="achieve_item_<?php echo $i;?>"><div style="font-weight:bold;font-size:14px;"><?php echo $data['achieve_types'][$arr['type_' . $i]];?></div><div>From: <?php echo $arr['value_' . $i];?></div></div>
													<?php 
												}
											} else {
												?>
												<div class="achieve-item" id="achieve_item_1"><div style="font-weight:bold;font-size:14px;"> <?php echo $arr['type_1'];?></div><div>From: <?php echo $arr['value_1'];?></div></div>
												<?php 
											}
										}								
								?></div>
								<div id="achieve_reset" onClick="uap_achieve_reset();" class="button button-primary button-large" style="display: <?php if ($is_edit) echo 'inline-block'; else echo 'none';?>;">Reset Achievement</div>
							</div>	
							<input type="hidden" value='<?php echo $data['metas']['achieve'];?>' name="achieve" id="achieve_type_value"/>
								
						</div>
					</div>
				</div> 
				<div class="uap-line-break"></div>	
				<div class="uap-inside-item">
					<div class="row">	
						<div class="col-xs-4">
							<h3><?php _e('Rank Color', 'uap');?></h3>						
							<div style="margin-bottom:15px;">		
								<ul id="uap_colors_ul" class="uap-colors-ul" style="display: inline-block; vertical-align: top; width: 200px;">
                                <?php
                                    $color_scheme = array('0a9fd8', '38cbcb', '27bebe', '0bb586', '94c523', '6a3da3', 'f1505b', 'ee3733', 'f36510', 'f8ba01');
                                    $i = 0;
                                    if (empty($data['metas']['color'])){
                                 		$data['metas']['color'] = $color_scheme[rand(0,9)];
                                 	}
                                    foreach ($color_scheme as $color){
                                        if ($i==5) echo "<div class='clear'></div>";
                                        $class = ($color==$data['metas']['color']) ? 'uap-color-scheme-item-selected' : 'uap-color-scheme-item';
                                        ?>
                                            <li class="<?php echo $class;?>" onClick="uap_chage_color(this, '<?php echo $color;?>', '#uap_color');" style="background-color: #<?php echo $color;?>;"></li>
                                        <?php
                                        $i++;
                                    }
                                ?>
                            </ul>
                            <input type="hidden" name="color" id="uap_color" value="<?php echo $data['metas']['color'];?>" />
							</div>
								
						</div>
					</div>
				</div> 														
				<input type="hidden" name="id"	value="<?php echo $data['metas']['id'];?>" />
				<div class="uap-submit-form" style="margin-top:10px;"> 
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>								
		</div>
		</div>
						
		<div class="uap-stuffbox uap-magic-stuffbox" style="display: <?php echo  (empty($data['bonus_enabled'])) ? 'none' : 'block'; ?>;">
			<h3 class="uap-h3"><?php _e('Bonus', 'uap');?></h3>
			<div class="inside">
			<div class="row">
				<div class="col-xs-4">
					<h3><?php _e('Achievement Bonus', 'uap');?></h3>
					<p><?php _e('Affiliates will receive a bonus of a flat amount each time they will reach a higher rank.', 'uap');?></p>	
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon1">Amount</span>
								 <input type="number" class="form-control" min="0" step="0.01" value="<?php echo $data['metas']['bonus'];?>" name="bonus" aria-describedby="basic-addon1">
								 <div class="input-group-addon"><?php echo $data['amount_types']['flat'];?></div>	
						</div>
						<div class="uap-submit-form" style="margin-top:10px;"> 
								<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>		
				</div>
			</div>
			</div>
		</div>	
		
		<div class="uap-stuffbox uap-magic-stuffbox" style="display: <?php echo ($data['display-signup_referrals']) ? 'block' : 'none'; ?>;">				
			<h3 class="uap-h3"><?php _e('SignUp Referrals', 'uap');?></h3>
			<div class="inside">
			<div class="row">
				<div class="col-xs-4">
					<h3><?php _e('SignUp Referrals', 'uap');?></h3>
					<p><?php _e('Available for membership system, awarding commission when referred user signs up.', 'uap');?></p>	
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon1">Amount</span>
								<?php $value = ($data['metas']['sign_up_amount_value']>-1) ? $data['metas']['sign_up_amount_value'] : '';?>
								 <input type="number" class="form-control" min="0" step="0.01" value="<?php echo $value;?>" name="sign_up_amount_value" aria-describedby="basic-addon1">
								 <div class="input-group-addon"><?php echo $data['amount_types']['flat'];?></div>	
						</div>
						<div class="uap-submit-form" style="margin-top:10px;"> 
								<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>
				</div>
			</div>
			</div>			
		</div>
		
		<div class="uap-stuffbox uap-magic-stuffbox" style=" display: <?php echo ($data['display-lifetime_commissions']) ? 'block' : 'none'; ?>;">				
			<h3 class="uap-h3"><?php _e('LifeTime', 'uap');?></h3>
			<div class="inside">
			<div class="row">
				<div class="col-xs-4">
					<h3><?php _e('LifeTime Comission', 'uap');?></h3>
					<p><?php _e('Allow for your affiliate to receive commission for all lifetime referrals.', 'uap');?></p>	
					
					<div style="margin-bottom:15px;">
										<select name="lifetime_amount_type" class="form-control m-bot15"><?php 
										foreach ($data['amount_types'] as $k=>$v):
											$selected = ($data['metas']['lifetime_amount_type']==$k) ? 'selected' : '';
											?>
											<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
											<?php 
										endforeach;
									?></select>
										
						</div>
								<div class="input-group">
									<span class="input-group-addon" id="basic-addon1"><?php _e('Value', 'uap');?></span>
									<?php $value = ($data['metas']['lifetime_amount_value']>-1) ? $data['metas']['lifetime_amount_value'] : '';?>
									<input type="number" min="0" step="0.01" class="form-control" value="<?php echo $value;?>" name="lifetime_amount_value" aria-describedby="basic-addon1">
								</div>
								
			
						<div class="uap-submit-form" style="margin-top:10px;"> 
								<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>		
				</div>
			</div>
			</div>
		</div>	
										
		<div class="uap-stuffbox uap-magic-stuffbox" style="display: <?php echo ($data['display-reccuring_referrals']) ? 'block' : 'none'; ?>;">
			<h3 class="uap-h3"><?php _e('Reccurring Referrals', 'uap');?></h3>
			<div class="inside">
			<div class="row">
				<div class="col-xs-4">
					<h3><?php _e('Reccurring Referrals', 'uap');?></h3>
					<p><?php _e('Award commissions for recurring subscriptions into membership systems.', 'uap');?></p>	
					<div style="margin-bottom:15px;">
										<select name="reccuring_amount_type" class="form-control m-bot15"><?php 
									foreach ($data['amount_types'] as $k=>$v):
										$selected = ($data['metas']['reccuring_amount_type']==$k) ? 'selected' : '';
										?>
										<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
										<?php 
									endforeach;
								?></select>
										
						</div>
								<div class="input-group">
									<span class="input-group-addon" id="basic-addon1"><?php _e('Value', 'uap');?></span>
									<?php $value = ($data['metas']['reccuring_amount_value']>-1) ? $data['metas']['reccuring_amount_value'] : '';?>
									<input type="number" min="0" step="0.01" class="form-control" value="<?php echo $value;?>" name="reccuring_amount_value" aria-describedby="basic-addon1">
								</div>
					
					<div class="uap-submit-form" style="margin-top:10px;"> 
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
					</div>		
				</div>
			</div>
			</div>
		</div>	
							
		<div class="uap-stuffbox uap-magic-stuffbox" style="display: <?php echo ($data['display-mlm']) ? 'block' : 'none'; ?>;">
			<h3 class="uap-h3"><?php _e("MLM Referrals For Each MLM Level", 'uap')?></h3>
			<div class="inside">
			<div class="row">
				<div class="col-xs-6">
					<h3><?php _e('MLM Referrals For Each MLM Level', 'uap');?></h3>
					<p><?php _e('Set a multi-level marketing system for your affiliates.', 'uap');?></p>	
					
					<table class="uap-dashboard-inside-table" id="mlm-amount-for-each-level">
								<thead>
									<th width="40%"><?php _e('Level', 'uap');?></th>
									<th><?php _e('Value', 'uap');?></th>
								</thead>
								<?php 
									for ($i=1; $i<=$data['mlm_matrix_depth']; $i++):
										?>
										<tr data-tr="<?php echo $i;?>" id="uap_mlm_level_<?php echo $i;?>">
											<td><?php echo __('Level', 'uap') . ' ' . $i;?></td>
											<td>
												<input type="number" step="0.01" min="0" class="uap-input-number" value="<?php echo @$data['metas']['mlm_amount_value'][$i];?>" name="<?php echo "mlm_amount_value[$i]";?>" />
												<select name="<?php echo "mlm_amount_type[$i]";?>"><?php 
													foreach ($data['amount_types'] as $k=>$v):
														$selected = (!empty($data['metas']['mlm_amount_type'][$i]) && $data['metas']['mlm_amount_type'][$i]==$k) ? 'selected' : '';
														?>
														<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
														<?php 
													endforeach;
												?></select>											
											</td>
										</tr>
										<?php 
									endfor;
								?>
						</table>	
						<div class="uap-submit-form" style="margin-top:10px;"> 
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>		
				</div>
			</div>
			</div>
		</div>	
        <div class="uap-stuffbox uap-magic-stuffbox" style="display: <?php echo  (empty($data['pay_per_click_enabled'])) ? 'none' : 'block'; ?>;">
			<h3 class="uap-h3"><?php _e('PayPerClick (CPC) Campaign', 'uap');?></h3>
			<div class="inside">
			<div class="row">
				<div class="col-xs-4">
					<h3><?php _e('PPC Amount', 'uap');?></h3>
					<p><?php _e('Affiliates will receive a PPC Referral with flat amount each time a new referred user visit your website.', 'uap');?></p>	
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon1">Amount</span>
								 <input type="number" class="form-control" min="0" step="0.01" value="<?php echo $data['metas']['pay_per_click'];?>" name="pay_per_click" aria-describedby="basic-addon1">
								 <div class="input-group-addon"><?php echo $data['amount_types']['flat'];?></div>	
						</div>
						<div class="uap-submit-form" style="margin-top:10px;"> 
								<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>		
				</div>
			</div>
			</div>
		</div>
        <div class="uap-stuffbox uap-magic-stuffbox" style="display: <?php echo  (empty($data['cpm_commission_enabled'])) ? 'none' : 'block'; ?>;">
			<h3 class="uap-h3"><?php _e('Cost Per Mile (CPM) Campaign', 'uap');?></h3>
			<div class="inside">
			<div class="row">
				<div class="col-xs-4">
					<h3><?php _e('CPM Amount', 'uap');?></h3>
					<p><?php _e('Affiliates will receive a CPM Referral with flat amount rewarded for 1000 impressions (displaying your banners 1000 times)', 'uap');?></p>	
						<div class="input-group">
							<span class="input-group-addon" id="basic-addon1">Amount</span>
								 <input type="number" class="form-control" min="0" step="0.01" value="<?php echo $data['metas']['cpm_commission'];?>" name="cpm_commission" aria-describedby="basic-addon1">
								 <div class="input-group-addon"><?php echo $data['amount_types']['flat'];?></div>	
						</div>
						<div class="uap-submit-form" style="margin-top:10px;"> 
								<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>		
				</div>
			</div>
			</div>
		</div>						
	</form>

<script>
	var achieve_arr = [];
	<?php 
		$i=1;
		foreach ($data['achieve_types'] as $k=>$v):?>
		achieve_arr[<?php echo $i;?>] = {label: '<?php echo $v;?>', value: '<?php echo $k;?>'};
	<?php 
		$i++; 
		endforeach;
	?>
</script>
<?php
