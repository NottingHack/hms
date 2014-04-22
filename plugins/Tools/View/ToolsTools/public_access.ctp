<?php
/* Breadcrumbs */
$this->Html->addCrumb('Tools', '/tools/');
$this->Html->addCrumb('Access Calendar', '/tools/publicAccess/' . $tool['Tool']['tool_id']);
?>
<h2>Access calendar for <?php echo($tool['Tool']['tool_name']); ?></h2>

<p>You can access this calendar by subscribing to it from your Google calendar using the address <strong><?php echo($addresses['email']); ?></strong></p>

<p>Alternatively, you can access the feed using any of the below methods:</p>

<ul>
	<li><a href="<?php echo($addresses['xml']); ?>">XML</a></li>
	<li><a href="<?php echo($addresses['ical']); ?>">iCal</a></li>
	<li><a href="<?php echo($addresses['html']); ?>">HTML</a></li>
</ul>