<div class="uap-wrapper">
<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Help', 'uap');?></span></div>

<div class="uap-stuffbox">
	<h3 class="uap-h3">
		<?php _e('Activate Ultimate Affiliate Pro', 'uap');?>
	</h3>
	<form method="post" action="">
		<div class="inside">
			<?php if ($disabled):?>
				<div class="iump-form-line iump-no-border" style="font-weight: bold; color: red;"><?php _e("cURL is disabled. You need to enable if for further activation request.")?></div>
			<?php endif;?>
			<div class="iump-form-line iump-no-border" style="width:10%; float:left; box-sizing:border-box; text-align:right; font-weight:bold;">
				<label for="tag-name" class="iump-labels" style="text-align: left;"><?php _e('Purchase Code', 'uap');?></label>
			</div>	
			<div class="iump-form-line iump-no-border" style="width:70%; float:left; box-sizing:border-box;">	
				<input name="uap_licensing_code" type="text" value="<?php echo $data['uap_envato_code'];?>" style="width:100%;"/>
			</div>
			<div class="uap-stuffbox-submit-wrap iump-submit-form" style="width:20%; float:right; box-sizing:border-box; text-align:center;">
				<input type="submit" value="<?php _e('Activate', 'uap');?>" name="uap_save_licensing_code" <?php echo $disabled;?> class="button button-primary button-large" />
			</div>
			<div class="uap-clear"></div>
				<div class="uap-license-status"><?php 
					if (isset($submited)){
							if ($submited){
							?>
								<div class="uap-dashboard-valid-license-code"><?php _e("You've activated the Ultimate Affiliate Pro plugin!", 'uap');?></div>
							<?php 
							} else {
							?>
								<div class="uap-dashboard-err-license-code"><?php _e("You have entered an invalid purchase code or the Envato API could be down for a moment.", 'uap');?></div>
							<?php 	
							}
					}
				?></div>
			<div style="padding:0 60px;">
				<p><?php _e('A valid purchase code Activate the Full Version of', 'uap');?><strong> Ultimate Affiliate Pro</strong> <?php _e('plugin and provides access on support system. A purchase code can only be used for ', 'uap');?><strong><?php _e('ONE', 'uap');?></strong> Ultimate Affiliate Pro <?php _e('for WordPress installation on', 'uap');?> <strong><?php _e('ONE', 'uap');?></strong> <?php _e('WordPress site at a time. If you previosly activated your purchase code on another website, then you have to get a', 'uap');?> <a href="http://codecanyon.net/item/ultimate-affiliate-pro-wordpress-plugin/16527729?ref=azzaroco" target="_blank"><?php _e('new Licence', 'uap');?></a>.</p>
				<h4><?php _e('Where can I find my Purchase Code?', 'uap');?></h4>
				<a href="http://codecanyon.net/item/ultimate-affiliate-pro-wordpress-plugin/16527729?ref=azzaroco" target="_blank">
					<img src="<?php echo UAP_URL;?>assets/images/purchase_code.jpg" style="margin: 0 auto; display: block;"/>
					</a>
				</div>	
			</div>
	</form>		
</div>

<div class="uap-stuffbox">
		<h3 class="uap-h3">
			<label style="text-transform: uppercase; font-size:16px;">
				<?php _e('Contact Support', 'uap');?>
			</label>
		</h3>
		<div class="inside">
			<div class="submit" style="float:left; width:80%;">
				<?php _e('In order to contact Indeed support team you need to create a ticket providing all the necessary details via our support system:', 'uap');?> support.wpindeed.com
			</div>
			<div class="submit" style="float:left; width:20%; text-align:center;">
				<a href="http://support.wpindeed.com/open.php?topicId=19" target="_blank" class="button button-primary button-large"> <?php _e('Submit Ticket', 'uap');?></a>
			</div>
			<div class="clear"></div>
		</div>
	</div>

	<div class="uap-stuffbox">
		<h3 class="uap-h3">
			<label style="text-transform: uppercase; font-size:16px;">
		    	<?php _e('Documentation', 'uap');?>
		    </label>
		</h3>
		<div class="inside">
			<iframe src="http://affiliate.wpindeed.com/documentation/" width="100%" height="1000px" ></iframe>
		</div>
	</div>	

</div>
