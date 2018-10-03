<div class="uap-banners-wrapp">

	<?php if (!empty($data['title'])):?>
		<h3><?php echo $data['title'];?></h3>
	<?php endif;?>
	<?php if (!empty($data['message'])):?>
		<p><?php echo do_shortcode($data['message']);?></p>
	<?php endif;?>

		<?php if (!empty($data['items'])):?>

			<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		    <script type="text/javascript">
		      google.charts.load('current', {packages:["orgchart"]});
		      google.charts.setOnLoadCallback(drawChart);

		      function drawChart() {
		        var data = new google.visualization.DataTable();
						var theParent = '<?php echo $data['parent'];?>';

		        data.addColumn('string', 'Name');
		        data.addColumn('string', 'Manager');
		        data.addColumn('string', 'ToolTip');

		        // For each orgchart box, provide the name, manager, and tooltip to show.

				data.addRows([
						<?php if (!empty($data['parent'])):?>
							[{v:'<?php echo $data['parent'];?>', f:'<?php echo $data['parent'];?>'}, '', ''],
						<?php endif;?>
		          [{v:'<?php echo $data['username']; ?>', f:''}, theParent, 'Main Affiliate'],
				<?php
					foreach ($data['items'] as $item):
					echo "[{v:'".$item['username']."',f:'".$item['username']."<div>".$item['amount_value']."</div>' }, '".$item['parent']."', ' Level ".$item['level']."'],";
					endforeach;
				?>
		        ]);
		        // Create the chart.
		        var chart = new google.visualization.OrgChart(document.getElementById('uap_mlm_chart'));
		        // Draw the chart, setting the allowHtml option to true for the tooltips.
						<?php if (!empty($data['parent'])):?>
						data.setRowProperty(0, 'style', 'background-color: #2a81ae; color: #fff;');
						data.setRowProperty(1, 'style', 'background-color: #f25a68; color: #fff;');
						<?php endif;?>
		        chart.draw(data, {allowHtml:true, size:"medium", allowCollapse:true});
		      }
		   </script>

<div id="uap_mlm_chart"></div>

			<table class="uap-account-table">
				<tbody>
					<thead>
						<tr>
							<th><?php _e('Subaffiliate', 'uap');?></th>
							<th><?php _e('E-mail Address', 'uap');?></th>
							<th><?php _e('Level', 'uap');?></th>
							<th><?php _e('Amount', 'uap');?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e('Subaffiliate', 'uap');?></th>
							<th><?php _e('E-mail Address', 'uap');?></th>
							<th><?php _e('Level', 'uap');?></th>
							<th><?php _e('Amount', 'uap');?></th>
						</tr>
					</tfoot>
					<?php foreach ($data['items'] as $item):?>
					<tr>
						<td><?php echo $item['username'];?></td>
						<td><?php echo $item['email'];?></td>
						<td><?php echo $item['level'];?></td>
						<td><?php echo $item['amount_value'];?></td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
		<?php else : ?>
			<?php _e('No Children Yet!', 'uap');?>
		<?php endif;?>

</div>
