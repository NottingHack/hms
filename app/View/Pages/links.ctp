<!-- File: /app/View/Pages/links.ctp -->

<?php
$this->Html->addCrumb('Links', '/links');
?>

<p>Useful links for members</p>

<ul>
<?php foreach ($links as $link): ?>
    <li><?php echo $this->Html->link($link['name'], $link['value']); ?></li>
<?php endforeach; ?>
</ul>
