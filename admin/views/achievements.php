<div class="uap-wrapper">
<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Achievements', 'uap');?></span></div>
<div class="uap-special-box">
	<form action="" method="post">
		<?php _e('Affiliate Username: ', 'uap');?> <input type="text" name="affiliate_username" value="<?php echo @$_POST['affiliate_username'];?>" style="min-width:300px; min-height:31px;"/>
		<input type="submit" value="<?php _e('Search', 'uap');?>" name="search" class="button button-primary button-large" />		
	</form>
</div>


<div class="uap-stuffbox">
	<h3 class="uap-h3"><?php _e('Last 50 Achievements', 'uap');?></h3>
	<div class="inside">
	<?php 
		if (!empty($data['history'])):
			foreach ($data['history'] as $item):
				$current = (empty($item['current_rank'])) ? __('None', 'uap') : $item['current_rank'];
				$prev = (empty($item['prev_rank'])) ? __('None', 'uap') : $item['prev_rank'];
				?>
				<div class="uap-achievement"><?php echo __('On', 'uap') . ' ' . $item['add_date'] . ' <b>' . $item['username'] . '</b> ' . __('has moved from ', 'uap') . $prev . ' ' . __('to', 'uap') . ' ' . $current;?>.</div>
				<?php 
			endforeach;
		endif;
	?>	
	</div>
</div>
</div>

