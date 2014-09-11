<!-- File: /app/View/Member/reject_details.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb($name, '/members/view/' . $id);
	$this->Html->addCrumb('Reject Details');
?>

<?php
	echo $this->Form->create('MemberEmail', array('novalidate' => true));
	echo $this->Form->input('MemberEmail.subject', array('default' => 'Issue With Contact Information')); //
	echo $this->Form->input('MemberEmail.message', array('type' => 'textarea'));
	echo $this->TinyMCE->editor('basic');
	echo $this->Form->end('Send');
?>
