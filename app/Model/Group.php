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
 * Model for all group data
 */
class Group extends AppModel {

/**
 * The id of the full access group.
 */
	const FULL_ACCESS = 1;

/**
 * The id of the current members group.
 */
	const CURRENT_MEMBERS = 2;

/**
 * The id of the snackspace admin group.
 */
	const SNACKSPACE_ADMIN = 3;

/**
 * The id of the gatekeeper admin group.
 */
	const GATEKEEPER_ADMIN = 4;

/**
 * The id of the membership admin group.
 */
	const MEMBERSHIP_ADMIN = 5;

/**
 * The id of the membership team group.
 */
	const MEMBERSHIP_TEAM = 6;

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = "grp";

/**
 * Specify the primary key.
 * @var string
 */
	public $primaryKey = 'grp_id';

/**
 * Has and belongs to many (HABTM) associations
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Permission' =>
			array(
				'className' => 'Permission',
				'joinTable' => 'group_permissions',
				'foreignKey' => 'grp_id',
				'associationForeignKey' => 'permission_code',
				'unique' => true,
			),
		'Member' =>
			array(
				'className' => 'Member',
				'joinTable' => 'member_group',
				'foreignKey' => 'grp_id',
				'associationForeignKey' => 'member_id',
				'unique' => true,
				'with' => 'GroupsMember',
			)
	);

/**
 * Validation rules
 * @var array
 */
	public $validate = array(
		'grp_description' => array(
			'length' => array(
				'rule' => array('between', 1, 200),
				'required' => true,
				'message' => 'Group description must be between 1 and 200 characters long',
			),
		),
	);

/**
 * Get the Group description for a given id.
 * 
 * @param int $groupId The primary key of the Group to get the description of.
 * @return mixed The description of the Group, or false if it can not be found.
 */
	public function getDescription($groupId) {
		return $this->find('first', array('fields' => array('Group.grp_description'), 'conditions' => array('Group.grp_id' => $groupId)));
	}

/**
 * Get a summary of the group records for all groups.
 * 
 * @return array A summary of the data of all groups.
 * @link Group::__getGroupSummary()
 */
	public function getGroupSummaryAll() {
		return $this->__getGroupSummary();
	}

/**
 * Get a list of groups
 * 
 * @return array A list of groups.
 */
	public function getGroupList() {
		return $this->find('list', array('fields' => array('Group.grp_id', 'Group.grp_description')));
	}

/**
 * Get a summary of the group records for all groups that match the conditions.
 *
 * @param array $conditions Only return a summary for groups that match these conditions.
 * @return array A summary (id, name, description and member count) of the data of all groups that match the conditions.
 */
	private function __getGroupSummary($conditions = array()) {
		$info = $this->find( 'all', array('conditions' => $conditions) );

		return $this->__formatGroupInfo($info);
	}

/**
 * Format group information into a nicer arrangement.
 * 
 * @param $info The info to format, usually retrieved from Group::__getGroupSummary.
 * @return array An array of group information, formatted so that nothing needs to know database rows.
 * @link Group::__getGroupSummary
 */
	private function __formatGroupInfo($info) {
		/*
			Data should be presented to the view in an array like so:
				[n] =>
					[id] => group id
					[description] => group description
					[count] => number of members with this group
		 */

		$formattedInfo = array();
		foreach ($info as $group) {
			$id = Hash::get($group, 'Group.grp_id');
			$description = Hash::get($group, 'Group.grp_description');
			$count = count( Hash::extract($group, 'Member') );

			array_push($formattedInfo,
				array(
					'id' => $id,
					'description' => $description,
					'count' => $count,
				)
			);
		}

		return $formattedInfo;
	}
}