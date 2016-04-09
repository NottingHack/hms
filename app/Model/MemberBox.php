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
 * Model for member box data
 */
class MemberBox extends AppModel {

/**
 * This box is considered active and being used
 */
	const BOX_INUSE = 10;

/**
 * Box has been removed from the hackspace
 */
	const BOX_REMOVED = 20;

/**
 * Box has been identified as abandoned and not beeing worked on
 */
	const BOX_ABANDONED = 30;

/**
 * String representation of states for display
 */
    public $statusStrings = array(
                                  10 => 'In Use',
                                  20 => 'Removed',
                                  30 => 'Abandoned',
                                  );

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'member_boxes';

/**
 * Specify the primary key.
 * @var string
 */
	public $primaryKey = 'member_box_id';

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
        'state' => array(
			'length' => array(
				'rule' => array('between', 1, 11),
				'message' => 'State must be between 1 and 11 characters long',
			),
			'content' => array(
				'rule' => 'numeric',
				'message' => 'State must be a number',
			),
		)
	);
    
/**
 * Get a single box and return formated or not
 *
 * @param int $memberBoxId
 * @param bool $format 
 * @return array
 */
    public function getBox($memberBoxId, $format = true) {
        $findOptions = array(
			'conditions' => array(
				'MemberBox.member_box_id' => $memberBoxId,
			),
			'fields' => array('MemberBox.*'),
		);

		$details = $this->find( 'first', $findOptions );

		if ($format) {
			return $this->formatDetails($details, false);
		}

		return $details;
    }

/** 
 * Get memberId for a box
 *
 * @param int $memberBoxId
 * @return int $memberId
 */
    public function getMemberIDforBox($memberBoxId) {
        $findOptions = array(
                             'conditions' => array(
                                                   'MemberBox.member_box_id' => $memberBoxId,
                                                   ),
                             'fields' => array('MemberBox.member_box_id', 'MemberBox.member_id'),
                             );
        
        $details = $this->find( 'first', $findOptions );
        
        return Hash::get($details ,'MemberBox.member_id');
    }
    
/**
 * Get a list of boxes for a member
 * 
 * @param bool $paginate If true, return a query to retrieve a page of the data, otherwise return the data.
 * @param array $conditions An array of conditions to decide which member records to access.
 * @return array A list of boxes or query to report a list of tags
 */
	public function getBoxesList($paginate, $conditions = array()) {
		$findOptions = array(
			'conditions' => $conditions,
			'fields' => array('MemberBox.*'),
		);

		if ($paginate) {
			return $findOptions;
		}

		$info = $this->find( 'all', $findOptions );

		return $info;
	}
    
/**
 * Format an array of boxes for the
 * 
 * @param array $boxesList The array of boxes.
 * @param bool $removeNullEntries If true then entries that have a value of null, false or an empty array won't exist in the final array.
 * @return array A list of formated boxes 
 */
    public function formatBoxesList($boxList, $removeNullEntries) {
        $formatted = array();
        foreach($boxList as $box) {
            array_push($formatted, $this->formatDetails($box, $removeNullEntries));
        }
        return $formatted;
    }

/**
 * Flatten box details array
 * 
 * @param array $box raw box record from the model
 * @param bool $removeNullEntries strips out null fields from the result
 * @return array Details for the tag serial number passed in $serial
 */
  public function formatDetails($box, $removeNullEntries = true) {
  	/*
  		Data should be presented to the view in an array like so:
        [memberBoxId] => box id
  		[memberId] => member id
        [broughtDate] => date box brought
        [removedDate] => removed date
  		[stateId] => state of card as used in the DB
  		[stateName] => description of the stateId value
  	*/
  		$formatted = array(
            'memberBoxId' => Hash::get($box, 'MemberBox.member_box_id'),
	  		'memberId' => Hash::get($box, 'MemberBox.member_id'),
	  		'broughtDate' => Hash::get($box, 'MemberBox.brought_date'),
            'removedDate' => Hash::get($box, 'MemberBox.removed_date'),
	  		'stateId' => Hash::get($box, 'MemberBox.state'),
	  		'stateName' => $this->statusStrings[Hash::get($box, 'MemberBox.state')],
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
 * create a new box record for a member id
 *
 * @param int $memberId
 * @return bool True is box created
 */
    public function newBoxForMember($memberId) {
        
        $box = array(
                         'MemberBox' => array(
                                                  'member_id' => $memberId,
                                                  'brought_date' => date('Y-m-d'),
                                                  'state' => MemberBox::BOX_INUSE
                                                  )
                         );
        $dataSource = $this->getDataSource();
        $dataSource->begin();
        
        $this->create();
        
        
        if (!$this->save($box)) {
            $dataSource->rollback();
            return false;
        }
        
        $dataSource->commit();
        return true;
    }

/**
 * Change state of a box
 *
 * @param int $memberBoxId
 * @param int $state
 * @retrun bool
 */
    public function changeStateForBox($memberBoxId, $state) {
        $completeDate = '';
        if ($state == MemberBox::BOX_REMOVED) {
            $removedDate = date( 'Y-m-d' );
        }
        $box = array(
                        'MemberBox' => array(
                                 'member_box_id' => $memberBoxId,
                                 'removed_date' => $removedDate,
                                 'state' => $state
                                 )
                        );
        
        return $this->save($box);
    }

/**
 * get box count for member
 *
 * @param int $memberId
 * @return int
 */
    public function boxCountForMember($memberId) {
        $findOptions = array(
                             'conditions' => array(
                                                   'MemberBox.member_id' => $memberId,
                                                   'MemberBox.state' => MemberBox::BOX_INUSE,
                                                   ),
                             );
        
        return $this->find('count', $findOptions);
    }

/**
 * get box count for space (BOX_INUSE)
 *
 * @return int
 */
    public function boxCountForSpace() {
        $findOptions = array(
                             'conditions' => array(
                                                   'MemberBox.state' => MemberBox::BOX_INUSE,
                                                   ),
                             );
        
        return $this->find('count', $findOptions);
    }
    
/**
 * get box count for member based on another box id
 *
 * @param int $memberId
 * @return int
 */
    public function boxCountForMemberByBox($memberBoxId) {
        $memberId = $this->getMemberIDforBox($memberBoxId);
        $findOptions = array(
                             'conditions' => array(
                                                   'MemberBox.member_id' => $memberId,
                                                   'MemberBox.state' => MemberBox::BOX_INUSE,
                                                   ),
                             );
        
        return $this->find('count', $findOptions);
    }
}
