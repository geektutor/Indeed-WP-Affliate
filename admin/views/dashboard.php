<div style="width: 97%">
	<div class="uap-dashboard-title">
		Ultimate Affiliate Pro -
		<span class="second-text">
			<?php _e('Dashboard Overall', 'uap');?>
		</span>
	</div>
	<div class="row">
	<div class="col-xs-3">
		<div class="uap-dashboard-top-box">
			<i class="fa-uap fa-dashboard-visits-uap"></i>
			<div class="stats">
				<h4><?php echo $data['stats']['affiliates'];?></h4>
				<?php _e('Total Affiliates', 'uap');?>
			</div>
		</div>
	</div>

	<div class="col-xs-3">
		<div class="uap-dashboard-top-box">
			<i class="fa-uap fa-dashboard-referrals-uap"></i>
			<div class="stats">
				<h4><?php echo $data['stats']['referrals'];?></h4>
				<?php _e('Total Referrals', 'uap');?>
			</div>
		</div>
	</div>

	<div class="col-xs-3">
		<div class="uap-dashboard-top-box">
			<i class="fa-uap fa-dashboard-payments-unpaid-uap"></i>
			<div class="stats">
				<h4><?php echo uap_format_price_and_currency($data['currency'], round($data['stats']['unpaid_payments_value'], 2));?></h4>
				<?php _e('Total UnPaid Referrals', 'uap');?>
			</div>
		</div>
	</div>

	<div class="col-xs-3">
		<div class="uap-dashboard-top-box">
			<i class="fa-uap fa-dashboard-rank-uap"></i>
			<div class="stats">
				<h4><?php echo $data['stats']['top_rank'];?></h4>
				<?php _e('Top Rank', 'uap');?>
			</div>
		</div>
 	</div>
  </div>



<div class="row">
   <div class="col-xs-8">
	<div class="uap-box-content-dashboard" >
	 <div style="padding: 20px;">
		<h4><?php _e('Total Affiliates per Rank', 'uap');?></h4>
		<?php if (!empty($data['rank_arr'])):?>
			<div id="uap_chart_1" class='uap-flot'></div>
		<?php endif;?>
	 </div>
	</div>

	<?php if (!empty($data['last_referrals'])):?>
	<div class="uap-box-content-dashboard uap-last-five" style="padding: 20px;">
		<div class="info-title"><i class="fa-uap fa-list-uap"></i><?php _e('Last 5 Referrals', 'uap');?></div>
		<?php foreach ($data['last_referrals'] as $array):?>
			<div style="margin-bottom:10px;">
				<i class="fa-uap fa-icon-pop-list-uap"></i>
				<span style="display: inline-block; vertical-align: text-top;"><?php echo '  ' . uap_format_price_and_currency($array['currency'], $array['amount']) . __(' for ', 'uap') .  '<strong>'.$array['affiliate_username'] .'</strong><br/>'. __(' on ', 'uap') . uap_convert_date_to_us_format($array['date']); ?></span>
			</div>
		<?php endforeach;?>
	</div>
	<?php endif;?>
   </div>

   <div class="col-xs-4">
		<?php if (!empty($data['top_affiliates'])) : ?>
			<div class="uap-box-right-dashboard">
			<div class="uap-dashboard-top-affiliate">
					<span class="uap-big-cunt">10</span>
					<span><?php _e('Top', 'uap');?><br/><?php _e('Affiliates', 'uap');?></span>
				</div>
				<?php $i = 1;?>
				<?php foreach ($data['top_affiliates'] as $key=>$value): ?>
					<div class="uap-dashboard-top-affiliate-single">
					 <div class="uap-top-name"><?php echo '<span>' . $i . '</span> ' . $value['name'] . ' (' . $key . ')';?> </div>
					 <div class="uap-top-count"><?php _e('Referrals', 'uap');?> <?php echo $value['referrals'];?> | <?php _e('Total Amount', 'uap');?> <?php echo uap_format_price_and_currency($data['currency'], $value['sum']);?> </div>
					</div>
					<?php $i++;?>
				<?php endforeach;?>
			</div>
		<?php endif;?>
   </div>
</div>
</div>
<script>
<?php
		if (!empty($data['rank_arr'])){
			?>
				if (jQuery("#uap_chart_1").length > 0) {
					var uap_ticks = [];
					var uap_chart_stats = [];
				<?php
				$i = 0;
				foreach ($data['rank_arr'] as $k=>$v){
					echo 'uap_ticks['.$i.']=['.$i.', "'.$k.'"];';
					echo 'uap_chart_stats['.$i.']={0:'.$i.',1:'.$v.'};';
					$i++;
				}
				if (count($data['rank_arr'])<10){
					for($j=count($data['rank_arr']);$j<11;$j++){
						echo 'uap_ticks['.$i.']=['.$i.', ""];';
						echo 'uap_chart_stats['.$i.']={0:'.$i.',1:0};';
						$i++;
					}
				}
				?>
				var options = {
					    bars: { show: true, barWidth: 0.75, fillColor: '#7ebffc', lineWidth: 0 },
						grid: { hoverable: false, backgroundColor: "#fff", minBorderMargin: 0,  borderWidth: {top: 0, right: 0, bottom: 1, left: 1}, borderColor: "#aaa" },
						xaxis: { ticks: uap_ticks, tickLength:0 },
						yaxis: { tickDecimals: 0, tickColor: "#eee"},
						legend: {show: true, position: "ne"}
				};
					jQuery.plot(jQuery("#uap_chart_1"), [ {
						color: "#669ccf",
						data: uap_chart_stats,
					} ], options
					);
				}
			<?php
		}
		?>
</script>

<?php
