<!-- File: /app/View/Member/login.ctp -->

<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo __('Please enter your username and password'); ?></legend>
    <?php
        echo $this->Form->input('username', array('label'=>'Username or Email'));
        echo $this->Form->input('password');
    ?>
    <?php echo $this->Html->link('Forgot Password?', array('controller' => 'members', 'action' => 'forgotPassword')); ?>
    </fieldset>
<?php echo $this->Form->end(__('Login')); ?>