<?php
	

	App::uses('HmsAuthenticate', 'Controller/Component/Auth');

	class MembersController extends AppController {
	    
	    public $helpers = array('Html', 'Form');

	    public function beforeFilter() {
	        parent::beforeFilter();
	        $this->Auth->allow('logout', 'login');
	    }

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

			if(isset($statusId))
			{
			#	print_r($this->Member->find('all', array( 'conditions' => array( 'Member.member_status' => $statusId ), 'fields' => array( 'DISTINCT Member.member_id' ))));
		        $this->set('members', $this->Member->find('all', array( 'conditions' => array( 'Member.member_status' => $statusId ) )));
		        $statusData = $this->Member->Status->find('all', array( 'conditions' => array( 'Status.status_id' => $statusId ) ));
				$this->set('statusData', $statusData[0]['Status']);
			}
			else
			{
				$this->redirect( array( 'controller' => 'members', 'action' => 'list_members' ) );
			}
	    }

	    # Add a new member
	    public function add() {
	    	if ($this->request->is('post')) {

	    		$this->request->data['Member']['member_status'] = 1;
				$this->request->data['Member']['unlock_text'] = 'Welcome ' . $this->request->data['Member']['handle'];
				$this->request->data['Member']['credit_limit'] = 5000;

				# Set some pin data
				$this->request->data['Pin']['unlock_text'] = 'Welcome';
				$this->request->data['Pin']['state'] = 40;

	            if ($this->Member->saveAll($this->request->data)) {
	                $this->Session->setFlash('New member added.');

	                # Email the new member, and notify the admins
	                $adminEmail = $this->prepare_email_for_members_in_group(5);
					$adminEmail->subject('New Prospective Member Notification');
					$adminEmail->template('notify_admins_member_added', 'default');
					$adminEmail->viewVars( array( 
						'member' => $this->request->data['Member'],
						 )
					);
					$adminEmail->send();

					$memberEmail = $this->prepare_email();
					$memberEmail->to( $this->request->data['Member']['email'] );
					$memberEmail->subject('Welcome to Nottingham Hackspace');
					$memberEmail->template('to_prospective_member', 'default');
					$memberEmail->viewVars( array(
						'memberName' => $this->request->data['Member']['name'],
						'guideName' => $this->request->data['Other']['guide'],
						) 
					);
					$memberEmail->send();

					$this->redirect(array('action' => 'index'));
	            } else {
	                $this->Session->setFlash('Unable to add member.');
	            }
	        }

	        # Generate the Pin data
			$this->request->data['Pin']['pin'] = $this->Member->Pin->generate_unique_pin();
	    }

	    public function change_password($id = null) {

	    	Controller::loadModel('ChangePassword');

			$this->Member->id = $id;
			$memberInfo = $this->Member->read();
			$memberIsMemberAdmin = $this->Member->memberInGroup(AuthComponent::user('Member.member_id'), 5);
			$this->request->data['Member']['member_id'] = $id;
			$this->set('memberInfo', $memberInfo);
			$this->set('memberIsMemberAdmin', $memberInfo);
			$this->set('memberEditingOwnProfile', AuthComponent::user('Member.member_id') == $id);

			if ($this->request->is('get')) {
			}
			else
			{
				# Validate the input using the dummy model
				$this->ChangePassword->set($this->data);
				if($this->ChangePassword->validates())
				{
					# Only member admins (group 5) and the member themselves can do this
					if( $this->request->data['Member']['member_id'] == AuthComponent::user('Member.member_id') ||
						$memberIsMemberAdmin ) 
					{
						# Check the current the user submitted
						if( isset($memberInfo['MemberAuth']) &&
							$memberInfo['MemberAuth'] != null ) 
						{
							$saltToUse = $memberInfo['MemberAuth']['salt'];
							$passwordToCheck = $memberInfo['MemberAuth']['passwd'];

							if( $memberIsMemberAdmin )
							{
								# Member admins need to enter their own password
								$memberAdminMemberInfo = $this->Member->find('first', array( 'conditions' => array( 'Member.member_id' => AuthComponent::user('Member.member_id') ) ) );
								$saltToUse = $memberAdminMemberInfo['MemberAuth']['salt'];
								$passwordToCheck = $memberAdminMemberInfo['MemberAuth']['passwd'];
							}

							$currentPasswordHash = HmsAuthenticate::make_hash($saltToUse, $this->request->data['ChangePassword']['current_password']);

							if( $currentPasswordHash === $passwordToCheck ) # MemberAdmins don't need to know the old password
							{
								# User submitted current password is ok, check the new one
								if( $this->request->data['ChangePassword']['new_password'] === $this->request->data['ChangePassword']['new_password_confirm'] )
								{
									# Good to go
									$memberInfo['MemberAuth']['passwd'] = HmsAuthenticate::make_hash($memberInfo['MemberAuth']['salt'], $this->request->data['ChangePassword']['new_password']);
									$memberInfo['MemberAuth']['member_id'] = $id;
									if( isset( $memberInfo['MemberAuth']['salt'] ) === false )
									{
										$memberInfo['MemberAuth']['salt'] = HmsAuthenticate::make_salt();
									}
									if( $this->Member->MemberAuth->save($memberInfo) )
									{
										$this->Session->setFlash('Password updated.');
										$this->redirect(array('action' => 'view', $id));
									}
									else
									{
										$this->Session->setFlash('Unable to update password.');
									}
								}
								else
								{
									$this->Session->setFlash('New password doesn\'t match new password confirm');
								}
							}
							else
							{
								$this->Session->setFlash('Current password incorrect');
							}
						}
					}
					else
					{
						$this->Session->setFlash('You are not authorised to do this');
					}
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
				unset($this->request->data['Pin']);
				print_r($this->request->data);
			    if ($this->Member->saveAll($this->request->data)) {
			    	$this->Member->clearGroupsIfMembershipRevoked($id, $this->request->data);
			        $this->Session->setFlash('Member details updated.');
			        $this->redirect(array('action' => 'index'));
			    } else {
			        $this->Session->setFlash('Unable to update member details.');
			    }
			}
		}

		public function set_member_status($id, $newStatus)
		{
			$data = $this->Member->read(null, $id);
			$newData = $this->Member->set('member_status', $newStatus);
			if($this->Member->save())
			{
				$this->Session->setFlash('Member status updated.');

				$this->Member->clearGroupsIfMembershipRevoked($id, $newData);

				# Notify all the member admins about the status change
				$email = $this->prepare_email_for_members_in_group(5);
				$email->subject('Member Status Change Notification');
				$email->template('notify_admins_member_status_change', 'default');
				
				$newStatusData = $this->Member->Status->find( 'all', array( 'conditions' => array( 'Status.status_id' => $newStatus ) ) );

				$email->viewVars( array( 
					'member' => $data['Member'],
					'oldStatus' => $data['Status']['title'],
					'newStatus' => $newStatusData[0]['Status']['title'],
					 )
				);

				$email->send();

				# Is this member being approved for the first time? If so we need to send out a message to the member admins
				# To tell them to e-mail the PIN etc to the new member
				$oldStatus = $data['Status']['status_id'];
				if(	$newStatus == 2 &&
					$oldStatus == 1)
				{
					$approvedEmail = $this->prepare_email_for_members_in_group(5);
					$approvedEmail->subject('Member Approved!');
					$approvedEmail->template('notify_admins_member_approved', 'default');

					$approvedEmail->viewVars( array( 
						'member' => $data['Member'],
						'pin' => $data['Pin']['pin']
						)
					);

					$approvedEmail->send();
				}
			}
			else
			{
				$this->Session->setFlash('Unable to update member status');
			}

			$this->redirect($this->referer());
		}

		private function get_emails_for_members_in_group($groupId)
		{
			# First grab all the members in the group
			$members = $this->Member->Group->find('all', array( 'conditions' => array( 'Group.grp_id' => $groupId ) ) );

			#Then spilt out the e-mails
			#return Hash::extract( $members, '{n}.Member.{n}.email' );
			return array( 'pyroka@gmail.com' );
		}


		private function prepare_email_for_members_in_group($groupId)
		{
			$email = $this->prepare_email();
			$email->to( $this->get_emails_for_members_in_group( $groupId ) );

			return $email;
		}

		private function prepare_email()
		{
			App::uses('CakeEmail', 'Network/Email');

			$email = new CakeEmail();
			$email->config('smtp');
			$email->from(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$email->sender(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
			$email->emailFormat('html');

			return $email;
		}

		public function login() {
		    if ($this->request->is('post')) {
		        if ($this->Auth->login()) {
		        	$memberInfo = AuthComponent::user();
		        	# Set the last login time
		        	unset($memberInfo['MemberAuth']);
		        	$memberInfo['MemberAuth']['member_id'] = $memberInfo['Member']['member_id'];
		        	$memberInfo['MemberAuth']['last_login'] = date( 'Y-m-d H:m:s' );
		        	$this->Member->MemberAuth->save($memberInfo);
		            $this->redirect($this->Auth->redirect());
		        } else {
		            $this->Session->setFlash(__('Invalid username or password, try again'));
		        }
		    }
		}

		public function logout() {
		    $this->redirect($this->Auth->logout());
		}
	}
?>