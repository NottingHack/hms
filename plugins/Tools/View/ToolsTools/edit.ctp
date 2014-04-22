<h2>Edit Tool</h2>
<?php
echo $this->Form->create('Tool');
echo $this->Form->input('tool_name', array(
	'label'		=>	'Name',
	));
echo $this->Form->input('tool_restrictions', array(
	'label'		=>	'Restricted?',
	'type'		=>	'select',
	'options'	=>	array('UNRESTRICTED' => 'Unrestricted', 'RESTRICTED' => 'Restricted'),
	));
echo $this->Form->input('tool_pph', array(
	'label'		=>	'Cost (pence per hour)',
	));
echo $this->Form->input('tool_address', array(
	'label'		=>	'Arduino address',
	));
echo $this->Form->end('Save Tool');

?>