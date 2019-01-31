<!-- File: /app/View/Member/setupLogin.ctp -->

<p>
	Please enter your name and a username and password that you'll use to login to HMS.
<p>

<?php echo $this->Form->create('Member'); ?>
    <?php
    	echo $this->Form->input('member_id', array('type' => 'hidden'));
        echo $this->Form->input('firstname', array('label' => 'First name'));
        echo $this->Form->input('surname', array('label' => 'Surname'));
        echo $this->Form->input('username', array('label' => 'Username, this can not be changed later'));
        echo $this->Form->input('email', array('label' => 'Email (the same one you used when you registered):'));
        echo $this->Form->input('password', array('label' => 'Password (Min ' . Member::MIN_PASSWORD_LENGTH . ' chars):'));
        echo $this->Form->input('password_confirm', array('type' => 'password'));
    ?>
<?php echo $this->Form->end(__('Create')); ?>