<?php
/* Breadcrumbs */
$this->Html->addCrumb('Tools', '/tools/');
$this->Html->addCrumb($tool['Tool']['tool_name'], '/tools/view/' . $tool['Tool']['tool_id']);
$this->Html->addCrumb('Add Booking', '#');

/* Load our CSS */
$this->Html->css('Tools.addbooking', null, array('inline' => false));

/* Load the Add Booking JS */
$this->Html->script('Tools.addbooking', array('inline' => false));

/* The chosen start date */
$start_date = new DateTime($this->request->query['t'], new DateTimeZone('Europe/London'));
$end_date = clone $start_date;
$end_date->add(new DateInterval('PT1H'));
?>

<h2>Add <?php echo($tool['Tool']['tool_name']); ?> Booking</h2>

<div id="booking">
<?php
	
	echo $this->Form->create('Tool');

	echo $this->Form->input('start_date', array(
		'label'		=>	'Start Date',
		'class'		=>	'datepicker',
		'value'		=>	$start_date->format('d/m/Y'),
	));

	echo('<div class="input select">');
	echo $this->Form->input('start_hours', array(
		'label'		=>	'Start Time',
		'div'		=>	false,
		'type'		=>	'select',
		'options'	=>	array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'),
		'selected'	=>	$start_date->format('H'),
	));
	echo('&nbsp;');
	echo $this->Form->input('start_mins', array(
		'label'		=>	false,
		'div'		=>	false,
		'type'		=>	'select',
		'options'	=>	array('0'=>'00','15'=>'15','30'=>'30','45'=>'45'),
		'selected'	=>	$start_date->format('i'),
	));
	echo('</div>');

	echo $this->Form->input('end_date', array(
		'label'		=>	'End Date',
		'class'		=>	'datepicker',
		'value'		=>	$end_date->format('d/m/Y'),
	));

	echo('<div class="input select">');
	echo $this->Form->input('end_hours', array(
		'label'		=>	'End Time',
		'div'		=>	false,
		'type'		=>	'select',
		'options'	=>	array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'),
		'selected'	=>	$end_date->format('H'),
	));
	echo('&nbsp;');
	echo $this->Form->input('end_mins', array(
		'label'		=>	false,
		'div'		=>	false,
		'type'		=>	'select',
		'options'	=>	array('0'=>'00','15'=>'15','30'=>'30','45'=>'45'),
		'selected'	=>	$end_date->format('i'),
	));
	echo('</div>');

	if (count($type_options) > 1) {
		echo $this->Form->input('booking_type', array(
			'label'		=>	'Type',
			'type'		=>	'select',
			'options'	=>	$type_options,
		));
	}
	else {
		$type = array_keys($type_options)[0];
		echo $this->Form->hidden('booking_type', array('value'=>$type));
	}
?>
</div>

<div id="dayview">
	
</div>