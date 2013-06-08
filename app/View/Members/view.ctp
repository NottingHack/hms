<!-- File: /app/View/Member/view.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb($member['username'], '/members/view/' . $member['id']);
?>


<dl>
	<dt>
		Name
	</dt>
	<dd>
		<?php echo $member['name']; ?>
	</dd>
	<dt>
		Username
	</dt>
	<dd>
		<?php echo $member['username']; ?>
	</dd>

	<dt>
		Email
	</dt>
	<dd>
		<?php echo $member['email']; ?>
	</dd>

	<?php 
		if(isset($member['joinDate'])):
	?>

		<dt>
			Member Since
		</dt>
		<dd>
			<?php echo $member['joinDate']; ?>
		</dd>
	<?php endif; ?>

	<?php if(isset($member['unlockText'])): ?>
		<dt>
			Unlock Text
		</dt>
		<dd>
			<?php echo $member['unlockText']; ?>
		</dd>
	<?php endif; ?>

	<?php if(isset($member['groups'])): ?>
		<dt>
			Groups
		</dt>
		<dd>
			<?php

			$numGroups = count($member['groups']);

	        if($numGroups === 0)
	        {
	            echo 'None';
	        }
	        else
	        {
	        	$groupsList = array();
	        	foreach ($member['groups'] as $group) 
	        	{
	        		array_push($groupsList, $this->Html->link($group['description'], array('controller' => 'groups', 'action' => 'view', $group['id'])));
	        	}

	        	echo String::toList($groupsList);
	        }
	       ?>
		</dd>
	<?php endif; ?>

	<?php if(isset($member['status'])): ?>
		<dt>
			Status
		</dt>
		<dd>
			<?php echo $this->Html->link($member['status']['name'], array('controller' => 'members', 'action' => 'listMembersWithStatus', $member['status']['id'])); ?>
		</dd>

	<?php endif; ?>

	<?php if( isset($member['pin']) ): ?>
		<dt>
			Pin
		</dt>
		<dd>
			<?php echo $member['pin']; ?>
		</dd>
	<?php endif; ?>

	<?php if(isset($member['balance'])): ?>
		<dt>
			Current Balance
		</dt>
		<dd>
			<?php echo $this->Currency->output($member['balance']); ?>
		</dd>
	<?php endif; ?>

	<?php if(isset($member['creditLimit'])): ?>
		<dt>
			Credit Limit
		</dt>
		<dd>
			<?php echo $this->Currency->output($member['creditLimit']); ?>
		</dd>
	<?php endif; ?>

	<?php if(isset($member['paymentRef'])): ?>
		<dt>
			Account Ref
		</dt>
		<dd>
			<?php echo $member['paymentRef']; ?>
		</dd>

	<?php endif; ?>

	<dt>
		Address
	</dt>
	<dd>
		<?php 

			$addressArray = array();
			if(isset($member['address']))
			{
				$addressArray = $member['address'];
			}

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
			if( isset($member['contactNumber']) &&
				$member['contactNumber'] != null &&
				strlen(trim($member['contactNumber'])) > 0 )
			{
				echo $member['contactNumber'];
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
			echo $this->Mailinglist->outputList($mailingLists);
		?>
	</dd>


	<?php if(isset($member['lastStatusUpdate'])): ?>
		<?php
			$showUpdate = true;
			$statusUpdate = '';
			switch ($member['lastStatusUpdate']['to']) {
				case 5:
					$statusUpdate = "Membership Granted";
					break;

				case 6:
					$statusUpdate = "Membership Revoked";
					break;

				case 2:
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
				<?php echo sprintf('%s by %s on %s', $statusUpdate, $this->Html->link($member['lastStatusUpdate']['by_username'], array('controller' => 'members', 'action' => 'view', $member['lastStatusUpdate']['by'])), date('d-M-Y \a\t H:i', strtotime($member['lastStatusUpdate']['at']))); ?>
			</dd>
		<?php endif; ?>
	<?php endif; ?>
</dl>
