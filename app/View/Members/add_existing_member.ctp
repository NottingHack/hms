<!-- File: /app/View/Member/add_existing_member.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb('Add Existing Member ' . $this->data['Member']['name'], '/members/add_existing_member/' . $this->data['Member']['member_id']);
?>

<?
	echo $this->Form->create('Member');
	echo $this->Form->hidden('member_id');
	echo $this->Form->input('username');
	echo $this->Form->input('handle');
	echo $this->Form->input('name');
	echo $this->Form->input('email');
	echo $this->Form->input('unlock_text');

	if( isset($this->data['Member']['member_status']) )
	{
		echo $this->Form->input('member_status', array( 
				'options' => $statuses, 
				'type' => 'select',
				'selected' => $this->Html->value('Status.Status'),
			) 
		);
	}

	echo $this->Form->input('address_1', array( 'label' => 'Address part 1 (House name/number and street)' ) );
	echo $this->Form->input('address_2', array( 'label' => 'Address part 2' ) );
	echo $this->Form->input('address_city', array( 'label' => 'City' ) );
	echo $this->Form->input('address_postcode', array( 'label' => 'Postcode' ) );

	echo $this->Form->input('contact_number' );

	echo $this->Form->Input('Account.payment_ref');

	echo $this->Form->end('Update');

	echo $this->Html->link('Next', array('controller' => 'members', 'action' => 'add_existing_member', ($memberInfo['Member']['member_id'] + 1)));
?>