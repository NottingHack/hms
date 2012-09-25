<?php

App::uses('Component', 'Controller');

App::import('Lib/MailChimp', 'MCAPI');	
App::uses('PhpReader', 'Configure');
Configure::config('default', new PhpReader());
Configure::load('mailchimp', 'default');

class MailChimpComponent extends Component {

	var $apiKey = '';
	var $api = null;

	public function __construct(ComponentCollection $collection, $settings = array())
	{
		$this->apikey = Configure::read('mailchimp_apiKey');
		$this->api = new MCAPI($this->apikey);
	}

	public function list_mailinglists()
	{
		return $this->api->lists();
	}

	public function list_subscribed_members($listId)
	{
		return $this->api->listMembers($listId, 'subscribed', null, 0, 5000 );
	}

	public function error_code()
	{
		return $this->api->errorCode;
	}

	public function error_msg()
	{
		return $this->api->errorMessage;
	}


}

?>