<?php
/**
 * 
 * PHP 5
 *
 * Copyright (C) HMS Team
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     HMS Team
 * @package       plugins.Tools.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Controller for Ideas, allows a user to view, add, and vote on ideas.
 */
class ToolsToolsController extends ToolsAppController {
	/**
	 * Views rendered from this controller will have access to the following helpers.
	 * @var array
	 */
	public $helpers = array('Html', 'Form');

	/**
	 * The models we will need in this controller
	 * @var array
	 */
	public $uses = array('Tools.ToolsTool', 'Tools.ToolsGoogle');

	/**
	 * List of components this controller uses.
	 * @var array
	 */
	public $components = array('RequestHandler');

	/**
	 * Google identity
	 * @var string
	 */
	private $googleId = 'bookings@nottinghack.org.uk';

	/**
	 * Options used when paginating ideas.
	 * @var array
	 */
	public $paginate = array(
		'limit' => 5,
		'order' => array(
			'Tool.tool_name' => 'desc'
		)
	);

	/**
	 * call the parent constructor and setup the google model
	 *
	 * @param CakeRequest
	 * @param CakeResponse
	 */
	public function __construct($request, $response) {
		// Make sure we call the parent constructor
		parent::__construct($request, $response);

		$this->ToolsGoogle->setIdentity($this->googleId);
	}

	public function setupGoogle() {
		
		if (!$this->ToolsGoogle->authorised()) {
			$authorised = false;
			$this->set("authurl", $this->ToolsGoogle->getAuthUrl());
		}
		else {
			$authorised = true;
			$token = $this->ToolsGoogle->getAccessToken();

			$this->set("access_token", $token['access_token']);
			$this->set("expires_in", $token['expires_in']);
			$this->set("refresh_token", $token['refresh_token']);
		}

		$this->set("authorised", $authorised);
		$this->set("identity", $this->googleId);
	}

	public function oauth2callback() {

		if (isset($_GET['code'])) {
			$token = $this->ToolsGoogle->authenticate($_GET['code']);

			// extract and save refreshToken
			if (isset($token['refresh_token']) && $token['refresh_token']) {
				// delete any existing refresh tokens
				$this->ToolsGoogle->deleteAllRefreshTokens();

				// Save the new one
				$this->ToolsGoogle->saveRefreshToken($token['refresh_token']);

				$this->redirect(array(
					'plugin'		=>	'Tools',
					'controller'	=>	'ToolsTools',
					'action'		=>	'setupGoogle'
					));
			}
			else {
				// refresh token not in response.
			}
		}
		else {
			// didn't get a code, why not??
		}
	}

	/**
	 * Show a list of all tools.
	 */
	public function index() {
		if ($this->isAuthorized($this->_getUserID(), $this->request)) {
			// Get the tools
			$tools = $this->ToolsTool->find('all');

			// Is user Full access
			$fullAccess = false;
			if ($this->Member->GroupsMember->isMemberInGroup( $this->_getUserID(), Group::FULL_ACCESS )) {
				$fullAccess = true;
			}

			for ($i = 0; $i < count($tools); $i++) {
				// is user inducted on this tool?
				$inducted = $this->ToolsTool->isUserInducted($tools[$i]['Tool']['tool_id']);
				// is user an inductor on this tool?
				$inductor = $this->ToolsTool->isUserAnInductor($tools[$i]['Tool']['tool_id']);

				$tools[$i]['Tool']['next_booking'] = $this->ToolsGoogle->getNextBooking($tools[$i]['Tool']['tool_calendar']);
				// all users can do these actions
				$tools[$i]['Tool']['actions'] = array();
				$tools[$i]['Tool']['actions']['Access Calendar'] = array('plugin' => 'Tools', 'controller' => 'ToolsTools', 'action' => 'publicAccess', $tools[$i]['Tool']['tool_id']);

				if ($inducted) {
					$tools[$i]['Tool']['actions']['Book timeslot'] = array('plugin' => 'Tools', 'controller' => 'ToolsTools', 'action' => 'createBooking', $tools[$i]['Tool']['tool_id']);
					$tools[$i]['Tool']['actions']['View my bookings'] = array('plugin' => 'Tools', 'controller' => 'ToolsTools', 'action' => 'bookings', $tools[$i]['Tool']['tool_id']);
				}
				if ($inductor) {
					$tools[$i]['Tool']['actions']['Induct user'] = array('plugin' => 'Tools', 'controller' => 'ToolsTools', 'action' => 'induct', $tools[$i]['Tool']['tool_id']);
				}
				if ($fullAccess) {
					$tools[$i]['Tool']['actions']['Edit'] = array('plugin' => 'Tools', 'controller' => 'ToolsTools', 'action' => 'edit', $tools[$i]['Tool']['tool_id']);
				}
			}

			// Set the view variables
			$this->set('tools', $tools);
		}
	}

