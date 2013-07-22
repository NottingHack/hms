<?php
/**
 * Model for all Ideas in MemberVoice
 *
 *
 * @package       plugin.MemberVoice.Model
 */
class MemberVoiceIdea extends MemberVoiceAppModel {
	public $useTable = 'ideas';
	public $alias = 'Idea';
	
	public $hasAndBelongsToMany = array(
		'Category'	=>	array(
				'className'	=>	'MemberVoice.MemberVoiceCategory',
				'joinTable'	=>	'mv_categories_ideas',
			)
		);
	public $hasMany = array(
		'Vote'	=>	array(
				'className'	=>	'MemberVoice.MemberVoiceVote',
			),
		'Comment'	=>	array(
				'className'	=>	'MemberVoice.MemberVoiceComment',
			),
		);
	public $belongsTo = array(
		'Status'	=>	array(
				'className'	=>	'MemberVoice.MemberVoiceStatus',
			)
		);

	//! Saves votes for an idea
	/*!
		@param integer $ideaID ID of idea to save this vote against
		@param mixed $userID ID of currently logged in user
		@param integer $votes Number of votes to apply, one of -1, 0, 1
		@retval mixed either false on failure, or total sum of votes for idea on success
	*/
	public function saveVote($ideaID, $userID, $votes) {
		$idea = $this->find('first', array('conditions' => array('Idea.id' => $ideaID)));
		$newvotes = $idea['Idea']['votes'];
		
		// Has this user already voted?
		$voted = false;
		foreach ($idea['Vote'] as $vote)
		{
			if ($vote['user_id'] == $userID)
			{
				$voted = $vote['id'];
				$oldvote = $vote['votes'];
			}
		}

		// Ok, what shall we do?
		$saveVote = true;
		if ($voted == false and $votes == 0)
		{
			// trying to clear a vote that doesn't exist! Don't do anything
			return false;
		}
		elseif ($voted == false)
		{
			// new vote, just save
			$newvotes = $newvotes + $votes;
		}
		else
		{
			// remove old vote first
			$newvotes = $newvotes - $oldvote;

			$this->Vote->delete($voted);

			if ($votes == 0)
			{
				// was clearing vote, just save the idea
				$saveVote = false;

			}
			else
			{
				$newvotes = $newvotes + $votes;
			}
		}

		// Actually save!
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
		if ($this->saveAssociated($data))
		{
			return $newvotes;
		}
		else
		{
			return false;
		}
	}
}

?>