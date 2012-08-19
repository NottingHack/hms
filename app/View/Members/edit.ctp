<!-- File: /app/View/Member/edit.ctp -->

<h1>Edit Member</h1>
<?php
	echo $this->Form->create('Member');
	echo $this->Form->hidden('id');
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

	echo $this->Form->end('Update Member');
?>