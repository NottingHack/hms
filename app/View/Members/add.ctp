<!-- File: /app/View/Member/add.ctp -->

<h1>Add Member</h1>
<?php
	echo $this->Form->create('Member');
	echo $this->Form->input('name');
	echo $this->Form->input('email');
	echo $this->Form->input('handle');
	echo $this->Form->end('Add Member');
?>