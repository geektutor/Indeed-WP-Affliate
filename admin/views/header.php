<script>
var uap_messages = { 
					referrals: "<?php _e('Are you sure you want to delete this Referral?', 'uap');?>",
					general_delete: "<?php _e('Delete This ', 'uap');?>",
					affiliates: "<?php _e('Delete selected Affiliates', 'uap');?>",
					email_server_check: "<?php _e('An E-mail was sent to your Admin address. Check your inbox or Spam/Junk Folder!', 'uap');?>",
};
</script>
<div class="uap-dashboard-wrap">
	<div class="uap-admin-header">
		<div class="uap-top-menu-section">
			<div class="uap-dashboard-logo">
			<a href="<?php echo $data['base_url'] . '&tab=dashboard';?>">
				<img src="<?php echo UAP_URL;?>assets/images/dashboard-logo.jpg"/>
				<div class="uap-plugin-version"><?php echo $plugin_vs; ?></div>
			</a>
			</div>
			<div class="uap-dashboard-menu">
				<ul>
					<?php foreach ($data['menu_items'] as $k=>$v) :?>
						<?php $selected = ($data['tab']==$k) ? 'selected' : '';?>
								<li class="<?php echo $selected;?>">
									<?php 
										$dezactivated_class ='';
										$url = $data['base_url'] . '&tab=' . $k;
										if ($k=='banners' && !UAP_LICENSE_SET):
											$url = '#';
											$dezactivated_class = 'uap-inactive-tab';
										endif;
										if ($k=='affiliates' && !empty($data['affiliates_notification_count'])){
											echo '<div class="uap-dashboard-notification-top">' . $data['affiliates_notification_count'] . '</div>';
										} else if ($k=='referrals' && !empty($data['referrals_notification_count'])){
											echo '<div class="uap-dashboard-notification-top">' . $data['referrals_notification_count'] . '</div>';
										}	
									?>									
									<a href="<?php echo $url;?>" title="<?php echo $v;?>">
										<div class="uap-page-title link-<?php echo $k; ?>  <?php echo $dezactivated_class;?>">
											<i class="fa-uap fa-uap-menu fa-<?php echo $k;?>-uap"></i>
											<div><?php echo $v;?></div>								
										</div>						
									</a>
								</li>	
					<?php endforeach;?>
				</ul>
			</div>
		</div>
	</div>
	
	
<div class="uap-right-menu">
	<?php 
		foreach ($data['right_tabs'] as $k=>$v){
		?>
		<div class="uap-right-menu-item">
			<a href="<?php echo $data['base_url']  . '&tab=' . $k;?>" title="<?php echo $v;?>">
				<div class="uap-page-title-right-menu">
					<i class="fa-uap fa-uap-menu fa-<?php echo $k;?>-uap"></i>
					<div class="uap-right-menu-title"><?php echo $v;?></div>								
				</div>						
			</a>	
		</div>
		<?php
		}
	?>
</div>	


