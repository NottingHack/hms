<!-- File: /app/View/Member/change_password.ctp -->

<?php
    $this->Html->addCrumb('Members', '/members');
    $this->Html->addCrumb('Change password for ' . $memberInfo['Member']['name'], '/members/change_password/' . $memberInfo['Member']['member_id']);
?>

<?php
    echo $this->Form->create('Member');
    echo $this->Form->hidden('member_id');
    echo $this->Form->input('Other.current_password', array( 'label' => 'Current password', 'type' => 'password' ));
    echo $this->Form->input('Other.new_password', array( 'label' => 'New password', 'type' => 'password' ));
    echo $this->Form->input('Other.new_password_confirm', array( 'label' => 'Confirm new password', 'type' => 'password' ));

    echo $this->Form->end('Change password');
?>