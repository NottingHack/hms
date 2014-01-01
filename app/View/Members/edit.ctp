<!-- File: /app/View/Member/edit.ctp -->

<?php
	$name = array_key_exists('username', $member) ? $member['username'] : $member['email'];
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb("Edit $name", '/members/view/' . $member['id']);
?>

<?php
	echo $this->Form->create('Member');
	echo $this->Form->hidden('member_id');
	if(isset($member['username']))
	{
		echo $this->Form->input('username');
	}

	if(isset($member['firstname']))
	{
		echo $this->Form->input('firstname');
	}

	if(isset($member['surname']))
	{
		echo $this->Form->input('surname');
	}

	echo $this->Form->input('email');

	if(isset($member['unlockText']))
	{
		echo $this->Form->input('unlock_text');
	}

	if(array_key_exists('address', $member))
	{

		echo $this->Form->input('address_1', array( 'label' => 'Address part 1 (House name/number and street)' ) );
		echo $this->Form->input('address_2', array( 'label' => 'Address part 2' ) );
		echo $this->Form->input('address_city', array( 'label' => 'City' ) );
		echo $this->Form->input('address_postcode', array( 'label' => 'Postcode' ) );

	}

	if(array_key_exists('contactNumber', $member))
	{
		echo $this->Form->input('contact_number' );
	}


	if( isset($member['paymentRef']) )
	{
		echo $this->Form->input('account_id', array( 
				'options' => $accounts,
				'label' => 'Account',
			) 
		);
	}

	// Pin details
	if( isset($member['pin']) )
	{

		echo '<fieldset>';
		echo '<legend>Pins</legend>';

		for($i = 0; $i < count($member['pin']); $i++)
		{
			echo $this->Form->input("Pin.$i.pin", array( 'readonly' => 'readonly' ));	
			//echo $this->Form->input('Pin.expiry', array('type'=>'date', 'empty' => true, 'minYear' => date("Y"), 'orderYear' => 'asc', 'dateFormat' => 'DMY'));
		}
	}

	if( isset($member['groups']) )
	{

		echo '<fieldset>';
		echo '<legend>Groups</legend>';

		echo $this->Form->input('Group',array(
	            'label' => __(' ',true),
	            'type' => 'select',
	            'multiple' => 'checkbox',
	            'options' => $groups,
	            'selected' => $this->Html->value('Group.Group'),
	        )); 

		echo '</fieldset>';
	}

	echo '<fieldset>';
	echo '<legend>Mailing Lists</legend>';

	echo $this->Mailinglist->output($mailingLists);

	echo '</fieldset>';

	echo $this->Form->end('Update');
?>