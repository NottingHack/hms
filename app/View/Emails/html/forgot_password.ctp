<p>You are receiving this e-mail because someone started the 'forgotten password' procedure on the Nottingham Hackspace HMS, if this was not you, please e-mail <?php echo $this->html->link('membership@nottinghack.org.uk', 'mailto:membership@nottinghack.org.uk;'); ?>.</p>

<p>To reset the password on your account, click the following link and enter your e-mail address: <?php echo $this->Html->link('Reset Password', array('controller' => 'members', 'action' => 'forgotPassword', $id, 'full_base' => true)); ?> </p>
