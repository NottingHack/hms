<?php

App::uses('Component', 'Controller');

App::import('Lib/MailChimp', 'MCAPI');	
App::uses('PhpReader', 'Configure');
Configure::config('default', new PhpReader());
Configure::load('mailchimp', 'default');

class MailChimpComponent extends Component {

	var $apiKey = '';
	var $api = null;
	static $resultCache;

	public function __construct(ComponentCollection $collection, $settings = array())
	{
		$this->apikey = Configure::read('mailchimp_apiKey');
		$this->api = new MCAPI($this->apikey);
	}

	public function list_mailinglists()
	{
		$cacheName = $this->_get_cache_name(__FUNCTION__);
		$cachedResult = $this->_get_cached_result($cacheName);
		if($cachedResult !== false)
		{
			return $cachedResult;
		}
		$result = $this->api->lists();
		$this->_cache_result($cacheName, $result);
		return $result;
	}

	public function list_subscribed_members($listId)
	{
		$cacheName = $this->_get_cache_name(__FUNCTION__, array($listId));
		$cachedResult = $this->_get_cached_result($cacheName);
		if($cachedResult != null)
		{
			return $cachedResult;
		}
		$result = $this->api->listMembers($listId, 'subscribed', null, 0, 5000 );
		$this->_cache_result($cacheName, $result);
		return $result;
	}

	public function error_code()
	{
		return $this->api->errorCode;
	}

	public function error_msg()
	{
		return $this->api->errorMessage;
	}

	private function _get_cache_name($functionName, $params = array())
	{
		$name = $functionName;
		foreach ($params as $val) {
			$name .= $val;
		}
		return $name;
	}

	private function _cache_result($function, $data)
	{
		Cache::write($function, $data, 'default');
	}

	private function _get_cached_result($function)
	{
		return Cache::read($function, 'default');
	}
}

?>