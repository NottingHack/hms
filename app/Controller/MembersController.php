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

	    # Add a new member
	    public function add() {
	    	if ($this->request->is('post')) {
	            if ($this->Member->save($this->request->data)) {
	                $this->Session->setFlash('New member added.');
	                $this->redirect(array('action' => 'index'));
	            } else {
	                $this->Session->setFlash('Unable to add member.');
	            }
	        }
	    }

	    public function view($id = null) {
	        $this->Member->id = $id;
	        $this->set('member', $this->Member->read());
	    }

	    public function edit($id = null) {

	    	$this->set('groups', $this->Member->Group->find('list',array('fields'=>array('grp_id','grp_description'))));
	    	$this->set('statuses', $this->Member->Status->find('list',array('fields'=>array('status_id','title'))));
			$this->Member->id = $id;
			if ($this->request->is('get')) {
			    $this->request->data = $this->Member->read();
			} else {
			    if ($this->Member->save($this->request->data)) {
			        $this->Session->setFlash('Member details updated.');
			        $this->redirect(array('action' => 'index'));
			    } else {
			        $this->Session->setFlash('Unable to update member details.');
			    }
			}
		}

		public function set_member_status($id, $newStatus)
		{
			$this->Member->read(null, $id);
			$this->Member->set('member_status', $newStatus);
			if($this->Member->save())
			{
				$this->Session->setFlash('Member status updated.');
			}
			else
			{
				$this->Session->setFlash('Unable to update member status');
			}

			# Notify all the member admins about the status change

			$this->redirect($this->referer());
		}

		public function email_test()
		{
			App::uses('CakeEmail', 'Network/Email');

			$email = new CakeEmail();
			$email->config('smtp');
			$email->from(array('me@example.com' => 'My Site'));
			$email->to('pyroka@gmail.com');
			$email->subject('About');
			$email->send('My message');
		}
	}
?>