<p>New prospective member!</p>

<p>
	Someone with the e-mail <?php echo $email; ?> has registered with HMS, they are subscribed to the following mailing lists:<br>
	<?php 
		if(count($mailingLists) > 0):
	?>
		<ul>
			<?php
				foreach($mailingLists as $list)
				{
					echo '<li>' . $this->Html->link($list['name'], array( 'controller' => 'mailingLists', 'action' => 'view', $list['id'], 'full_base' => true )) . '</li>';
				}
			?>
		</ul>
	<?php else: ?>
		User is not subscribed to any mailing lists
	<?php endif; ?>
</p>
