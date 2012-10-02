<p>Member has been approved:</p>

<p>We have seen a standing-order payment from <?php echo $this->Html->link($member['name'] . ' [' . $member['email'] . ']', array( 'controller' => 'members', 'action' => 'view', $member['member_id'] , 'full_base' => true ) ); ?> and should now e-mail them to organise access to the space. Their gatekeeper PIN is <?php echo $pin; ?>. Please notify the other member admins if you're going to be the one to contact the new member.</p>
