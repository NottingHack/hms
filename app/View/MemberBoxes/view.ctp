<!-- File: /app/View/MemberBoxe/view.ctp -->

<?php
$this->Html->addCrumb('Members', '/members');
$this->Html->addCrumb(isset($member['username']) ? $member['username'] : $member['email'], '/members/view/' . $member['id']);
$this->Html->addCrumb('Boxes', '/memberBoxes/listboxes/' . $member['id']);
$this->Html->addCrumb($box['memberBoxId'], '/memberBoxes/view/' . $box['memberBoxId']);
?>

<dl>
<dt>
Box Id
</dt>
<dd>
<?php echo $box['memberBoxId']; ?>
</dd>
<dt>
Brought Date
</dt>
<dd>
<?php echo $box['broughtDate']; ?>
</dd>
<?php if( isset($box['removedDate']) ): ?>
<dt>
emoved Date
</dt>
<dd>
<?php echo $box['removedDate']; ?>
</dd>
<?php endif; ?>
<dt>
Status
</dt>
<dd>
<?php echo $box['stateName']; ?>
</dd>
</dl>