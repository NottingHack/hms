<p>
	Please be on the look-out for a payment from <?php echo $this->Html->link($memberName, array( 'controller' => 'members', 'action' => 'view', $memberId, 'full_base' => true)); ?> (<?php echo $memberEmail; ?>). Their reference should be: <?php echo $memberPayRef; ?>.
</p>

<p>
	Members will be approved automatically once their payment has been received.  A gatekeeper pin will then be generated for them (all member admins will receive an e-mail with this information).
</p>