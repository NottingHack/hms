<p>Member has been approved:</p>

<p>We have seen a standing-order payment from <?php echo $this->Html->link($memberName, array( 'controller' => 'members', 'action' => 'view', $memberId , 'full_base' => true ) ); ?> <?php echo $memberEmail; ?> and should now e-mail them to organise access to the space. Their gatekeeper PIN is <?php echo $memberPin; ?>. Please notify the other member admins if you're going to be the one to contact the new member.</p>
