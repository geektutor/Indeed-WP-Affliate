	<div class="uap-stuffbox">
		<h3 class="uap-h3">
			<?php _e('Main ShortCodes', 'uap');?>
		</h3>
		<div class="inside">
			<div class="uap-popup-content help-shortcodes" style="text-align: center;">
        	<div style="margin: 0 auto; display: inline-block;">
	            <div class="uap-popup-shortcodevalue"><i class="fa-uap fa-user-plus-uap"></i><?php _e('Register Form', 'uap');?><span>[uap-register]</span></div>
	            <div class="uap-popup-shortcodevalue"><i class="fa-uap fa-sign-in-uap"></i><?php _e('Login Form', 'uap');?><span>[uap-login-form]</span></div>
	            <div class="uap-popup-shortcodevalue"><i class="fa-uap fa-sign-out-uap"></i><?php _e('Logout Button', 'uap');?><span>[uap-logout]</span></div>
	            <div class="uap-popup-shortcodevalue"><i class="fa-uap fa-unlock-uap"></i><?php _e('Password Recovery', 'uap');?><span>[uap-reset-password]</span></div>
	            <div class="uap-popup-shortcodevalue"><i class="fa-uap fa-user-uap"></i><?php _e('Account Page', 'uap');?><span>[uap-account-page]</span></div>
				<div class="uap-clear"></div>
        	</div>
    	</div>
			<div class="clear"></div>
		</div>
	</div>

	<div class="uap-stuffbox">
		<h3 class="uap-h3">
			<?php _e('User ShortCodes', 'uap');?>
		</h3>
		<div class="inside">
			<div class="uap-popup-content help-shortcodes" style="">
				<table class="wp-list-table widefat fixed tags uap-manage-user-expire">
				<thead>
					<tr>
						<th><?php _e('Field', 'uap');?></th>
						<th><?php _e('Private Shortcode', 'uap');?></th>
						<th><?php _e('Public Shortcode', 'uap');?></th>
					</tr>
				</thead>
				<tbody>
		       	<?php
				$constants = array(	"username",
									"first_name",
									"last_name",
									"user_id",
									"affiliate_id",
									"user_email",
									"account_page",
									"login_page",
									"blogname",
									"blogurl",
									"siteurl",
									'rank_id',
									'rank_name',
									'user_url,',
									'uap_avatar',
				);
		       	foreach ($constants as $k=>$v){
		       		?>
					<tr>
						<td><?php echo $v;?></td>
						<td>[uap-affiliate field="<?php echo $v;?>"]</td>
		       			<td>[uap-public-affiliate-info field="<?php echo $v;?>"]</td>
					</tr>
		       		<?php
		       	}
		       	$custom_fields = uap_get_custom_constant_fields();
		       	foreach ($custom_fields as $k=>$v){
		       		$k = str_replace('{', '', $k);
		       		$k = str_replace('}', '', $k);
		       		?>
		       			<tr>
		       				<td><?php echo $v;?></td>
		       				<td>[uap-affiliate field="<?php echo $k;?>"]</td>
		       				<td>[uap-public-affiliate-info field="<?php echo $k;?>"]</td>
		       			</tr>
		       		<?php
		       	}
		       	?>
		       	</tbody></table>
	    	</div>
			<div class="uap-clear"></div>
		</div>
	</div>

	<div class="uap-stuffbox">
		<h3 class="uap-h3">
			<?php _e('Other ShortCodes', 'uap');?>
		</h3>
		<div class="inside">
			<div class="uap-popup-content help-shortcodes">
            <table class="wp-list-table widefat fixed tags uap-manage-user-expire">
				<thead>
					<tr>
						<th><?php _e('ShortCode', 'uap');?></th>
						<th><?php _e('What it does', 'uap');?></th>
						<th><?php _e('Arguments available', 'uap');?></th>
					</tr>
				</thead>
				<tbody>
                		<tr>
		       				<td><strong>[uap-user-become-affiliate]</strong></td>
		       				<td><?php _e('User Become Affiliate Button', 'uap');?></td>
		       				<td>-</td>
		       			</tr>
                        <tr>
		       				<td><strong>[if_affiliate]<i><?php _e('Your content here!', 'uap');?> </i>[/if_affiliate]</strong></td>
		       				<td><?php _e('Show content only for affiliate users.', 'uap');?></td>
		       				<td>-</td>
		       			</tr>
                        <tr>
		       				<td><strong>[if_not_affiliate]<i><?php _e('Your content here!', 'uap');?> </i>[/if_not_affiliate]</strong></td>
		       				<td><?php _e('Show content only for non-affiliate users.', 'uap');?></td>
		       				<td>-</td>
		       			</tr>
                         <tr>
		       				<td><strong>[visitor_referred]<i><?php _e('Your content here!', 'uap');?> </i>[/visitor_referred]</strong></td>
		       				<td><?php _e('Show content only for referred users.', 'uap');?></td>
		       				<td>-</td>
		       			</tr>
                        <tr>
		       				<td><strong>[visitor_not_referred]<i><?php _e('Your content here!', 'uap');?> </i>[/visitor_not_referred]</strong></td>
		       				<td><?php _e('Show content only for non-referred users.', 'uap');?></td>
		       				<td>-</td>
		       			</tr>
                </tbody>
            </table>    
        	
    	</div>
			<div class="clear"></div>
		</div>
	</div>
