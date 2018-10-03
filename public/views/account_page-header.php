<style>
<?php foreach ($data['available_tabs'] as $k=>$v):?>
	<?php echo '.fa-' . $k . '-account-uap:before';?>{
		content: '\<?php echo $v['uap_tab_' . $k . '_icon_code'];?>';
	}
<?php endforeach;?>
<?php   if (!empty($data['uap_account_page_custom_css'])) echo $data['uap_account_page_custom_css'];?>
</style>

<link href='<?php echo UAP_URL . 'assets/css/croppic.css';?>' rel='stylesheet' type='text/css' />
<script src="<?php echo UAP_URL . 'assets/js/jquery.mousewheel.min.js';?>"></script>
<script src="<?php echo UAP_URL . 'assets/js/croppic.js';?>"></script>
<script src="<?php echo UAP_URL . 'assets/js/account_page-banner.js';?>"></script>

<script>
UapAccountPageBanner.init({
		triggerId					: 'js_uap_edit_top_ap_banner',
		saveImageTarget		: '<?php echo UAP_URL . 'public/ajax-upload.php';?>',
		cropImageTarget   : '<?php echo UAP_URL . 'public/ajax-upload.php';?>',
		bannerClass       : 'uap-user-page-top-background'
});
</script>

<div class="uap-user-page-wrapper">
<?php
	$top_style='';
	if (empty($data['top-background']) && ($data['uap_ap_top_theme'] == 'uap-ap-top-theme-2' || $data['uap_ap_top_theme'] == 'uap-ap-top-theme-3' )) $top_style .='style="padding-top:75px;"'; ?>
<div class="uap-user-page-top-wrapper  <?php echo (!empty($data['uap_ap_top_theme']) ? $data['uap_ap_top_theme'] : '');?>" <?php echo $top_style;?>>

  <div class="uap-left-side">
	<div class="uap-user-page-details">
		<?php if (!empty($data['avatar'])):?>
			<div class="uap-user-page-avatar"><img src="<?php echo $data['avatar'];?>" class="uap-member-photo"/></div>
		<?php endif;?>
	 </div>
	</div>
	<div class="uap-middle-side">
		<div class="uap-account-page-top-mess"><?php echo do_shortcode($data['message']);?></div>
		<?php if (!empty($data['top-rank']) && !empty($data['rank'])):?>
		<div class="uap-top-rank">
			<div class="uap-top-rank-box" style="background-color:#<?php echo $data['rank']['color'];?>;" title=""><?php echo $data['rank']['label'];?></div>
		</div>
		<?php endif;?>
	</div>
	<div class="uap-right-side">
		<?php if (!empty($data['top-earning'])):?>
			<div class="uap-top-earnings">
				<div class="uap-stats-label"><?php echo __('Earnings', 'uap'); ?></div>
				<div class="uap-stats-content"> <?php echo uap_format_price_and_currency($data['stats']['currency'], round($data['stats']['paid_payments_value']+$data['stats']['unpaid_payments_value'], 2)); ?></div>
			</div>
		<?php endif;?>
		<?php if (!empty($data['top-referrals'])):?>
			<div class="uap-top-referrals">
				<div class="uap-stats-label"><?php echo __('Referrals', 'uap'); ?></div>
				<div class="uap-stats-content"> <?php echo $data['stats']['referrals']; ?></div>
			</div>
		<?php endif;?>


		<?php if (!empty($data['top-achievement']) && $data['achieved']>-1):?>
			<div class="uap-clear uap-special-clear"></div>
			<div class="uap-top-achievement">
				<div class="uap-stats-label"><?php echo __('Until the next Rank...', 'uap'); ?></div>
				<div class="uap-achievement-line">
					<div class="uap-achieved" style="width:<?php echo $data['achieved']; ?>%;"></div>
				</div>
			</div>
		<?php endif;?>
        <?php if (!empty($data['uap_ap_edit_show_metrics'])):?>
			<div class="uap-top-metrics">
				<div class="uap-stats-content">
					<div class="uap-metris-rightside">
						<div>
								<?php echo __('3 months EPC: ', 'uap');
								echo uap_format_price_and_currency($data['stats']['currency'], $data['metrics'][3]); ?>
						</div>
						<div>
								<?php echo __('7 days EPC: ', 'uap');
								echo uap_format_price_and_currency($data['stats']['currency'], $data['metrics'][7]); ;?>
						</div>
					</div>
				</div>
			</div>
		<?php endif;?>

		<div class="uap-clear"></div>
	</div>
	<div class="uap-clear"></div>
	<?php if (!empty($data['top-background'])):
  	$bk_style='';

		///
		$bkStyle = '';
		$banner = '';
		if (!empty($data['uap_account_page_personal_header'])):
				$banner = $data ['uap_account_page_personal_header'];
		endif;

		if (empty($banner) && !empty($data ['top_banner'])):
			$banner = $data ['top_banner'];
		elseif (empty($banner) && !empty($data ['top-background-image'])): 
			$banner = $data ['top-background-image'];	
		endif;
		if (!empty($banner)){
				$bkStyle = 'style="background-image:url('.$banner.');"';
		}
		///
	?>
  <div class="uap-user-page-top-background" <?php echo $bkStyle;?> data-banner="<?php echo $banner;?>">
			<div class="uap-edit-top-ap-banner" id="js_uap_edit_top_ap_banner"></div>
	</div>
  <?php endif;?>
