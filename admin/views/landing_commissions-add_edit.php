<div class="uap-wrapper">
	<div class="uap-stuffbox">
	<form action="<?php echo $data['url-manage'];?>" method="post">

	<h3 class="uap-h3"><?php _e('Landing Commission (CPA) Shortcode', 'uap');?></h3>
	<div class="inside">
		<div class="uap-inside-item">
			<div class="row">
				<div class="col-xs-6">
				<h4><?php _e('Activate/Hold (CPA) Shortcode', 'uap');?></h4>
					<p><?php _e('Activate or deactivate a shortcode without needing to delete it.', 'uap');?></p>
					<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
						<?php $checked = ($data['metas']['status']) ? 'checked' : '';?>
						<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#the_status');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="status" value="<?php echo $data['metas']['status'];?>" id="the_status" />
				</div>
			</div>
		</div>
		<div class="uap-line-break"></div>
		<div class="uap-inside-item">
			<div class="row">
				<div class="col-xs-6">
					<div class="input-group">
						<span class="input-group-addon" id=""><?php _e('Slug', 'uap');?></span>
						<input type="text" class="form-control" placeholder="<?php _e('unique slug', 'uap');?>"  value="<?php echo $data['metas']['slug'];?>" name="slug" />
					</div>
					<p style="font-style:italic;"><?php _e('Be sure that you set a Unique Slug based only on lowercase characters and no additional symbols or spaces', 'uap');?></p>
				</div>
			</div>
		</div>
		<div class="uap-line-break"></div>

		<div class="uap-inside-item">
			<div class="row">
				<div class="col-xs-6">
					<h3><?php echo __('Commission Price', 'uap');?></h3>
					<p><?php _e('Based on the landing commission price, the referral amount will be calculated depending on each affiliate rank amount.', 'uap');?></p>
					 <div class="input-group">
						<span class="input-group-addon" id="basic-addon1"><?php _e('Value', 'uap');?></span>
						<input type="number" min="0" style="max-width: 100px;"  step="0.01" class="form-control" name="amount_value" value="<?php echo $data['metas']['amount_value'];?>" aria-describedby="basic-addon1" /> <span style="padding: 7px; display:inline-block;"><?php echo $currency;?></span>
					 </div>
				 		</br>
					 		<h4><?php echo __('Additional dynamic Workflow', 'uap');?></h4>
					 		<p style="font-weight:bold"><?php _e('You can come out with a Dynamic reference Price via GET or POST if the  "lc_amount" variable is sent where the Landing Commission shortcode is set.', 'uap');?></p>
				 		</br>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-4">
					<h3><?php _e('Referral Default Status', 'uap');?></h3>
					<div style="margin-bottom:15px;">
						<select name="default_referral_status" class="form-control m-bot15"><?php
							foreach (array(1=>__('Unverified', 'uap'), 2=>__('Verified', 'uap')) as $k=>$v):
								$selected = ($data['metas']['default_referral_status']==$k) ? 'selected' : '';
								?>
								<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
								<?php
							endforeach;
						?></select>
					</div>
				</div>
			</div>
		</div>

		<div class="uap-line-break"></div>
		<div class="uap-inside-item">
			<div class="row">
				<div class="col-xs-6">
					<div class="input-group">
						<span class="input-group-addon" id=""><?php _e('Source Name', 'uap');?></span>
						<input type="text" class="form-control" placeholder=""  value="<?php echo $data['metas']['source'];?>" name="source" />
					</div>
				</div>
			</div>
		</div>

		<div class="uap-inside-item">
			<div class="row">
				<div class="col-xs-6">
					<div class="form-group">
						<h4><?php _e('Referral Description', 'uap');?></h4>
						<textarea class="form-control text-area" name="description"><?php echo $data['metas']['description'];?></textarea>
					</div>
				</div>
			</div>
		</div>

			<div class="row">
				<div class="col-xs-6">
					<h3><?php echo __('Cookie Expire Time', 'uap');?></h3>
					<p><?php _e('Set time interval', 'uap');?></p>
					<div class="input-group">
						<?php if (!isset($data['metas']['cookie_expire'])) $data['metas']['cookie_expire'] = 0;?>
						<input type="number" style="max-width: 100px;" min="0" step="1" class="form-control" name="cookie_expire" value="<?php echo $data['metas']['cookie_expire'];?>" aria-describedby="basic-addon1" />  <span style="padding: 7px; display:inline-block;"><?php _e('Hours', 'uap');?></span>
					 </div>
				</div>
			</div>

		<div class="uap-line-break"></div>
				<div class="uap-inside-item">
					<div class="row">
						<div class="col-xs-4">
							<h3><?php _e('Color', 'uap');?></h3>
							<div style="margin-bottom:15px;">
							<ul id="uap_colors_ul" class="uap-colors-ul" style="display: inline-block; vertical-align: bottom;">
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


					<div class="uap-submit-form">
						<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large">
					</div>
				</div>

				<input type="hidden" name="id" value="<?php echo $data['metas']['id'];?>" />

			</form>
		</div>

</div>


</div><!-- end of uap-dashboard-wrap -->
