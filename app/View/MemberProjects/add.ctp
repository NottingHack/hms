<!-- File: /app/View/MemberProjects/add -->

<?php
$this->Html->addCrumb('Members', '/members');
$this->Html->addCrumb(isset($member['username']) ? $member['username'] : $member['email'], '/members/view/' . $member['id']);
$this->Html->addCrumb('Projects', '/memberProjects/listprojects/' . $member['id']);
?>

<!-- add some text here about needing a little info to start a project-->

<?
echo $this->Form->create('MemberProject');
echo $this->Form->input('project_name');
echo $this->Form->input('description');
echo $this->Form->end('Add');

?>