</div>
<div class="uap-user-page-content-wrapper <?php echo $data['uap_ap_theme'];?>">


<?php //=================================== TABS ====================================//?>

<?php
$tabs = array(
					'overview' => array(
						'type' => 'tab',
						'label' => __('Overview', 'uap'),
						'slug' => 'overview',
						'print_link' => TRUE,
						'icon_code' => '',
					),
					'profile' => array(
						'type' => 'tab',
						'label' => __('Profile', 'uap'),
						'slug' => 'profile',
						'icon_code' => '',
						'print_link' => FALSE,
						'children' => array(
											'edit_account' => array(
												'type' => 'subtab',
												'label' => __('Edit Account', 'uap'),
												'slug' => 'edit_account',
												'icon_code' => '',
												'print_link' => TRUE,
												'parent' => 'profile',
											),
											'change_pass' => array(
												'type' => 'subtab',
												'label' => __('Change Password', 'uap'),
												'slug' => 'change_pass',
												'icon_code' => '',
												'print_link' => TRUE,
												'parent' => 'profile',
											),
											'payments_settings' => array(
												'type' => 'subtab',
												'label' => __('Payment Settings', 'uap'),
												'slug' => 'payments_settings',
												'icon_code' => '',
												'print_link' => TRUE,
												'parent' => 'profile',
											),
											'custom_affiliate_slug' => array(
												'type' => 'subtab',
												'label' => __('Custom Affiliate Slug', 'uap'),
												'slug' => 'custom_affiliate_slug',
												'icon_code' => '',
												'print_link' => TRUE,
												'parent' => 'profile',
											),
											'pushover_notifications' => array(
												'type' => 'subtab',
												'label' => __('Pushover Notifications', 'uap'),
												'slug' => 'pushover_notifications',
												'icon_code' => '',
												'print_link' => TRUE,
												'parent' => 'profile',
											),
						),
					),
					'marketing' => array(
						'type' => 'tab',
						'label' => __('Marketing', 'uap'),
						'slug' => 'marketing',
						'print_link' => FALSE,
						'icon_code' => '',
						'children' =>	array(
											'affiliate_link' => array(
												'type' => 'subtab',
												'label' => __('Affiliate Links', 'uap'),
												'slug' => 'affiliate_link',
												'icon_code' => '',
												'print_link' => TRUE,
												'parent' => 'marketing',
											),
					  						'simple_links' => array(
												'type' => 'subtab',
												'label' => __('Simple Links', 'uap'),
												'slug' => 'simple_links',
												'icon_code' => '',
												'print_link' => TRUE,
												'parent' => 'marketing',
											),
					  						'campaigns' => array(
												'type' => 'subtab',
												'label' => __('Campaigns', 'uap'),
												'slug' => 'campaigns',
												'icon_code' => '',
												'print_link' => TRUE,
												'parent' => 'marketing',
											),
											'banners' => array(
												'type' => 'subtab',
												'label' => __('Banners', 'uap'),
												'slug' => 'banners',
												'icon_code' => '',
												'print_link' => TRUE,
												'parent' => 'marketing',
											),
											'landing_pages' => array(
												'type' => 'subtab',
												'label' => __('Your Landing Pages', 'uap'),
												'slug' => 'landing_pages',
												'print_link' => true,
												'icon_code' => '',
												'parent' => 'marketing',
											),
											'coupons' => array(
												'type' => 'subtab',
												'label' => __('Coupons', 'uap'),
												'slug' => 'coupons',
												'icon_code' => '',
												'print_link' => TRUE,
												'parent' => 'marketing',
											),
						)
					),
					'referrals' => array(
						'type' => 'tab',
						'label' => __('Statements', 'uap'),
						'slug' => 'referrals',
						'print_link' => TRUE,
						'icon_code' => '',
					),
					'payments' => array(
						'type' => 'tab',
						'label' => __('Earnings', 'uap'),
						'slug' => 'payments',
						'print_link' => TRUE,
						'icon_code' => '',
					),
					'wallet' => array(
						'type' => 'tab',
						'label' => __('Wallet', 'uap'),
						'slug' => 'wallet',
						'print_link' => TRUE,
						'icon_code' => '',
					),
					'reports' => array(
						'type' => 'tab',
						'label' => __('Reports', 'uap'),
						'slug' => 'reports',
						'icon_code' => '',
						'print_link' => FALSE,
						'children' => array(
					  						'reports' => array(
												'type' => 'subtab',
												'label' => __('OverAll', 'uap'),
												'slug' => 'reports',
												'print_link' => TRUE,
												'icon_code' => '',
												'parent' => 'reports',
											),
					  					  	'visits' => array(
												'type' => 'subtab',
												'label' => __('Traffic Log', 'uap'),
												'slug' => 'visits',
												'print_link' => TRUE,
												'icon_code' => '',
												'parent' => 'reports',
											),
										  	'campaign_reports' => array(
												'type' => 'subtab',
												'label' => __('Campaign Reports', 'uap'),
												'slug' => 'campaign_reports',
												'print_link' => TRUE,
												'icon_code' => '',
												'parent' => 'reports',
											),
										  	'referrals_history' => array(
												'type' => 'subtab',
												'label' => __('Referrals History', 'uap'),
												'slug' => 'referrals_history',
												'print_link' => TRUE,
												'icon_code' => '',
												'parent' => 'reports',
											),
										  	'mlm' => array(
												'type' => 'subtab',
												'label' => __('MLM', 'uap'),
												'slug' => 'mlm',
												'print_link' => TRUE,
												'icon_code' => '',
												'parent' => 'reports',
											),
						)
					),
				    'referral_notifications' => array(
						'type' => 'tab',
						'label' => __('Referral Notifications & Reports', 'uap'),
						'slug' => 'referral_notifications',
						'print_link' => TRUE,
						'icon_code' => '',
					),
					'help' => array(
						'type' => 'tab',
						'label' => __('Help', 'uap'),
						'slug' => 'help',
						'print_link' => TRUE,
						'icon_code' => '',
					),
					'logout' => array(
						'type' => 'tab',
						'label' => __('LogOut', 'uap'),
						'slug' => 'logout',
						'print_link' => TRUE,
						'icon_code' => '',
					),
);

