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
 * Model for all vote data.
 */
class MemberVoiceVote extends MemberVoiceAppModel {

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'votes';

/**
 * Return value for valid vote.
 */
	const VOTE_VALID = 1;

/**
 * Return value when no id provided.
 */
	const VOTE_NO_ID = 2;

/**
 * Return value when idea is not found.
 */
	const VOTE_NOT_FOUND = 3;

/**
 * Return value when no vote provided.
 */
	const VOTE_MISSING = 4;

/**
 * Return value when vote is not within expected range.
 */
	const VOTE_INVALID = 5;

/**
 * Return value when vote doesn't save.
 */
	const VOTE_NOT_SAVED = 6;
}