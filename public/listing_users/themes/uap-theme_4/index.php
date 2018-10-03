<?php
if (empty($total_items)){
	die("Direct access not permitted");
}
$dir_path = plugin_dir_path (__FILE__);
$style="<style>".file_get_contents( $dir_path.'style.css')."
</style>";
$list_item_template = '
<div class="team-member">
<div class="member-img">
 	<a #POST_LINK#>
 	<img title="UAP_FIRST_NAME UAP_LAST_NAME" src="UAP_AVATAR" alt=""/>
	</a>
</div>
<div class="member-content">
<div class="member-name">
UAP_FIRST_NAME UAP_LAST_NAME
</div>	
<div class="member-username">
- UAP_USERNAME
 </div>
<div class="uap-top-counts-wrapper">EARNINGSREFERRALSVISITS</div> 
<div class="member-email">
UAP_EMAIL
</div>
<div class="member-extra-fields">
UAP_EXTRA_FIELDS
</div>
</div>
 <div class="clear"></div>
';