foreach ($custom_menu as $temp_k=>$temp_arr){
	if (emptY($temp_arr['type'])){
		$custom_menu[$temp_k]['type'] = 'tab';
	}
}
$tabs = array_merge($tabs, $custom_menu);
$tabs = uap_reorder_menu_items($order, $tabs);

$selected_parent = '';
foreach ($tabs as $first_slug => $array){
	/// exclude item
	if (in_array($first_slug, $exclude_tabs)){
		unset($tabs[$first_slug]);
		continue;
	} else if (isset($array['children'])){
		/// parent check
		foreach ($array['children'] as $children_slug => $children_array){
			if (in_array($children_slug, $exclude_tabs)){
				unset($tabs[$first_slug]['children'][$children_slug]);
			} else if (!in_array($children_slug, $data['show_tab_list'])){
				unset($tabs[$first_slug]['children'][$children_slug]);
			}
		}
		if (count($tabs[$first_slug]['children'])==0){
			unset($tabs[$first_slug]);
			continue;
		}
	} else if (!in_array($first_slug, $data['show_tab_list'])){
		unset($tabs[$first_slug]);
		continue;
	}

	///// UPDATE MENU LABEL
	if (!empty($this->account_page_settings['uap_tab_' . $first_slug . '_menu_label'])){
		$tabs[$first_slug]['label'] = $this->account_page_settings['uap_tab_' . $first_slug . '_menu_label'];
	}


	if (!empty($tabs[$first_slug]['children'])){
		foreach ($tabs[$first_slug]['children'] as $second_slug => $second_array){

			/// exclude item
			if (in_array($second_slug, $exclude_tabs)){
				unset($tabs[$first_slug]['children'][$second_slug]);
				continue;
			} else if (!in_array($second_slug, $data['show_tab_list'])){
				unset($tabs[$first_slug]['children'][$second_slug]);
			}

			//// SET THE SELECTED PARENT
			if ($second_slug==$data['selected_tab']){
				$selected_parent = $second_array['parent'];
			}

			///// UPDATE MENU LABEL
			if (!empty($this->account_page_settings['uap_tab_' . $second_slug . '_menu_label'])){
				$tabs[$first_slug]['children'][$second_slug]['label'] = $this->account_page_settings['uap_tab_' . $second_slug . '_menu_label'];
			}
		}
	}
}
//// DO REORDER MENU ITEMS

