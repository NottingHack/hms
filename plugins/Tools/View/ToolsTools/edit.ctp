<?php
/* Breadcrumbs */
$this->Html->addCrumb('Tools', '/tools/');
$this->Html->addCrumb('Edit', '/tools/edit/' . $this->request->data['Tool']['tool_id']);
?>
<h2>Edit Tool</h2>
<?php
echo $this->Form->create('Tool');
echo $this->Form->input('tool_name', array(
	'label'		=>	'Name',
	));
echo $this->Form->input('tool_restrictions', array(
	'label'		=>	'Restricted?',
	'type'		=>	'select',
	'options'	=>	$restricted,
	));
echo $this->Form->input('tool_pph', array(
	'label'		=>	'Cost (pence per hour)',
	));
echo $this->Form->input('tool_booking_length', array(
	'label'		=>	'Default booking length (minutes)',
	));
echo $this->Form->input('tool_length_max', array(
	'label'		=>	'Maximum booking length (minutes)',
	));
echo $this->Form->input('tool_bookings_max', array(
	'label'		=>	'Maximum number of booking per user',
	));
echo $this->Form->end('Save Tool');

?>