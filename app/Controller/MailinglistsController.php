<?php

	class MailingListsController extends AppController 
	{
	    
	    public $helpers = array('Html', 'Form');

	    public function isAuthorized($user, $request)
	    {
	    	return true;
	    }

	    public function index() 
	    {
	    	$result = $this->MailingList->listMailinglists();
	    	$mailingLists = $result['data'];

	    	$memberEmails = $this->_getMemberEmails();

	    	for($i = 0; $i < count($mailingLists); $i++)
	    	{
	    		$mailingLists[$i] = $this->_getListStats($mailingLists[$i], $memberEmails);
	    	}

	    	$this->set('mailingLists', $mailingLists);
	    	$this->Nav->addExternal('Edit Lists', 'https://admin.mailchimp.com/lists/' );
	    }

	    public function view($id = null) 
	    {
	    	$memberEmails = $this->_getMemberEmails();
	    	$result = $this->MailingList->listMailinglists($id, false);
	    	$mailingList = $this->_getListStats($result['data'][0], $memberEmails);

	    	$membersSubscribed = array();
	    	$membersNotSubscribed = array();

	    	$this->loadModel('Member');
	    	$allMembers = $this->Member->getMemberSummaryAll(false);
	    	
	    	foreach($allMembers as $memberInfo)
	    	{
	    		if(in_array($memberInfo['email'], $mailingList['stats']['hms_members']))
	    		{
	    			array_push($membersSubscribed, $memberInfo);
	    		}
	    		else
	    		{
	    			array_push($membersNotSubscribed, $memberInfo);
	    		}
	    	}

	    	$this->set('mailingList', $mailingList);
	    	$this->set('membersSubscribed', $membersSubscribed);
	    	$this->set('membersNotSubscribed', $membersNotSubscribed);

	    	$this->Nav->addExternal('Edit List', 'https://admin.mailchimp.com/lists/' );
	    }

	    private function _getListStats($list, $memberEmails)
	    {
	    	$subscribersResult = $this->MailingList->listSubscribers($list['id'], false);
    		$subscriberEmails = Hash::extract($subscribersResult['data'], '{n}.email');
    		$memberSubscriberEmails = array_intersect($memberEmails, $subscriberEmails);

    		$list['stats']['hms_member_count'] = count($memberSubscriberEmails);
    		$list['stats']['hms_members'] = $memberSubscriberEmails;

    		return $list;
	    }

	    private function _getMemberEmails()
	    {
	    	$this->loadModel('Member');
	    	$memberEmails = $this->Member->getEmailsForAllMembers();
	    	return $memberEmails;
	    }
	}
?>