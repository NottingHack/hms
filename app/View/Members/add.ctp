<!-- File: /app/View/Member/add.ctp -->

<?php
    $this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb('Add Member', '/members/add');
?>

<?php
	echo $this->Form->create('Member');
	echo $this->Form->input('name');
	echo $this->Form->input('email');
	echo $this->Form->input('handle');

	echo $this->Form->input('Other.guide');

	echo $this->Form->input('account_id', array( 
			'options' => $accounts,
			'label' => 'Account'
		) 
	);

	# Pin details
	echo $this->Form->hidden('Pin.pin');
	echo $this->Form->input('Pin.expiry', array('type'=>'date', 'empty' => true, 'minYear' => date("Y"), 'orderYear' => 'asc', 'dateFormat' => 'DMY'));

	echo $this->Form->end('Add Member');
?>