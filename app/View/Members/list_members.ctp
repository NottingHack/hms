<!-- File: /app/View/Members/list_members.ctp -->

<?php
    $this->Html->addCrumb('Members', '/members');
    $this->Html->addCrumb('List Members', '/members/list_members_with_status');
    if(isset($statusData))
    {
        $this->Html->addCrumb($statusData['title'], '/members/list_members_with_status/' . $statusData['status_id']);
    }
?>

<table>
    <?php if(count($memberList) > 0): ?>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Groups</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($memberList as $member): ?>
        <tr>
            <td>
                <?php echo $this->Html->link($member['name'], array('controller' => 'members', 'action' => 'view', $member['id'])); ?>
            </td>
            <td><?php echo $member['email']; ?></td>
            <td>
                <?php
                    $numGroups = count($member['groups']);
                    if($numGroups === 0)
                    {
                        echo 'None';
                    }
                    else
                    {
                        for($i = 0; $i < $numGroups; $i++) {
                            echo $this->Html->link($member['groups'][$i]['description'], array('controller' => 'groups', 'action' => 'view', $member['groups'][$i]['id']));
                            if($i < $numGroups - 1)
                            {
                                echo ', ';
                            }
                        }
                    }
                ?>
            </td>
            <td>
                <?php echo $this->Html->link($member['status']['name'], array('controller' => 'members', 'action' => 'list_members_with_status', $member['status']['id'])); ?>
            </td>
            <td>
                <?php
                    foreach ($member['actions'] as $action) 
                    {
                        $linkOptions = $action['params'];
                        $linkOptions['controller'] = $action['controller'];
                        $linkOptions['action'] = $action['action'];

                        echo $this->Html->link($action['title'], $linkOptions);
                    }
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <p> No members to list. </p>
    <?php endif; ?>
</table>