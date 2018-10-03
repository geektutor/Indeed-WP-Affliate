<div class="uap-ap-wrap">
<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['content'])):?>
	<p><?php echo do_shortcode($data['content']);?></p>
<?php endif;?>
</div>
<?php if (empty($data['pages'])):?>
  <div><?php _e('No Landing Pages assigned', 'uap');?></div>
<?php else :?>
<table class="uap-account-table">
    <thead>
        <tr>
            <td><?php _e('Title', 'uap');?></td>
            <td><?php _e('Link', 'uap');?></td>
        </tr>
    </thead>
    <?php foreach ($data['pages'] as $object):?>
      <tr>
          <td><?php echo $object->post_title;?></td>
          <td><?php echo get_permalink($object->ID);?></td>
      </tr>
    <?php endforeach;?>
</table>
<?php endif;?>
