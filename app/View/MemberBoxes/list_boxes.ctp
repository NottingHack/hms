<!-- File: /app/View/MemberBoxess/list_boxes.ctp -->

<?php
$this->Html->addCrumb('Members', '/members');
$this->Html->addCrumb(isset($member['username']) ? $member['username'] : $member['email'], '/members/view/' . $member['id']);
$this->Html->addCrumb('Boxes', '/memberBoxes/listboxes/' . $member['id']);
?>

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


