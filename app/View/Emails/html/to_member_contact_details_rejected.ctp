<p>
	Hello!
</p>

<p>
	One of the member admins have indicated that there is an issue with the contact details you entered, they have send you the following message:
</p>

<p>
	<?php echo $reason; ?>
</p>

<p>
	Please <?php echo $this->Html->link('Login to HMS', array('controller' => 'members', 'action' => 'login', 'full_base' => true)); ?> and update your contact details.
</p>

<p>
	Thanks,<br>
	Nottinghack Member Admin Team
</p>