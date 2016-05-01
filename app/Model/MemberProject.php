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
 * Model for member project data
 */
class MemberProject extends AppModel {

/**
 * This projcet is considered active and being worked on
 */
	const PROJCET_ACTIVE = 10;

/**
 * Project has been finished/removed from the hackspace
 */
	const PROJCET_COMPLETE = 20;

/**
 * Project has been identified as abandoned and not beeing worked on
 */
	const PROJCET_ABANDONED = 30;

/**
 * String representation of states for display
 */
    public $statusStrings = array(
                                  10 => 'Active',
                                  20 => 'Complete',
                                  30 => 'Abandoned',
                                  );

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'member_projects';

/**
 * Specify the primary key.
 * @var string
 */
	public $primaryKey = 'member_project_id';

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
		'project_name' => array(
			'length' => array(
				'rule' => array('between', 1, 100),
				'message' => 'Project name must be between 1 and 100 characters long; can not be empty',
				'allowEmpty' => false,
			),
		),
		'description' => array(
			'length' => array(
				'rule' => array('between', 1, 500),
				'message' => 'Project description must be between 1 and 500 characters long; can not be empty',
				'allowEmpty' => false,
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
 * Get a single project and return formated or not
 *
 * @param int $memberProjectId
 * @param bool $format 
 * @return array
 */
    public function getProject($memberProjectId, $format = true) {
        $findOptions = array(
			'conditions' => array(
				'MemberProject.member_project_id' => $memberProjectId,
			),
			'fields' => array('MemberProject.*'),
		);

		$details = $this->find( 'first', $findOptions );

		if ($format) {
			return $this->formatDetails($details, false);
		}

		return $details;
    }

/** 
 * Get memberId for a project
 *
 * @param int $memberProjectId
 * @return int $memberId
 */
    public function getMemberIDforProject($memberProjectId) {
        $findOptions = array(
                             'conditions' => array(
                                                   'MemberProject.member_project_id' => $memberProjectId,
                                                   ),
                             'fields' => array('MemberProject.member_project_id', 'MemberProject.member_id'),
                             );
        
        $details = $this->find( 'first', $findOptions );
        
        return Hash::get($details ,'MemberProject.member_id');
    }
    
/**
 * Get a list of projects for a member
 * 
 * @param bool $paginate If true, return a query to retrieve a page of the data, otherwise return the data.
 * @param array $conditions An array of conditions to decide which member records to access.
 * @return array A list of projects or query to report a list of tags
 */
	public function getProjectsList($paginate, $conditions = array()) {
		$findOptions = array(
			'conditions' => $conditions,
			'fields' => array('MemberProject.*'),
		);

		if ($paginate) {
			return $findOptions;
		}

		$info = $this->find( 'all', $findOptions );

		return $info;
	}
    
/**
 * Format an array of projects for the
 * 
 * @param array $projectsList The array of projects.
 * @param bool $removeNullEntries If true then entries that have a value of null, false or an empty array won't exist in the final array.
 * @return array A list of formated projects 
 */
    public function formatProjectsList($projectList, $removeNullEntries) {
        $formatted = array();
        foreach($projectList as $project) {
            array_push($formatted, $this->formatDetails($project, $removeNullEntries));
        }
        return $formatted;
    }

/**
 * Flatten project details array
 * 
 * @param array $project raw project record from the model
 * @param bool $removeNullEntries strips out null fields from the result
 * @return array Details for the tag serial number passed in $serial
 */
  public function formatDetails($project, $removeNullEntries = true) {
  	/*
  		Data should be presented to the view in an array like so:
        [memberProjectId] => project id
  		[memberId] => member id
  		[projectName] => Project Name
  		[description] => full lenght description
  		[startDate] => date project started
        [completeDate] => complete date
  		[stateId] => state of card as used in the DB
  		[stateName] => description of the stateId value
  	*/
  		$formatted = array(
            'memberProjectId' => Hash::get($project, 'MemberProject.member_project_id'),
	  		'memberId' => Hash::get($project, 'MemberProject.member_id'),
	  		'projectName' => Hash::get($project, 'MemberProject.project_name'),
	  		'description' => Hash::get($project, 'MemberProject.description'),
	  		'startDate' => Hash::get($project, 'MemberProject.start_date'),
            'completeDate' => Hash::get($project, 'MemberProject.complete_date'),
	  		'stateId' => Hash::get($project, 'MemberProject.state'),
	  		'stateName' => $this->statusStrings[Hash::get($project, 'MemberProject.state')],
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
 * create a new project record for a member id
 *
 * @param int $memberId
 * @param string $projectName
 * @param string $description
 * @return bool True is project created
 */
    public function newProjectForMember($memberId, $projectName, $description) {
        
        $project = array(
                         'MemberProject' => array(
                                                  'member_id' => $memberId,
                                                  'project_name' => $projectName,
                                                  'description' => $description,
                                                  'start_date' => date('Y-m-d'),
                                                  'state' => MemberProject::PROJCET_ACTIVE
                                                  )
                         );
        $dataSource = $this->getDataSource();
        $dataSource->begin();
        
        $this->create();
        
        
        if (!$this->save($project)) {
            $dataSource->rollback();
            return false;
        }
        
        $dataSource->commit();
        return true;
    }
    

/**
 * Change state of a project
 *
 * @param int $memberProjectId
 * @param int $state
 * @retrun bool
 */
    public function changeStateForProject($memberProjectId, $state) {
        $completeDate = '';
        if ($state == MemberProject::PROJCET_COMPLETE) {
            $completeDate = date( 'Y-m-d' );
        }
        $project = array(
                        'MemberProject' => array(
                                 'member_project_id' => $memberProjectId,
                                 'complete_date' => $completeDate,
                                 'state' => $state
                                 )
                        );
        
        return $this->save($project);
    }
}
