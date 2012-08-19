<!-- File: /app/View/Status/index.ctp -->

<h1>Status</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
    </tr>
    <?php foreach ($statuses as $status): ?>
    <tr>
        <td><?php echo $status['Status']['id']; ?></td>
        <td><?php echo $status['Status']['title']; ?></td>
    </tr>
    <?php endforeach; ?>

</table>