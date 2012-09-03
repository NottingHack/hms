<!-- File: /app/View/Member/edit.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb('Edit ' . $this->data['Member']['name'], '/members/edit/' . $this->data['Member']['member_id']);
?>

<?
	print_r($statuses);
	print_r($members);
	print_r($this->Html->value('Member.Member'));
	echo $this->Form->create('Member');
	echo $this->Form->hidden('member_id');
	echo $this->Form->input('name');
	echo $this->Form->input('email');
	echo $this->Form->input('handle');
	echo $this->Form->input('unlock_text');
	echo $this->Form->input('member_status', array( 
			'options' => $statuses, 
			'type' => 'select',
			'selected' => $this->Html->value('Status.Status'),
		) 
	);

	echo $this->Form->input('parent_member_id', array( 
			'options' => $members,
		) 
	);

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

	# Pin details
	echo $this->Form->input('Pin.pin', array( 'readonly' => 'readonly' ));

	echo $this->Form->end('Update Member');
?>