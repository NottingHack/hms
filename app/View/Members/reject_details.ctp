<!-- File: /app/View/Member/reject_details.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb('Reject Details', '/members/reject_details/' . $memberInfo['Member']['member_id']);
?>

<?php
	echo $this->Form->create('MemberEmail');
	echo $this->Tinymce->input('Member.message', 
		array( 'label' => 'Reason' ),
		array( 'language'=>'en' ), 
        'basic' 
    ); 
    echo $this->Form->end('Send');
?>
