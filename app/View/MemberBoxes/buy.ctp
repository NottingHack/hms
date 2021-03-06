<!-- File: /app/View/MemberBoxes/buy -->

<?php
$this->Html->addCrumb('Members', '/members');
$this->Html->addCrumb(isset($member['username']) ? $member['username'] : $member['email'], '/members/view/' . $member['id']);
$this->Html->addCrumb('Boxes', '/memberBoxes/listboxes/' . $member['id']);
?>

<?php
if ($canBuyBox) {
?>
By clicking the Buy button you agreee to debit your account by <?php echo $this->Currency->output($boxCost);?><br/>
<?php
echo $this->Form->create('MemberBox');
echo $this->Form->end('Buy');
} else {
?>
Sorry you can not have a box at this time.
<?php
}
?>