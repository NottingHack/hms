<?php

App::uses('Component', 'Controller');

# AT [16/09/2012] NavComponent exists to check if a navigation link is
# allowed (by checking the authorization), allowed links are added to a list
# which can then be rendered in the view
class NavComponent extends Component {
	
	public $components = array( 'AuthUtil' );

	var $allowedActions = array();

	# Add a navigation option to an external URL
	public function addExternal($text, $url)
	{
		array_push($this->allowedActions, array( 'text' => $text, 'url' => $url ) );
	}

	# Add a navigation option, testing if it's authorized first
	public function add($text, $controller, $action, $params = array())
	{
		if( $this->AuthUtil->is_authorized($controller, $action, $params) )
		{
			array_push($this->allowedActions, array( 'text' => $text, 'controller' => $controller, 'action' => $action, 'params' => $params ) );
		}
	}

	public function get_allowed_actions()
	{
		return $this->allowedActions;
	}

}

?>