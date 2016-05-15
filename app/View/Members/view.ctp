<!-- File: /app/View/Member/view.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb(isset($member['username']) ? $member['username'] : $member['email'], '/members/view/' . $member['id']);
    $this->Html->script('listmembers', array('inline' => false));
?>


<dl>
	<?php if(isset($member['username'])): ?>
	<dt>
		Username
	</dt>
	<dd>
		<?php echo $member['username']; ?>
	</dd>
	<?php endif; ?>
	
	<?php if(isset($member['firstname'])): ?>
	<dt>
		First Name
	</dt>
	<dd>
		<?php echo $member['firstname']; ?>
	</dd>
	<?php endif; ?>

	<?php if(isset($member['surname'])): ?>
	<dt>
		Surname
	</dt>
	<dd>
		<?php echo $member['surname']; ?>
	</dd>
	<?php endif; ?>

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

	        	echo CakeText::toList($groupsList);
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
			<?php 
				foreach($member['pin'] as $pin)
				{
					echo $pin['pin'] . '<br/>';	
				}
			?>
		</dd>	
	<?php endif; ?>

    <?php if( isset($member['rfidtag']) ): ?>
	    <dt>
	    	Registered Cards
	    </dt>
	    <dd>
	    	<?php
	    	    	  
	    	  $msg = ((count($member['rfidtag']) == 1) ? 'card' : 'cards');

	    	  echo $this->Html->link(
	    	  	count($member['rfidtag']) . ' '.  $msg . ' registered', 
	    	  	array(
	    	  		'controller' => 'rfidTags',
	    	  		'action' => 'view',
	    	  		$member['id']
	    	  	),
	    	  	array('escape' => false)
	    	  );
	    	?>
	    </dd>
	<?php endif; ?>

	<?php if(isset($member['balance'])): ?>
		<dt>
			Current Balance
		</dt>
		<dd>
			<?php 
			echo $this->Html->link($this->Currency->output($member['balance']), array('controller' => 'snackspace', 'action' => 'history', $member['id']), array('escape' => false));

			
			?>
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
            <?php echo $this->Html->link($member['paymentRef'], array('controller' => 'bankTransactions', 'action' => 'history', $member['id']), array('escape' => false)); ?>
		</dd>

	<?php endif; ?>

	<?php
		if(array_key_exists('address', $member)):
	?>
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
	<?php
		endif;
	?>

	<?php
		if(array_key_exists('contactNumber', $member)):
	?>
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
	<?php
		endif;
	?>
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
					if ($member['lastStatusUpdate']['from'] == 3) {
						$statusUpdate = "Contact Details Rejected";
					}
					else {
						$showUpdate = false;
					}
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

	<?php
		if(array_key_exists('lastEmail', $member)):
	?>
	<dt>
		Last Email
	</dt>
	<dd>
		<?php
			if( isset($member['lastEmail']) &&
				$member['lastEmail'] != null )
			{
				echo sprintf('%s on %s', $member['lastEmail']['subject'], date('d-M-Y \a\t H:i', strtotime($member['lastEmail']['timestamp'])));
			}
			else
			{
				echo "None";
			}
		?>
	</dd>
	<?php
		endif;
	?>

</dl>

<div id="dialog-confirm" title="Are you sure?">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Approve this member?</p>
</div>
