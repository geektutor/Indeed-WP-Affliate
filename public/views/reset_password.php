<?php if (!empty($meta_arr['uap_login_custom_css'])): ?>
	<style><?php echo $meta_arr['uap_login_custom_css'];?></style>
<?php endif;?>
<div class="uap-pass-form-wrap <?php echo $meta_arr['uap_login_template'];?>">
	<form action="" method="post" >
		<input name="uapaction" type="hidden" value="reset_pass">

	<?php switch ($meta_arr['uap_login_template']){
		 case 'uap-login-template-2': ?>
			<div class="uap-form-line-fr">
					<input type="text" value="" name="email_or_userlogin" placeholder="<?php _e('Username or E-mail');?>" />
			</div>
			<div class="uap-form-line-fr uap-form-submit"><input type="submit" value="<?php _e('Get New Password', 'ulp');?>" name="Submit"></div>
	<?php break;?>

	<?php case 'uap-login-template-3': ?>
		<div >
			<div class="impu-form-line-fr">
				<input type="text" value="" name="email_or_userlogin" placeholder="<?php _e('Username or E-mail');?>" />
			</div>
			<div class="uap-form-line-fr uap-form-submit">
				<input type="submit" value="<?php _e('Get New Password', 'uap');?>" style="width: 80%;" name="Submit" class="button button-primary button-large" />
			</div>
			<div class="uap-clear"></div>
		</div>
	<?php break;?>

		<?php case 'uap-login-template-4': ?>
			<div class="uap-form-line-fr">
				<i class="fa-uap fa-username-uap"></i><input type="text" value="" name="email_or_userlogin" placeholder="<?php _e('Username or E-mail', 'uap');?>" />
			</div>
			<div class="uap-form-line-fr uap-form-submit">
				<input type="submit" value="<?php _e('Get New Password', 'uap');?>" style="width: 80%;" name="Submit" class="" />
			</div>
		<?php break;?>

		<?php case 'uap-login-template-5': ?>
			<div class="uap-form-line-fr">
					<span class="uap-form-label-fr uap-form-label-username"><?php _e('Username or E-mail', 'uap');?></span>
				  <input type="text" value="" name="email_or_userlogin" style="    padding: 8px 4px 7px 9px;" placeholder="" />
			</div>
			<div class="uap-form-line-fr uap-form-submit">
				<input type="submit" value="<?php _e('Get New Password', 'uap');?>" style="width: 80%;" name="Submit" class="" />
			</div>
		<?php break;?>

				<?php case 'uap-login-template-6': ?>
					<div class="uap-form-line-fr">
							<span class="uap-form-label-fr uap-form-label-username"><b><?php _e('Username or E-mail', 'uap');?></b></span>
						  <input type="text" value="" name="email_or_userlogin" style="width: 94%;" placeholder="" />
					</div>
					<div class="uap-temp6-row-right">
							<div class="uap-form-line-fr uap-form-submit">
								<input type="submit" value="<?php _e('Get New Password', 'uap');?>" style="font-weight: 500;" name="Submit" class="" />
							</div>
					</div>
				<?php break;?>

				<?php case 'uap-login-template-8':?>
					<div class="uap-form-line-fr">
						<i class="fa-uap fa-username-uap"></i>
						<input type="text" value="" name="email_or_userlogin" placeholder="<?php _e('Username or E-mail', 'uap');?>" />
					</div>
					<div class="uap-form-line-fr uap-form-submit">
						<input type="submit" value="<?php _e('Get New Password', 'uap');?>" name="Submit" style="    width: 85%;padding: 15px 30px !important; min-height: 0px;" class="button button-primary button-large" />
					</div>
				<?php break;?>

				<?php case 'uap-login-template-9':?>
					<div class="uap-form-line-fr">
						<i class="fa-uap fa-username-uap"></i>
						<input type="text" value="" name="email_or_userlogin" placeholder="<?php _e('Username or E-mail', 'uap');?>" />
					</div>
					<div class="uap-form-line-fr uap-form-submit">
						<input type="submit" value="<?php _e('Get New Password', 'uap');?>" name="Submit" style="width: 85%;padding: 15px 30px !important; min-height: 0px;" class="button button-primary button-large" />
					</div>
				<?php break;?>

				<?php case 'uap-login-template-10':?>
					<div class="uap-form-line-fr">
						<i class="fa-uap fa-username-uap"></i>
						<input type="text" value="" name="email_or_userlogin" placeholder="<?php _e('Username or E-mail', 'uap');?>" />
					</div>
					<div class="uap-form-line-fr uap-form-submit">
						<input type="submit" value="<?php _e('Get New Password', 'uap');?>" name="Submit" style="width: 85%;padding: 15px 30px !important; min-height: 0px;" class="button button-primary button-large" />
					</div>
				<?php break;?>
                <?php case 'uap-login-template-11':?>
					<div class="uap-form-line-fr">
						<i class="fa-uap fa-username-uap"></i>
						<input type="text" value="" name="email_or_userlogin" placeholder="<?php _e('Username or E-mail', 'uap');?>" />
					</div>
					<div class="uap-form-line-fr uap-form-submit">
						<input type="submit" value="<?php _e('Get New Password', 'uap');?>" name="Submit" style="width: 85%;padding: 15px 30px !important; min-height: 0px;" class="button button-primary button-large" />
					</div>
				<?php break;?>
                <?php case 'uap-login-template-12':?>
					<div class="uap-form-line-fr">
						<i class="fa-uap fa-username-uap"></i>
						<input type="text" value="" name="email_or_userlogin" placeholder="<?php _e('Username or E-mail', 'uap');?>" />
					</div>
					<div class="uap-form-line-fr uap-form-submit">
						<input type="submit" value="<?php _e('Get New Password', 'uap');?>" name="Submit" style="width: 85%;padding: 15px 30px !important; min-height: 0px;" class="button button-primary button-large" />
					</div>
				<?php break;?>
                <?php case 'uap-login-template-13': ?>
                	<div class="uap-form-pass-additional-content">
					<?php _e('To reset your password, please enter your email address or username below', 'uap');?>
					</div>
			<div class="uap-form-line-fr">				
				  <input type="text" value="" name="email_or_userlogin" style="    padding: 8px 4px 7px 9px;" placeholder="<?php _e('Enter your username or email', 'uap');?>" />
			</div>
			<div class="uap-form-line-fr uap-form-submit">
				<input type="submit" value="<?php _e('Reset My Password', 'uap');?>" style="width: 80%;" name="Submit" class="" />
			</div>
		<?php break;?>

				<?php default:?>
					<div class="uap-form-line-fr">
						<span class="uap-form-label-fr uap-form-label-username"><?php _e('Username or E-mail', 'uap');?></span>
						<input type="text" value="" name="email_or_userlogin" />
					</div>
					<div class="uap-form-line-fr uap-form-submit">
						<input type="submit" value="<?php _e('Get New Password', 'uap');?>" name="Submit" class="button button-primary button-large" />
					</div>
				<?php break;?>

	<?php }?>

	</form>
	<?php
	if (!empty($data['success_message'])){
		echo "<div class='uap-reset-pass-success-msg'>" . $data['success_message'] . '</div>';
	} else if (!empty($data['error_message'])){
		echo "<div class='uap-wrapp-the-errors'>" . $data['error_message'] . '</div>';
	}
	?>
</div>
