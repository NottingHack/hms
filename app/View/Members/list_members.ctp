<!-- File: /app/View/Members/list_members.ctp -->

<?php
    $this->Html->addCrumb('Members', '/members');
    $this->Html->addCrumb('List Members', '/members/list_members_with_status');
    if(isset($statusData))
    {
        $this->Html->addCrumb($statusData['title'], '/members/list_members_with_status/' . $statusData['status_id']);
    }
?>

<h1>
    <?php
        if(isset($statusData))
        {
            echo $statusData['title'];
        }
        else
        {
            echo 'Members';
        }
    ?>
</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Email</th>
        <th>Join Date</th>
        <th>Handle</th>
        <th>Unlock Text</th>
        <th>Groups</th>
        <?php
            if(isset($statusData) === false)
            {
                echo '<th>Status</th>';
            }
        ?>
        <th>Actions</th>
    </tr>
    <?php foreach ($members as $member): ?>
    <tr>
        <td><?php echo $member['Member']['member_id']; ?></td>
        <td>
            <?php echo $this->Html->link($member['Member']['name'], array('controller' => 'members', 'action' => 'view', $member['Member']['member_id'])); ?>
        </td>
        <td><?php echo $member['Member']['email']; ?></td>
        <td><?php echo $member['Member']['join_date']; ?></td>
        <td><?php echo $member['Member']['handle']; ?></td>
        <td><?php echo $member['Member']['unlock_text']; ?></td>
        <td>
            <?php
                $numGroups = count($member['Group']);
                if($numGroups === 0)
                {
                    echo 'None';
                }
                else
                {
                    for($i = 0; $i < $numGroups; $i++) {
                        echo $this->Html->link($member['Group'][$i]['grp_description'], array('controller' => 'groups', 'action' => 'view', $member['Group'][$i]['grp_id']));
                        if($i < $numGroups - 1)
                        {
                            echo ', ';
                        }
                    }
                }
            ?>
        </td>
        <?php
            if(isset($statusData) === false)
            {
                echo '<td>' . $this->Html->link($member['Status']['title'], array('controller' => 'members', 'action' => 'list_members_with_status', $member['Status']['status_id'])) . '</td>';
            }
        ?>
        <td>
            <?php 
                switch ($member['Member']['member_status']) {
                    case 1: # Prospective member
                        echo $this->Html->link("Approve member", array('controller' => 'members', 'action' => 'set_member_status', $member['Member']['member_id'], 2));
                        break;

                    case 2: # Current member
                        echo $this->Html->link("Revoke membership", array('controller' => 'members', 'action' => 'set_member_status', $member['Member']['member_id'], 3));
                        break;

                    case 3: # Ex-member
                        echo $this->Html->link("Reinstate membership", array('controller' => 'members', 'action' => 'set_member_status', $member['Member']['member_id'], 2));
                        break;
                }
            ?>
        </td>
    </tr>
    <?php endforeach; ?>

</table>