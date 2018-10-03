<div class="uap-wrapper">
<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Reports', 'uap');?></span></div>

		<?php if (!empty($data['subtitle'])):?>
			<h4><?php echo $data['subtitle'];?></h4>
		<?php endif;?>	

<div class="uap-special-box">
<form action="" method="post">
			<?php _e('Select reports from:', 'uap');?>
			
			<select name="search" style="min-width:300px; min-height:31px;"><?php foreach ($data['select_values'] as $k=>$v):?>
				<?php $selected = ($data['selected']==$k) ? 'selected' : '';?>
				<option <?php echo $selected;?> value="<?php echo $k?>"><?php echo $v;?></option>
			<?php endforeach;?></select>
			<input type="submit" value="<?php _e('Check Results', 'uap');?>" name="submit" class="button button-primary button-large" />		
			
		</form>
</div>
<div class="uap-stuffbox">
	<div class="inside">			
		
		<div class="row" style="margin-top: 10px;">
			
			<div class="col-xs-3" style="width: 30%;">
				<div class="uap-dashboard-top-box" style="background: #f1f4f8; color: #47597a;">
					<i class="fa-uap fa-dashboard-payments-unpaid-uap"></i>
					<div class="stats">
						<h4><?php echo round($data['reports']['total_paid'], 2) . $data['currency'];?></h4>
						<?php _e('Paid Amount', 'uap');?>
					</div>
				</div>	
			</div>	
			
			<div class="col-xs-3" style="width: 30%;">
				<div class="uap-dashboard-top-box" style="background: #f1f4f8; color: #47597a;">
					<i class="fa-uap fa-dashboard-payments-unpaid-uap"></i>
					<div class="stats">
						<h4><?php echo uap_format_price_and_currency($data['currency'], round($data['reports']['total_unpaid'], 2));?></h4>
						<?php _e('UnPaid Amount', 'uap');?>
					</div>
				</div>	
			</div>	
								
			<div class="col-xs-3" style="width: 30%;">
				<div class="uap-dashboard-top-box" style="background: #f1f4f8; color: #47597a;">
					<i class="fa-uap fa-dashboard-visits-uap"></i>
					<div class="stats">
						<h4><?php echo $data['reports']['affiliates'];?></h4>
						<?php _e('Total Affiliates', 'uap');?>		
					</div>
				</div>	
			</div>
			
			<div class="col-xs-3" style="width: 30%;">
				<div class="uap-dashboard-top-box" style="background: #f1f4f8; color: #47597a;">
					<i class="fa-uap fa-dashboard-referrals-uap"></i>
					<div class="stats">
						<h4><?php echo $data['reports']['referrals'];?></h4>
						<?php _e('Total Referrals', 'uap');?>
					</div>
				</div>
			</div>

			<div class="col-xs-3" style="width: 30%;">
				<div class="uap-dashboard-top-box" style="background: #f1f4f8; color: #47597a;">
					<i class="fa-uap fa-visits-reports-uap"></i>
					<div class="stats">
						<h4><?php echo $data['reports']['visits'];?></h4>
						<?php _e('Visits', 'uap');?>
					</div>
				</div>
			</div>				

			<div class="col-xs-3" style="width: 30%;">
				<div class="uap-dashboard-top-box" style="background: #f1f4f8; color: #47597a;">
					<i class="fa-uap fa-success-number-uap"></i>
					<div class="stats">
						<h4><?php echo $data['reports']['conversions'];?></h4>
						<?php _e('Successful Conversions', 'uap');?>
					</div>
				</div>
			</div>	

			<div class="col-xs-3" style="width: 30%;">
				<div class="uap-dashboard-top-box" style="background: #f1f4f8; color: #47597a;">
					<i class="fa-uap fa-success-rate-uap"></i>
					<div class="stats">
						<h4><?php echo $data['reports']['success_rate'] . '%';?></h4>
						<?php _e('Succesfully Rate', 'uap');?>
					</div>
				</div>
			</div>
			
	  </div>
	</div>
</div>

<?php 
if ($data['visit_graph']){
	/// VISIT GRAPH
	?>
		<div class="uap-stuffbox">
			<div class="inside">
				<div id="uap-plot-1" style="height: 300px;"></div>
			</div>
		</div>	
	<?php 
	reset($data['visit_graph']);
	$first_key = key($data['visit_graph']);
	if (isset($data['visit_graph'][$first_key])){
		$start_time = strtotime($first_key);
	}
	
	end($data['visit_graph']);
	$last_key = key($data['visit_graph']);
	if (isset($data['visit_graph'][$last_key])){
		$end_time = strtotime($last_key);
	}
	reset($data['visit_graph']);
	?>
	<script>
	jQuery(document).ready(function(){
		var uap_visits = [
									<?php 
										if (!empty($data['visit_graph']) && is_array($data['visit_graph'])):
											foreach ($data['visit_graph'] as $date=>$value):
												echo '[' . strtotime($date) . '000, ' . $value . '], ';
											endforeach;
										endif;
									?>
		];
		var uap_visits_success = [
					    			<?php 
					    				if (!empty($data['visit_graph_success']) && is_array($data['visit_graph_success'])):
					    					if (count($data['visit_graph_success'])<2){
					    						if (empty($data['visit_graph_success'][$first_key])){
					    							$data['visit_graph_success'][$first_key] = 0;
					    						} else if (empty($data['visit_graph_success'][$last_key])){
					    							$data['visit_graph_success'][$last_key] = 0;
					    						}
					    					}
							    			foreach ($data['visit_graph_success'] as $date=>$value):
							    				echo '[' . strtotime($date) . '000, ' . $value . '], ';
							    			endforeach;
						    			endif;
					    			?>
		];
		jQuery.plot(
				jQuery("#uap-plot-1"), [{
					label : "<?php _e('All Visits', 'uap');?>",
					data : uap_visits,
					color : "#f16161"
				},{
					label : "<?php _e('Converted Visits', 'uap');?>",
					data : uap_visits_success,
					color : "#38d0e3"
				}
				], {
							
					grid: { hoverable: false, backgroundColor: "#fff", minBorderMargin: 0,  borderWidth: {top: 0, right: 0, bottom: 1, left: 1}, borderColor: "#aaa" },	
					xaxis : {					
						min : <?php echo $start_time . '000'; ?>,
						max : <?php echo $end_time . '000';?>,
						mode : "time",
						tickSize: [1, "<?php echo $data['tick_type'];?>"],
					},
						
				}
		);
	});
	</script>	
	<?php 
}

