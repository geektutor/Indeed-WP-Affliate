<div class="uap-wrapper">
	<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Landing Commissions (CPA)', 'uap');?></span></div>
		<a href="<?php echo $data['url-add_edit'];?>" class="uap-add-new-like-wp"><i class="fa-uap fa-add-uap"></i><span><?php _e('Add New Landing Commission (CPA)', 'uap');?></span></a>
		<span class="uap-top-message"><?php _e('...create your Landing Commission (CPA -CostPerAction) Shortcode', 'uap');?></span>
		<?php if (!empty($data['errors'])) : ?>
			<div class="uap-wrapp-the-errors"><?php echo $data['errors'];?></div>
		<?php endif;?>
		<?php if (!empty($data['listing_items'])) : ?>
			<form action="" method="post" id="form_shortcodes">
				<div class="uap-offer-items-wrap">
				<div class="uap-info-box"><?php _e('Just copy the landing commission (CPA) shortcode into any successful page (ex: Thank You Register page) and the affiliate will receive a certain commission based on generated referral.', 'uap');?></div>
			
					<?php foreach ($data['listing_items'] as $arr) : ?>
						<?php 	
							$inside_data = unserialize($arr['settings']);
							$color = (empty($inside_data['color']))	? '#000' : '#' . $inside_data['color'];
							$disabled = (empty($arr['status'])) ? 'uap-disabled-box' : '';
						?>
					   
					   <div class="uap-admin-dashboard-offer-box-wrap <?php echo $disabled;?>">
					      <div class="uap-admin-dashboard-offer-box" id="uap-b-item-1" style="background-color: <?php echo $color;?>">
					         <div class="uap-admin-dashboard-offer-box-main">
					            <div class="uap-admin-dashboard-offer-box-title"><?php echo $arr['slug']?></div>
					            <div class="uap-admin-dashboard-offer-box-content">
					            	<?php echo __('Source Name:', 'uap') . ' ' . $inside_data['source'];?>
								</div>
					            <div class="uap-admin-dashboard-offer-box-links-wrap">
					               <div class="uap-admin-dashboard-offer-box-links">
					                  <a href="<?php echo $data['url-add_edit'] . '&slug=' . $arr['slug'];?>" class="uap-admin-dashboard-offer-box-link"><?php _e('Edit', 'uap');?></a>
					                  <div onclick="uap_delete_from_table('<?php echo $arr['slug'];?>', 'Shortcode', '#delete_landing_referral', '#form_shortcodes');" class="uap-admin-dashboard-offer-box-link"><?php _e('Delete', 'uap');?></div>
					               </div>
					            </div>
					         </div>
					         <div class="uap-admin-dashboard-offer-box-bottom">
					            <div class="uap-admin-dashboard-offer-box-files">
									<?php echo uap_format_price_and_currency($currency, $inside_data['amount_value']);?>
					               <div class="uap-admin-dashboard-offer-box-dest">&nbsp;</div>
					               <span style="font-size: 14px;">[uap-landing-commission slug='<?php echo $arr['slug'];?>']</span>
					            </div>
					            <div class="uap-admin-dashboard-offer-box-date"></div>
					            <div class="clear"></div>
					         </div>
					      </div>
					   </div>					   
					   
					<?php endforeach;?>
					<div class="uap-clear"></div>
				</div>
				<input type="hidden" name="delete_landing_referral" value="" id="delete_landing_referral" />
			</form>
		<?php else : ?>
			<h4 style="margin-top:50px;"><?php _e('No Shortcode to show. Please, add your first Shortcode. ', 'uap');?></h4>
		<?php endif;?>	
</div><!-- end of uap-dashboard-wrap -->
							
