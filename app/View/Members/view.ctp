<!-- File: /app/View/Member/view.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb($member['Member']['name'], '/members/view/' . $member['Member']['member_id']);
?>


<dl>
	<dt>
		Username
	</dt>
	<dd>
		<?php echo $member['Member']['username']; ?>
	</dd>

	<dt>
		Handle
	</dt>
	<dd>
		<?php echo $member['Member']['handle']; ?>
	</dd>

	<dt>
		Email
	</dt>
	<dd>
		<?php echo $member['Member']['email']; ?>
	</dd>

	<?php 
		# Only show join date to current members
		if($member['Member']['member_status'] == 1):
	?>

		<dt>
			Member Since
		</dt>
		<dd>
			<?php echo $member['Member']['join_date']; ?>
		</dd>
	<?php endif; ?>

	<?php if(isset($member['Member']['unlock_text'])): ?>
		<dt>
			Unlock Text
		</dt>
		<dd>
			<?php echo $member['Member']['unlock_text']; ?>
		</dd>
	<?php endif; ?>

	<dt>
		Groups
	</dt>
	<dd>
		<?php

		$numGroups = count($member['Group']);
        if($numGroups === 0)
        {
            echo 'None';
        }
        else
        {
            for($i = 0; $i < $numGroups; $i++) {
                echo $this->Html->link($member['Group'][$i]['grp_description'], array('controller' => 'groups', 'action' => 'view', $member['Group'][$i]['grp_id']));
                if($i < $numGroups - 1)
                {
                    echo ', ';
                }
            }
        }
       ?>
	</dd>

	<?php if(isset($member['Status'])): ?>
		<dt>
			Status
		</dt>
		<dd>
			<?php echo $this->Html->link($member['Status']['title'], array('controller' => 'members', 'action' => 'list_members_with_status', $member['Status']['status_id'])); ?>
		</dd>

	<?php endif; ?>

	<?php if( isset($member['Pin']['pin']) ): ?>
		<dt>
			Pin
		</dt>
		<dd>
			<?php echo $member['Pin']['pin']; ?>
		</dd>

		<?php 
			if( isset($member['Pin']['expiry']) &&
				$member['Pin']['expiry'] != null ):
		?>

			<dt>
				Pin Expires
			</dt>
			<dd>
				<?php echo date('l, dS F, Y', strtotime($member['Pin']['expiry'])); ?>
			</dd>

		<?php endif; ?>
	<?php endif; ?>

	<?php if(isset($member['Member']['balance'])): ?>
		<dt>
			Current Balance
		</dt>
		<dd>
			<?php echo $this->Currency->output($member['Member']['balance']); ?>
		</dd>
	<?php endif; ?>

	<?php if(isset($member['Member']['credit_limit'])): ?>
		<dt>
			Credit Limit
		</dt>
		<dd>
			<?php echo $this->Currency->output($member['Member']['credit_limit']); ?>
		</dd>
	<?php endif; ?>

	<?php if(isset($member['Account']['account_id'])): ?>
		<dt>
			Account Ref
		</dt>
		<dd>
			<?php echo $member['Account']['payment_ref']; ?>
		</dd>

	<?php endif; ?>

	<dt>
		Address
	</dt>
	<dd>
		<?php 
			$addressArray = array( $member['Member']['address_1'], $member['Member']['address_2'], $member['Member']['address_city'], $member['Member']['address_postcode'] );

			$addressBlock = "";
			foreach ($addressArray as $item) {
				if( isset($item) &&
					$item != null &&
					strlen(trim($item)) > 0 )
				{
					$addressBlock .= $item . '</br>';
				}
			}

			if(strlen($addressBlock) > 0)
			{
				echo $addressBlock;
			}
			else
			{
				echo "None";
			}
		?>
	</dd>
	<dt>
		Contact No.
	</dt>
	<dd>
		<?php
			if( isset($member['Member']['contact_number']) &&
				$member['Member']['contact_number'] != null &&
				strlen(trim($member['Member']['contact_number'])) > 0 )
			{
				echo $member['Member']['contact_number'];
			}
			else
			{
				echo "None";
			}
		?>
	</dd>
	<dt>
		Mailing Lists
	</dt>
	<dd>
		<?php
			$processedMailingLists = array();
			foreach ($mailingLists as $list) {
				if($list['subscribed'])
				{
					$text = '';
					if($list['canView'])
					{
						$text = $this->Html->link($list['name'], array('controller' => 'mailinglists', 'action' => 'view', $list['id']));
					}
					else
					{
						$text = $list['name'];
					}
					array_push($processedMailingLists, $text);
				}
			}

			echo $this->List->output($processedMailingLists);
		?>
	</dd>


	<?php if(	isset($member['StatusUpdate']) &&
				isset($member['StatusUpdate']['StatusUpdate'])): ?>
		<?php
			$showUpdate = true;
			$statusUpdate = '';
			switch ($member['StatusUpdate']['StatusUpdate']['new_status']) {
				case 2:
					$statusUpdate = "Membership Granted";
					break;

				case 3:
					$statusUpdate = "Membership Revoked";
					break;

				case 7:
					$statusUpdate = "Contact Details Accepted";
					break;
				
				default:
					$showUpdate = false;
					break;
			}
		?>

		<?php if($showUpdate): ?>
			<dt>
				Last Status Update
			</dt>
			<dd>
				<?php echo sprintf('%s by %s on %s', $statusUpdate, $this->Html->link($member['StatusUpdate']['MemberAdmin']['name'], array('controller' => 'members', 'action' => 'view', $member['StatusUpdate']['MemberAdmin']['member_id'])), date('d-M-Y \a\t H:i', strtotime($member['StatusUpdate']['StatusUpdate']['timestamp']))); ?>
			</dd>
		<?php endif; ?>
	<?php endif; ?>
</dl>
