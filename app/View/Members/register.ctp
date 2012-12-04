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

	# Need to alter the list of mailing lists
	$formattedMailingLists = array();
	$selectedMailingLists = array();
	$mailingListOptions = array();
	foreach ($mailingLists as $mailingList) {
		$key = $mailingList['id'];
		$formattedMailingLists[$key] = $mailingList;
		if(	isset($mailingList['subscribed']) &&
			$mailingList['subscribed'] )
		{
			array_push($selectedMailingLists, $key);	
		}
		$mailingListOptions[$key] = $mailingList['name'];
	}

	echo $this->Form->input('MailingLists.MailingLists',array(
        'label' => __(' ',true),
        'type' => 'select',
        'multiple' => 'checkbox',
        'options' => $mailingListOptions,
        'selected' => $selectedMailingLists,
    )); 

	echo '</fieldset>';

	echo $this->Form->end('Register');
?>