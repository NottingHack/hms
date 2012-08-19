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
	    	$this->set('permissions', $this->Group->Permission->find('list',array('fields'=>array('permission_code','permission_desc'))));

	    	if ($this->request->is('post')) {
	            if ($this->Group->save($this->request->data)) {
	                $this->Session->setFlash('New group added.');
	                $this->redirect(array('action' => 'index'));
	            } else {
	                $this->Session->setFlash('Unable to add group.');
	            }
	        }
	    }

	    public function edit($id = null) {
	    	$this->set('permissions', $this->Group->Permission->find('list',array('fields'=>array('permission_code','permission_desc'))));
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
	}
?>