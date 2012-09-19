<?php if($jsonData->open): ?>
	<p>Hackspace is open!</p>
<?php else: ?>
	<p>Hackspace is closed.</p>
<?php endif; ?>

<?php 

if( isset($rssData) )
{
	if( isset($rssData->channel) && 
		isset($rssData->channel->item) && 
		count($rssData->channel->item) > 0 )
	{
		echo '<h2>Hackspace News</h2>';

		foreach ($rssData->channel->item as $item):
?>
		<div class="news_item">
			<a href="<?php echo $item->link; ?>"><h3><?php echo $item->title; ?></h3></a>
			<p>
				<?php echo $item->description; ?>
			</p>
		</div>
<?php
		endforeach;
	}
}

?>
