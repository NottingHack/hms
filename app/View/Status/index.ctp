<!-- File: /app/View/Status/index.ctp -->

<?php
    $this->Html->addCrumb('Status', '/status');
?>

<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
    </tr>
    <?php foreach ($statuses as $status): ?>
    <tr>
        <td><?php echo $status['Status']['status_id']; ?></td>
        <td><?php echo $status['Status']['title']; ?></td>
    </tr>
    <?php endforeach; ?>

</table>