	/**
	 * Add a tool.  Only available to admins
	 */
	public function add() {
		if ($this->isAuthorized($this->_getUserID(), $this->request)) {
			// is google setup?
			if (!$this->ToolsGoogle->authorised()) {
				$this->redirect(array(
					'plugin'		=>	'Tools',
					'controller'	=>	'ToolsTools',
					'action'		=>	'setupGoogle'
					));
			}

			if ($this->request->is('post')) {
				// first, save the entered details as a new tool
				// this will throw validation errors early.
				$this->ToolsTool->create();

				$saveFields = array('tool_name', 'tool_restrictions', 'tool_pph', 'tool_address');
				if ($this->ToolsTool->save($this->request->data, true, $saveFields)) {
					// The data is saved, and validated.
					// This means that tool_name is definitely unique.
					// we now need to create the calendar for the tool and save to the DB
					$calendar = $this->ToolsGoogle->createCalendar($this->request->data['Tool']['tool_name']);
					if ($calendar) {
						// save into the DB
						$this->ToolsTool->saveField('tool_calendar', $calendar);
						$this->Session->setFlash(__('Tool Added.'));
						$this->redirect(array(
							'plugin'		=>	'Tools',
							'controller'	=>	'ToolsTools',
							'action'		=>	'index'
							));
					}
					else {
						// delete the entire record!
						$this->Session->setFlash(__('Unable to add your Tool.'));
						$this->ToolsTool->delete($this->ToolsTool->id);
					}
				}
				else {
					$this->Session->setFlash(__('Unable to add your Tool.'));
				}
			}
		}
	}

	public function edit($tool_id = null) {
		if (!$tool_id) {
			throw new NotFoundException(__('Invalid tool'));
		}

		$tool = $this->ToolsTool->findByToolId($tool_id);
		if (!$tool) {
			throw new NotFoundException(__('Invalid tool'));
		}
		
		if ($this->request->is('post') || $this->request->is('put')) {
			// If the form data can be validated and saved...
			$this->ToolsTool->id = $tool_id;
			if ($this->ToolsTool->save($this->request->data)) {
				$this->Session->setFlash('Tool Saved!');
				return $this->redirect(array(
					'plugin'		=>	'Tools',
					'controller'	=>	'ToolsTools',
					'action'		=>	'index'
					));
			}
		}
		else {
			debug("crap");
		}

		if (!$this->request->data) {
			$this->request->data = $tool;
		}
	}

	public function publicAccess($tool_id) {
		if (!$tool_id) {
			throw new NotFoundException(__('Invalid tool'));
		}

		$tool = $this->ToolsTool->findByToolId($tool_id);
		if (!$tool) {
			throw new NotFoundException(__('Invalid tool'));
		}

		$addresses = $this->ToolsGoogle->getPublicAddresses($tool['Tool']['tool_calendar']);
		$this->set("addresses", $addresses);

		$this->set("tool", $tool['Tool']['tool_name']);

	}
}
?>