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
if (isset($this->request->query['t'])) {
	$start_date = new DateTime($this->request->query['t'], new DateTimeZone('Europe/London'));
	$end_date = clone $start_date;
	$end_date->add(new DateInterval('PT' . $tool['Tool']['tool_booking_length'] . 'M'));
}
?>

<h2>Add <?php echo($tool['Tool']['tool_name']); ?> Booking</h2>

<div id="booking">
<?php
	$options = array(
		'url' => array(
			'plugin'		=>	'Tools',
			'controller'	=>	'ToolsTools',
			'action'		=>	'addbooking',
			$this->request->params['pass'][0],
			)
		);
	echo $this->Form->create('Tool', $options);

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
		'options'	=>	array('00'=>'00','01'=>'01','02'=>'02','03'=>'03','04'=>'04','05'=>'05','06'=>'06','07'=>'07','08'=>'08','09'=>'09','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23'),
		'selected'	=>	$start_date->format('H'),
	));
	echo('&nbsp;');
	echo $this->Form->input('start_mins', array(
		'label'		=>	false,
		'div'		=>	false,
		'type'		=>	'select',
		'options'	=>	array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'),
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
		'options'	=>	array('00'=>'00','01'=>'01','02'=>'02','03'=>'03','04'=>'04','05'=>'05','06'=>'06','07'=>'07','08'=>'08','09'=>'09','10'=>'10','11'=>'11','12'=>'12','13'=>'13','14'=>'14','15'=>'15','16'=>'16','17'=>'17','18'=>'18','19'=>'19','20'=>'20','21'=>'21','22'=>'22','23'=>'23'),
		'selected'	=>	$end_date->format('H'),
	));
	echo('&nbsp;');
	echo $this->Form->input('end_mins', array(
		'label'		=>	false,
		'div'		=>	false,
		'type'		=>	'select',
		'options'	=>	array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'),
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

	echo $this->Form->end('Add Booking');
?>
</div>

<div id="dayview">
	<p>A preview will appear here</p>
</div>