<!-- File: /app/View/Member/change_password.ctp -->

<?php
    $this->Html->addCrumb('Members', '/members');
    $this->Html->addCrumb('Change password for ' . $memberInfo['Member']['name'], '/members/change_password/' . $memberInfo['Member']['member_id']);
?>

<?php
    echo $this->Form->create('Member');
    echo $this->Form->hidden('member_id');
    $label = "Current password";
    # If a member admin is editing this profile, we need to show a different label
    # Unless the member admin is editing their own profile
    if( $memberEditingOwnProfile == false &&
        $memberIsMemberAdmin == true)
    {
        $label = 'Admin password';
    }
    echo $this->Form->input('Other.current_password', array( 'label' => $label, 'type' => 'password' ));
    
    echo $this->Form->input('Other.new_password', array( 'label' => 'New password', 'type' => 'password' ));
    echo $this->Form->input('Other.new_password_confirm', array( 'label' => 'Confirm new password', 'type' => 'password' ));

    echo $this->Form->end('Change password');
?>