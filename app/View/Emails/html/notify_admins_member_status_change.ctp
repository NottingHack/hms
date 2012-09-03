<p>Member status was changed:</p>

<p><?php echo $this->Html->link($member['name'] . ' [' . $member['email'] . ']', array( 'controller' => 'members', 'action' => 'view', $member['member_id'] ), array( 'full_base' => true ) ); ?>, was changed from <?php echo $oldStatus; ?> to <?php echo $newStatus; ?> on <?php echo strftime( '%A, %d of %B %Y at %H:%M:%S (%Z)' ); ?></p>
