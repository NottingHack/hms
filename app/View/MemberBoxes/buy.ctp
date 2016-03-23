<!-- File: /app/View/MemberBoxes/buy -->

<?php
$this->Html->addCrumb('Members', '/members');
$this->Html->addCrumb(isset($member['username']) ? $member['username'] : $member['email'], '/members/view/' . $member['id']);
$this->Html->addCrumb('Boxes', '/memberBoxes/listboxes/' . $member['id']);
?>

<!-- add some text here about needing a little info to start a project-->

<?
echo $this->Form->create('MemberBox');
echo $this->Form->hidden('member_id');
echo $this->Form->end('Buy');

?>