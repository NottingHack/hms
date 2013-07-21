<!-- File: /app/View/EmailRecords/view.ctp -->

<?php
	
	$name = $memberNames[$id];

    $this->Html->addCrumb('EmailRecords', '/emailrecords');
    $this->Html->addCrumb($name, '/emailrecords/view/' . $id);
?>

<h3>All E-mail Records for <?php echo $name; ?></h3>
<p><?php echo $this->Html->link('View member profile', array('controller' => 'members', 'action' => 'view', $id)); ?></p>

<?php
    echo $this->element('email_records');
?>