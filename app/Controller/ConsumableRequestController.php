<?php

	App::uses('AppController', 'Controller');

	class ConsumableRequestController extends AppController 
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
	    	if(parent::isAuthorized($user, $request))
	    	{
	    		return true;
	    	}

	    	return true;
	    }

	    //! Perform any actions that should be performed before any controller action
	    /*!
	    	@sa http://api20.cakephp.org/class/controller#method-ControllerbeforeFilter
	    */
	    public function beforeFilter() 
	    {
	    	$this->Auth->allow(array(
	    		'index',
	    		'view',
	    		'add',
	    	));
	    }

	    public function index()
	    {
	    	return $this->redirect(array('controller' => 'ConsumableRequest', 'action' => 'listRequests', 0));
	    }

	    public function listRequests($filterId = null)
	    {
	    	$memberId = $this->_getLoggedInMemberId();
	    	$filtersAndCounts = $this->ConsumableRequest->getRequestCounts($memberId);

	    	if( !(is_numeric($filterId) && Hash::check($filtersAndCounts, "{n}[id=$filterId]")) )
	    	{
	    		return $this->redirect(array('controller' => 'ConsumableRequest', 'action' => 'listRequests', 0));
	    	}

	    	// Add the 'current' status to the filters and counts
	    	for($i = 0; $i < count($filtersAndCounts); $i++)
	    	{
	    		$filtersAndCounts[$i]['current'] = $filtersAndCounts[$i]['id'] == $filterId;
	    	}

	    	// Need to change the name of the first count
	    	$filtersAndCounts[0]['name'] = 'Requests Involving You';

	    	// Build up the list of requests and headers
	    	$requestsAndHeaders = array();
	    	if($filterId == 0)
	    	{
	    		$requestData = $this->ConsumableRequest->getRequestsInvolvingMember($memberId);
	    		$requestsAndHeaders['Your Requests'] = $requestData['openedBy'];
	    		$requestsAndHeaders['Requests You Commented On'] = $requestData['commentedOn'];
	    	}
	    	else
	    	{
	    		$header = $filtersAndCounts[$filterId]['name'] . ' Requests';
	    		$statusId = $filtersAndCounts[$filterId]['id'];
	    		$requestsAndHeaders[$header] = $this->ConsumableRequest->getAllWithStatus($statusId);
	    	}

	    	$this->set('counts', $filtersAndCounts);
	    	$this->set('requests', $requestsAndHeaders);
	    }

	    public function view($id)
	    {
	    	try
	    	{
	    		$this->set('request', $this->ConsumableRequest->get($id));
	    	}
	    	catch(InvalidArgumentException $e)
	    	{
	    		return $this->redirect($this->referer());
	    	}
	    }

	    public function add()
	    {
	    	if($this->request->is('post'))
			{
				$loggedInMemberId = $this->_getLoggedInMemberId();
				if($loggedInMemberId === 0)
				{
					$loggedInMemberId = null;
				}

				try
				{
					if($this->ConsumableRequest->add($this->request->data, $loggedInMemberId))
					{
						$this->Session->setFlash('Created request');
						return;
					}
				}
				catch(InvalidArgumentException $e)
				{
					// Nothing to do
				}

				$this->Session->setFlash('Unable to create request');
			}	
	    }
	}
?>