<!-- File: /app/View/MemberProjects/edit -->

<?php
$this->Html->addCrumb('Members', '/members');
$this->Html->addCrumb(isset($member['username']) ? $member['username'] : $member['email'], '/members/view/' . $member['id']);
$this->Html->addCrumb('Projects', '/memberProjects/listprojects/' . $member['id']);
$this->Html->addCrumb($project['projectName'], '/memberProjects/view/' . $project['memberProjectId']);
?>

<!-- add some text here about keeping a little info on a project-->

<?
echo $this->Form->create('MemberProject');
echo $this->Form->hidden('member_project_id');
echo $this->Form->input('project_name');
echo $this->Form->input('description');
echo $this->Form->end('Save');

?>