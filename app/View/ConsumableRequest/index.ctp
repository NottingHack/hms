<!-- File: /app/View/ConsumableRequest/index.ctp -->

<?php
    $this->Html->addCrumb('Consumables Request', '/consumableRequest');
?>

<h3>All Requests</h3>

<?php

	// Sort them by date
    usort($requests, function ($a, $b) { return strtotime($a['timestamp']) - strtotime($b['timestamp']); } );

    // Now split them into the different statuses
    $statusAndRequest = array();

    foreach ($requests as $request) 
    {
    	$statusId = $request['request_status_id'];
    	if(!array_key_exists($statusId, $statusAndRequest))
    	{
    		$statusAndRequest[$statusId] = array(
    			'name' => $request['status']['name'],
    			'requests' => array(),
    		);
    	}

    	array_push($statusAndRequest[$statusId]['requests'], $request);
    }

    // Finally print them
    foreach ($statusAndRequest as $statusId => $section) 
    {
    	echo '<h3>' . $section['name'] .'</h3>';

    	foreach ($section['requests'] as $request):
?>
			<div class="consumable_request_overview">
				<h4><?php echo $this->Html->link($request['title'], array('action' => 'view', $request['request_id'])); ?></h4>
				<div class="consumable_reqest_overview_timestamp">
					<?php echo 'Opened on ' . date('l jS \of F Y h:i:s a', strtotime($request['timestamp'])); ?>
				</div>
				<div class="consumable_request_overview_comments">
					<?php 
						$numComments = count($request['comments']);
						echo $numComments . ($numComments == 1 ? ' comment' : ' comments'); 
					?>
				</div>
			</div>
<?php endforeach;
    }
?>