<?php
	
	App::uses('HmsAuthenticate', 'Controller/Component/Auth');
	App::uses('Member', 'Model');

	App::uses('PhpReader', 'Configure');
	Configure::config('default', new PhpReader());
	Configure::load('hms', 'default');

	/**
	 * Controller for Member functions.
	 *
	 *
	 * @package       app.Controller
	 */
	class MembersController extends AppController 
	{
	    
	    //! We need the Html, Form, Tinymce and Currency helpers.
	    /*!
	    	@sa http://api20.cakephp.org/class/html-helper
	    	@sa http://api20.cakephp.org/class/form-helper
	    	@sa TinymceHelper
	    	@sa CurrencyHelper
	    */
	    public $helpers = array('Html', 'Form', 'Tinymce', 'Currency');

	    //! We need the MailChimp and Krb components.
	    /*!
	    	@sa MailChimpComponent
	    	@sa KrbComponent
	    */
	    public $components = array('MailChimp', 'Krb');

	    //! Test to see if a user is authorized to make a request.
	    /*!
	    	@param array $user Member record for the user.
	    	@param CakeRequest $request The request the user is attempting to make.
	    	@retval bool True if the user is authorized to make the request, otherwise false.
	    	@sa http://api20.cakephp.org/class/cake-request
	    */
	    public function isAuthorized($user, $request)
	    {
	    	if(parent::isAuthorized($user, $request))
	    	{
	    		return true;
	    	}

	    	$userIsMemberAdmin = $this->Member->GroupsMember->isMemberInGroup( Hash::extract($user, 'Member.member_id'), Group::MEMBER_ADMIN );
	    	$actionHasParams = isset( $request->params ) && isset($request->params['pass']) && count( $request->params['pass'] ) > 0;
	    	$userIdIsSet = isset( $user['Member'] ) && isset( $user['Member']['member_id'] );
	    	$userId = $userIdIsSet ? $user['Member']['member_id'] : null;

	    	switch ($request->action) 
	    	{
	    		case 'index':
	    		case 'list_members':
	    		case 'list_members_with_status':
	    		case 'email_members_with_status':
	    		case 'search':
	    		case 'set_member_status':
	    		case 'accept_details':
	    		case 'reject_details':
	    		case 'approve_member':
	    		case 'send_membership_reminder':
	    		case 'send_contact_details_reminder':
	    		case 'send_so_details_reminder':
	    		case 'add_existing_member':
	    			return $userIsMemberAdmin; 

	    		case 'change_password':
	    		case 'view':
	    		case 'edit':
	    		case 'setup_details':
	    			if( $userIsMemberAdmin || 
	    				( $actionHasParams && $userIdIsSet && $request->params['pass'][0] == $userId ) )
	    			{
	    				return true;
	    			}
	    			break;

	    		case 'login':
	    		case 'logout':
	    		case 'setup_login':
	    		case 'register':
	    			return true;
	    	}

	    	return false;
	    }

	    //! Perform any actions that should be performed before any controller action
	    /*!
	    	@sa http://api20.cakephp.org/class/controller#method-ControllerbeforeFilter
	    */
	    public function beforeFilter() 
	    {
	        parent::beforeFilter();
	        $this->Auth->allow('logout', 'login', 'register', 'forgot_password', 'setup_login', 'setup_details');
	    }

	    //! Show a list of all Status and a count of how many members are in each status.
	    public function index() 
	    {
	    	$this->set('memberStatusInfo', $this->Member->Status->getStatusSummaryAll());
	    	$this->set('memberTotalCount', $this->Member->getCount());

	    	$this->Nav->add('Register Member', 'members', 'register');
    		$this->Nav->add('E-mail all current members', 'members', 'email_members_with_status', array( Status::CURRENT_MEMBER ) );
	    }

		//! Show a list of all members, their e-mail address, status and the groups they're in.
		public function listMembers() 
		{

			/*
	    	    Actions should be added to the array like so:
	    	    	[actions] =>
	    					[n]
	    						[title] => action title
	    						[controller] => action controller
	    						[action] => action name
	    						[params] => array of params
	    	*/

	    	// Get the member summary from the model.
	    	$memberList = $this->Member->getMemberSummaryAll();

	    	// Have to add the actions ourselves
	    	for($i = 0; $i < count($memberList); $i++)
	    	{
	    		$actions = array();

	    		$status = $memberList[$i]['status']['id'];
	    		$id = $memberList[$i]['id'];

	    		switch($status)
	    		{
	    			case Status::PROSPECTIVE_MEMBER:
	    				array_push($actions, 
	    					array(
	    						'title' => 'Send Membership Reminder',
	    						'controller' => 'members',
	    						'action' => 'send_membership_reminder',
	    						'params' => array(
	    							$id,
	    						),
	    					)
	    				);
	    			break;

	    			case Status::PRE_MEMBER_1:
	    			break;

	    			case Status::PRE_MEMBER_2:
	    				array_push($actions, 
	    					array(
	    						'title' => 'Send Contact Details Reminder',
	    						'controller' => 'members',
	    						'action' => 'send_contact_details_reminder',
	    						'params' => array(
	    							$id,
	    						),
	    					)
	    				);
	    			break;

	    			case Status::PRE_MEMBER_3:

	    				array_push($actions, 
	    					array(
	    						'title' => 'Send SO Details Reminder',
	    						'controller' => 'members',
	    						'action' => 'send_so_details_reminder',
	    						'params' => array(
	    							$id,
	    						),
	    					)
	    				);

	    				array_push($actions, 
	    					array(
	    						'title' => 'Approve Member',
	    						'controller' => 'members',
	    						'action' => 'approve_member',
	    						'params' => array(
	    							$id,
	    						),
	    					)
	    				);

	    			break;

	    			case Status::CURRENT_MEMBER:
	    				array_push($actions, 
	    					array(
	    						'title' => 'Revoke Membership',
	    						'controller' => 'members',
	    						'action' => 'set_member_status',
	    						'params' => array(
	    							$id,
	    							Status::EX_MEMBER,
	    						),
	    					)
	    				);
	    			break;

	    			case Status::EX_MEMBER:
	    				array_push($actions, 
	    					array(
	    						'title' => 'Reinstate Membership',
	    						'controller' => 'members',
	    						'action' => 'set_member_status',
	    						'params' => array(
	    							$id,
	    							Status::CURRENT_MEMBER,
	    						),
	    					)
	    				);
	    			break;
	    		}

	    		$memberList[$i]['actions'] = $actions;
	    	}

	        $this->set('memberList', $memberList);
	    }

		# List info about all members with a certain status
		public function list_members_with_status($statusId) 
		{
			# Uses the default list view
			$this->view = 'list_members';

			if(isset($statusId))
			{
		        $this->set('members', $this->Member->find('all', array( 'conditions' => array( 'Member.member_status' => $statusId ) )));
		        $statusData = $this->Member->Status->find('all', array( 'conditions' => array( 'Status.status_id' => $statusId ) ));
				$this->set('statusData', $statusData[0]['Status']);
			}
			else
			{
				$this->redirect( array( 'controller' => 'members', 'action' => 'list_members' ) );
			}
	    }

	    # List info about all members who's email or name is like $query
		public function search() 
		{

			# Uses the default list view
			$this->view = 'list_members';
			if(isset($this->request->data['Member']))
			{
				$keyword = $this->request->data['Member']['query'];
				$this->set('members', $this->Member->find('all', array( 'conditions' => array( 'OR' => array("Member.name Like'%$keyword%'", "Member.email Like'%$keyword%'" )))));
			}
			else
			{
				$this->redirect( array( 'controller' => 'members', 'action' => 'list_members' ) );
			}
	    }

	    # Add a new member
	    public function register() 
	    {

	    	$mailingLists = $this->_get_mailing_lists_and_subscruibed_status(null);
			$this->set('mailingLists', $mailingLists);

	    	if ($this->request->is('post')) 
	    	{
	    		 
	    		$this->Member->set($this->data);
	    		if($this->Member->validates(array('fieldList' => array('email'))))
	    		{

		    		$this->request->data['Member']['member_status'] = 1;

		    		# Do we already know about this email?
		    		$memberInfo = $this->Member->find('first', array( 'conditions' => array('Member.email' => $this->request->data['Member']['email'])));
		    		$emailAlreadyKnown = !empty($memberInfo);

		            if ( $emailAlreadyKnown == true ||
		            	 ($emailAlreadyKnown == false && $this->Member->saveAll($this->request->data)) ) 
		            {
		            	$memberId = $emailAlreadyKnown ? $memberInfo['Member']['member_id'] : $this->Member->getLastInsertId();

		            	$flashMessage = 'Registration successful';
		            	if(isset($this->request->data['MailingLists']['MailingLists']) &&
		            		empty($this->request->data['MailingLists']['MailingLists']) == false)
		            	{
		            		$flashMessage .= '</br>';
		            		$flashMessage .= $this->_update_mailing_list_subscriptions($memberId, $this->request->data['MailingLists']['MailingLists']);
		            	}

		            	$this->Session->setFlash($flashMessage);

		                # Get a list of all the mailing lists this user is subscribing to
		                $subscribedMailingLists = array();
		                if(isset($this->request->data['MailingLists']['MailingLists']))
		                {
			                foreach ($this->request->data['MailingLists']['MailingLists'] as $key => $value) 
			                {
			                	$mailingListToSubscruibe = $mailingLists[$key];
			                	array_push($subscribedMailingLists, $mailingListToSubscruibe);
			                }
			            }

		                # Only notify the member admins if it is a new email
		                if($emailAlreadyKnown == false)
		                {
			                # Email the member admins then
			                $adminEmail = $this->prepare_email_for_members_in_group(5);
							$adminEmail->subject('New Prospective Member Notification');
							$adminEmail->template('notify_admins_member_added', 'default');
							$adminEmail->viewVars( array( 
								'email' => $this->request->data['Member']['email'],
								'mailingLists' => $subscribedMailingLists,
								 )
							);
							$adminEmail->send();
						}

						# We always send this e-mail to the member though
						$memberEmail = $this->prepare_email();
						$memberEmail->to( $this->request->data['Member']['email'] );
						$memberEmail->subject('Welcome to Nottingham Hackspace');
						$memberEmail->template('to_prospective_member', 'default');
						$memberEmail->viewVars( array(
							'mailingLists' => $subscribedMailingLists,
							'memberId' => $memberId,
							) 
						);
						$memberEmail->send();

						$this->redirect(array( 'controller' => 'pages', 'action' => 'home'));
		            } 
		            else 
		            {
		                $this->Session->setFlash('Unable to register.');
		            }
		        }
	        }
	    }

	    # Get a registered member to set-up a username and Password
	    public function setup_login($id = null)
	    {
	    	if($id != null)
	    	{
	    		$this->Member->id = $id;
	    		$memberInfo = $this->Member->read();
	    		# Does this member already have a username?
	    		if(	$memberInfo != null &&
	    			isset($memberInfo['Member']['username']) == false &&
	    			$memberInfo['Member']['member_status'] == 1) # Can only do this if we have the correct status
	    		{
	    			$this->set('memberInfo', $memberInfo);

	    			$this->request->data['Member']['member_id'] = $id;

	    			if($this->request->is('put'))
	    			{
	    				$this->Member->addEmailMustMatch();

	    				$this->request->data['Member']['member_status'] = 5;
	    				$this->Member->set($this->request->data);
	    				if($this->Member->validates(array('fieldList' => array('name', 'email', 'username', 'password', 'password_confirm'))))
	    				{
	    					# Make the handle the same as the username for now...
	    					$this->request->data['Member']['handle'] = $this->request->data['Member']['username'];

		    				if(	$this->Krb->addUser($this->request->data['Member']['username'], $this->request->data['Member']['password']) &&
		    				 	$this->Member->save($this->request->data, array('validate' => false)))
		    				{
		    					$this->Session->setFlash('Username and Password set, please login.');
								$this->redirect(array( 'controller' => 'members', 'action' => 'login'));
		    				}
		    				else
		    				{
		    					$this->Session->setFlash('Unable to set username and password.');
		    				}
		    			}

		    			$this->Member->removeEmailMustMatch();
	    			}
	    		}
	    		else
	    		{
	    			# Redirect somewhere, they shouldn't be here
	    			$this->redirect(array('controller' => 'pages', 'action' => 'home'));
	    		}
	    	}
	    	else
	    	{
	    		# Redirect somewhere, they shouldn't be here
	    		$this->redirect(array('controller' => 'pages', 'action' => 'home'));
	    	}
	    }

	    # Get a member with a login to set-up their address details
	    public function setup_details($id = null)
	    {
	    	if(	$id != null &&
	    		$id == AuthComponent::user('Member.member_id') )
	    	{
	    		$this->Member->id = $id;
	    		$memberInfo = $this->Member->read();
	    		$this->set('memberInfo', $memberInfo);

	    		# Can only be here if we have the correct status
	    		if($memberInfo['Member']['member_status'] == 5)
	    		{
		    		$this->request->data['Member']['member_id'] = $id;
		    		if($this->request->is('put'))
	    			{
	    				$this->request->data['Member']['email'] = $memberInfo['Member']['email'];
	    				$this->request->data['Member']['member_status'] = 6;
	    				$this->Member->set($this->request->data);
	    				if($this->Member->validates(array('fieldList' => array('address_1', 'address_city', 'address_postcode', 'contact_number'))))
		    			{
		    				if($this->Member->save($this->request->data, array('validate' => false)))
		    				{
		    					$this->Session->setFlash('Contact details saved.');

								# Email the admins
								$adminEmail = $this->prepare_email_for_members_in_group(5);
								$adminEmail->subject('New Member Contact Details');
								$adminEmail->template('notify_admins_check_contact_details', 'default');
								$adminEmail->viewVars( array( 
									'email' => $this->request->data['Member']['email'],
									'id' => $id,
									 )
								);
								$adminEmail->send();

								# Email the member an update
								$memberEmail = $this->prepare_email();
								$memberEmail->to( $this->request->data['Member']['email'] );
								$memberEmail->subject('Contact Information Completed');
								$memberEmail->template('to_member_post_contact_update', 'default');
								$memberEmail->send();

								$this->redirect(array( 'controller' => 'members', 'action' => 'view', $id));
		    				}
		    				else
		    				{
		    					$this->Session->setFlash('Unable to save contact details.');
		    				}
		    			}
	    			}
	    			else
	    			{
	    				$this->request->data = $memberInfo;
	    			}
	    		}
	    		else
		    	{
		    		# Redirect somewhere, they shouldn't be here
		    		$this->redirect(array('controller' => 'pages', 'action' => 'home'));
		    	}
	    	}
	    	else
	    	{
	    		# Redirect somewhere, they shouldn't be here
	    		$this->redirect(array('controller' => 'pages', 'action' => 'home'));
	    	}
	    }

	    public function reject_details($id = null) 
	    {
	    	Controller::loadModel('MemberEmail');

	    	if($id != null)
	    	{
	    		$this->Member->id = $id;
				$memberInfo = $this->Member->read();
				$this->set('memberInfo', $memberInfo);
				$this->request->data['Member']['member_id'] = $id;

				# Can only be here if we have the correct status
	    		if($memberInfo['Member']['member_status'] == 6)
	    		{
					if($this->request->is('post'))
					{
						if($this->MemberEmail->validates(array('fieldList' => array('message'))))
						{
							# Set the status back to 5
							$memberInfo['Member']['member_status'] = 5;
							if($this->Member->save($memberInfo, array('validate' => false)))
							{
								$this->Session->setFlash('Member has been contacted.');
								$memberEmail = $this->prepare_email();
								$memberEmail->to( $memberInfo['Member']['email'] );
								$memberEmail->subject('Issue With Contact Information');
								$memberEmail->template('to_member_contact_details_rejected', 'default');
								$memberEmail->viewVars( array( 
									'reason' => $this->request->data['Member']['message'],
									 )
								);
								$memberEmail->send();

								$this->redirect(array( 'controller' => 'members', 'action' => 'view', $id));
							}
							else
							{
								$this->Session->setFlash('Unable to set member status.');
							}
						}
					}
				}
				else
				{
					# Redirect somewhere, they shouldn't be here
		    		$this->redirect(array('controller' => 'pages', 'action' => 'home'));
				}
			}
			else
			{
				# Redirect somewhere, they shouldn't be here
	    		$this->redirect(array('controller' => 'pages', 'action' => 'home'));
			}
	    }

	    public function accept_details($id = null)
	    {
	    	if($id != null)
	    	{
	    		$this->Member->id = $id;
				$memberInfo = $this->Member->read();
				$this->set('memberInfo', $memberInfo);

				# Can only be here if we have the correct status
	    		if($memberInfo['Member']['member_status'] == 6)
	    		{
		    		# Ok so the contact details are fine, at this point we set-up the
		    		# account for the member and e-mail them the SO details...
		    		$accountsList =	$this->get_readable_account_list( array( -1 => 'Create New' ) );
		    		$this->set('accounts', $accountsList);

		    		$this->request->data['Member']['member_id'] = $id;

		    		if($this->request->is('put'))
					{
						$this->request->data['Member']['member_status'] = 7;
						$accountInfo = $this->set_account($this->request->data, false);

						$memberInfo['Account'] = $accountInfo['Account'];

						# Now mail the member the SO details
						$this->_send_so_details($memberInfo);

						# Finally mail the member admins to look out for a payment from this new member
						$adminEmail = $this->prepare_email_for_members_in_group(5);
						$adminEmail->subject('Impending Payment');
						$adminEmail->template('notify_admins_payment_incoming', 'default');
						$adminEmail->viewVars( array( 
							'memberName' => $memberInfo['Member']['name'],
							'memberId' => $id,
							'memberEmail' => $memberInfo['Member']['email'],
							'memberPayRef' => $memberInfo['Account']['payment_ref'],
							 )
						);
						$adminEmail->send();

						$this->_create_status_update_record($id, AuthComponent::user('Member.member_id'), 7, 6);

						$this->Session->setFlash('Member details accepted.');

						$this->redirect(array( 'controller' => 'members', 'action' => 'view', $id));
					}
					else
					{
						$this->request->data = $memberInfo;
					}
				}
				else
				{
					# Redirect somewhere, they shouldn't be here
		    		$this->redirect(array('controller' => 'pages', 'action' => 'home'));
				}
	    	}
	    	else
	    	{
	    		$this->redirect($this->referer);
	    	}
	    }

	    public function approve_member($id = null) 
	    {
	    	if($id != null)
	    	{
	    		# Grab the member
	    		$this->Member->id = $id;
				$memberInfo = $this->Member->read();

				# Check the member status
				if($memberInfo['Member']['member_status'] == 7)
				{
					# Ok, we can do this

					# Set the status to 'current member'
					$memberInfo['Member']['member_status'] = 2;
					# Generate a PIN for gate-keeper
					$memberInfo['Member']['unlock_text'] = 'Welcome ' . $memberInfo['Member']['name'];

					# Set some pin data
                    $memberInfo['Pin']['unlock_text'] = 'Welcome';
                    $memberInfo['Pin']['pin'] = $this->Member->Pin->generate_unique_pin();
                    $memberInfo['Pin']['state'] = 40;
                    $memberInfo['Pin']['member_id'] = $memberInfo['Member']['member_id'];

                    # And give a credit limit
                    $memberInfo['Member']['credit_limit'] = 5000;

                    $memberInfo['Member']['join_date'] = date( 'Y-m-d' );

                    unset($memberInfo['Status']);
                    unset($memberInfo['Account']);
                    unset($memberInfo['MemberAuth']);
                    unset($memberInfo['Group']);

                    if($this->Member->SaveAll($memberInfo))
                    {
                    	$this->_create_status_update_record($id, AuthComponent::user('Member.member_id'), 2, 7);

                    	$this->Session->setFlash('Member has been approved.');

                    	# Only notify the admin that approved them
						$adminEmail = $this->prepare_email();
						$adminEmail->to( AuthComponent::user('Member.email') );
						$adminEmail->subject('Member Approved');
						$adminEmail->template('notify_admins_member_approved', 'default');
						$adminEmail->viewVars( array( 
							'memberName' => $memberInfo['Member']['name'],
							'memberId' => $id,
							'memberEmail' => $memberInfo['Member']['email'],
							'memberPin' => $memberInfo['Pin']['pin'],
							 )
						);
						$adminEmail->send();

						# Notify the new member
						$memberEmail = $this->prepare_email();
						$memberEmail->to( $memberInfo['Member']['email'] );
						$memberEmail->subject('Membership Complete');
						$memberEmail->template('to_member_access_details', 'default');
						$memberEmail->viewVars( array( 
							'adminName' => AuthComponent::user('Member.name'),
							'adminEmail' => AuthComponent::user('Member.email'),
							'manLink' => Configure::read('hms_help_manual_url'),
							'outerDoorCode' => Configure::read('hms_access_street_door'),
							'innerDoorCode' => Configure::read('hms_access_inner_door'),
							'wifiSsid' => Configure::read('hms_access_wifi_ssid'),
							'wifiPass' => Configure::read('hms_access_wifi_password'),
							 )
						);
						$memberEmail->send();

                    }
                    else
                    {
                    	$this->Session->setFlash('Member details could not be updated.');	
                    }
				}
	    	}
	    	$this->redirect($this->referer());
	    }

	    public function change_password($id = null) 
	    {

	    	Controller::loadModel('ChangePassword');

			$this->Member->id = $id;
			$memberInfo = $this->Member->read();
			$memberIsMemberAdmin = $this->Member->memberInGroup(AuthComponent::user('Member.member_id'), 5);
			$this->request->data['Member']['member_id'] = $id;
			$this->set('memberInfo', $memberInfo);
			$this->set('memberIsMemberAdmin', $memberInfo);
			$this->set('memberEditingOwnProfile', AuthComponent::user('Member.member_id') == $id);

			if ($this->request->is('get')) 
			{
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
						$usernameToCheck = AuthComponent::user('Member.username');
						$passwordToCheck = $this->request->data['ChangePassword']['current_password'];

						if($this->Krb->checkPassword($usernameToCheck, $passwordToCheck))
						{
							if( $this->request->data['ChangePassword']['new_password'] === $this->request->data['ChangePassword']['new_password_confirm'] )
							{
								if($this->_set_member_password($memberInfo, $this->request->data['ChangePassword']['new_password']))
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
					else
					{
						$this->Session->setFlash('You are not authorised to do this');
					}
				}
			}
	    }

	    public function forgot_password($guid = null)
	    {
	    	if($guid != null)
	    	{
	    		# Check it's a valid UUID v4
	    		# With this handy regex
	    		if(preg_match('/^\{?[a-f\d]{8}-(?:[a-f\d]{4}-){3}[a-f\d]{12}\}?$/i', $guid) == false)
	    		{
	    			$guid = null;
	    		}
	    	}

	    	$this->set('guid', $guid);

	    	Controller::loadModel('ForgotPassword');

			if ($this->request->is('get')) 
			{
			}
			else
			{
				# Validate the input using the dummy model
				$this->ForgotPassword->set($this->data);
				if($this->ForgotPassword->validates())
				{
					# If guid is not set then we should be sending out the e-mail
					if($guid == null)
					{
						# Grab the member
						$memberInfo = $this->Member->find('all', array( 'conditions' => array('Member.email' => $this->request->data['ForgotPassword']['email']) ));
						if($memberInfo && count($memberInfo) > 0)
						{
							$entry = $this->ForgotPassword->generate_entry($memberInfo[0]);
							if($entry != null)
							{
								# E-mail the user...
								$email = $this->prepare_email();
								$email->to( $memberInfo[0]['Member']['email'] );
								$email->subject('Password Reset Request');
								$email->template('forgot_password', 'default');
								$email->viewVars( array( 
									'id' => $entry['ForgotPassword']['request_guid'],
									 )
								);
								$email->send();
								
								$this->redirect(array('controller' => 'pages', 'action' => 'forgot_password_sent'));
							}
						}
					}
					else
					{
						# Check all is well and then proceed with the password reset!

						# Grab the record 
						$record = $this->ForgotPassword->find('first', array('conditions' => array( 'ForgotPassword.request_guid' => $guid )));
						if(isset($record) == false || $record == null)
						{
							# FAIL INVALID GUID
							$this->Session->setFlash('Invalid request id');
							$this->redirect(array('controller' => 'pages', 'action' => 'forgot_password_error'));
						}
						else
						{
							$memberInfo = $this->Member->find('first', array( 'conditions' => array( 'Member.member_id' => $record['ForgotPassword']['member_id'] )));
							if(isset($memberInfo) == false || $memberInfo == null)
							{
								# FAIL INVALID RECORD
								$this->Session->setFlash('Invalid request id');
								$this->redirect(array('controller' => 'pages', 'action' => 'forgot_password_error'));
							}
							else
							{
								# CHECK FOR E-MAIL MATCH
								if($this->request->data['ForgotPassword']['email'] != $memberInfo['Member']['email'])
								{
									# FAIL INCORRECT E-MAIL
									# Don't tell them the actual reason
									$this->Session->setFlash('Invalid request id');
									$this->redirect(array('controller' => 'pages', 'action' => 'forgot_password_error'));
								}
								else
								{
									# AT [01/10/2012] Has this link already been used?
									# AT [01/10/2012] Or has it expired due to time passing?
									if($record['ForgotPassword']['expired'] != 0 || ( (time() - strtotime($record['ForgotPassword']['timestamp'])) > (60 * 60 * 2) ) )
									{
										# FAIL EXPIRED LINK
										$this->Session->setFlash('Invalid request id');
										$this->redirect(array('controller' => 'pages', 'action' => 'forgot_password_error'));
									}
									else
									{
										# AT [01/10/2012] Looks like we're all good to go
										# Need to do this next bit in a transaction so we can roll it back if needed

										$datasource = $this->Member->getDataSource();
										$datasource->begin();

										$succeeded = false;
										# First we set the password
										if($this->_set_member_password($memberInfo, $this->request->data['ForgotPassword']['new_password']))
										{
											# Then we expire the forgot password request
											$record['ForgotPassword']['expired'] = 1;
											$this->ForgotPassword->id = $record['ForgotPassword']['request_guid'];
											if($this->ForgotPassword->save($record))
											{
												if($datasource->commit())
												{
													# Success!
													$succeeded = true;
												}
											}
										}

										if($succeeded === true)
										{
											$this->Session->setFlash('Password successfully set.');
											$this->redirect(array('controller' => 'members', 'action' => 'login'));
										}
										else
										{
											$this->Session->setFlash('Unable to set password');
											$datasource->rollback();
											$this->redirect(array('controller' => 'pages', 'action' => 'forgot_password_error'));
										}
									}
								}
							}
						}
					}
				}
			}
	    }

	    private function _create_status_update_record($memberId, $adminId, $newStatus, $oldStatus)
	    {
	    	if($newStatus != null && $oldStatus != null && $newStatus != $oldStatus)
	    	{

		    	$this->Member->StatusUpdate->create();
		    	$this->Member->StatusUpdate->save(
		    		array(
		    			'StatusUpdate' => array(
		    				'member_id' => $memberId,
		    				'admin_id' => $adminId,
		    				'new_status' => $newStatus,
		    				'old_status' => $oldStatus,
		    				'timestamp' => null,
		    			),
		    		)
		    	);
		    }
	    }

	    private function _set_member_password($memberInfo, $newPassword)
	    {
	    	switch ($this->Krb->userExists($memberInfo['Member']['username'])) 
	    	{
	    		case TRUE:
	    			return $this->Krb->changePassword($memberInfo['Member']['username'], $newPassword);

	    		case FALSE:
	    			return $this->krb->addUser($memberInfo['Member']['username'], $newPassword);
	    		
	    		default:
	    			return false;
	    	}
	    }

	    public function send_membership_reminder($id = null)
	    {
	    	if($id != null)
	    	{
	    		$this->Member->id = $id;
				$memberInfo = $this->Member->read();
				$email = $this->prepare_email();
				$email->to( $memberInfo['Member']['email'] );
				$email->subject('Membership Info');
				$email->template('to_member_membership_reminder', 'default');
				$email->viewVars( array( 
					'memberId' => $id,
					 )
				);
				$email->send();

				$this->Session->setFlash('Member has been contacted');
				$this->redirect($this->referer());
	    	}
	    }

	    public function send_contact_details_reminder($id = null)
	    {
	    	if($id != null)
	    	{
	    		$this->Member->id = $id;
				$memberInfo = $this->Member->read();
				$email = $this->prepare_email();
				$email->to( $memberInfo['Member']['email'] );
				$email->subject('Membership Info');
				$email->template('to_member_contact_details_reminder', 'default');
				$email->viewVars( array( 
					'memberId' => $id,
					 )
				);
				$email->send();

				$this->Session->setFlash('Member has been contacted');
				$this->redirect($this->referer());
	    	}
	    }

	    public function send_so_details_reminder($id = null)
	    {
	    	if($id != null)
	    	{
	    		$this->Member->id = $id;
				$memberInfo = $this->Member->read();
				
				$this->_send_so_details($memberInfo);

				$this->Session->setFlash('Member has been contacted');
				$this->redirect($this->referer());
	    	}
	    }

	    public function _send_so_details($memberInfo)
	    {
			$memberEmail = $this->prepare_email();
			$memberEmail->to( $memberInfo['Member']['email'] );
			$memberEmail->subject('Bank Details');
			$memberEmail->template('to_member_so_details', 'default');
			$memberEmail->viewVars( array( 
				'name' => $memberInfo['Member']['name'],
				'reference' => $memberInfo['Account']['payment_ref'],
				'accountNum' => Configure::read('hms_so_accountNumber'),
				'sortCode' => Configure::read('hms_so_sortCode'),
				'accountName' => Configure::read('hms_so_accountName'),
				 )
			);
			$memberEmail->send();
	    }

	    public function view($id = null) 
	    {
	        $this->Member->id = $id;
	        $memberInfo = $this->Member->read();

	        # Sanitise data
		    $user = $this->Member->findByMemberId(AuthComponent::user('Member.member_id'));
		    $canSeeAll = Member::isInGroupMemberAdmin($user) || Member::isInGroupFullAccess($user);
		    if(!$canSeeAll)
		    {
		    	unset($memberInfo['Pin']);
		    	unset($memberInfo['Status']);
		    	unset($memberInfo['StatusUpdate']);

		    	// Only current members can see credit limit and balances
		    	if($user['Member']['member_status'] != 2)
		    	{
		    		unset($memberInfo['Member']['balance']);
		    		unset($memberInfo['Member']['credit_limit']);
		    	}
		    }
		    else
		    {
		    	if(empty($memberInfo['StatusUpdate']) == false)
		    	{
		    		$memberInfo['StatusUpdate'] = $this->Member->StatusUpdate->findById($memberInfo['StatusUpdate'][0]['id']);
		    	}
		    }

	        $this->set('member', $memberInfo);

	        $this->Nav->add('Edit', 'members', 'edit', array( $id ) );
	        $this->Nav->add('Change Password', 'members', 'change_password', array( $id ) );
			switch ($memberInfo['Member']['member_status']) 
			{
		        case 1: # Prospective member
		        	$this->Nav->add('Send Membership Reminder', 'members', 'send_membership_reminder', array($id));
		        	break;

		        case 2: # Current member
		            $this->Nav->add('Revoke Membership', 'members', 'set_member_status', array( $id, 3 ) );
		            break;

		        case 3: # Ex-member
		            $this->Nav->add('Reinstate Membership', 'members', 'set_member_status', array( $id, 2 ) );
		            break;

				case 5: # Waiting for contact details
					$this->Nav->add('Send Contact Details Reminder', 'members', 'send_contact_details_reminder', array($id));
		        	break;

		        case 6: # Prospective member
		            $this->Nav->add('Approve contact details', 'members', 'accept_details', array( $id ), 'positive' );
		            $this->Nav->add('Reject contact details', 'members', 'reject_details', array( $id ), 'negative' );
		            break;

		        case 7: # Waiting for SO
		        	$this->Nav->add('Send SO Details Reminder', 'members', 'send_so_details_reminder', array($id));
		        	$this->Nav->add('Approve Member', 'members', 'approve_member', array($id), 'positive');
		        	break;
		    }

		    $this->set('mailingLists', $this->_get_mailing_lists_and_subscruibed_status($memberInfo));
	    }

	    public function edit($id = null) 
	    {

	    	$this->set('groups', $this->Member->Group->find('list',array('fields'=>array('grp_id','grp_description'))));
	    	$this->set('statuses', $this->Member->Status->find('list',array('fields'=>array('status_id','title'))));
	    	# Add a value for using the existing account
	    	$accountsList =	$this->get_readable_account_list( array( -1 => 'Use Default' ) );

	    	$this->set('accounts', $accountsList);
			$this->Member->id = $id;
			$data = $this->Member->read(null, $id);

			# Can't be on this screen unless we've entered all the member details
			if($data['Member']['member_status'] == 5)
			{
				$this->redirect(array('action' => 'setup_details', $data['Member']['member_id']));
			}
			else
			{

				$mailingLists = $this->_get_mailing_lists_and_subscruibed_status($data);
				$this->set('mailingLists', $mailingLists);

				if ($this->request->is('get')) 
				{
				    $this->request->data = $this->sanitise_edit_data($data);
				} 
				else 
				{
					# Need to set some more info about the pin
					$this->request->data['Pin']['pin_id'] = $data['Pin']['pin_id'];

					# Clear the actual pin number though, so that won't get updated
					unset($this->request->data['Pin']['pin']);

					$this->request->data = $this->sanitise_edit_data($this->request->data);


				    if ($this->Member->saveAll($this->request->data)) 
				    {

				    	$flashMessage = 'Member details updated.';

				    	if(isset($this->request->data['MailingLists']))
				    	{
				    		if(!isset($this->request->data['MailingLists']['MailingLists']) ||
				    			!is_array($this->request->data['MailingLists']['MailingLists']))
				    		{
				    			$this->request->data['MailingLists']['MailingLists'] = array();
				    		}

				    		$flashMessage .= '<br>';
				    		$flashMessage .= $this->_update_mailing_list_subscriptions($id, $this->request->data['MailingLists']['MailingLists']);
				    	}


				    	$memberInfo = $this->set_account($this->request->data);
				    	$this->set_member_status_impl($data, $memberInfo);
						$this->update_status_on_joint_accounts($data, $memberInfo);

				        $this->Session->setFlash($flashMessage);
				        $this->redirect(array('action' => 'view', $id));
				    } 
				    else 
				    {
				        $this->Session->setFlash('Unable to update member details.');
				    }
				}
			}
		}

		private function _update_mailing_list_subscriptions($memberId, $subscribeToLists)
		{
			$resultMessage = '';

			# Grab a list of all the mailing lists we know about
			# including whether this member is subscribed to them or not
			$memberInfo = $this->Member->find('first', array('conditions' => array('Member.member_id' => $memberId)));
			$currentMailingLists = $this->_get_mailing_lists_and_subscruibed_status($memberInfo);

			foreach ($currentMailingLists as $mailingList) 
			{
				# Does the member want to be want to be subscribed to this list?
				$wantToBeSubscribed = in_array($mailingList['id'], $subscribeToLists);
				if($wantToBeSubscribed != $mailingList['subscribed'])
				{
					# Need to edit the subscription
					if($wantToBeSubscribed)
					{
						$this->MailChimp->subscribe($mailingList['id'], $memberInfo['Member']['email']);
    					if($this->MailChimp->error_code())
    					{
    						$resultMessage .= 'Unable to subscribe to: ' . $mailingList['name'] . ' because ' . $this->MailChimp->error_msg() . '</br>';
    					}
    					else
    					{
    						$resultMessage .= 'E-mail confirmation of mailing list subscription for: ' . $mailingList['name'] . ' has been sent.' . '</br>';	
    					}
					}
					else
					{
						$this->MailChimp->unsubscribe($mailingList['id'], $memberInfo['Member']['email']);
    					if($this->MailChimp->error_code())
    					{
    						$resultMessage .= 'Unable to un-subscribe from: ' . $mailingList['name'] . ' because ' . $this->MailChimp->error_msg() . '</br>';
    					}
    					else
    					{
    						$resultMessage .= 'Un-Subscribed from: ' . $mailingList['name'] . '</br>';
    					}
					}
				}
			}

			return $resultMessage;
		}

		private function _get_mailing_lists_and_subscruibed_status($memberInfo)
		{
			$mailingListsRet = $this->MailChimp->list_mailinglists();
		    if(!$this->MailChimp->error_code())
		    {
		    	$mailingLists = $mailingListsRet['data'];

		    	if($memberInfo != null)
		    	{
			    	$numMailingLists = count($mailingLists);
			    	for($i = 0; $i < $numMailingLists; $i++)
			    	{
			    		// Grab the list of subscribed members
			    		$subscribedMembers = $this->MailChimp->list_subscribed_members($mailingLists[$i]['id']);
			    		if(!$this->MailChimp->error_code())
			    		{
			    			// Extract the emails
			    			$emails = Hash::extract($subscribedMembers['data'], '{n}.email');
			    			// Are we subscribed to this list?
			    			$mailingLists[$i]['subscribed'] = (in_array($memberInfo['Member']['email'], $emails));
			    			if($i > 0)
			    			{
			    				$mailingLists[$i]['subscribed'] = rand() % 2 == 0;
			    			}
			    			// Can we view it?
			    			$mailingLists[$i]['canView'] = $this->AuthUtil->is_authorized('mailinglists', 'view', array( $mailingLists[$i]['id'] ));
			    		}
			    	}
			    }
		    	return $mailingLists;
		    }
		    return null;
		}

		private function sanitise_edit_data($data)
		{
			$user = AuthComponent::user();
		    $canSeeAll = Member::isInGroupMemberAdmin($user) || Member::isInGroupFullAccess($user);
		    if(!$canSeeAll)
		    {
		    	unset($data['Pin']);
		    	unset($data['Group']);
		    	unset($data['Member']['account_id']);
		    	unset($data['Member']['status_id']);
		    }

		    $isEditingSelf = $data['Member']['member_id'] == $user['Member']['member_id'];
		    if(!$isEditingSelf)
		    {
		    	unset($data['MailingLists']);
		    }

			return $data;
		}

		public function set_member_status($id, $newStatus)
		{
			$oldData = $this->Member->read(null, $id);
			$data = $oldData;
			$newData = $this->Member->set('member_status', $newStatus);

			$data['Member']['member_status'] = $newStatus;
			# Need to unset the group here, as it's not properly filled in
			# So it adds partial data to the database
			unset($data['Group']);
			if($this->Member->save($data, array( 'fieldList' => array( 'member_id', 'member_status' ) ) ))
			{
				$this->Session->setFlash('Member status updated.');

				$this->set_member_status_impl($oldData, $newData);
				$this->update_status_on_joint_accounts($data, $newData);
			}
			else
			{
				$this->Session->setFlash('Unable to update member status');
			}

			$this->redirect($this->referer());
		}

		private function set_member_status_impl($oldData, $newData)
		{
			if(isset($newData['Member']['member_status']))
			{
				$this->_create_status_update_record($oldData['Member']['member_id'], AuthComponent::user('Member.member_id'), $newData['Member']['member_status'], $oldData['Member']['member_status']);
			}

			$this->Member->clearGroupsIfMembershipRevoked($oldData['Member']['member_id'], $newData);
			$this->Member->addToCurrentMemberGroupIfStatusIsCurrentMember($oldData['Member']['member_id'], $newData);
			$this->notify_status_update($oldData, $newData);
		}

		private function update_status_on_joint_accounts($oldData, $newData)
		{
			# Find any members using the same account as this one, and set their status too
			foreach ($this->Member->find( 'all', array( 'conditions' => array( 'Member.account_id' => $oldData['Member']['account_id'], 'Member.account_id NOT' => null ) ) ) as $memberInfo) 
			{
				if($memberInfo['Member']['member_id'] != $oldData['Member']['member_id'])
				{
					$oldMemberInfo = $memberInfo;
					if(isset($newData['Member']['member_status']))
					{
						$memberInfo['Member']['member_status'] = $newData['Member']['member_status'];
					}
					$this->data = $memberInfo;
					$newMemberInfo = $this->Member->save($memberInfo);
					if($newMemberInfo)
					{
						$this->set_member_status_impl($oldMemberInfo, $newMemberInfo);
					}
				}
			}
		}

		private function notify_status_update($oldData, $newData)
		{
			if( isset($oldData['Member']['member_status']) && isset($newData['Member']['member_status']) )
			{
				if($oldData['Member']['member_status'] != $newData['Member']['member_status'])
				{
					# Notify all the member admins about the status change
					$email = $this->prepare_email_for_members_in_group(5);
					$email->subject('Member Status Change Notification');
					$email->template('notify_admins_member_status_change', 'default');
					
					$newStatusData = $this->Member->Status->find( 'all', array( 'conditions' => array( 'Status.status_id' => $newData['Member']['member_status'] ) ) );

					$email->viewVars( array( 
						'member' => $oldData['Member'],
						'oldStatus' => $oldData['Status']['title'],
						'newStatus' => $newStatusData[0]['Status']['title'],
						'memberAdmin' => AuthComponent::user('Member.name'),
						 )
					);

					$email->send();

					# Is this member being approved for the first time? If so we need to send out a message to the member admins
					# To tell them to e-mail the PIN etc to the new member
					$oldStatus = $oldData['Member']['member_status'];
					$newStatus = $newData['Member']['member_status'];
					if(	$newStatus == 2 &&
						$oldStatus == 1)
					{
						$approvedEmail = $this->prepare_email_for_members_in_group(5);
						$approvedEmail->subject('Member Approved!');
						$approvedEmail->template('notify_admins_member_approved', 'default');

						$approvedEmail->viewVars( array( 
							'member' => $oldData['Member'],
							'pin' => $oldData['Pin']['pin']
							)
						);

						$approvedEmail->send();
					}
				}
			}
		}

		public function email_members_with_status($status) 
		{

			Controller::loadModel('MemberEmail');

			$members = $this->Member->find('all', array('conditions' => array( 'Member.member_status' => $status )));
			$memberEmails = Hash::extract( $members, '{n}.Member.email' );

			$statusName = "Unknown";
			$statusId = $status;
			$statusList = $this->Member->Status->find( 'all', array( 'conditions' => array( 'status_id' => $status ) ) );
			if(count($statusList) > 0)
			{
				$statusName = $statusList[0]['Status']['title'];
			}

			$this->set('members', $members);
			$this->set('statusName', $statusName);
			$this->set('statusId', $status);

			if ($this->request->is('get')) 
			{
			}
			else 
			{
				$this->MemberEmail->set($this->data);
				if($this->MemberEmail->validates())
				{
					$subject = $this->request->data['MemberEmail']['subject'];
					$message = $this->request->data['Member']['message'];
					if( isset($subject) &&
						$subject != null &&
						strlen(trim($subject)) > 0 &&

						isset($message) &&
						$message != null &&
						strlen(trim($message)) > 0 )
					{
						# Send these out as seperate e-mails
						foreach ($memberEmails as $email) 
						{
							# Send the message out
							$email = $this->prepare_email();
							$email->to($memberEmails);
							$email->subject($subject);
							$email->template('default', 'default');
							$email->send($message);
						}

						$this->Session->setFlash('E-mail sent');
						$this->redirect(array('action' => 'index'));
					}
					else
					{
						$this->Session->setFlash('Unable to send e-mail');
					}
				}
			}
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

		public function login() 
		{
		    if ($this->request->is('post')) 
		    {
		        if ($this->Auth->login()) 
		        {
		        	$memberInfo = AuthComponent::user();
		        	# Set the last login time
		        	unset($memberInfo['MemberAuth']);
		        	$memberInfo['MemberAuth']['member_id'] = $memberInfo['Member']['member_id'];
		        	$memberInfo['MemberAuth']['last_login'] = date( 'Y-m-d H:m:s' );
		        	$this->Member->MemberAuth->save($memberInfo);
		            $this->redirect($this->Auth->redirect());
		        } 
		        else 
		        {
		            $this->Session->setFlash(__('Invalid username or password, try again'));
		        }
		    }
		}

		public function logout() 
		{
		    $this->redirect($this->Auth->logout());
		}

		# Function to make it easier to enter the data for an existing (pre HMS) member
		public function add_existing_member($id = null)
		{
			if($id == null)
			{
				$id = 1;
			}

			$this->Member->id = $id;
	        $memberInfo = $this->Member->read();

	        $this->set('statuses', $this->Member->Status->find('list'));
	        $this->set('memberInfo', $memberInfo);

	        if($this->request->is('put'))
	        {
	        	# Populate the account info
	        	$this->request->data['Account']['member_id'] = $id;

	        	$accountInfo = $this->Member->Account->save($this->request->data);
	        	if($accountInfo)
	        	{
	        		$this->request->data['Account']['account_id'] = $accountInfo['Account']['account_id']; 
	        		$this->request->data['Member']['account_id'] = $accountInfo['Account']['account_id'];

	        		if($this->Member->saveAll($this->request->data, array('validate' => false)))
		        	{
		        		$this->Session->setFlash('Member details added');

		        		$this->redirect(array('action' => 'add_existing_member', $id + 1));
		        	}
		        	else
		        	{
		        		$this->Session->setFlash('Member update failed');
		        	}
	        	}
	        }
	        else
	        {
	        	$this->request->data = $memberInfo;
	        }
		}

		private function get_readable_account_list($initialElement = null) 
		{
			# Grab a list of member names and ID's and account id's
			$memberList = $this->Member->find('all', array( 'fields' => array( 'member_id', 'name', 'account_id' )));

			# Create a list with account_id and member_names
			foreach ($memberList as $memberInfo) 
			{
				if( isset($accountList[$memberInfo['Member']['account_id']]) == false )
				{
					$accountList[$memberInfo['Member']['account_id']] = array( );
				}
				array_push($accountList[$memberInfo['Member']['account_id']], $memberInfo['Member']['name']);
				natcasesort($accountList[$memberInfo['Member']['account_id']]);
			}

			$accountNameList = $this->Member->Account->find('list', array( 'fields' => array( 'account_id', 'payment_ref' )));

			foreach ($accountList as $accountId => $members) 
			{
				$formattedMemberList = $members[0];
				$numMembers = count($members);
				for($i = 1; $i < $numMembers; $i++)
				{
					$prefix = ', ';
					if($i = $numMembers - 1)
					{
						$prefix = ' & ';
					}
					$formattedMemberList .= $prefix . $members[$i];
				}

				$readableAccountList[$accountId] = $formattedMemberList;
				# Append the payment ref if any
				if(isset($accountNameList[$accountId]))
				{
					$readableAccountList[$accountId] .= ' [ ' . $accountNameList[$accountId] . ' ]';
				}
			}

			# Sort it alphabetically
			natcasesort($readableAccountList);

			# If the initial item is set, we need to make a new list starting with that
			if(	isset($initialElement) &&
				$initialElement != null)
			{
				$tempArray = $initialElement;
				foreach ($readableAccountList as $key => $value) 
				{
					if($key >= 0)
					{
						$tempArray[$key] = $value;
					}
				}

				$readableAccountList = $tempArray;
			}

			return $readableAccountList;
		}

		private function set_account($memberInfo, $validateMember = true)
		{
			if( isset($memberInfo['Member']['account_id']) )
			{
				# Do we need to create a new account?
	            if($memberInfo['Member']['account_id'] < 0)
	            {
	            	# Check if there's already an account for this member
	            	# This could happen if they started off on their own account, moved to a joint one and then they wanted to move back

	            	$existingAccountInfo = $this->Member->Account->find('first', array( 'conditions' => array( 'Account.member_id' => $memberInfo['Member']['member_id'] ) ));
	            	if(	isset($existingAccountInfo) &&
	            		count($existingAccountInfo) > 0 &&
	            		empty($existingAccountInfo) == false)
	            	{
	            		# Already an account, just use that
	            		$memberInfo['Member']['account_id'] = $existingAccountInfo['Account']['account_id'];
	            		$memberInfo['Account'] = $existingAccountInfo['Account'];
	            		$this->Member->Account->save($memberInfo);
	            	}
	            	else
	            	{
	            		# Need to create one
	            		$memberInfo['Account']['member_id'] = $memberInfo['Member']['member_id'];
	            		$memberInfo['Account']['payment_ref'] = $this->Member->Account->generate_payment_ref();
	            		$accountInfo = $this->Member->Account->save($memberInfo);

	            		$memberInfo['Member']['account_id'] = $accountInfo['Account']['account_id'];
	            	}
	            }

	            if($validateMember)
	            {
	           		$this->Member->save($memberInfo);
	           	}
	           	else
	           	{
	           		$this->Member->save($memberInfo, array('validate' => false));
	           	}
	        }
            return $memberInfo;
		}
	}
?>