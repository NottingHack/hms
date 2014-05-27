<?php
/* Breadcrumbs */
$this->Html->addCrumb('Tools', '/tools/');
$this->Html->addCrumb($tool['Tool']['tool_name'], '/tools/view/' . $tool['Tool']['tool_id']);

/* Load our CSS */
$this->Html->css('Tools.view', null, array('inline' => false));

/* Load the Add Booking JS */
// Actually, let's do this as seperate pages for now
//$this->Html->script('Tools.addbooking_ajax', array('inline' => false));
?>
<p>Hello</p>

<div class="toolscalendar" cellspacing="0" cellpadding="0">
	<table>
		<tr>
			<th width="50">&nbsp;</th>
<?php
	$date = clone $monday;
	for ($i = 0; $i < 7; $i++) {
		echo('<th width="127">' . substr($date->format('D'), 0, 1) . ', ' . $date->format('d/m/Y') . '</th>' . "\n");
		$date->add(new DateInterval('P1D'));
	}
?>
		</tr>

<?php
	$date = clone $monday;

	for ($i = 0; $i < 48; $i++) {

		echo('<tr>' . "\n");
		if ($i%2 == 0) {
			echo('<td class="time" rowspan="2">');
			echo('<span>' . $date->format('H:i') . '</span>');
			echo('</td>');
			$class = ' class="light"';
		}
		else {
			$class = '';
		}

		for ($j = 0; $j < 7; $j++) {
			echo('<td' . $class . '>');
			echo($this->Html->link("", array(
				'plugin'		=>	'Tools',
				'controller'	=>	'ToolsTools',
				'action'		=>	'addbooking',
				16,
				'?'				=>	array(
					't'	=> $date->format(ToolsGoogle::DATETIME_STR),
					),
				)));
			//echo('<a href="?t=' . $date->format(ToolsGoogle::DATETIME_STR) . '" class="addbooking"></a>');
			echo('</td>');
			$date->add(new DateInterval('P1D'));
		}
		$date->sub(new DateInterval('P7D'));

		echo('</tr>' . "\n");
		$date->add(new DateInterval('PT30M'));
	}
?>

	</table>
<?php
	
	foreach ($events as $event) {
		// Does this event start last week?
		if ($event['start']->getTimestamp() < $monday->getTimestamp()) {
			$day = 1;
			$length = ($event['end']->format('H') * 60) + $event['end']->format('i');
			echo(getEventDiv($event['title'], $day, $length, '0000'));
		}
		else {
			$day = strtolower($event['start']->format('N'));
			$start_mins = ($event['start']->format('H') * 60) + $event['start']->format('i');
			$length = ($event['end']->getTimestamp() - $event['start']->getTimestamp()) / 60;

			if ($start_mins + $length > 1440) {
				// goes over the end of the day, truncate and add to next day, if not sunday
				$new_length = 1440 - $start_mins;
				if ($day < 7) {
					echo(getEventDiv('', $day+1, $length-$new_length, "0000"));
				}
				$length = $new_length;
			}

			echo(getEventDiv($event['title'], $day, $length, $event['start']->format('Hi')));
		}
	}


?>
</div>
<?php

function getEventDiv($title, $day, $length, $start) {
	$div = '<div class="event booking day_' . $day . ' len_' . $length . ' start_' . $start .'">';
	$div .= $title;
	$div .= '</div>';

	return $div;
}


?>