?>
		<div class="uap-ap-menu">
			<ul>
				<?php foreach ($tabs as $slug => $array) : ?>
			        <?php if ($array['type'] == 'tab'):
							if (!empty($array['children'])){
			        			if ($selected_parent==$slug){
			        				$extra_style = 'display: block';
			        				$i_class = 'fa-account-down-uap';
			        			} else {
			        				$extra_style = '';
			        				$i_class = 'fa-account-right-uap';
			        			}
								if ($data['uap_ap_theme']=='uap-ap-theme-1'){
									$action = "onClick=uap_show_subtabs('" . $slug . "');";
								} else {
									$action = "";
								}
			        ?>
								<li class="uap-ap-submenu-item"><div class="uap-ap-menu-tab-item" <?php echo $action;?> ><a href="javascript:void(0);"><i class="uap-ap-menu-sign fa-uap <?php echo $i_class;?>" id="<?php echo 'uap_fa_sign-' . $slug;?>"></i><?php echo $array['label'];?></a></div>
									<ul class="uap-public-ap-menu-subtabs" style="<?php echo $extra_style;?>" id="<?php echo 'uap_public_ap_' . $slug;?>">
										<?php foreach ($array['children'] as $second_slug => $second_array): ?>
											<?php $extra_class = ($data['selected_tab']==$second_slug) ? 'uap-ap-menu-item-selected' : '';?>
											<li class="uap-ap-menu-item <?php echo $extra_class;?>"><a href="<?php echo $data['urls'][$second_slug];?>"><i class="<?php echo 'fa-uap fa-' . $second_slug . '-account-uap';?>"></i><?php
			        						  	echo $second_array['label'];
											?></a></li>
										<?php endforeach;?>
									</ul>
								</li>
						<?php } else { ?>
					  		<?php $extra_class = ($data['selected_tab']==$slug) ? 'uap-ap-menu-tab-item-selected' : '';?>
						  	<li class="uap-ap-menu-tab-item <?php echo $extra_class;?>"><a href="<?php echo $data['urls'][$slug];?>"><i class="<?php echo 'fa-uap fa-' . $slug . '-account-uap';?>"></i><?php
						  		echo $array['label'];
						  	?></a></li>
						<?php }?>

					<?php endif; ?>

				<?php endforeach;?>
			</ul>
		</div>

<?php //=================================== TABS ====================================//?>



<div class="uap-user-page-content">
