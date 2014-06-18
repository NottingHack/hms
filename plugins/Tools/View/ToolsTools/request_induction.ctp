<?php
/* Breadcrumbs */
$this->Html->addCrumb('Tools', '/tools/');
$this->Html->addCrumb($tool['Tool']['tool_name'], '/tools/view/' . $tool['Tool']['tool_id']);
$this->Html->addCrumb('Request Induction', '#');



?>