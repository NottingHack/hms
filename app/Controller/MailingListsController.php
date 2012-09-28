<?php

	class MailingListsController extends AppController {
	    
	    public $helpers = array('Html', 'Form');

	    public $components = array('MailChimp');

	    public function isAuthorized($user, $request)
	    {
	    	return true;
	    }

	    public function index() {
	    	$result = $this->MailChimp->list_mailinglists(false);

	    	$mailingLists = $result['data'];

	    	$memberEmails = $this->_get_member_emails();

	    	for($i = 0; $i < count($mailingLists); $i++)
	    	{
	    		$mailingLists[$i] = $this->_get_list_stats($mailingLists[$i], $memberEmails);
	    	}

	    	$this->set('mailingLists', $mailingLists);
	    	$this->Nav->addExternal('Edit Lists', 'https://admin.mailchimp.com/lists/' );
	    }

	    public function view($id = null) {

	    	$memberEmails = $this->_get_member_emails();
	    	$result = $this->MailChimp->get_mailinglist($id, false);
	    	$mailingList = $this->_get_list_stats($result['data'][0], $memberEmails);

	    	$membersSubscribed = array();
	    	$membersNotSubscribed = array();
	    	foreach($this->Member->find('all') as $memberInfo)
	    	{
	    		if(in_array($memberInfo['Member']['email'], $mailingList['stats']['hms_members']))
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

	    private function _get_list_stats($list, $memberEmails)
	    {
	    	$subscribersResult = $this->MailChimp->list_subscribed_members( $list['id'], false);
    		$subscriberEmails = Hash::extract($subscribersResult['data'], '{n}.email');
    		$memberSubscriberEmails = array_intersect($subscriberEmails, $memberEmails);
    		$list['stats']['hms_member_count'] = count($memberSubscriberEmails);
    		$list['stats']['hms_members'] = $memberSubscriberEmails;

    		return $list;
	    }

	    private function _get_member_emails()
	    {
	    	$this->loadModel('Member');
	    	$memberEmails = $this->Member->find('list', array( 'fields' => array( 'Member.email' ) ));
	    	return $memberEmails;
	    }
	}
?>