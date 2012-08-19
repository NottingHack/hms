<?php

	class MembersController extends AppController {
	    
	    public $helpers = array('Html', 'Form');

	    # Show some basic info, and link to other things
	    public function index() {

	    	# Need the Status model here
	    	$statusList = $this->Member->Status->find('all');

	    	# Come up with a count of all members
	    	# And a count for each status
	    	
	    	$memberStatusCount = array();
	    	# Init the array
	    	foreach ($statusList as $current) {
	    		$memberStatusCount[$current['Status']['title']] = 
	    			array( 'id' => $current['Status']['status_id'],
	    					'count' => 0
	    			);
	    	}

	    	$memberTotalCount = 0;
	    	foreach ($this->Member->find('all') as $member) {
	    		$memberTotalCount++;
	    		$memberStatus = $member["Status"]['title'];
	    		if(isset($memberStatusCount[$memberStatus]))
	    		{
	    			$memberStatusCount[$memberStatus]['count']++;	
	    		}
	    	}

	    	$this->set('memberStatusCount', $memberStatusCount);
	    	$this->set('memberTotalCount', $memberTotalCount);
	    }

		# List info about all members
		public function list_members() {
	        $this->set('members', $this->Member->find('all'));
	    }

		# List info about all members with a certain status
		public function list_members_with_status($statusId) {
			# Uses the default list view
			$this->view = 'list_members';

	        $this->set('members', $this->Member->find('all', array( 'conditions' => array( 'Member.member_status' => $statusId ) )));
	    }
	}
?>