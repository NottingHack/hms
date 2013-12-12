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

/**
 * Model for all pin data
 */
class Pin extends AppModel {

/**
 * This pin can be used for entry (up until the expiry date), cannot be used to register a card.
 */
	const STATE_ACTIVE = 10;

/**
 * Pin has expired and can no longer be used for entry.
 */
	const STATE_EXPIRED = 20;

/**
 * This pin cannot be used for entry, and has likely been used to activate an RFID card.
 */
	const STATE_CANCELLED = 30;

/**
 * This pin may be used to enrol an RFID card.
 */
	const STATE_ENROLL = 40;

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'pins';

/**
 * Specify the primary key.
 * @var string
 */
	public $primaryKey = 'pin_id';

/**
 * Specify 'belongs to' associations.
 * @var array
 */
	public $belongsTo = array(
		'Member' => array(
			'className' => 'Member',
			'foreignKey' => 'member_id',
			'type' => 'inner'
		)
	);

/**
 * Validation rules.
 * @var array
 */
	public $validate = array(
		'pin' => array(
			'length' => array(
				'rule' => array('between', 1, 12),
				'message' => 'Pin must be between 1 and 12 characters long',
			),
		),
		'state' => array(
			'length' => array(
				'rule' => array('between', 1, 11),
				'message' => 'State must be between 1 and 11 characters long',
			),
			'content' => array(
				'rule' => 'numeric',
				'message' => 'State must be a number',
			),
		),
		'member_id' => array(
			'length' => array(
				'rule' => array('maxLength', 11),
				'message' => 'Member id must be no more than 11 characters long',
			),
			'content' => array(
				'rule' => 'numeric',
				'message' => 'Member id must be a number',
			),
		),
	);

/**
 * Generate a random pin.
 * 
 * @returnint A random pin.
 */
	public static function generatePin() {
		# Currently a PIN is a 4 digit number between 1000 and 9999
		return rand(1000, 9999);
	}

/**
 * Generate a unique (at the time this function was called) pin.
 * 
 * @returnint A random pin that was not in the database at the time this function was called.
 */
	public function generateUniquePin() {
		// A loop hiting the database? Why not...
		$pin = 0;
		do {
			$pin = Pin::generatePin();
		} while ( $this->find( 'count', array( 'conditions' => array( 'Pin.pin' => $pin ) ) ) > 0 );

		return $pin;
	}

/**
 * Create a new pin record
 * 
 * @param int $memberId The id of the member to create the pin for.
 * @return bool True if creation was successful, false otherwise.
 */
	public function createNewRecord($memberId) {
		if (is_numeric($memberId) && $memberId > 0) {

			$this->Create();

			$data = array( 'Pin' =>
				array(
					'pin' => $this->generateUniquePin(),
					'state' => Pin::STATE_ENROLL,
					'member_id' => $memberId,
					'date_added' => null,
				)
			);

			return ($this->save($data) != false);
		}
		return false;
	}
}