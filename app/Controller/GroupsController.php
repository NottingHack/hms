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
 * @package       app.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Controller for the Groups functionality, allows users to
 * view all the groups, and modify which users belong in which groups.
 */
class GroupsController extends AppController {

/**
 * Views rendered from this controller will have access to the following helpers.
 * @var array
 */
	public $helpers = array('Html', 'Form');

/** 
 * Test to see if a user is authorized to make a request.
 *
 * @param array $user Member record for the user.
 * @param CakeRequest $request The request the user is attempting to make.
 * @return bool True if the user is authorized to make the request, otherwise false.
 * @link http://api20.cakephp.org/class/cake-request
 */
	public function isAuthorized($user, $request) {
		return true;
	}

/**
 * Show a list of all groups and their associated permissions.
 */
	public function index() {
		$this->set('groups', $this->Group->find('all'));
		$this->set('permissions', $this->Group->Permission->find('all'));

		$this->Nav->add('Add Group', 'groups', 'add');
	}

/**
 * Add a new group.
 */
	public function add() {
		$this->set('permissions', $this->Group->Permission->find('list', array('fields' => array('permission_code', 'permission_desc'))));

		if ($this->request->is('post')) {
			if ($this->Group->save($this->request->data)) {
				$this->Session->setFlash('New group added.');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Unable to add group.');
			}
		}
	}

/**
 * Edit an existing group.
 * @param  int|null $id The id of the group to edit.
 */
	public function edit($id = null) {
		$this->set('permissions', $this->Group->Permission->find('list', array('fields' => array('permission_code', 'permission_desc'))));
		$this->Group->id = $id;

		if ($this->request->is('get')) {
			$this->request->data = $this->Group->read();
		} else {
			if ($this->Group->save($this->request->data)) {
				$this->Session->setFlash('Group has been updated.');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Unable to update group.');
			}
		}
	}

/**
 * View details about a group
 * @param  int|null $id The id of the group to look at.
 */
	public function view($id = null) {
		$this->Group->id = $id;
		$this->set('group', $this->Group->read());

		$this->Nav->add('Edit Group', 'groups', 'edit', array( $id ) );
	}
}