<?php
/**
 * 
 * PHP 5
 *
 * Copyright (C) HMS Team
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     HMS Team
 * @package       plugins.MemberVoice.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Model for all idea data.
 */
class MemberVoiceIdea extends MemberVoiceAppModel {

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'ideas';

/**
 * Specify a nicer alias for this model.
 * @var string
 */
	public $alias = 'Idea';

/**
 * Specify 'has and belongs to many (HABTM)'' associations.
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Category'	=>	array(
			'className'	=>	'MemberVoice.MemberVoiceCategory',
			'joinTable'	=>	'mv_categories_ideas',
		)
	);

/**
 * Specify 'has many associations.
 * @var array
 */
	public $hasMany = array(
		'Vote'	=>	array(
			'className'	=>	'MemberVoice.MemberVoiceVote',
		),
		'Comment'	=>	array(
			'className'	=>	'MemberVoice.MemberVoiceComment',
		),
	);

/**
 * Specify 'belongs to' associations.
 * @var array
 */
	public $belongsTo = array(
		'Status'	=>	array(
			'className'	=>	'MemberVoice.MemberVoiceStatus',
		)
	);

/**
 * Saves votes for an idea
 * 
 * @param integer $ideaID ID of idea to save this vote against
 * @param mixed $userID ID of currently logged in user
 * @param integer $votes Number of votes to apply, one of -1, 0, 1
 * @return mixed either false on failure, or total sum of votes for idea on success
 */
	public function saveVote($ideaID, $userID, $votes) {
		$idea = $this->find('first', array('conditions' => array('Idea.id' => $ideaID)));
		$newvotes = $idea['Idea']['votes'];

		// Has this user already voted?
		$voted = false;
		foreach ($idea['Vote'] as $vote) {
			if ($vote['user_id'] == $userID) {
				$voted = $vote['id'];
				$oldvote = $vote['votes'];
			}
		}

		// Ok, what shall we do?
		$saveVote = true;
		if ($voted == false && $votes == 0) {
			// trying to clear a vote that doesn't exist! Don't do anything
			return false;
		} elseif ($voted == false) {
			// new vote, just save
			$newvotes = $newvotes + $votes;
		} else {
			// remove old vote first
			$newvotes = $newvotes - $oldvote;

			$this->Vote->delete($voted);

			if ($votes == 0) {
				// was clearing vote, just save the idea
				$saveVote = false;
			} else {
				$newvotes = $newvotes + $votes;
			}
		}

		// Actually save!
		$data = array(
			'Idea' => array(
				'id' => $ideaID,
				'votes' => $newvotes,
			),
		);
		if ($saveVote) {
			$data['Vote'] = array(
				array(
					'user_id' => $userID,
					'idea_id' => $ideaID,
					'votes' => $votes,
				),
			);
		}
		if ($this->saveAssociated($data)) {
			return $newvotes;
		} else {
			return false;
		}
	}
}