<!-- File: /app/View/Meta/edit -->

<?php
$this->Html->addCrumb('Meta', '/meta');
$this->Html->addCrumb($meta['name'], '/meta/edit/' . $meta['name']);
?>

<!-- add some text here about keeping a little info on a project-->

<h2><?=$meta['name']?></h2>
<?
echo $this->Form->create('Meta');
echo $this->Form->hidden('name');
echo $this->Form->input('value');
echo $this->Form->end('Save');

?>