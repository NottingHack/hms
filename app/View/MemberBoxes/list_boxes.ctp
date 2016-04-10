<!-- File: /app/View/MemberBoxess/list_boxes.ctp -->

<?php
$this->Html->addCrumb('Members', '/members');
$this->Html->addCrumb(isset($member['username']) ? $member['username'] : $member['email'], '/members/view/' . $member['id']);
$this->Html->addCrumb('Boxes', '/memberBoxes/listboxes/' . $member['id']);
?>

<p>All members are entitled to a member's box in the storage room, but we have limited space.</p>

<p>If there is an empty box available (please check in real life first!) you can purchase it using the "Buy new box" button below. This will debit your snackspace account by £5 and assign a box to you. The system will also check that there is space available for your box.</p>

<p><strong>Note:</strong> The system does not know if there is an actual empty box available for you, and will debit you either way - please check first!</p>

<p>Once you have bought a box, please print a label (using the link below) and place it on the front of your box so it can be identified.</p>

<br />

<table>
<tr>
<th>Box Id</th>
<th>Brought Date</th>
<th>Removed Date</th>
<th>State</th>
<th>Actions</th>
</tr>
<?php
foreach ($boxesList as $box) {
echo '<tr>';
echo '<td>' . $box['memberBoxId'] . '</td>';
echo '<td>' . $box['broughtDate'] . '</td>';
echo '<td>' . $box['removedDate'] . '</td>';
echo '<td>' . $box['stateName'] . '</td>';
echo '<td>';
foreach ($box['actions'] as $action) {
    $linkOptions = $action['params'];
    $linkOptions['controller'] = $action['controller'];
    $linkOptions['action'] = $action['action'];

    $options = null;
    if (isset($action['class']))
    {
        $options = array('class' => $action['class']);
    }

    echo $this->Html->link($action['title'], $linkOptions, $options);
    echo "<br>";
}
echo '</td>';
echo '</tr>';
}
?>
</tr>
</table>

<div class="paginate">
<?php echo $this->Paginator->numbers(); ?>
</div>


