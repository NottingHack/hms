<!-- File: /app/View/Member/setup_login.ctp -->

<p>
	Please choose a username and password that you'll use to login to HMS
<p>

<?php echo $this->Form->create('Member'); ?>
    <?php
    	echo $this->Form->input('member_id', array('type' => 'hidden'));
        echo $this->Form->input('name');
        echo $this->Form->input('username');
        echo $this->Form->input('email', array('label' => 'Email (the same one you used when you registered):'));
        echo $this->Form->input('password', array('label' => 'Password (Min ' . Member::MIN_PASSWORD_LENGTH . ' chars):'));
        echo $this->Form->input('password_confirm', array('type' => 'password'));
    ?>
<?php echo $this->Form->end(__('Create')); ?>