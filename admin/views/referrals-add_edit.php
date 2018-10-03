<div class="uap-wrapper">
		<div class="uap-stuffbox">
			<form action="<?php echo $data['url-manage'];?>" method="post">
				
				<h3 class="uap-h3"><?php _e('Add/Edit Referral', 'uap');?></h3>
				
				<div class="inside">
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Visit Id:', 'uap');?></label>
						<input type="text" value="<?php echo $data['metas']['visit_id'];?>" name="visit_id" />
					</div>
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Referral User WP Id:', 'uap');?></label>
						<input type="text" value="<?php echo $data['metas']['refferal_wp_uid'];?>" name="refferal_wp_uid" />
					</div>
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Campaign:', 'uap');?></label>
						<input type="text" value="<?php echo $data['metas']['campaign'];?>" name="campaign" />
					</div>
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Affiliate:', 'uap');?></label>
						<select name="affiliate_id"><?php 
							foreach ($data['affiliates'] as $k=>$v):
								$selected = ($data['metas']['affiliate_id']==$k) ? 'selected' : '';
								?>
								<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v['username'];?></option>
								<?php 
							endforeach;
						?></select>
					</div>	
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Description:', 'uap');?></label>
						<textarea name="description"><?php echo $data['metas']['description'];?></textarea>
					</div>	
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Source:', 'uap');?></label>
						<input type="text" value="<?php echo $data['metas']['source'];?>" name="source" />
					</div>						
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Reference:', 'uap');?></label>
						<input type="text" value="<?php echo $data['metas']['reference'];?>" name="reference" />
					</div>	
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Reference Details:', 'uap');?></label>
						<textarea name="reference_details"><?php echo $data['metas']['reference_details'];?></textarea>
					</div>		
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Referral Parent:', 'uap');?></label>
						<input type="text" value="<?php echo $data['metas']['parent_referral_id'];?>" name="parent_referral_id" />
					</div>	
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Referral Child:', 'uap');?></label>
						<input type="text" value="<?php echo $data['metas']['child_referral_id'];?>" name="child_referral_id" />
					</div>														
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Amount:', 'uap');?></label>
						<input type="number" min="0" step="0.01" value="<?php echo $data['metas']['amount'];?>" name="amount" />
					</div>	
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Currency:', 'uap');?></label>
						<select name="currency"><?php 
							$currency = uap_get_currencies_list();
							foreach ($currency as $k=>$v){
								$selected = ($k==$data['metas']['currency']) ? 'selected' : '';
								?>
								<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
								<?php 
							}
						?></select>
					</div>														
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Date:', 'uap');?></label>
						<input type="text" value="<?php echo $data['metas']['date'];?>" name="date" id="referrals_date"/>
					</div>
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Status:', 'uap');?></label>
						<select name="status"><?php 
							foreach ($data['status_posible'] as $k=>$v):
								$selected = ($data['metas']['status']==$k) ? 'selected' : '';
								?>
								<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
								<?php 
							endforeach;
						?></select>
					</div>					
					<div class="uap-form-line">
						<label class="uap-label"><?php _e('Payment:', 'uap');?></label>
						<select name="payment"><?php 
							foreach ($data['payment_posible'] as $k=>$v):
								$selected = ($data['metas']['payment']==$k) ? 'selected' : '';
								?>
								<option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
								<?php 
							endforeach;
						?></select>
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
			
			<script>
				jQuery(document).ready(function() {
				    jQuery('#referrals_date').datepicker({
			            dateFormat : 'yy-mm-dd ',
			            onSelect: function(datetext){
			                var d = new Date();
			                datetext = datetext+d.getHours()+":"+uap_add_zero(d.getMinutes())+":"+uap_add_zero(d.getSeconds());
			                jQuery(this).val(datetext);
			            }
				    });
				});
			</script>