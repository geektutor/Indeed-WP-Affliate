<div class="uap-ap-wrap">
<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['stats'])):?>
<div class="uap-row">
		<div class="uapcol-md-4 uap-account-overview-tab1">
			<div class="uap-account-no-box" style="padding-left:0px;">
			 <div class="uap-account-no-box-inside">
			  	<div class="uap-count"> <?php echo $data['stats']['referrals']; ?> </div>
				<div class="uap-detail"><?php echo __('Total Referrals', 'uap'); ?></div>
			 </div>
			</div>
		</div>
		<div class="uapcol-md-4 uap-account-overview-tab2">
			<div class="uap-account-no-box" style="padding-left:0px;">
			 <div class="uap-account-no-box-inside">
			  	<div class="uap-count"> <?php echo $data['stats']['paid_referrals_count']; ?> </div>
				<div class="uap-detail"><?php echo __('Paid Referrals', 'uap'); ?></div>
			 </div>
			</div>
		</div>
		<div class="uapcol-md-4 uap-account-overview-tab3">
			<div class="uap-account-no-box" style="padding-left:0px;">
			 <div class="uap-account-no-box-inside">
			  	<div class="uap-count"> <?php echo $data['stats']['unpaid_referrals_count']; ?> </div>
				<div class="uap-detail"><?php echo __('UnPaid Referrals', 'uap'); ?></div>
			 </div>
			</div>
		</div>
		<div class="uapcol-md-4 uap-account-overview-tab4">
			<div class="uap-account-no-box uap-account-box-blue " style="padding-left:0px;">
			 <div class="uap-account-no-box-inside">
			  	<div class="uap-count"> <?php echo $data['stats']['payments']; ?> </div>
				<div class="uap-detail"><?php echo __('Total Transactions', 'uap'); ?></div>
			 </div>
			</div>
		</div>
</div>
<div class="uap-row">
	<div class="uapcol-md-2 uap-account-overview-tab5">
			<div class="uap-account-no-box uap-account-box-green">
			 <div class="uap-account-no-box-inside">
			  	<div class="uap-count"> <?php echo uap_format_price_and_currency($data['stats']['currency'], round($data['stats']['paid_payments_value'], 2));?> </div>
				<div class="uap-detail"><?php echo __('Your Earnings by Now (total Transactions)', 'uap'); ?></div>
			 </div>
			</div>
		</div>
		<div class="uapcol-md-2 uap-account-overview-tab6">
			<div class="uap-account-no-box uap-account-box-red">
			 <div class="uap-account-no-box-inside">
			  	<div class="uap-count"> <?php echo uap_format_price_and_currency($data['stats']['currency'], round($data['stats']['unpaid_payments_value'], 2));?> </div>
				<div class="uap-detail"><?php echo __('Your current Balance', 'uap'); ?></div>
			 </div>
			</div>
		</div>
</div>
	<!--div class="uap-public-general-stats">
		<div><?php echo __('Total number of Referrals:') . $data['stats']['referrals'];?></div>
		<div><?php echo __('Total number of Payments:') . $data['stats']['payments'];?></div>
		<div><?php echo __('Total number of Paid Referrals:') . $data['stats']['paid_referrals_count'];?></div>
		<div><?php echo __('Total number of UnPaid Referrals:') . $data['stats']['unpaid_referrals_count'];?></div>
		<div><?php echo __('Total value of Paid Payments:') . uap_format_price_and_currency($data['stats']['currency'], round($data['stats']['paid_payments_value'], 2));?></div>
		<div><?php echo __('Total value of Unpaid Payments:') . uap_format_price_and_currency($data['stats']['currency'], round($data['stats']['unpaid_payments_value'], 2));?></div>
	</div-->
<?php endif;?>

<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>
</div>
