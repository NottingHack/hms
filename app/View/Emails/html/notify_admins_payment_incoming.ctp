<p>
	Please be on the look-out for a payment from <?php echo $this->Html->link($memberName, array( 'controller' => 'members', 'action' => 'view', $memberId, 'full_base' => true)); ?> (<?php echo $memberEmail; ?>). Their reference should be: <?php echo $memberPayRef; ?>.
</p>

<p>
	Once their payment has arrived please click <?php echo $this->Html->link('here', array('controller' => 'members', 'action' => 'approve_member', $memberId, 'full_base' => true)); ?> to approve them as a member and generate a gatekeeper pin for them (all member admins will receive an e-mail with this information).
</p>