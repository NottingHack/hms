<!-- File: /app/View/ConsumableRequest/list_requests.ctp -->

<?php
    $this->Html->addCrumb('Consumables Request', '/consumableRequest');
?>

<?php

    function renderRequest($request)
    {
        echo 'rerer';
    }

    function renderRequestList($header, $requestList)
    {
        echo "<h3>$header</h3>";

        foreach ($requestList as $request) 
        {
            renderRequest($request);
        }
    }

?>

<?php

    // Render the navigation
    echo '<ul class="consumableRequestNav">';
    for($i = 0; $i < count($counts); $i++)
    {
        $countData = $counts[$i];

        $liClass = $countData['current'] ? 'current' : '';
        $spanClass = ($i == count($counts) - 1) ? 'last' : '';

        echo "<li class=\"$liClass\">";
        echo "<div class=\"$spanClass\">";
        echo $this->Html->link(sprintf('%s (%d)', $countData['name'], $countData['count']), array('controller' => 'consumableRequest', 'action' => 'listRequests', $countData['id']));
        echo '</div>';
        echo '</li>'; 
    }
    echo '<div class="clear" />';
    echo '</ul>';

    // Render the requests
    foreach ($requests as $header => $list) 
    {
        renderRequestList($header, $list);
    }
?>