<?php

    // Split the list of all e-mails into groups by day
    $daysAndEmailRecords = array();

    $currentDay = null;
    $currentDayEmails = array();
    foreach ($emails as $email) 
    {
        $getDayPart = date('dS F Y', strtotime($email['timestamp']));
        if($getDayPart != $currentDay)
        {
            if($currentDay != null)
            {
                $daysAndEmailRecords[$currentDay] = $currentDayEmails;
                $currentDayEmails = array();
            }
            $currentDay = $getDayPart;
        }
        
        array_push($currentDayEmails, $email);
    }

    $daysAndEmailRecords[$currentDay] = $currentDayEmails;

    // Output them nicely

    foreach ($daysAndEmailRecords as $day => $records) 
    {
        echo '<div class="emailRecordDay">';
        echo "<h4>$day</h4>";
        echo '<ul>';
        foreach ($records as $email) 
        {
            echo '<li>';
            $id = $email['member_id'];
            $name = $this->Html->link( $memberNames[$id], array('controller' => 'emailrecords', 'action' => 'view', $id) );
            echo sprintf('%s - %s to %s', date('H:i', strtotime($email['timestamp'])), $email['subject'], $name);
            echo '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }

?>