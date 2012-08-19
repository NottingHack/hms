<?php

	class MembersController extends AppController {
	    
	    public $helpers = array('Html', 'Form');

		# Index lists all members at the moment
		public function index() {
	        $this->set('members', $this->Member->find('all'));
	    }

	}
?>