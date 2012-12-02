<p>
	We have some contact details for <?php echo $email; ?>. Please <?php echo $this->Html->link('check them', array( 'controller' => 'members', 'action' => 'view', $id, 'full_base' => true )); ?>.
</p>