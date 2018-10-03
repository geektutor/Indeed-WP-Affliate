<?php $current_url = UAP_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];?>
<div class="uap-subtab-menu">
	<?php 
		foreach ($data['submenu'] as $url=>$name){
			$selected = ($current_url==$url) ? 'uap-subtab-selected' : '';
			?>
			<a href="<?php echo $url;?>" class="uap-subtab-menu-item <?php echo $selected;?>"><?php echo $name;?></a>
			<?php 
		}
	?>
	<div class="clear"></div>
</div>