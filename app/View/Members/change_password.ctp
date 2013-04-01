<!-- File: /app/View/Member/change_password.ctp -->

<?php
    $this->Html->addCrumb('Members', '/members');
    $this->Html->addCrumb('Change password for ' . $name, '/members/changePassword/' . $id);
?>

<?php
    echo $this->Form->create('ChangePassword');
    echo $this->Form->hidden('Member.member_id');
    $label = "Current password";
    if( !$ownAccount )
    {
        $label = 'Admin password';
    }
    echo $this->Form->input('ChangePassword.current_password', array( 'label' => $label, 'type' => 'password' ));
    
    echo $this->Form->input('ChangePassword.new_password', array( 'label' => 'New password (Min ' . Member::MIN_PASSWORD_LENGTH . ' chars):', 'type' => 'password' ));
    echo $this->Form->input('ChangePassword.new_password_confirm', array( 'label' => 'Confirm new password', 'type' => 'password' ));

    echo $this->Form->end('Change password');
?>