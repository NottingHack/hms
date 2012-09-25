<!-- File: /app/View/Member/view.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb($member['Member']['name'], '/members/view/' . $member['Member']['member_id']);
?>


<dl>
	<dt>
		Email
	</dt>
	<dd>
		<?php echo $member['Member']['email']; ?>
	</dd>

	<dt>
		Member Since
	</dt>
	<dd>
		<?php echo $member['Member']['join_date']; ?>
	</dd>

	<dt>
		Handle
	</dt>
	<dd>
		<?php echo $member['Member']['handle']; ?>
	</dd>

	<dt>
		Unlock Text
	</dt>
	<dd>
		<?php echo $member['Member']['unlock_text']; ?>
	</dd>

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
	<dt>
		Status
	</dt>
	<dd>
		<?php echo $this->Html->link($member['Status']['title'], array('controller' => 'members', 'action' => 'list_members_with_status', $member['Status']['status_id'])); ?>
	</dd>
	<?php if( isset($member['Pin']) ): ?>
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

	<dt>
		Current Balance
	</dt>
	<dd>
		<?php echo $member['Member']['balance']; ?>
	</dd>
	<dt>
		Credit Limit
	</dt>
	<dd>
		<?php echo $member['Member']['credit_limit']; ?>
	</dd>
	<dt>
		Account Ref
	</dt>
	<dd>
		<?php echo $member['Account']['payment_ref']; ?>
	</dd>
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
			foreach ($subscribedMailingLists as $list) {
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

			echo $this->List->output($processedMailingLists);
		?>
	</dd>
</dl>
