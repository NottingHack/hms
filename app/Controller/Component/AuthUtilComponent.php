<?php

App::uses('Component', 'Controller');

# AT [16/09/2012] AuthUtil component allows one to check to see if an action is
# authorized on any controller. It is slow and it is sad about this.
class AuthUtilComponent extends Component {
	
	public $components = array( 'Auth' );

	var $controllers = array();
	var $currentController = null;

	public function initialize(Controller $controller) {
		parent::initialize($controller);
		$this->currentController = $controller;
	}

	public function is_authorized($controller, $action, $params = array())
	{
		# AT [25/09/2012] Build the url and CakeRequest
		$url = '/' . $controller . '/' . $action;

		if(count($params) > 0)
		{
			$url .= '/' . join($params, '/');
		}

		$request = new CakeRequest($url, false);
		$request->addParams(array(
			'plugin' => null,
			'controller' => $controller,
			'action' => $action,
			'pass' => $params,
		));

		# AT [25/09/2012] Grab the controller, this may have to create it :(
		$controllerObj = $this->get_controller($controller);
		# AT [25/09/2012] Have to call beforeFilter to set-up the auth properly
		$controllerObj->beforeFilter();

		# AT [29/09/2012] First we need to check if the user must be logged in to do this action
		$allowedActions = $controllerObj->Auth->allowedActions;
		$isAllowed = (
			$allowedActions == array('*') || # AT [29/09/2012] Allow all actions?
			in_array($action, array_map('strtolower', $allowedActions))
		);

		if($isAllowed)
		{
			return true;
		}

		$user = AuthComponent::user();
		return $controllerObj->Auth->isAuthorized($user, $request);
	}

	private function get_controller($name)
	{
		# AT [25/09/2012] First check to see if we have a cached copy of this controller
		if(array_key_exists($name, $this->controllers))
		{
			return $this->controllers[$name];
		}
		else
		{
			# AT [25/09/2012] Are we lucky enough to be checking isAuthorized on the
			# controller we're currently attached to?
			$controllerToUse = null;
			$controllerName = '';
			if($this->currentController != null &&
				$this->currentController->name == $name)
			{
				# AT [25/09/2012] Awesome, this is not too slow
				$controllerToUse = $this->currentController;
				$controllerName = $name;
			}

			if($controllerToUse == null)
			{
				# AT [25/09/2012] Nope, we are left to construct the controller and everything
				$controllers = App::objects('controller');
				foreach($controllers as $controllerClassName)
				{
					$controllerName = strtolower(str_replace('Controller', '', $controllerClassName));
					if($controllerName == $name)
					{
						App::import('Controller', $controllerName);
						$controllerToUse = new $controllerClassName;

						$collection = new ComponentCollection();
						$collection->init($controllerToUse);
						$controllerToUse->Auth = new AuthComponent($collection);

						break;
					}
				}
			}

			if($controllerToUse != null)
			{
				$this->controllers[$controllerName] = $controllerToUse;
				return $controllerToUse;
			}
		}
		return null;
	}
}

?>