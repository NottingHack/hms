<!-- File: /app/View/EmailRecords/index.ctp -->

<?php
    $this->Html->addCrumb('EmailRecords', '/emailrecords');
?>

<h3>All E-mail Records</h3>

<?php
    echo $this->element('email_records');
?>