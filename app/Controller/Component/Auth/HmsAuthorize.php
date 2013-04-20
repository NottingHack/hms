<?php
App::uses('BaseAuthorize', 'Controller/Component/Auth');

# AT [16/09/2012] HmsAuthorize is basically the same as ControllerAuthorize 
# in that it calls the 'isAuthorized' method of the controller. However
# HmsAuthorize doesn't discard the CakeRequest object, as it is used in the
# controller instead of $this->request
class HmsAuthorize extends BaseAuthorize {

	public function controller(Controller $controller = null) {
		if ($controller) {
			if (!method_exists($controller, 'isAuthorized')) {
				throw new CakeException(__d('cake_dev', '$controller does not implement an isAuthorized() method.'));
			}
		}
		return parent::controller($controller);
	}

	public function authorize($user, CakeRequest $request) 
	{
		return (bool)$this->_Controller->isAuthorized($user, $request);
	}

}
