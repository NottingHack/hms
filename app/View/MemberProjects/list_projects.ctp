<!-- File: /app/View/MemberProjects/list_projects.ctp -->

<?php
$this->Html->addCrumb('Members', '/members');
$this->Html->addCrumb(isset($member['username']) ? $member['username'] : $member['email'], '/members/view/' . $member['id']);
$this->Html->addCrumb('Projects', '/memberprojects/listprojects/' . $member['id']);
?>

<table>
<tr>
<th>Project Name</th>
<th>Description</th>
<th>Start Date</th>
<th>Complete Date</th>
<th>State</th>
<th>Actions</th>
</tr>
<?php
foreach ($projectsList as $project) {
echo '<tr>';
echo '<td>' . $project['projectName'] . '</td>';
echo '<td>';
if(strlen($project['description']) > $shortDescriptionLength) {
    echo substr($project['description'], 0, $shortDescriptionLength) . '...';
} else {
    echo $project['description'];
}
echo '</td>';
echo '<td>' . $project['startDate'] . '</td>';
echo '<td>' . $project['completeDate'] . '</td>';
echo '<td>' . $project['stateName'] . '</td>';
echo '<td>';
foreach ($project['actions'] as $action) {
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