if ($data['referrals_graph']){
	/// REFERRALS GRAPH
	?>
		<div class="uap-stuffbox">
			<div class="inside">
				<div id="uap-plot-2" style="height: 300px;"></div>
			</div>
		</div>	
	<?php 
	reset($data['referrals_graph']);
	$first_key = key($data['referrals_graph']);
	if (isset($data['referrals_graph'][$first_key])){
		$start_time = strtotime($first_key);
	}
	
	end($data['referrals_graph']);
	$last_key = key($data['referrals_graph']);
	if (isset($data['referrals_graph'][$last_key])){
		$end_time = strtotime($last_key);
	}
	reset($data['referrals_graph']);
	?>
	<script>
	jQuery(document).ready(function(){
		var uap_all_referrals = [
									<?php 
										if (!empty($data['referrals_graph']) && !empty($data['referrals_graph'])):
											foreach ($data['referrals_graph'] as $date=>$value):
												echo '[' . strtotime($date) . '000, ' . $value . '], ';
											endforeach;
										endif;
									?>
		];
		var uap_all_referrals_refuse = [ 
				             			<?php 
				             			if (!empty($data['referrals_graph-refuse']) && is_array($data['referrals_graph-refuse'])):	
					             			if (count($data['referrals_graph-refuse'])<2 ){
					             				if (empty($data['referrals_graph-refuse'][$first_key])){
					             					$data['referrals_graph-refuse'][$first_key] = 0;
					             				} else if (empty($data['referrals_graph-refuse'][$last_key])){
					             					$data['referrals_graph-refuse'][$last_key] = 0;
					             				}
					             			}
					             			foreach ($data['referrals_graph-refuse'] as $date=>$value):
					             				echo '[' . strtotime($date) . '000, ' . $value. '], ';
					             			endforeach;
				             			endif;
				             			?>
		];
		var uap_all_referrals_unverified = [
						             		<?php
						             			if (!empty($data['referrals_graph-unverified']) && is_array($data['referrals_graph-unverified'])):
							             			if (count($data['referrals_graph-unverified'])<2 ){
							             				if (empty($data['referrals_graph-unverified'][$first_key])){
							             					$data['referrals_graph-unverified'][$first_key] = 0;
							             				} else if (empty($data['referrals_graph-unverified'][$last_key])){
							             					$data['referrals_graph-unverified'][$last_key] = 0;
							             				}
							             			}
							             			foreach ($data['referrals_graph-unverified'] as $date=>$value):
							             				echo '[' . strtotime($date) . '000, ' . $value . '], ';
							             			endforeach;
							             		endif;
					             			?>
		                        		];
		var uap_all_referrals_verified = [
					             			<?php 
					             			if (!empty($data['referrals_graph-verified']) && is_array($data['referrals_graph-verified'])):
						             			if ( count($data['referrals_graph-verified'])<2 ){
					             					if (empty($data['referrals_graph-verified'][$first_key])){
							             				$data['referrals_graph-verified'][$first_key] = 0;
							             			} else if (empty($data['referrals_graph-verified'][$last_key])){
							             				$data['referrals_graph-verified'][$last_key] = 0;
							             			}
						             			}					             			
						             			foreach ($data['referrals_graph-verified'] as $date=>$value):
						             				echo '[' . strtotime($date) . '000, ' . $value . '], ';
						             			endforeach;
					             			endif;
					             			?>
		                        		];				
		jQuery.plot(
				jQuery("#uap-plot-2"), [{
					label : "<?php _e('All Referrals', 'uap');?>",
					data : uap_all_referrals,
					color : "#f8ba01"
				},{
					label : "<?php _e('Refuse Referrals', 'uap');?>",
					data : uap_all_referrals_refuse,
					color : "#f36b6b"
				},{
					label : "<?php _e('Unverified Referrals', 'uap');?>",
					data : uap_all_referrals_unverified,
					color : "#4cc0c1"
				},{
					label : "<?php _e('Verified Referrals', 'uap');?>",
					data : uap_all_referrals_verified,
					color : "#94C523"
				}
				], {
					grid: { hoverable: false, backgroundColor: "#fff", minBorderMargin: 0,  borderWidth: {top: 0, right: 0, bottom: 1, left: 1}, borderColor: "#aaa" },	
					xaxis : {					
						min : <?php echo $start_time . '000'; ?>,
						max : <?php echo $end_time . '000';?>,
						mode : "time",
						tickSize: [1, "<?php echo $data['tick_type'];?>"],
					},	
				}
		);
	});
	</script>	
	<?php 
}
?>

</div>
