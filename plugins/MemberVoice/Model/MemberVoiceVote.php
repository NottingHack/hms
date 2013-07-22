<?php
/**
 * Model for all Ideas in MemberVoice
 *
 *
 * @package       plugin.MemberVoice.Model
 */
class MemberVoiceVote extends MemberVoiceAppModel {
	public $useTable = 'votes';

	const VOTE_VALID = 1; //!< Return value for valid vote
	const VOTE_NO_ID = 2; //!< Return value when no id provided
	const VOTE_NOT_FOUND = 3; //!< Return value when idea is not found
	const VOTE_MISSING = 4; //!< Return value when no vote provided
	const VOTE_INVALID = 5; //!< Return value when vote is not within expected range
	const VOTE_NOT_SAVED = 6; //!< Return value when vote doesn't save
}

?>