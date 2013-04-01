<!-- File: /app/View/Member/reject_details.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb('Reject Details', '/members/reject_details/');
?>

<?php
	echo $this->Form->create('MemberEmail');
	echo $this->Tinymce->input('MemberEmail.message', 
		array( 'label' => 'Reason' ),
		array( 'language'=>'en' ), 
        'basic' 
    );
    echo $this->Form->end('Send');
?>
