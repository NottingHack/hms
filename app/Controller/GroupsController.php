<?php

	class GroupsController extends AppController {
	    
	    public $helpers = array('Html', 'Form');

	    # Show a list of the groups
	    public function index() {
	    	$this->set('groups', $this->Group->find('all'));
	    	$this->set('permissions', $this->Group->Permission->find('all'));
	    }

	    # Add a new group
	    public function add() {
	    	if ($this->request->is('post')) {
	            if ($this->Group->save($this->request->data)) {
	                $this->Session->setFlash('New group added.');
	                $this->redirect(array('action' => 'index'));
	            } else {
	                $this->Session->setFlash('Unable to add group.');
	            }
	        }
	    }
	}
?>