<?php
/**
 * Model for all Ideas in MemberVoice
 *
 *
 * @package       plugin.MemberVoice.Model
 */
class MVIdea extends MemberVoiceAppModel {
	public $useTable = 'ideas';
	public $alias = 'Idea';
	
	public $hasAndBelongsToMany = array(
		'Category'	=>	array(
				'className'	=>	'MemberVoice.MVCategory',
				'joinTable'	=>	'mv_categories_ideas',
			)
		);
	public $hasMany = array(
		'Vote'	=>	array(
				'className'	=>	'MemberVoice.MVVote',
			),
		'Comment'	=>	array(
				'className'	=>	'MemberVoice.MVComment',
			),
		);
	public $belongsTo = array(
		'Status'	=>	array(
				'className'	=>	'MemberVoice.MVStatus',
			)
		);

	public function saveVote($ideaID, $userID, $votes) {
		$idea = $this->find('first', array('conditions' => array('Idea.id' => $ideaID)));
		$newvotes = $idea['Idea']['votes'];
		
		/* Has this user already voted? */
		$voted = false;
		foreach ($idea['Vote'] as $vote) {
			if ($vote['user_id'] == $userID) {
				$voted = $vote['id'];
				$oldvote = $vote['votes'];
			}
		}

		/* Ok, what shall we do? */
		$saveVote = true;
		if ($voted == false and $votes == 0) {
			/* trying to clear a vote that doesn't exist! Don't do anything */
			return false;
		}
		else if ($voted == false) {
			/* new vote, just save */
			$newvotes = $newvotes + $votes;
		}
		else if ($voted !== false) {
			/* remove old vote first */
			$newvotes = $newvotes - $oldvote;

			$this->Vote->delete($voted);

			if ($votes == 0) {
				/* was clearing vote, just save the idea */
				$saveVote = false;

			}
			else {
				$newvotes = $newvotes + $votes;
			}
		}

		/* Actually save! */
		$data = array(
					  'Idea' => array(
									  'id'		=>	$ideaID,
									  'votes'	=>	$newvotes,
									  ),
					  );
		if ($saveVote) {
			$data['Vote'] = array(
								  array(
										'user_id'	=>	$userID,
										'idea_id'	=>	$ideaID,
										'votes'		=>	$votes,
										),
								  );
		}
		if ($this->saveAssociated($data)) {
			return $newvotes;
		}
		else {
			return false;
		}
	}
}

?>