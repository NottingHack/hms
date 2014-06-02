<?php if(AuthComponent::user()): ?>

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
<?php else: ?>

	<h2>Welcome to Nottingham Hackspace HMS!</h2>

	<h3>What is Nottinghack?</h3>
	<p>Nottinghack is a Nottingham based group for hackers, makers and crafty creatives!</p>
	<p>Hacking is NOT to be confused with network hacking, identity theft and computer virus propagation, etc. Nottinghack does not condone anything illegal; hardware Hacking is a creative, educational hobby!</p>
	<p>Who is it for? If you like to build, make & learn it’s for you. You’ll probably be interested in learning about and sharing knowledge of electronics, crafts, robotics, DIY, hardware hacking, photography, computing, reverse engineering, prototyping, film making, animation, building RC vehicles and other creative challenges and projects.</p>
	<p>You’ll be looking for a group who can share tools, techniques and time; pool resources for bigger projects, get funding, discounts on kits and components and start classes, all in a safe friendly environment!</p>
	<p><?php echo $this->Html->link('Read more...', 'http://nottinghack.org.uk/?page_id=10'); ?></p>

	<h3>What is HMS?</h3>
	<p>HMS (Hackspace Management System) is a program designed to help keep track of members, it's a bit basic at the moment, but we have big plans for it. It's current main goal is to make new member registration easier.</p>

	<h3>Interested in Nottingham Hackspace?</h3>
	<p>Excellent! Have you had a tour yet? If not come down to one of our open hack-nights (every Wednesday from 6:30pm at the address below). Already in the building? Look for the human near <?php echo $this->Html->link('Ein the duck', 'http://www.flickr.com/photos/nottinghack/7048461835/')?>, they'll be able to help you.</p>
	<p> You may also want to follow Nottingham Hackspace on your choice of social network: <?php echo $this->Html->link('Twitter', 'http://twitter.com/#!/hsnotts'); ?>, <?php echo $this->Html->link('Google Group', 'http://groups.google.com/group/nottinghack'); ?>, <?php echo $this->Html->link('Flickr', 'http://www.flickr.com/photos/nottinghack'); ?>, <?php echo $this->Html->link('YouTube', 'http://www.youtube.com/user/nottinghack'); ?>, or <?php echo $this->Html->link('Facebook', 'http://www.facebook.com/pages/NottingHack/106946729335123'); ?>.</p>
	<p>We also have a <?php echo $this->Html->link('Blog', 'http://nottinghack.org.uk/'); ?>.</p>

	

<?php endif; ?>