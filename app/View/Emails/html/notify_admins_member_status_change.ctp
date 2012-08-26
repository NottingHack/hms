<p>Member status was changed:</p>

<p><?php echo $member['name'] . ' [' . $member['email'] . ']'; ?>, was changed from <?php echo $oldStatus; ?> to <?php echo $newStatus; ?> on <?php echo strftime( '%A, %d of %B %Y at %H:%M:%S (%Z)' ); ?></p>
