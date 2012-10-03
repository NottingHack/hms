<!-- File: /app/View/Members/index.ctp -->

<?php
    $this->Html->addCrumb('Members', '/members');
?>

<div class="search">
<?php
    echo $this->form->create("Member",array('action' => 'search')); 
    echo $this->form->input("query", array('label' => ''));
    echo $this->form->end("Search"); 
?>
</div>

<h3>Summary</h3>

<table>
    <tr>
        <th>
            Member Type
        </th>
        <th>
            Description
        </th>
        <th>
            No. Members with this status
        </th>
    </tr>
    <?php foreach ($memberStatusCount as $title => $data): ?>
        <tr>
            <td>
                <?php echo $this->Html->link($title, array('controller' => 'members', 'action' => 'list_members_with_status', $data['id'])); ?>
            </td>
            <td>
                <?php echo $data['desc']; ?>
            </td>
            <td>
                <?php echo $data['count'] ?>
            </td>
        </tr>
    <? endforeach; ?>
    <tr>
        <td>
            <?php echo $this->Html->link('All', array('controller' => 'members', 'action' => 'list_members')); ?>
        </td>
        <td>
            All Members
        </td>
        <td>
            <?php echo $memberTotalCount; ?>
        </td>
    </tr>
</table>

