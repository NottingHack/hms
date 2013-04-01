<!-- File: /app/View/Member/accept_details.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb('Accept Details', '/members/acceptDetails/');
?>

<p>
	Please choose the account that <?php echo $name; ?> will be paying from, default is to create a new account.
</p>

<?php
	echo $this->Form->create('Member');
	echo $this->Form->hidden('member_id');

	echo $this->Form->input('Account.account_id', array( 
				'options' => $accounts,
				'label' => 'Account [ Payment Ref ]',
			)
	);

	echo $this->Form->end('Update');
?>