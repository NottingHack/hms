<!-- File: /app/View/MemberProjects/view.ctp -->

<?php
$this->Html->addCrumb('Members', '/members');
$this->Html->addCrumb(isset($member['username']) ? $member['username'] : $member['email'], '/members/view/' . $member['id']);
$this->Html->addCrumb('Projects', '/memberProjects/listprojects/' . $member['id']);
$this->Html->addCrumb($project['projectName'], '/memberProjects/view/' . $project['memberProjectId']);
?>

<dl>
    <dt>
        Name
    </dt>
    <dd>
        <?php echo $project['projectName']; ?>
    </dd>
    <dt>
        Description
    </dt>
    <dd>
        <?php echo $project['description']; ?>
    </dd>
    <dt>
        Start Date
    </dt>
    <dd>
        <?php echo $project['startDate']; ?>
    </dd>
    <?php if( isset($project['completeDate']) ): ?>
    <dt>
        Complete Date
    </dt>
    <dd>
        <?php echo $project['completeDate']; ?>
    </dd>
	<?php endif; ?>
    <dt>
        Status
    </dt>
    <dd>
        <?php echo $project['stateName']; ?>
    </dd>
</dl>