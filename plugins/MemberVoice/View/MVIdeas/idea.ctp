<?php
/* Breadcrumbs */
$this->Html->addCrumb('MemberVoice', '/membervoice/ideas');
$this->Html->addCrumb($idea['Idea']['id'] , '/membervoice/ideas/idea/' . $idea['Idea']['id']);

/* Load our CSS */
$this->Html->css('MemberVoice.mvideas', null, array('inline' => false));

/* The JSON URL */
$this->append('script');
echo('<script type="text/javascript">' . "\n");
echo('var mvVoteUrl = "' . $voteurl . '";' . "\n");
echo('</script>' ."\n");
$this->end();

/* Load the Ideas JS */
$this->Html->script('MemberVoice.idea', array('inline' => false));

/* Enclose all HTML to isolate css */
?>

<div class="memberVoice" id="mvIdea<?php echo($idea['Idea']['id']); ?>">

<div class="mvIdeaDetail">
	<h2><?php echo($idea['Idea']['idea']); ?></h2>
	<p><?php echo($idea['Idea']['description']); ?></p>
</div>

<div class="mvIdeaVotes">
	<div class="mvVoteCount">
		<strong><?php echo($idea['Idea']['votes']); ?></strong>
		<span>vote<?php echo($idea['Idea']['votes'] == 1 ? "" : "s"); ?></span>
	</div>

	<?php
	$hasVoted = 0;
	foreach ($idea['Vote'] as $vote) {
		if ($vote['user_id'] == $user) {
			$hasVoted = $vote['votes'];
		}
	}
	echo($this->element('vote', array('id' => $idea['Idea']['id'], 'hasVoted' => $hasVoted)));

	?>

</div>

<div class="mvIdeaComments">
	<h3><a name="comments">Comments</a></h3>

	<?php

	if (count($comments) > 0) {
		$start = 'Join';
		?>
		<ul class="mvComments">
		<?php
		foreach ($comments as $comment) :
			?>
			<li class="mvComment"><p><?php echo(str_replace("\n", '</p><p>', $comment['Comment']['comment'])); ?></p><div class="mvAuthor"><p><strong>Author</strong></p><p><?php echo($comment['User'][$firstname] . ' ' . $comment['User'][$lastname]); ?></p></div></li>
			<?php
		endforeach;
		?>
	</ul>
		<?php
	}
	else {
		$start = 'Start';
		echo('<p>No comments yet!</p>');
	}

	?>

	<h3><?php echo($start); ?> the conversation:</h3>
	<?php
	echo $this->Form->create('Comment', array('url' => $this->Html->url(array('plugin' => 'membervoice', 'controller' => 'comments', 'action' => 'add', 'base' => false))));
	/*echo $this->Tinymce->input(
		'Comment.comment', 
		array( 'label' => 'Message' ),
		array( 'language'=>'en' ), 
		'basic' 
		);*/
	echo $this->Form->input('Comment.comment');
	echo $this->Form->hidden('Comment.idea_id', array('value' => $idea['Idea']['id']));
	echo $this->Form->hidden('Comment.user_id', array('value' => $user));
	echo $this->Form->end('Comment');

?>
</div>

<?php
/* End isolation block */
?>
</div>