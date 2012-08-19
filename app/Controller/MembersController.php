<?php

	class MembersController extends AppController {
	    
	    public $helpers = array('Html', 'Form');

	    # Show some basic info, and link to other things
	    public function index() {
	    	$this->set('members', $this->Member->find('all'));
	    }

		# List info about all members
		public function list_members() {
	        $this->set('members', $this->Member->find('all'));
	    }

	}
?>