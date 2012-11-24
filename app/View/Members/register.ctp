<!-- File: /app/View/Member/add.ctp -->

<?php
    $this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb('Register Member', '/members/register');
?>

<?php
	echo $this->Form->create('Member');
	echo $this->Form->input('email');

	echo '<fieldset>';
	echo '<legend>Mailing Lists</legend>';
	echo 'Please tick the boxes the mailing list(s) you\'d like to subscribe to.';

	echo $this->Form->input('MailingLists.MailingLists',array(
        'label' => __(' ',true),
        'type' => 'select',
        'multiple' => 'checkbox',
        'options' => Hash::extract($mailingLists, '{n}.name'),
    )); 

	echo '</fieldset>';

	echo $this->Form->end('Register');
?>