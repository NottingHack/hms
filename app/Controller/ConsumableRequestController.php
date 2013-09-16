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
	    	Controller::loadModel('Member');

	    	if(parent::isAuthorized($user, $request))
	    	{
	    		return true;
	    	}

	    	return true;
	    }

	    public function index()
	    {
	    	$this->set('requests', $this->ConsumableRequest->getAll());
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
	}
?>