<h2>Contributors</h2>


<h2>Media</h2>

<?php
foreach ($media as $item) {
?>

<div style="clear: both; margin-top: 15px; overflow: hidden;">
	<img src="<?php echo(Router::url('/') . $item['location']); ?>" style="float: left;" />
	<div>
		<p><a href="<?php echo($item['link']) ?>"><?php echo($item['attribution']) ?></a></p>
		<p><a href="<?php echo($item['licence_link']) ?>"><?php echo($item['licence']) ?></a></p>
	</div>
</div>

<?php
}
?>