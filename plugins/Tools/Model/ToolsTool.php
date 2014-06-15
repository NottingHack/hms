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
 * @package       plugins.Tools.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Model for all tool data.
 */
class ToolsTool extends ToolsAppModel {

	/**
	 * What we store in the level column
	 */
	const LVL_USER = 'USER';
	const LVL_INDUCTOR = 'INDUCTOR';
	const LVL_MAINTAINER = 'MAINTAINER';

	/**
	 * What we store in the restrictions column
	 */
	const RESTRICTED = 'RESTRICTED';
	const UNRESTRICTED = 'UNRESTRICTED';

	/**
	 * Specify the table to use.
	 *
	 * @var string
	 */
	public $useTable = 'tools';

	/**
	 * Uses a different primary key
	 *
	 * @var int
	 */
	public $primaryKey = 'tool_id';

	/**
	 * Specify a nicer alias for this model.
	 *
	 * @var string
	 */
	public $alias = 'Tool';

	/**
	 * The validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'tool_name' => array(
			'rule'    => 'isUnique',
			'message' => 'This tool name has already been taken.'
			),
		); 

	/**
	 * Specify 'has many associations.
	 * @var array
	 */
	public $hasMany = array(
		'Member'	=> array(
				'className'	=>	'Tools.ToolsMember',
			),
		);

	public function isUserInducted($toolId, $userId) {
		if (!$toolId) {
			throw new NotFoundException(__('Invalid tool'));
		}

		$tool = $this->findByToolId($toolId);
		if (!$tool) {
			throw new NotFoundException(__('Invalid tool'));
		}
		
		if ($tool['Tool']['tool_restrictions'] == self::UNRESTRICTED) {
			return true;
		}

		$level = $this->__extractMemberLevel($tool, $userId);
		if ($level != false) {
			return true;
		}
		else {
			return false;
		}
	}

	public function isUserAnInductor($toolId, $userId) {
		if (!$toolId) {
			throw new NotFoundException(__('Invalid tool'));
		}

		$tool = $this->findByToolId($toolId);
		if (!$tool) {
			throw new NotFoundException(__('Invalid tool'));
		}
		
		$level = $this->__extractMemberLevel($tool, $userId);
		if ($level == self::LVL_INDUCTOR || $level == self::LVL_MAINTAINER) {
			return true;
		}
		else {
			return false;
		}
	}

	public function isUserAMaintainer($toolId, $userId) {
		if (!$toolId) {
			throw new NotFoundException(__('Invalid tool'));
		}

		$tool = $this->findByToolId($toolId);
		if (!$tool) {
			throw new NotFoundException(__('Invalid tool'));
		}
		
		$level = $this->__extractMemberLevel($tool, $userId);
		if ($level == self::LVL_MAINTAINER) {
			return true;
		}
		else {
			return false;
		}
	}

	private function __extractMemberLevel($tool, $userId) {
		foreach ($tool['Member'] as $member) {
			if ($member['member_id'] == $userId) {
				return $member['mt_access_level'];
			}
		}
		return false;
	}
}

?>