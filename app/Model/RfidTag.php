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
 * Model for all rfid data
 */
class RfidTag extends AppModel {

/**
 * This RfidTag can be used for entry (up until the expiry date), cannot be used to register a card.
 */
	const STATE_ACTIVE = 10;

/**
 * RfidTag has expired and can no longer be used for entry.
 */
	const STATE_EXPIRED = 20;

/**
 * RfidTag has been lost and can no longer be used for entry.
 */
	const STATE_LOST = 30;

/**
 * String representation of states for display
 */
    public $statusStrings = array(
                                  10 => 'Active',
                                  20 => 'Expired',
                                  30 => 'Lost',
                                  );

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'rfid_tags';

/**
 * Specify the primary key.
 * @var string
 */
	public $primaryKey = 'rfid_id';

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
		'rfid_serial' => array(
			'length' => array(
				'rule' => array('between', 1, 50),
				'message' => 'Card serial number must be between 1 and 50 characters long',
			),
			'content' => array(
				'rule' => 'numeric',
				'message' => 'State must be a number',
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
		'friendly_name' => array(
			'length' => array(
				'rule' => array('between', 1, 128),
				'message' => 'Friendly name must be between 1 and 128 characters long; can be empty',
				'allowEmpty' => true,
			),
		)
	);

/**
 * Get a list of tags for a member
 * 
 * @param $paginate If true, return a query to retrieve a page of the data, otherwise return the data.
 * @param array $conditions An array of conditions to decide which member records to access.
 * @return array A list of tags or query to report a list of tags
 */
	public function getRfidTagList($paginate, $conditions = array()) {
		$findOptions = array(
			'conditions' => $conditions,
			'fields' => array('RfidTag.*'),

		);

		if ($paginate) {
			return $findOptions;
		}

		$info = $this->find( 'all', $findOptions );

		return $info;
	}

/**
 * Format an array of tags
 * 
 * @param array $tagsList The array of tags.
 * @param bool $removeNullEntries If true then entries that have a value of null, false or an empty array won't exist in the final array.
 * @return array A list of tags or query to report a list of tags
 */
    public function formatRfidTagList($tagsList, $removeNullEntries) {
        $formatted = array();
        foreach($tagsList as $tag) {
            array_push($formatted, $this->formatDetails($tag, $removeNullEntries));
        }
        return $formatted;
    }

/**
 * Flatten tag details array
 * 
 * @param array $details raw tag record from the model
 * @param bool $removeNullEntries strips out null fields from the result
 * @return array Details for the tag serial number passed in $serial
 */
  public function formatDetails($details, $removeNullEntries = true) {
  	/*
  		Data should be presented to the view in an array like so:
  		[memberId] => member id
  		[tagName] => friendly_name for card
  		[tagSerial] => rfid_serial
  		[lastSeen] => last_used
  		[stateId] => state of card as used in the DB
  		[stateName] => description of the stateId value
  	*/
        if (Hash::get($details, 'RfidTag.rfid_serial') == null) {
            $serial = Hash::get($details, 'RfidTag.rfid_serial_legacy');
        } else {
            $serial = Hash::get($details, 'RfidTag.rfid_serial');
        }
  		$formatted = array(
            'rfidId' => Hash::get($details, 'RfidTag.rfid_id'),
	  		'memberId' => Hash::get($details, 'RfidTag.member_id'),
	  		'tagName' => Hash::get($details, 'RfidTag.friendly_name'),
	  		'tagSerial' => $serial,
	  		'lastSeen' => Hash::get($details, 'RfidTag.last_used'),
	  		'stateId' => Hash::get($details, 'RfidTag.state'),
	  		'stateName' => $this->statusStrings[Hash::get($details, 'RfidTag.state')],
  		);

  		if (!$removeNullEntries) {
  			return $formatted;
  		}

  		$validValues = array();
  		foreach($formatted as $key => $value) {
  			if (isset($value) != false) {
  				$validValues[$key] = $value;
  			}
  		}

  		return $validValues;
  }

/**
 * Get details for a given tag serial number
 * 
 * @param $serial Serial number of the tag we want to retrieve data for
 * @param bool $format Determines if we're going to flatten out the results array or not
 * @return array Details for the tag serial number passed in $serial
 */
	public function getDetailsForTag($rfidId, $format = true) {
		$findOptions = array(
			'conditions' => array(
				'RfidTag.rfid_id' => $rfidId,
			),
			'fields' => array('RfidTag.*'),
		);

		$details = $this->find( 'first', $findOptions );

		if ($format) {
			return $this->formatDetails($details, false);
		}

		return $details;
	}

/**
 * Gets member id for a given tag serial number
 * 
 * @param $serial Serial number of the tag we want to retrieve data for
 * @return int|null member id associated with this serial or null if serial does not exist
 */
	public function getMemberIdForTag($rfidId) {

		$details = $this->getDetailsForTag($rfidId, false);

		if (is_array($details)) {
			return $details['RfidTag']['member_id'];
		}

		return null;
	}
}
