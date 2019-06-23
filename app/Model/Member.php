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
 * @package       app.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppModel', 'Model');
App::uses('Status', 'Model');
App::uses('InvalidStatusException', 'Error/Exception');
App::uses('NotAuthorizedException', 'Error/Exception');
App::uses('CakeText', 'Utility');

/**
 * Model for all member data
 */
class Member extends AppModel {
/**
 * The minimum length passwords must be.
 */
	const MIN_PASSWORD_LENGTH = 6;

/**
 * Initial credit limit, in pence.
 */
	const INITAL_CREDIT_LIMIT = 0;

/**
 * Specify the primary key.
 * @var string
 */
	public $primaryKey = 'member_id';

/**
 * Specify our 'belongs to' associations
 * @var array
 */
	public $belongsTo = array(
		'Status' => array(
				'className' => 'Status',
				'foreignKey' => 'member_status',
				'type' => 'inner'
		),
		'Account' => array(
				'className' => 'Account',
				'foreignKey' => 'account_id',
		),
	);

/**
 * Specify our 'has many' associations
 * @var array
 */
	public $hasMany = array(
		'StatusUpdate' => array(
			'order' => 'StatusUpdate.timestamp DESC',
			'limit'	=> '1',
		),
		'Pin' => array(
			'className' => 'Pin',
		),
		'RfidTag' => array(
			'className' => 'RfidTag',
		),
	);

/**
 * Has and belongs to many (HABTM) associations
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Group' =>
			array(
				'className' => 'Group',
				'joinTable' => 'member_group',
				'foreignKey' => 'member_id',
				'associationForeignKey' => 'grp_id',
				'unique' => true,
				'with' => 'GroupsMember',
			),
	);

/**
 * Validation rules.
 * @var array
 */
	public $validate = array(
		'firstname' => array(
			'length' => array(
				'rule' => array('between', 1, 100),
				'message' => 'Firstname must be between 1 and 100 characters long',
			),
		),
		'surname' => array(
			'length' => array(
				'rule' => array('between', 1, 100),
				'message' => 'Surname must be between 1 and 100 characters long',
			),
		),
		'email' => array(
			'length' => array(
				'rule' => array('between', 1, 100),
				'required' => true,
				'message' => 'Email must be between 1 and 100 characters long',
			),
			'content' => array(
				'rule' => 'email',
			),
		),
		'password' => array(
			'noEmpty' => array(
				'rule' => 'notBlank',
				'message' => 'This field cannot be left blank'
			),
			'minLen' => array(
				'rule' => array('minLength', self::MIN_PASSWORD_LENGTH),
				'message' => 'Password too short',
			),
		),
		'password_confirm' => array(
			'noEmpty' => array(
				'rule' => 'notBlank',
				'message' => 'This field cannot be left blank'
			),
			'minLen' => array(
				'rule' => array('minLength', self::MIN_PASSWORD_LENGTH),
				'message' => 'Password too short',
			),
			'matchNewPassword' => array(
				'rule' => array( 'passwordConfirmMatchesPassword' ),
				'message' => 'Passwords don\'t match',
			),
		),
		'username' => array(
			'noEmpty' => array(
				'rule' => 'notBlank',
				'message' => 'This field cannot be left blank'
			),
			'mustbeUnique' => array(
				'rule' => array( 'checkUniqueUsername' ),
				'message' => 'That username is already taken',
			),
			'alphaNumeric' => array(
				'rule' => 'alphaNumeric',
				'message' => 'Aplha-numeric characters only'
			),
			'between' => array(
				'rule' => array('between', 3, 50),
				'message' => 'Username must be between 3 to 50 characters long'
			),
		),
		'unlock_text' => array(
			'length' => array(
				'rule' => array('between', 1, 95),
				'message' => 'Unlock text must be between 1 and 95 characters long',
			),
		),
		'usernameOrEmail' => array(
			'notBlank',
		),
		'account_id' => array(
			'length' => array(
				'rule' => array('between', 1, 11),
				'message' => 'Account id must be between 1 and 12 characters long',
			),
			'content' => array(
				'rule' => 'numeric',
				'message' => 'Account id must be a number',
			),
		),
		'member_status' => array(
			'rule' => array(
				'inList', array(
					Status::PROSPECTIVE_MEMBER,
					Status::PRE_MEMBER_1,
					Status::PRE_MEMBER_2,
					Status::PRE_MEMBER_3,
					Status::CURRENT_MEMBER,
					Status::EX_MEMBER,
				),
			),
		),
		'address_1' => array(
			'length' => array(
				'rule' => array('between', 1, 100),
				'message' => 'Address must be between 1 and 100 characters long',
			),
		),
		'address_2' => array(
			'foo' => array(
				'rule' => array('maxLength', 100),
				'required' => false,
				'allowEmpty' => true,
				'message' => 'Address must be no more than 100 characters long',
			),
		),
		'address_city' => array(
			'length' => array(
				'rule' => array('between', 1, 100),
				'message' => 'City must be between 1 and 100 characters long',
			),
		),
		'address_postcode' => array(
			'length' => array(
				'rule' => array('between', 1, 100),
				'message' => 'Postcode must be between 1 and 100 characters long',
			),
		),
		'contact_number' => array(
			'length' => array(
				'rule' => array('between', 1, 20),
				'message' => 'Contact number must be between 1 and 20 characters long',
			),
			'characters' => array(
				'rule' => '/^\+?[0-9() ]*$/',
				'message' => 'Contact number can only have digits, (, ) a + character at the start.'
			)
		),
	);

/**
 * Specify the behavors we implement
 * @var array
 */
	public $actsAs = array('KrbAuth');

/**
 * MailingList object, for easy mocking
 * @var MailingList
 */
	public $mailingList = null;

/**
 * Validation function to see if the user-supplied password and password confirmation match.
 *
 * @param array $check The password to be validated.
 * @return bool True if the supplied password values match, otherwise false.
 */
	public function passwordConfirmMatchesPassword($check) {
		return $this->data['Member']['password'] === $check['password_confirm'];
	}

/**
 * Validation function to see if the user-supplied username is already taken.
 *
 * @param array $check The username to check.
 * @return bool True if the supplied username exists in the database (case-insensitive) registered to a different user, otherwise false.
 */
	public function checkUniqueUsername($check) {
		$lowercaseUsername = strtolower($check['username']);
		$records = $this->find('all',
			array('fields' => array('Member.username'),
				'conditions' => array(
					'Member.username LIKE' => $lowercaseUsername,
					'Member.member_id NOT' => $this->data['Member']['member_id'],
				)
			)
		);

		foreach ($records as $record) {
			if (strtolower($record['Member']['username']) == $lowercaseUsername) {
				return false;
			}
		}
		return true;
	}

/**
 * Validation function to see if the user-supplied email matches what's in the database.
 *
 * @param array $check The email to check.
 * @return bool True if the supplied email value matches the database, otherwise false.
 * @link Member::addEmailMustMatch()
 * @link Member::removeEmailMustMatch()
 */
	public function checkEmailMatch($check) {
		$ourEmail = $this->find('first', array('fields' => array('Member.email'), 'conditions' => array('Member.member_id' => $this->data['Member']['member_id'])));
		return strcasecmp($ourEmail['Member']['email'], $check['email']) == 0;
	}

/**
 * Actions to perform before saving any data
 *
 * @param array $options Any options that were passed to the Save method
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
 */
	public function beforeSave($options = array()) {
		// Must never ever ever alter the balance
		unset( $this->data['Member']['balance'] );

		return true;
	}

/**
 * Add an extra validation rule to the e-mail field stating that the user supplied e-mail must match what's in the database.
 *
 * @link Member::checkEmailMatch()
 * @link Member::removeEmailMustMatch()
 */
	public function addEmailMustMatch() {
		$this->validator()->add('email', 'emailMustMatch', array( 'rule' => array( 'checkEmailMatch' ), 'message' => 'Incorrect email used' ));
	}

/**
 * Remove the 'e-mail must match' validation rule.
 *
 * @link Member::checkEmailMatch()
 * @link Member::addEmailMustMatch()
 */
	public function removeEmailMustMatch() {
		$this->validator()->remove('email', 'emailMustMatch');
	}

/**
 * Find how many members have a certain Status.
 *
 * @param int $statusId The id of the Status record to check.
 * @return int The number of member records that belong to the Status.
 */
	public function getCountForStatus($statusId) {
		return $this->find( 'count', array( 'conditions' => array( $this->belongsTo['Status']['foreignKey'] => $statusId ) ) );
	}

/**
 * Find out how many member records exist in the database.
 *
 * @return int The number of member records in the database.
 */
	public function getCount() {
		return $this->find( 'count' );
	}

/**
 * Find out if we have record of a Member with a specific e-mail address.
 *
 * @param string $email E-mail address to check.
 * @return bool True if there is a Member with this e-mail, false otherwise.
 */
	public function doesMemberExistWithEmail($email) {
		return $this->find( 'count', array( 'conditions' => array( 'Member.email' => strtolower($email) ) ) ) > 0;
	}

/**
 * Get a summary of the member records for a specific member.
 *
 * @param int $memberId The id of the member to work with.
 * @param $format If true format the return data.
 * @return array A summary of the data for a specific member.
 * @link Member::__getMemberSummary()
 */
	public function getMemberSummaryForMember($memberId, $format = true) {
		$memberList = $this->getMemberSummaryForMembers(array($memberId), $format);
		if (!empty($memberList)) {
			return $memberList[0];
		}
		return array();
	}

/**
 * Get a summary of the member records for a list of members.
 *
 * @param array $memberIds The array of member ids to work with.
 * @param $format If true format the return data.
 * @return array A summary of the data for the members in the list
 * @link Member::__getMemberSummary()
 */
	public function getMemberSummaryForMembers($memberIds, $format = true) {
		return $this->__getMemberSummary(false, array('Member.member_id' => $memberIds), $format);
	}

/**
 * Get a summary of the member records for all members.
 *
 * @param $paginate If true, return a query to retrieve a page of the data, otherwise return the data.
 * @return array A summary of the data of all members.
 * @link Member::__getMemberSummary()
 */
	public function getMemberSummaryAll($paginate) {
		return $this->__getMemberSummary($paginate);
	}

/**
 * Get a summary of the member records for all members with an account id that is in the list passed in.
 *
 * @param $paginate If true, return a query to retrieve a page of the data, otherwise return the data.
 * @param array $accountIds Retrieve information about members who have one of these account ids.
 * @return array A summary of the data of all members with those account ids.
 * @link Member::__getMemberSummary()
 */
	public function getMemberSummaryForAccountIds($paginate, $accountIds) {
		return $this->__getMemberSummary($paginate, array( 'Member.account_id' => $accountIds ) );
	}

/**
 * Get a summary of the member records for all members with a certain status.
 *
 *
 * @param $paginate If true, return a query to retrieve a page of the data, otherwise return the data.
 * @param int $statusId Retrieve information about members who have this status.
 * @return array A summary of the data of all members of a status.
 * @link Member::__getMemberSummary()
 */
	public function getMemberSummaryForStatus($paginate, $statusId) {
		return $this->__getMemberSummary($paginate, array( 'Member.member_status' => $statusId ) );
	}

/**
 * Get a summary of the member records for all member records where their name, email or username is similar to the keyword.
 *
 *
 * @param $paginate If true, return a query to retrieve a page of the data, otherwise return the data.
 * @param string $keyword Term to search for.
 * @return array A summary of the data of all members who match the query.
 * @link Member::__getMemberSummary()
 */
	public function getMemberSummaryForSearchQuery($paginate, $keyword) {
		return $this->__getMemberSummary( $paginate,
			array( 'OR' =>
				array(
					"Member.firstname Like'%$keyword%'",
					"Member.surname Like'%$keyword%'",
					"Member.email Like'%$keyword%'",
					"Member.username Like'%$keyword%'",
					"Account.payment_ref Like'%$keyword%'",
				)
			)
		);
	}

/**
 * Format an array of member infos.
 *
 * @param array $memberInfoList The array of member infos.
 * @param bool $removeNullEntries If true then entries that have a value of null, false or an empty array won't exist in the final array.
 * @return array An array of formatted member infos.
 */
	public function formatMemberInfoList($memberInfoList, $removeNullEntries) {
		$formattedInfos = array();
		foreach ($memberInfoList as $memberInfo) {
			array_push($formattedInfos, $this->formatMemberInfo($memberInfo, $removeNullEntries));
		}
		return $formattedInfos;
	}

/**
 * Format member information into a nicer arrangement.
 *
 * @param array $memberInfo The info to format, usually retrieved from Member::__getMemberSummary or Member::getMemberDetails.
 * @param bool $removeNullEntries If true then entries that have a value of null, false or an empty array won't exist in the final array.
 * @return array An array of member information, formatted so that nothing needs to know database rows.
 * @link Member::__getMemberSummary
 * @link Member::getMemberDetails
 */
	public function formatMemberInfo($memberInfo, $removeNullEntries) {
		/*
			Data should be presented to the view in an array like so:
			[id] => member id
			[bestName] => The name of the member, or the username, or the e-mail (depending on what data we have)
			[firstname] => member first name
			[surname] => member surname
			[username] => member username
			[email] => member email
			[groups] =>
				[n] =>
					[id] => group id
					[description] => group description
			[status] =>
				[id] => status id
				[name] => name of the status
			[joinDate] => member join data
			[unlockText] => member unlock text
			[balance] => member balance
			[creditLimit] => member credit limit
			[pin] =>
				[n] =>
					[id] => pin id
					[pin] => pin number
					[state] => pin state (see constance defined in Pin.php)
            [rfidtag] =>
                [n] =>
                    [id] => rfid_id
                    [serial] => rfid_serial
                    [state] => state (see constance deined in RfidTag.php)
                    [last_used] => last_used
                    [name] => friendly_name
			[paymentRef] => member payment ref
			[accountId] => account_id
			[address] =>
				[part1] => member address part 1
				[part2] => member address part 2
				[city] => member address part 2
				[postcode] => member address postcode
			[contactNumber] => member contact number
			[lastStatusUpdate] =>
				[id] => member id
				[by] => admin member id
				[by_username] => admin username
				[from] => previous status id
				[to] => current status id
				[at] => time the update happened
			[joint] => bool
	 */

		$id = Hash::get($memberInfo, 'Member.member_id');
		$firstname = Hash::get($memberInfo, 'Member.firstname');
		$surname = Hash::get($memberInfo, 'Member.surname');
		$username = Hash::get($memberInfo, 'Member.username');
		$email = Hash::get($memberInfo, 'Member.email');

		$status = array();
		if (array_key_exists('Status', $memberInfo)) {
			$status['id'] = Hash::get($memberInfo, 'Status.status_id');
			$status['name'] = Hash::get($memberInfo, 'Status.title');
		}

		$joinDate = Hash::get($memberInfo, 'Member.join_date');
		$unlockText = Hash::get($memberInfo, 'Member.unlock_text');

		$groups = array();
		if (array_key_exists('Group', $memberInfo)) {
			foreach ($memberInfo['Group'] as $group) {
				array_push($groups,
					array(
						'id' => Hash::get($group, 'grp_id'),
						'description' => Hash::get($group, 'grp_description'),
					)
				);
			}
		}

		$balance = Hash::get($memberInfo, 'Member.balance');
		$creditLimit = Hash::get($memberInfo, 'Member.credit_limit');
		$pins = array();
		if (array_key_exists('Pin', $memberInfo)) {
			foreach ($memberInfo['Pin'] as $pin) {
				array_push($pins,
					array(
						'id' => Hash::get($pin, 'pin_id'),
						'pin' => Hash::get($pin, 'pin'),
						'state' => Hash::get($pin, 'state'),
					)
				);
			}
		}

		$rfidtags = array();
		if (array_key_exists('RfidTag', $memberInfo)) {
			foreach ($memberInfo['RfidTag'] as $tag) {
				if (Hash::get($tag, 'rfid_serial') == null) {
					$serial = Hash::get($tag, 'rfid_serial_legacy');
				} else {
					$serial = Hash::get($tag, 'rfid_serial');
				}
				array_push($rfidtags,
					array(
						'id' => Hash::get($tag, 'rfid_id'),
						'serial' => $serial,
						'state' => Hash::get($tag, 'state'),
						'last_used' => Hash::get($tag, 'last_used'),
						'name' => Hash::get($tag, 'friendly_name'),
					)
				);
			}
		}

		$accountId = Hash::get($memberInfo, 'Member.account_id');
		$paymentRef = Hash::get($memberInfo, 'Account.payment_ref');
		$address = array(
			'part1' => Hash::get($memberInfo, 'Member.address_1'),
			'part2' => Hash::get($memberInfo, 'Member.address_2'),
			'city' => Hash::get($memberInfo, 'Member.address_city'),
			'postcode' => Hash::get($memberInfo, 'Member.address_postcode'),
		);
		$contactNumber = Hash::get($memberInfo, 'Member.contact_number');

		$lastStatusUpdate = null;
		if (Hash::check($memberInfo, 'StatusUpdate.0.id')) {
			$lastStatusUpdate = $this->StatusUpdate->formatStatusUpdate(Hash::get($memberInfo, 'StatusUpdate.0.id'));
		}

		$bestName = $email;
		if (isset($username)) {
			$bestName = $username;
		}
		if (isset($firstname) && isset($surname)) {
			$bestName = $firstname . ' ' . $surname;
		}

		$joinAccount = (count($this->getMemberIdsForAccount(Hash::get($memberInfo, 'Member.account_id'))) > 1);

		$allValues = array(
			'id' => $id,
			'bestName' => $bestName,
			'firstname' => $firstname,
			'surname' => $surname,
			'username' => $username,
			'email' => $email,
			'groups' => $groups,
			'status' => $status,
			'joinDate' => $joinDate,
			'unlockText' => $unlockText,
			'accountId' => $accountId,
			'paymentRef' => $paymentRef,
			'balance' => $balance,
			'creditLimit' => $creditLimit,
			'pin' => $pins,
			'rfidtag' => $rfidtags,
			'address' => $address,
			'contactNumber' => $contactNumber,
			'lastStatusUpdate' => $lastStatusUpdate,
			'joint' => $joinAccount,
		);

		if (!$removeNullEntries) {
			return $allValues;
		}

		// Filter out any values that are null or false etc.
		$onlyValidValues = array();

		foreach ($allValues as $key => $value) {
			if (isset($value) != false) {
				if (is_array($value) && empty($value)) {
					continue;
				}
				$onlyValidValues[$key] = $value;
			}
		}

		// Address part 1 is required so if any part of the address exists then that will
		if (!$onlyValidValues['address']['part1']) {
			unset($onlyValidValues['address']);
		}

		return $onlyValidValues;
	}

/**
 * Create a member info array for a new member.
 *
 * @param string $email The e-mail address for the new member.
 * @return array An array of member info suitable for saving.
 */
	public function createNewMemberInfo($email) {
		return array(
			'Member' => array(
				'email' => $email,
				'member_status' => Status::PROSPECTIVE_MEMBER,
				'join_date' => '0000-00-00',
			),
		);
	}

/**
 * Get the Status for a member, may hit the database.
 *
 * @param mixed $memberData If array, assumed to be an array of member info in the same format that is returned from database queries, otherwise assumed to be a member id.
 * @return int The status for the member, or 0 if status could not be found.
 */
	public function getIdForMember($memberData) {
		if (!isset($memberData)) {
			return 0;
		}

		if (is_array($memberData)) {
			$memberData = Hash::get($memberData, 'Member.member_id');
		}

		return $memberData;
	}

/**
 * Get the username for a member, may hit the database.
 *
 * @param mixed $memberData If array, assumed to be an array of member info in the same format that is returned from database queries, otherwise assumed to be a member id.
 * @return int The username for the member, or 0 if username could not be found.
 */
	public function getUsernameForMember($memberData) {
		if (!isset($memberData)) {
			return 0;
		}

		if (is_array($memberData)) {
			$status = Hash::get($memberData, 'Member.username');
			if (isset($status)) {
				return $status;
			} else {
				$memberData = Hash::get($memberData, 'Member.member_id');
			}
		}

		$memberInfo = $this->find('first', array('fields' => array('Member.username'), 'conditions' => array('Member.member_id' => $memberData) ));
		if (is_array($memberInfo)) {
			return Hash::get($memberInfo, 'Member.username');
		}

		return 0;
	}

/**
 * Get the Status for a member, may hit the database.
 *
 * @param mixed $memberData If array, assumed to be an array of member info in the same format that is returned from database queries, otherwise assumed to be a member id.
 * @return int The status for the member, or 0 if status could not be found.
 */
	public function getStatusForMember($memberData) {
		if (!isset($memberData)) {
			return 0;
		}

		if (is_array($memberData)) {
			$status = Hash::get($memberData, 'Member.member_status');
			if (isset($status)) {
				return (int)$status;
			} else {
				$memberData = Hash::get($memberData, 'Member.member_id');
			}
		}

		$memberInfo = $this->find('first', array('fields' => array('Member.member_status'), 'conditions' => array('Member.member_id' => $memberData) ));
		if (is_array($memberInfo)) {
			$status = Hash::get($memberInfo, 'Member.member_status');
			if (isset($status)) {
				return (int)$status;
			}
		}

		return 0;
	}

/**
 * Get the email for a member, may hit the database.
 *
 * @param mixed $memberData If array, assumed to be an array of member info in the same format that is returned from database queries, otherwise assumed to be a member id.
 * @return int The email for the member, or null if email could not be found.
 */
	public function getEmailForMember($memberData) {
		if (!isset($memberData)) {
			return null;
		}

		if (is_array($memberData)) {
			$email = Hash::get($memberData, 'Member.email');
			if (isset($email)) {
				return $email;
			} else {
				$memberData = Hash::get($memberData, 'Member.member_id');
			}
		}

		$memberInfo = $this->find('first', array('fields' => array('Member.email'), 'conditions' => array('Member.member_id' => $memberData) ));
		if (is_array($memberInfo)) {
			$email = Hash::get($memberInfo, 'Member.email');
			if (isset($email)) {
				return $email;
			}
		}

		return null;
	}

/**
 * Get a list of e-mail addresses for all members in a Group.
 *
 * @param int $groupId The id of the group the members must belong to.
 * @return array A list of member e-mails.
 */
	public function getEmailsForMembersInGroup($groupId) {
		$memberIds = $this->GroupsMember->getMemberIdsForGroup($groupId);
		if (count($memberIds) > 0) {
			$emails = $this->find('all', array('fields' => array('email'), 'conditions' => array('Member.member_id' => $memberIds)));
			return Hash::extract( $emails, '{n}.Member.email' );
		}
		return array();
	}

/**
 * Get a list of e-mail addresses for all members.
 *
 * @return array A list of member e-mails.
 */
	public function getEmailsForAllMembers() {
		$emails = $this->find('all', array('fields' => array('email')));
		return Hash::extract( $emails, '{n}.Member.email' );
	}

/**
 * Attempt to register a new member record.
 *
 * @param array $data Information to use to create the new member record.
 * @return mixed Array of details if the member record was created or didn't need to be, or null if member record could not be created.
 */
	public function registerMember($data) {
		if (!isset($data) || !is_array($data)) {
			return null;
		}

		if ( (isset($data['Member']) && isset($data['Member']['email'])) == false ) {
			return null;
		}

		$this->set($data);

		// Need to validate only the e-mail
		if ( !$this->validates( array( 'fieldList' => array( 'email' ) ) ) ) {
			// Failed
			return null;
		}

		// Grab the e-mail
		$email = $data['Member']['email'];

		// Start to build the result data
		$resultDetails = array();
		$resultDetails['email'] = $email;

		// Do we already know about this e-mail?
		$memberInfo = $this->findByEmail( $email );

		// Find returns an empty array for no results
		$newMember = count($memberInfo) == 0;
		$resultDetails['createdRecord'] = $newMember;

		$memberId = -1;
		if ($newMember) {
			$memberInfo = $this->createNewMemberInfo( $email );
			$memberInfo['MailingLists'] = Hash::get($data, 'MailingLists');

			// If the user ticked no boxes, the $memberInfo['MailingLists']['MailingLists']
			// might not be an array
			if (!is_array($memberInfo['MailingLists']['MailingLists'])) {
				$memberInfo['MailingLists']['MailingLists'] = array();
			}

			$this->set( $memberInfo );
			if ( $this->validates( array( 'Member' => array('member_id', 'email', 'member_status' ) ) ) ) {
				// If this e-mail is already subscribed to any mailing list
				// but hasn't checked the box for that mailing list, pretend they have.
				// Otherwise they will get unsubscribed from said mailing list

				$mailingList = $this->getMailingList();
				$listAndStatus = $mailingList->getListsAndSubscribeStatus($email, false);

				foreach ($listAndStatus['data'] as $list) {
					// Are they actually subscribed to this list?
					if ($list['subscribed']) {
						// Have they ticked the box for that list?
						if (!in_array($list['id'], $memberInfo['MailingLists']['MailingLists'])) {
							// Nope, add it to the list
							array_push($memberInfo['MailingLists']['MailingLists'], $list['id']);
						}
					}
				}

				$saveResult = $this->__saveMemberData( $memberInfo, array( 'Member' => array('member_id', 'email', 'member_status', 'join_date' ), 'MailingLists' => array()), 0);
				if ( !is_array($saveResult) ) {
					// Save failed for reasons.
					return null;
				}

				$resultDetails['mailingLists'] = Hash::get($saveResult, 'mailingLists');

				$memberId = $this->id;
			}
		} else {
			$memberId = Hash::get($memberInfo, 'Member.member_id');
		}

		$resultDetails['status'] = (int)$this->getStatusForMember( $memberInfo );
		$resultDetails['memberId'] = $memberId;

		// Success!
		return $resultDetails;
	}

/**
 * Attempt to set-up login details for a member.
 *
 * @param int $memberId The id of the member to set-up the login details for.
 * @param array $data The data to use.
 * @return bool True on success, otherwise false.
 * @throws InvalidStatusException if the member is not a prospective member.
 */
	public function setupLogin($memberId, $data) {
		if (!isset($memberId) || $memberId <= 0) {
			return false;
		}

		$memberStatus = $this->getStatusForMember( $memberId );
		if ($memberStatus == 0) {
			return false;
		}

		if ($memberStatus != Status::PROSPECTIVE_MEMBER ) {
			throw new InvalidStatusException( 'Member does not have status: ' . Status::PROSPECTIVE_MEMBER );
		}

		if (!isset($data) || !is_array($data)) {
			return false;
		}

		if ((isset($data['Member']) &&
			isset($data['Member']['firstname']) &&
			isset($data['Member']['surname']) &&
			isset($data['Member']['username']) &&
			isset($data['Member']['email']) &&
			isset($data['Member']['password']) ) == false ) {
			return false;
		}

		$memberInfo = $this->find('first', array('conditions' => array('Member.member_id' => $memberId)));
		if (!$memberInfo) {
			return false;
		}

		// Merge all the data
		$hardcodedData = array(
			'member_status' => Status::PRE_MEMBER_1,
		);
		unset($data['Member']['member_id']);
		$dataToSave = array('Member' => Hash::merge($memberInfo['Member'], $data['Member'], $hardcodedData));

		$this->set($dataToSave);

		$this->addEmailMustMatch();

		$saveOk = false;
		if ($this->validates(array( 'fieldList' => array('member_id', 'firstname', 'surname', 'username', 'email', 'password', 'password_confirm', 'member_status')))) {
			$saveOk = is_array($this->__saveMemberData($dataToSave, array('Member' => array('member_id', 'firstname', 'surname', 'username', 'member_status')), $memberId));
		}

		$this->removeEmailMustMatch();

		return $saveOk;
	}

/**
 * Attempt to set-up contact details for a member.
 *
 * @param int $memberId The id of the member to set-up the contact details for.
 * @param array $data The data to use.
 * @return bool True on success, otherwise false.
 * @throws InvalidStatusException if the member is not a pre-member stage 1.
 */
	public function setupDetails($memberId, $data) {
		if (!isset($memberId) || $memberId <= 0) {
			return false;
		}

		$memberStatus = $this->getStatusForMember( $memberId );
		if ($memberStatus == 0) {
			return false;
		}

		if ($memberStatus != Status::PRE_MEMBER_1 ) {
			throw new InvalidStatusException( 'Member does not have status: ' . Status::PRE_MEMBER_1 );
		}

		if (!isset($data) || !is_array($data)) {
			return false;
		}

		if ((isset($data['Member']) &&
			isset($data['Member']['address_1']) &&
			isset($data['Member']['address_city']) &&
			isset($data['Member']['address_postcode']) &&
			isset($data['Member']['contact_number']) ) == false ) {
			return false;
		}

		$memberInfo = $this->find('first', array('conditions' => array('Member.member_id' => $memberId)));
		if (!$memberInfo) {
			return false;
		}

		$hardcodedData = array(
			'member_status' => Status::PRE_MEMBER_2,
		);
		unset($data['Member']['member_id']);
		$dataToSave = array('Member' => Hash::merge($memberInfo['Member'], $data['Member'], $hardcodedData));

		$this->set($dataToSave);

		if ($this->validates(array( 'fieldList' => array('member_id', 'address_1', 'address_2', 'address_city', 'address_postcode', 'contact_number', 'member_status'))) ) {
			return is_array($this->__saveMemberData(
				$dataToSave,
				array('Member' =>
					array(
						'member_id',
						'address_1',
						'address_2',
						'address_city',
						'address_postcode',
						'contact_number',
						'member_status'
					)
				),
				$memberId)
			);
		}

		return false;
	}

/**
 * Mark a members contact details as invalid.
 *
 * @param int $memberId The id of the member.
 * @param array $data The data to use.
 * @param int $adminId The id of the member admin who is rejecting the details.
 * @return bool True if the members data was altered successfully, false otherwise.
 * @throws InvalidStatusException if the member is not a pre-member stage 2.
 */
	public function rejectDetails($memberId, $data, $adminId) {
		// Need some extra validation
		$memberEmail = ClassRegistry::init('MemberEmail');

		if (!isset($memberId) || $memberId <= 0) {
			return false;
		}

		if (!isset($adminId) || $adminId <= 0) {
			return false;
		}

		$memberStatus = $this->getStatusForMember( $memberId );
		if ($memberStatus == 0) {
			return false;
		}

		if ($memberStatus != Status::PRE_MEMBER_2 ) {
			throw new InvalidStatusException( 'Member does not have status: ' . Status::PRE_MEMBER_2 );
		}

		if (!isset($data) || !is_array($data)) {
			return false;
		}

		if ((isset($data['MemberEmail']) &&
			isset($data['MemberEmail']['message']) ) == false ) {
			return false;
		}

		$memberInfo = $this->find('first', array('conditions' => array('Member.member_id' => $memberId)));
		if (!$memberInfo) {
			return false;
		}

		$hardcodedData = array(
			'member_status' => Status::PRE_MEMBER_1,
		);
		$dataToSave = array('Member' => Hash::merge($memberInfo['Member'], $hardcodedData));

		$this->set($dataToSave);

		if ( $memberEmail->validates( array( 'fieldList' => array( 'body' ) ) ) ) {
			return is_array($this->__saveMemberData($dataToSave, array('Member' => array('member_id', 'member_status')), $adminId));
		}
	}

/**
 * Mark a members details as valid.
 *
 * @param int $memberId The id of the member who's details we want to mark as valid.
 * @param array $data The account data to use.
 * @param int $adminId The id of the member admin who's accepting the details.
 * @return mixed Array of member details on success, or null on failure.
 * @throws InvalidStatusException if the member is not a pre-member stage 2.
 */
	public function acceptDetails($memberId, $data, $adminId) {
		if (!isset($memberId) || $memberId <= 0) {
			return null;
		}

		if (!isset($adminId) || $adminId <= 0) {
			return false;
		}

		$memberStatus = $this->getStatusForMember( $memberId );
		if ($memberStatus == 0) {
			return null;
		}

		if ($memberStatus != Status::PRE_MEMBER_2 ) {
			throw new InvalidStatusException( 'Member does not have status: ' . Status::PRE_MEMBER_2 );
		}

		if (!isset($data) || !is_array($data)) {
			return null;
		}

		if ((isset($data['Account']) &&
			isset($data['Account']['account_id']) ) == false ) {
			return null;
		}

		$memberInfo = $this->find('first', array('conditions' => array('Member.member_id' => $memberId)));
		if (!$memberInfo) {
			return null;
		}

		$hardcodedData = array(
			'member_status' => Status::PRE_MEMBER_3,
		);
		unset($data['Member']);
		$dataToSave = array('Member' => Hash::merge($memberInfo['Member'], $hardcodedData));

		$dataToSave = Hash::merge($dataToSave, $data);

		$this->set($dataToSave);

		if ( is_array($this->__saveMemberData($dataToSave, array('Member' => array( 'member_id', 'member_status', 'account_id' ), 'Account' => 'account_id'), $adminId)) ) {
			return $this->getSoDetails($memberId);
		}

		return null;
	}

/**
 * Approve a member, making them a current member.
 *
 * @param int $memberId The id of the member to approve.
 * @param int $adminId The id of the member admin who is approving the member.
 * @return mixed Array of member details on success, or null on failure.
 * @throws InvalidStatusException if the member is not a pre-member stage 3.
 */
	public function approveMember($memberId, $adminId) {
		if (!isset($memberId) || $memberId <= 0) {
			return null;
		}

		$memberStatus = $this->getStatusForMember( $memberId );
		if ($memberStatus == 0) {
			return null;
		}

		if ($memberStatus != Status::PRE_MEMBER_3 &&  $memberStatus != Status::EX_MEMBER) {
			throw new InvalidStatusException( 'Member does not have a valid status to approve them');
		}

		$memberInfo = $this->find('first', array('conditions' => array('Member.member_id' => $memberId)));
		if (!$memberInfo) {
			return null;
		}

		$dataSource = $this->getDataSource();
		$dataSource->begin();

		// has this member already got a pin?
		$createPin = true;
		if (count($this->Pin->find('first', array('conditions' => array('Pin.member_id' => $memberId)))) > 0) {
			$createPin = false;
		}

		// create one if not
		if ($createPin === true) {

			if ( !$this->Pin->createNewRecord($memberId) ) {
				$dataSource->rollback();
				return null;
			}
		}

		$hardcodedMemberData = array(
			'member_status' => Status::CURRENT_MEMBER,
			'unlock_text' => 'Welcome ' . $memberInfo['Member']['firstname'],
			'credit_limit' => Member::INITAL_CREDIT_LIMIT,
			'join_date' => date( 'Y-m-d' ),
		);
		$dataToSave = array('Member' => Hash::merge($memberInfo['Member'], $hardcodedMemberData));

		$this->set($dataToSave);

		$fieldsToSave = array(
			'Member' => array(
				'member_id',
				'member_status',
				'unlock_text',
				'credit_limit',
				'join_date'
			)
		);

		if ($createPin == true) {
			$fieldsToSave['Pin'] = array(
				'unlock_text',
				'pin',
				'state',
				'member_id'
			);
		}

		if ( is_array($this->__saveMemberData($dataToSave, $fieldsToSave, $adminId)) ) {
			$approveDetails = $this->getApproveDetails($memberId);
			if ($approveDetails) {
				$dataSource->commit();
				return $approveDetails;
			}
		}

		return null;
	}

/**
 * Change a users password.
 *
 * @param int $memberId The id of the member whose password is being changed.
 * @param int $adminId The id of the member who is changing the password.
 * @param array $data The array of password data.
 * @throws InvalidStatusException if the member is a prospective member.
 * @throws NotAuthorizedException if the member specified by $adminId doesn't have permission to do this.
 */
	public function changePassword($memberId, $adminId, $data) {
		// Need some extra validation
		$changePasswordModel = ClassRegistry::init('ChangePassword');

		if (!isset($memberId) || $memberId <= 0) {
			return false;
		}

		if (!isset($adminId) || $adminId <= 0) {
			return false;
		}

		$memberStatus = $this->getStatusForMember( $memberId );
		if ($memberStatus == 0) {
			return false;
		}

		if ($memberStatus == Status::PROSPECTIVE_MEMBER ) {
			throw new InvalidStatusException( 'Member has status: ' . Status::PROSPECTIVE_MEMBER );
		}

		if ($memberId != $adminId &&
			!($this->GroupsMember->isMemberInGroup($adminId, Group::MEMBERSHIP_ADMIN) || $this->GroupsMember->isMemberInGroup($adminId, Group::FULL_ACCESS))) {
			throw new NotAuthorizedException('Only member admins can change another members password.');
		}

		if (!isset($data) || !is_array($data)) {
			return false;
		}

		if ((isset($data['ChangePassword']) &&
			isset($data['ChangePassword']['current_password']) &&
			isset($data['ChangePassword']['new_password']) &&
			isset($data['ChangePassword']['new_password_confirm']) ) == false ) {
			return false;
		}

		$changePasswordModel->set($data);
		if (!$changePasswordModel->validates()) {
			return false;
		}

		$passwordToCheckMember = $this->find('first', array('conditions' => array('Member.member_id' => $adminId)));
		if (!$passwordToCheckMember) {
			return false;
		}

		$passwordToSetMember = ($adminId === $memberId) ? $passwordToCheckMember : $this->find('first', array('conditions' => array('Member.member_id' => $memberId)));

		if (!$passwordToSetMember) {
			return false;
		}

		if ($this->krbCheckPassword(Hash::get($passwordToCheckMember, 'Member.username'), Hash::get($data, 'ChangePassword.current_password'))) {
			return $this->krbChangePassword(Hash::get($passwordToSetMember, 'Member.username'), Hash::get($data, 'ChangePassword.new_password'));
		}

		return false;
	}

/**
 * Generate a forgot password request from an e-mail.
 *
 * @param array $data Array of data containing the user submitted e-mail.
 * @return mixed An array of id and email data if creation succeeded, false otherwise.
 * @throws InvalidStatusException if the member is a prospective member.
 */
	public function createForgotPassword($data) {
		// Need some extra validation
		$forgotPasswordModel = ClassRegistry::init('ForgotPassword');

		if (!isset($data) || !is_array($data)) {
			return false;
		}

		if ((isset($data['ForgotPassword']) &&
			isset($data['ForgotPassword']['email']) ) == false ) {
			return false;
		}

		if (isset($data['ForgotPassword']['new_password']) ||
			isset($data['ForgotPassword']['new_password_confirm']) ) {
			return false;
		}

		$emailAddress = Hash::get($data, 'ForgotPassword.email');

		$memberInfo = $this->find('first', array('conditions' => array('Member.email' => $emailAddress), 'fields' => array('Member.member_id', 'Member.member_status')));
		if ($memberInfo) {
			$memberStatus = $this->getStatusForMember( $memberInfo );
			if ($memberStatus == 0) {
				return false;
			}

			if ($memberStatus == Status::PROSPECTIVE_MEMBER ) {
				throw new InvalidStatusException( 'Member has status: ' . Status::PROSPECTIVE_MEMBER );
			}

			$guid = $forgotPasswordModel->createNewEntry(Hash::get($memberInfo, 'Member.member_id'));
			if ($guid != null) {
				return array('id' => $guid, 'email' => $emailAddress);
			}
		}

		return false;
	}

/**
 * Complete a forgot password request
 *
 * @param string $guid The id of the forgot password request.
 * @param array $data Array of data containing the user submitted e-mail.
 * @return bool True if password was changed, false otherwise.
 */
	public function completeForgotPassword($guid, $data) {
		if (!ForgotPassword::isValidGuid($guid)) {
			return false;
		}

		// Need some extra validation
		$forgotPasswordModel = ClassRegistry::init('ForgotPassword');

		if (!isset($data) || !is_array($data)) {
			return false;
		}

		if ((isset($data['ForgotPassword']) &&
			isset($data['ForgotPassword']['email']) &&
			isset($data['ForgotPassword']['new_password']) &&
			isset($data['ForgotPassword']['new_password_confirm'])) == false ) {
			return false;
		}

		$forgotPasswordModel->set($data);
		if ($forgotPasswordModel->validates()) {
			$emailAddress = Hash::get($data, 'ForgotPassword.email');

			$memberInfo = $this->find('first', array('conditions' => array('Member.email' => $emailAddress), 'fields' => array('Member.member_id')));
			if ($memberInfo) {
				$memberId = $this->getIdForMember($memberInfo);
				if ($memberId > 0 && $forgotPasswordModel->isEntryValid($guid, $memberId)) {
					$username = $this->getUsernameForMember($memberId);
					if ($username) {
						$password = Hash::get($data, 'ForgotPassword.new_password');

						$dataSource = $this->getDataSource();
						$dataSource->begin();

						if (($this->__setPassword($username, $password, true) &&
							$forgotPasswordModel->expireEntry($guid)) ) {
							$dataSource->commit();
							return true;
						}

						$dataSource->rollback();
						return false;
					}
				}
			}
		}
		return false;
	}

/**
 * Update all the updatable info for a member.
 *
 * @param int $memberId The id of the member to update.
 * @param array $data The array of new data.
 * @param int $adminId The id of the member who is updating the details.
 * @return bool True if member details were updated ok, false otherwise.
 */
	public function updateDetails($memberId, $data, $adminId) {
		if (!is_numeric($memberId) || $memberId <= 0) {
			return false;
		}

		if (!is_array($data) || empty($data)) {
			return false;
		}

		$data['Member']['member_id'] = $memberId;

		$fieldsToSave = array(
			'Member' => array(
				'member_id',
				'firstname',
				'surname',
				'username',
				'email',
				'unlock_text',
				'account_id',
				'address_1',
				'address_2',
				'address_city',
				'address_postcode',
				'contact_number',
			),
			'GroupsMember' => array(
				'member_id',
				'grp_id',
			),
			'MailingLists' => array(
			),
		);
		return $this->__saveMemberData($data, $fieldsToSave, $adminId);
	}

/**
 * Get a members name, email and payment ref.
 *
 * @param int $memberId The id of the member to get the details for.
 * @return mixed Array of info on success, null on failure.
 */
	public function getSoDetails($memberId) {
		$memberInfo = $this->find('first', array('conditions' => array('Member.member_id' => $memberId)));
		if ($memberInfo) {
			$firstname = Hash::get($memberInfo, 'Member.firstname');
			$surname = Hash::get($memberInfo, 'Member.surname');
			$email = Hash::get($memberInfo, 'Member.email');
			$paymentRef = Hash::get($memberInfo, 'Account.payment_ref');

			if (isset($firstname) &&
				isset($surname) &&
				isset($email) &&
				isset($paymentRef)) {
				return array(
					'firstname' => $firstname,
					'surname' => $surname,
					'email' => $email,
					'paymentRef' => $paymentRef,
				);
			}
		}
		return null;
	}

/**
 * Get a members name, email and pin.
 *
 * @param int $memberId The id of the member to get the details for.
 * @return mixed Array of info on success, null on failure.
 */
	public function getApproveDetails($memberId) {
		$memberInfo = $this->find('first', array('conditions' => array('Member.member_id' => $memberId)));

		if ($memberInfo) {
			$firstname = Hash::get($memberInfo, 'Member.firstname');
			$surname = Hash::get($memberInfo, 'Member.surname');
			$email = Hash::get($memberInfo, 'Member.email');
			$pin = Hash::get($memberInfo, 'Pin.0.pin');

			if (isset($firstname) &&
				isset($surname) &&
				isset($email) &&
				isset($pin)) {
				return array(
					'firstname' => $firstname,
					'surname' => $surname,
					'email' => $email,
					'pin' => $pin,
				);
			}
		}
		return null;
	}

/**
 * Get a list of account and member details that is suitable for populating a drop-down box
 *
 * @return null List of values on success, null on failure.
 */
	public function getReadableAccountList() {
		$memberList = $this->find('all', array(
			'fields' => array('Member.member_id', 'Member.firstname', 'Member.surname', 'Member.account_id'),
			'order' => array('Member.account_id ASC'),
			'conditions' => array('Member.account_id !=' => null)
			)
		);

		// Group the member list by the account id
		$groupedMemberList = Hash::combine($memberList, '{n}.Member.member_id', '{n}.Member', '{n}.Member.account_id');

		$accountList = array();
		foreach ($groupedMemberList as $accountId => $members) {
			$memberNames = array();
			foreach (Hash::sort($members, '{n}.surname', 'asc') as $id => $data) {
				$fullName = sprintf('%s %s', $data['firstname'], $data['surname']);
				array_push($memberNames, $fullName);
			}

			$accountList[$accountId] = CakeText::toList($memberNames);
		}
		$accountList['-1'] = 'Create new';
		ksort($accountList);

		return $accountList;
	}

/**
 * Revoke a members membership.
 *
 * @param int $memberId The id of the membership to revoke.
 * @param int $adminId The id of the member doing the revoking.
 * @return bool True if membership was revoked, false otherwise.
 */
	public function revokeMembership($memberId, $adminId) {
        // TODO: remove from Group::CURRENT_MEMBERS
		return $this->__setMemberStatus($memberId, $adminId, Status::EX_MEMBER, Status::CURRENT_MEMBER);
	}

/**
 * Reinstate an ex-members membership.
 *
 * @param int $memberId The id of the membership to reinstate.
 * @param int $adminId The id of the member doing the reinstating.
 * @return bool True if membership was reinstated, false otherwise.
 */
	public function reinstateMembership($memberId, $adminId) {
        // TODO: re-add to Group::CURRENT_MEMBERS
		return $this->__setMemberStatus($memberId, $adminId, Status::CURRENT_MEMBER, Status::EX_MEMBER);
	}

/**
 * Set a members member_status.
 *
 * @param int $memberId The id of the member to change the status of.
 * @param int $adminId The id of the member doing then changing.
 * @param int $newStatus The new member_status.
 * @param int $requiredCurrentStatus The status the member must currently have.
 * @return bool True if status was set, otherwise false.
 * @throws InvalidStatusException if the members status does not match $requiredCurrentStatus.
 * @throws NotAuthorizedException if the member specified by $adminId doesn't have permission to do this.
 */
	private function __setMemberStatus($memberId, $adminId, $newStatus, $requiredCurrentStatus) {
		if (!is_numeric($memberId) ||
			!is_numeric($adminId) ||
			!is_numeric($newStatus) ||
			!is_numeric($requiredCurrentStatus)) {
			return false;
		}

		if (	!($this->GroupsMember->isMemberInGroup($adminId, Group::MEMBERSHIP_ADMIN) || $this->GroupsMember->isMemberInGroup($adminId, Group::FULL_ACCESS))) {
			throw new NotAuthorizedException('Only member admins can change member status.');
		}

		$memberStatus = $this->getStatusForMember( $memberId );
		if ($memberStatus != $requiredCurrentStatus ) {
			throw new InvalidStatusException( 'Member doesn\'t have the correct status.' );
		}

		$data = array(
			'Member' => array(
				'member_id' => $memberId,
				'member_status' => $newStatus,
			),
		);

		$fieldsToSave = array(
			'Member' => array(
				'member_id',
				'member_status'
			),
		);

		return is_array($this->__saveMemberData($data, $fieldsToSave, $adminId));
	}

/**
 * Validate that e-mail data is ok.
 *
 * @param array $data The data to validate.
 * @return mixed Array of e-mail data if $data is valid, false otherwise.
 */
	public function validateEmail($data) {
		if (is_array($data) &&
			isset($data['MemberEmail']) &&
			isset($data['MemberEmail']['subject']) &&
			isset($data['MemberEmail']['message']) ) {

			$emailModel = ClassRegistry::init('MemberEmail');
			$emailModel->set($data);

			if ($emailModel->validates($data)) {
				return array('subject' => Hash::get($data, 'MemberEmail.subject'), 'message' => Hash::get($data, 'MemberEmail.message'));
			}
		}
		return false;
	}

/**
 * Sanitise an array of member info, removing certain fields.
 *
 * @param array $memberInfo The array of member info to sanitise.
 * @param bool $showAdminFeatures If true then all data should be shown.
 * @param bool $showFinances If true then finance data should be shown.
 * @param bool $hasJoined If true then show data that is only relevant to members who have joined.
 * @param bool $showAccount If true then show account info.
 * @param bool $showStatus If true then show member status.
 * @param bool $showPersonalDetails If true then show member personal details.
 * @return mixed The array of sanitised member info, or false on error.
 */
	public function sanitiseMemberInfo($memberInfo, $showAdminFeatures, $showFinances, $hasJoined, $showAccount, $showStatus, $showPersonalDetails) {
		if (is_array($memberInfo) && !empty($memberInfo)) {

			// Hide things they shouldn't be seeing
			if (!$showAdminFeatures) {
				unset($memberInfo['Pin']);
				unset($memberInfo['StatusUpdate']);
				unset($memberInfo['Group']);
			}

			if (!$showFinances) {
				unset($memberInfo['Member']['balance']);
				unset($memberInfo['Member']['credit_limit']);
			}

			if (!$hasJoined) {
				unset($memberInfo['Member']['join_date']);
				unset($memberInfo['Member']['unlock_text']);
			}

			if (!$showAccount) {
				unset($memberInfo['Member']['account_id']);
				unset($memberInfo['Account']);
			}

			if (!$showAdminFeatures || !$showStatus) {
				unset($memberInfo['Status']);
				unset($memberInfo['Member']['member_status']);
			}

			if (!$showPersonalDetails) {
				unset($memberInfo['Member']['address_1']);
				unset($memberInfo['Member']['address_2']);
				unset($memberInfo['Member']['address_city']);
				unset($memberInfo['Member']['address_postcode']);
				unset($memberInfo['Member']['contact_number']);
			}

			$unsetIfNull = array('username', 'firstname', 'surname', 'account_id', 'contact_number', 'address_1', 'address_2', 'address_city', 'address_postcode', 'contact_number');
			foreach ($unsetIfNull as $index) {
				if (array_key_exists($index, $memberInfo['Member']) &&
					!isset($memberInfo['Member'][$index]) ) {
					unset($memberInfo['Member'][$index]);
				}
			}

			return $memberInfo;
		}
		return false;
	}

/**
 * Create or save a member record, and all associated data.
 *
 * @param array $memberInfo The information to use to create or update the member record.
 * @param array $fields The fields that should be saved.
 * @param int $adminId The id of the member who is making the change that needs saving.
 * @return mixed Array of result data if save succeed, false otherwise.
 */
	private function __saveMemberData($memberInfo, $fields, $adminId) {
		$result = array();

		$dataSource = $this->getDataSource();
		$dataSource->begin();

		$memberId = Hash::get( $memberInfo, 'Member.member_id' );

		// If the member already exists, sort out the groups
		$oldStatus = 0;
		$newStatus = (int)$this->getStatusForMember( $memberInfo );
		if ($memberId != null) {
			$oldStatus = (int)$this->getStatusForMember( $memberId );

			$newGroups = array( 'Group' );
			if ( $newStatus != Status::CURRENT_MEMBER ) {
				$newGroups['Group'] = array();
			} else {
				// Get a list of existing groups this member is a part of
				// Maybe from the data to be saved, maybe from the existing member data...
				$existingGroups = array();
				if (isset($fields['GroupsMember']) &&
					isset($memberInfo['Group']) &&
					isset($memberInfo['Group']['Group']) ) {
					$fields['Group'] = array();
					if (!is_array($memberInfo['Group']['Group'])) {
						// Someone has attempted to wipe all groups
						$existingGroups = array();
					} else {
						// Group data is coming in..
						foreach ($memberInfo['Group']['Group'] as $key => $value) {
							array_push($existingGroups, $value);
						}
					}
				} else {
					// We'll need to save it now
					$fields['Group'] = array();
					$fields['GroupsMember'] = array('member_id', 'grp_id');

					// Use the groups currently associated with this member
					$existingGroups = $this->GroupsMember->getGroupIdsForMember( $memberId );
				}

                // TODO: fix this nasty hack so we can remove ex members from Group::CURRENT_MEMBERS
				if (!in_array(Group::CURRENT_MEMBERS, $existingGroups)) {
					array_push($existingGroups, Group::CURRENT_MEMBERS);
				}

				$groupIdx = 0;
				foreach ($existingGroups as $group) {
					$newGroups['Group'][$groupIdx] = $group;
					$groupIdx++;
				}
			}

			$memberInfo['Group'] = $newGroups;

			// Do we have to change the password?
			if (isset($memberInfo['Member']['username']) &&
				isset($memberInfo['Member']['password'])) {
				$username = Hash::get($memberInfo, 'Member.username');
				$password = Hash::get($memberInfo, 'Member.password');

				if (!$this->__setPassword($username, $password, true)) {
					$dataSource->rollback();
					return false;
				}
			}
		} else {
			$this->Create();
		}

		// Do we want to be saving an account id?
		if (isset($fields['Member']) &&
			is_array($fields['Member']) &&
			in_array('account_id', $fields['Member'])) {
			// Attempt to get the account id we're meant to be saving, try from the account field first.
			$accountId = Hash::get($memberInfo, 'Account.account_id');

			if (!isset($accountId)) {
				// Try the member field?
				$accountId = Hash::get($memberInfo, 'Member.account_id');
			}

			// Do we actually have an account id?
			if (isset($accountId)) {
				// Check with the Account model if this account exists or not
				// should return the id of the account we should be saving
				$accountId = $this->Account->setupAccountIfNeeded($accountId);
				if ($accountId < 0) {
					// Either account creation failed or account does not exist.
					$dataSource->rollback();
					return false;
				}

				// Set the account id in the member info, as it may have changed.
				$memberInfo = Hash::insert($memberInfo, 'Member.account_id', $accountId);
			}
		}

		unset($memberInfo['Account']);

		$safeMemberInfo = array();
		// Need to unset anything in the $memberInfo that's not in $fields.
		foreach ($fields as $allowedModel => $allowedFields) {
			if (is_array($allowedFields)) {
				if (isset($memberInfo[$allowedModel])) {
					// If the field entry array is empty, then all the children of that are valid
					if (empty($allowedFields)) {
						$safeMemberInfo[$allowedModel] = $memberInfo[$allowedModel];
					} else {
						$validValues = array();

						// Copy any 'safe' values
						foreach ($allowedFields as $allowedField) {
							if (isset($memberInfo[$allowedModel][$allowedField])) {
								$validValues[$allowedField] = $memberInfo[$allowedModel][$allowedField];
							}
						}

						// Don't bother adding it to the safe member info if it's empty
						if (!empty($validValues)) {
							$safeMemberInfo[$allowedModel] = $validValues;
						}
					}
				}
			}
		}

		$mailingListsInData = array_key_exists('MailingLists', $memberInfo) &&
								array_key_exists('MailingLists', $memberInfo['MailingLists']);

		$mailingLists = array();
		if ($mailingListsInData) {
			$mailingLists = Hash::get($memberInfo, 'MailingLists.MailingLists');
			// $mailingLists will be an empty string if no mailing lists are selected
			if (is_string($mailingLists)) {
				$mailingLists = array();
			}
		}

		$memberEmail = $this->getEmailForMember($memberInfo);

		if ($mailingListsInData && $memberEmail) {
			$mailingList = $this->getMailingList();
			$result['mailingLists'] = $mailingList->updateSubscriptions( $memberEmail, $mailingLists );
		}

		if ( !$this->saveAll( $safeMemberInfo, array( 'fieldList' => $fields )) ) {
			$dataSource->rollback();
			return false;
		}

		// Do we need to create a status update record?
		if ($newStatus != $oldStatus) {
			// If $memberId is null then we've just created this member.
			if ($memberId == null) {
				$memberId = $this->id;
			}

			// Admin id of 0 means the member is making the change themselves
			if ($adminId === 0) {
				$adminId = $memberId;
			}

			if (!$this->StatusUpdate->createNewRecord( $memberId, $adminId, $oldStatus, $newStatus )) {
				$dataSource->rollback();
				return false;
			}
		}

		// We're good
		$dataSource->commit();
		return $result;
	}

/**
 * Get a MailingList model.
 *
 * @return MailingList The MailingList model.
 */
	public function getMailingList() {
		if ($this->mailingList == null) {
			$this->mailingList = ClassRegistry::init('MailingList');
		}
		return $this->mailingList;
	}

/**
 * Given either a single e-mail or an array of e-mails, return a member id or array of member ids.
 *
 * @param mixed $email Either a single e-mail address or an array of e-mail addresses.
 * @return mixed If $email is a single e-mail, returns the member id of the record matching that e-mail (or null if none can be found).
 *               If $email is an array of e-mail addresses, return an array of all matching member ids that can be found.
 *	             Returns null on error.
 */
	public function emailToMemberId($email) {
		if (is_array($email) || is_string($email)) {
			$records = $this->find('list', array( 'fields' => array('Member.member_id', 'Member.email'), 'conditions' => array('Member.email' => $email) ));

			if (is_array($records) && count($records) > 0) {

				$idList = array_keys($records);
				if (count($idList) == 1) {
					return $idList[0];
				}
				return $idList;
			}
		}
		return null;
	}

/**
 *  Get the balance for a member, may hit the database.
 *
 * @param mixed $memberData If array, assumed to be an array of member info in the same format that is returned from database queries, otherwise assumed to be a member id.
 * @return int The balance for the member, or null if balaance could not be found.
 */
		public function getBalanceForMember($memberData) {
			if (!isset($memberData)) {
				return null;
			}

			if (is_array($memberData)) {
				$balance = Hash::get($memberData, 'Member.balance');
				if (isset($balance)) {
					return $balance;
				} else {
					$memberData = Hash::get($memberData, 'Member.member_id');
				}
			}

			$memberInfo = $this->find('first', array('fields' => array('Member.balance'), 'conditions' => array('Member.member_id' => $memberData) ));
			if (is_array($memberInfo)) {
				$balance = Hash::get($memberInfo, 'Member.balance');
				if (isset($balance)) {
					return $balance;
				}
			}
			return null;
		}

/**
 *  Update the balance for a member, by amount given
 *
 * @param mixed $memberData If array, assumed to be an array of member info in the same format that is returned from database queries, otherwise assumed to be a member id.
 * @param int
 * @return bool
 */
		public function updateBalanceForMember($memberData, $amount) {
			if (!isset($memberData)) {
				return null;
			}

			if (!is_array($memberData)) {
                $memberInfo = $this->find('first', array('fields' => array('Member.balance'), 'conditions' => array('Member.member_id' => $memberData) ));
            } else {
                $memberInfo = array('Member' => array(
                                                      'member_id' => $memberData['Member']['member_id'],
                                                      'balance' => $memberData['Member']['balance'],
                                                      )
                                    );
            }

			if (is_array($memberInfo)) {
                $memberInfo['Member']['balance'] += $amount;

                $result = $this->save($memberInfo,
                                      $params = array(
                                                      'callbacks' => false,
                                                            'fieldList' => array('balance'),
                                                            )
                                      );
                if ($result) {
                    return true;
                }

			}
			return false;
		}

/**
 *  Get the account_id for a member, will hit the database.
 *
 * @param mixed $memberData If array, assumed to be an array of member info in the same format that is returned from database queries, otherwise assumed to be a member id.
 * @return int The account_id for the member, or null if account_id could not be found.
 */
		public function getAccountIdForMember($memberData) {
			if (!isset($memberData)) {
				return null;
			}

			if (is_array($memberData)) {
				$memberData = Hash::get($memberData, 'Member.member_id');
			}

            $memberInfo = $this->find('first', array(
                                                     'fields' => array('Member.account_id'),
                                                     'conditions' => array('Member.member_id' => $memberData),
                                                     'recursive' => -1,
                                                     )
                                      );

			if (is_array($memberInfo)) {
				$accountId = Hash::get($memberInfo, 'Member.account_id');
				if (isset($accountId)) {
					return $accountId;
				}
			}
			return null;
		}

/**
 * Get the member Id's for all members of this account
 *
 * @param int $accountId
 * @return bool true if this is a joint account
 */
	public function getMemberIdsForAccount($accountId) {
    	$ids = $this->find('all', array(
    		'fields' => array('Member.member_id'),
    		'conditions' => array('Member.account_id' => $accountId),
    		'recursive' => 0
    		)
    	);

    	if (is_array($ids) && count($ids) > 0) {
    		return Hash::extract($ids, '{n}.Member.member_id');
    		}
		return null;
	}

/**
 * Get a list of member names or e-mails (if we don't have their name) for all members.
 *
 * @return array Array of member info, indexed by member id.
 */
	public function getBestMemberNames() {
		$records = $this->find('all', array( 'fields' => array('Member.member_id', 'Member.firstname', 'Member.surname', 'Member.email')));

		$idAndBestName = array();

		foreach ($records as $record) {
			$id = Hash::get($record, 'Member.member_id');
			$firstname = Hash::get($record, 'Member.firstname');
			$surname = Hash::get($record, 'Member.surname');
			$email = Hash::get($record, 'Member.email');

			$bestName = $email;

			if (is_string($firstname) && strlen(trim($firstname)) > 0) {
				$bestName = trim("$firstname $surname");
			}

			$idAndBestName[$id] = $bestName;
		}

		return $idAndBestName;
	}

/**
 * Get a full name for a member.
 *
 * @param int $memberId
 * @return string Member Name.
 */
	public function getFullNameForMember($memberId) {
		$findOptions = array(
			'conditions' => array(
				'Member.member_id' => $memberId,
			),
			'fields' => array(
				'Member.member_id',
				'Member.firstname',
				'Member.surname'
			),
            'recursive' => -1,
		);
		$record = $this->find('first', $findOptions );

		$firstname = Hash::get($record, 'Member.firstname');
		$surname = Hash::get($record, 'Member.surname');

		return trim("$firstname $surname");
	}

/**
 * Set the password for the member, with the option to create a new password entry if needed.
 *
 * @param string $username The username of the member.
 * @param string $password The new password.
 * @param bool $allowCreate If true, will create a new auth record for a member that doesn't currently have one.
 * @return bool True if the password was set ok, false otherwise.
 */
	private function __setPassword($username, $password, $allowCreate) {
		switch ($this->krbUserExists($username)) {
			case true:
				return $this->krbChangePassword($username, $password);

			case false:
				return ($allowCreate && $this->krbAddUser($username, $password));

			default:
				return false;
		}
		return false;
	}

/**
 * Get a summary of the member records for all members that match the conditions.
 *
 * @param bool $paginate If true, just return the query for pagination instead of the data.
 * @param array $conditions An array of conditions to decide which member records to access.
 * @param bool $format If true format the data first, otherwise just return it in the same format as the datasource gives it us.
 * @return array A summary (id, name, email, Status and Groups) of the data of all members that match the conditions.
 */
	private function __getMemberSummary($paginate, $conditions = array(), $format = true) {
		$findOptions = array('conditions' => $conditions);

		if ($paginate) {
			return $findOptions;
		}

		$info = $this->find( 'all', $findOptions );

		if ($format) {
			return $this->formatMemberInfoList($info, false);
		}
		return $info;
	}
}
