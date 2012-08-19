<?php

	class MembersController extends AppController {
	    
	    public $helpers = array('Html', 'Form');

		# List info about all members
		public function list_members() {
	        $this->set('members', $this->Member->find('all'));
	    }

	}
?>