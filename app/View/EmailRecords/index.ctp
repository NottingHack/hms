<!-- File: /app/View/EmailRecords/index.ctp -->

<?php
    $this->Html->addCrumb('EmailRecords', '/emailrecords');
?>

<h3>Summary</h3>

<table>
    <tr>
        <th>
            Member Status
        </th>
        <th>
            No. Members with this status
        </th>
    </tr>
        <?php
            foreach ($memberStatusInfo as $data):
        ?>
        <tr>
            <td>
                <?php echo $this->Html->link($data['name'], array('controller' => 'members', 'action' => 'listMembersWithStatus', $data['id'])); ?>
            </td>
            <td>
                <?php echo $data['count'] ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td>
            <?php echo $this->Html->link('Total', array('controller' => 'members', 'action' => 'listMembers')); ?>
        </td>
        <td>
            <?php echo $memberTotalCount; ?>
        </td>
    </tr>
</table>

