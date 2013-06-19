<!-- File: /app/View/Members/list_members.ctp -->

<?php
    $this->Html->addCrumb('Members', '/members');
    $this->Html->addCrumb('List Members', '/members/listMembersWithStatus');
    if( isset($statusInfo) &&
        isset($statusInfo['id']) &&
        isset($statusInfo['name']) )
    {
        $this->Html->addCrumb($statusInfo['name'], '/members/listMembersWithStatus/' . $statusInfo['id']);
    }
?>

<?php if(count($memberList) > 0): ?>
    <table>
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
                    <?php echo $this->Html->link(sprintf('%s %s', $member['firstname'], $member['surname']), array('controller' => 'members', 'action' => 'view', $member['id'])); ?>
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
                    <?php echo $this->Html->link($member['status']['name'], array('controller' => 'members', 'action' => 'listMembersWithStatus', $member['status']['id'])); ?>
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
    </table>
    <div class="paginate">
        <?php echo $this->Paginator->numbers(); ?>
    </div>
<?php else: ?>
    <p> No members to list. </p>
<?php endif; ?>