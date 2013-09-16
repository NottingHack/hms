<!-- File: /app/View/ConsumableRequest/View.ctp -->

<?php
    $this->Html->addCrumb('Consumables Request', '/consumableRequest');
    $this->Html->addCrumb($request['title'], '/view/' . $request['request_id']);
?>

<div class="consumable_request_detail">
	<div class="consumable_request_detail_title">
		<?php echo $request['title']; ?>
	</div>
	<div class="consumable_request_detail_timestamp">
		Opened on: <?php echo date('l jS \of F Y h:i:s a', strtotime($request['timestamp'])); ?>
	</div>
	<div class="consumable_request_detail_status">
		Status: <?php echo $request['status']['name']; ?>
	</deiv>
	<div class="consumable_request_detail_detail">
		Detail: <p><?php echo $request['detail']; ?></p>
	</div>

	<div class="consumable_request_detail_comments">
		<?php foreach($request['comments'] as $comment): ?>
			<div class="consumable_request_detail_comment">
				<h3><?php echo $comment['member_id']; ?></h3>
				<span class="consumable_request_detail_comment_timestamp">
					<?php echo date('l jS \of F Y h:i:s a', strtotime($comment['timestamp'])); ?>
				</span>
				<p><?php echo $comment['text']; ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</div>