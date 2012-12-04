<!-- File: /app/View/Member/forgot_password.ctp -->

<?php
    $this->Html->addCrumb('Forgot Password', '/members/forgot_password');
?>

<?php if(isset($guid) && $guid != null): ?>

    <p>Please confirm your e-mail address and enter your new password below</p>
    <?php
        echo $this->Form->create('ForgotPassword');
        echo $this->Form->input('ForgotPassword.email', array( 'label' => 'Email:'));        
        echo $this->Form->input('ForgotPassword.new_password', array( 'label' => 'New password (Min ' . Member::MIN_PASSWORD_LENGTH . ' chars):', 'type' => 'password' ));
        echo $this->Form->input('ForgotPassword.new_password_confirm', array( 'label' => 'Confirm new password', 'type' => 'password' ));

        echo $this->Form->end('Change password');
    ?>

<?php else: ?>

    <p>Please enter the your e-mail address below, and link to reset your password will be sent there.</p>

    <?php
        echo $this->Form->create('ForgotPassword');
        echo $this->Form->input('ForgotPassword.email', array( 'label' => 'Email:'));
        echo $this->Form->end('Submit');
    ?>

<?php endif; ?>