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
	    	$this->set('requests', $this->ConsumableRequest->getOverviewData());
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