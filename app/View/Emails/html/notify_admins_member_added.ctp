<p>New prospective member!</p>

<p>
	<?php echo $this->Html->link($member['name'] . ' [' . $member['email'] . ']', array( 'controller' => 'members', 'action' => 'view', $member['member_id'], 'full_base' => true ) ); ?>,
	 was added by on <?php echo strftime( '%A, %d of %B %Y at %H:%M:%S (%Z)' ); ?> by <?php echo $memberAdmin ?>. Watch out for a standing order with the payment ref: <?php echo $paymentRef; ?>.</p>
