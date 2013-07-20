<?php

	App::uses('AppController', 'Controller');

	class EmailRecordsController extends AppController 
	{
	    public $helpers = array('Html', 'Form');

	    //! Test to see if a user is authorized to make a request.
	    /*!
	    	@param array $user Member record for the user.
	    	@param CakeRequest $request The request the user is attempting to make.
	    	@retval bool True if the user is authorized to make the request, otherwise false.
	    	@sa http://api20.cakephp.org/class/cake-request
	    */
	    public function isAuthorized($user, $request)
	    {
	    	Controller::loadModel('Member');

	    	if(parent::isAuthorized($user, $request))
	    	{
	    		return true;
	    	}

	    	$memberId = $this->Member->getIdForMember($user);
	    	$memberIsMembershipAdmin = $this->Member->GroupsMember->isMemberInGroup( $memberId, Group::MEMBERSHIP_ADMIN );
	    	$memberIsOnMembershipTeam = $this->Member->GroupsMember->isMemberInGroup( $memberId, Group::MEMBERSHIP_TEAM );
	    	$actionHasParams = isset( $request->params ) && isset($request->params['pass']) && count( $request->params['pass'] ) > 0;
	    	$memberIdIsSet = is_numeric($memberId);

	    	$firstParamIsMemberId = ( $actionHasParams && $memberIdIsSet && $request->params['pass'][0] == $memberId );

	    	switch ($request->action) 
	    	{
	    		case 'index':
	    			return ($memberIsMembershipAdmin || $memberIsOnMembershipTeam);

	    		case 'view':
	    			return ($memberIsMembershipAdmin || $memberIsOnMembershipTeam || $firstParamIsMemberId);
	    	}

	    	return false;
	    }

	    private function _setupEmailListView($emails)
	    {
	    	Controller::loadModel('Member');
	    	$this->set('memberNames', $this->Member->getBestMemberNames());
	    	$this->set('emails', $emails);
	    }

	    public function index()
	    {
	    	$this->_setupEmailListView($this->EmailRecord->getAllEmails());
	    }

	    public function view($id = null) 
	    {
	    	$viewerId = $this->_getLoggedInMemberId();

	    	$memberHasFullAccess = $this->Member->GroupsMember->isMemberInGroup($viewerId, Group::FULL_ACCESS);
			$memberIsMembershipAdmin = $this->Member->GroupsMember->isMemberInGroup($viewerId, Group::MEMBERSHIP_ADMIN);
			$memberIsOnMembershipTeam = $this->Member->GroupsMember->isMemberInGroup($viewerId, Group::MEMBERSHIP_TEAM);

	    	$canView = 
	    		is_numeric($id) &&
	    		( $memberHasFullAccess || $memberIsMembershipAdmin || $memberIsOnMembershipTeam || $id == $viewerId );

	        if($canView)
	        {
	        	$this->_setupEmailListView($this->EmailRecord->getAllEmailsForMember($id));
	        	$this->set('id', $id);
	        }
	        else
	        {
	        	return $this->redirect($this->referer());
	        }
	    }
	}
?>