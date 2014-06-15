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
	public $uses = array('Tools.ToolsTool', 'Tools.ToolsGoogle', 'Member');

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
	 * Booking types
	 */
	const TYPE_NORMAL = "normal";
	const TYPE_INDUCTION = "induction";
	const TYPE_MAINTAIN = "maintenance";

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

			for ($i = 0; $i < count($tools); $i++) {
				$tools[$i]['Tool']['next_booking'] = $this->ToolsGoogle->getNextBooking($tools[$i]['Tool']['tool_calendar']);
				
				// The view action
				$tools[$i]['Tool']['view'] = array(
					'image'	=> 'icon_calendar.png',
					'link'	=> array('plugin' => 'Tools', 'controller' => 'ToolsTools', 'action' => 'view', $tools[$i]['Tool']['tool_id']),
					);
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
			$this->loadModel('Tools.ToolsTool');
			$restricted = array(ToolsTool::UNRESTRICTED => 'Unrestricted', ToolsTool::RESTRICTED => 'Restricted');
			$this->set("restricted", $restricted);

			if ($this->request->is('post')) {
				// first, save the entered details as a new tool
				// this will throw validation errors early.
				$this->ToolsTool->create();

				$saveFields = array('tool_name', 'tool_restrictions', 'tool_pph', 'tool_booking_length', 'tool_length_max', 'tool_bookings_max');
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

	/**
	 * Edit a tool
	 *
	 * @param int the tool ID
	 */
	public function edit($toolId = null) {
		if (!$toolId) {
			throw new NotFoundException(__('Invalid tool'));
		}

		$tool = $this->ToolsTool->findByToolId($toolId);
		if (!$tool) {
			throw new NotFoundException(__('Invalid tool'));
		}
		
		$this->loadModel('Tools.ToolsTool');
		$restricted = array(ToolsTool::UNRESTRICTED => 'Unrestricted', ToolsTool::RESTRICTED => 'Restricted');
		$this->set("restricted", $restricted);

		if ($this->request->is('post') || $this->request->is('put')) {
			// If the form data can be validated and saved...
			$this->ToolsTool->id = $toolId;
			if ($this->ToolsTool->save($this->request->data)) {
				$this->Session->setFlash('Tool Saved!');
				return $this->redirect(array(
					'plugin'		=>	'Tools',
					'controller'	=>	'ToolsTools',
					'action'		=>	'index'
					));
			}
		}

		if (!$this->request->data) {
			$this->request->data = $tool;
		}
	}

	/**
	 * Shows the links to the calendar
	 *
	 * @param int the tool ID
	 */
	public function publicAccess($toolId) {
		if (!$toolId) {
			throw new NotFoundException(__('Invalid tool'));
		}

		$tool = $this->ToolsTool->findByToolId($toolId);
		if (!$tool) {
			throw new NotFoundException(__('Invalid tool'));
		}

		$addresses = $this->ToolsGoogle->getPublicAddresses($tool['Tool']['tool_calendar']);
		$this->set("addresses", $addresses);

		$this->set("tool", $tool);
	}

	/**
	 * The main page for each tool.
	 * 
	 * @param int the tool ID
	 */
	public function view($toolId) {
		// check we have a valid tool
		if (!$toolId) {
			throw new NotFoundException(__('Invalid tool'));
		}

		$tool = $this->ToolsTool->findByToolId($toolId);
		if (!$tool) {
			throw new NotFoundException(__('Invalid tool'));
		}

		if (isset($this->request->query['mon'])) {
			$monday = new DateTime($this->request->query['mon']);
		}
		else {
			$monday = $this->__getMonday();
		}
		$events = $this->ToolsGoogle->getWeeksEvents($monday, $tool['Tool']['tool_calendar']);
		//debug($events);

		$this->set('tool', $tool);
		$this->set('events', $events);
		$this->set('monday', $monday);
	}

	/**
	 * Adds a booking
	 *
	 * @param int the tool ID
	 */
	public function addBooking($toolId) {
		if (!$toolId) {
			throw new NotFoundException(__('Invalid tool'));
		}

		$tool = $this->ToolsTool->findByToolId($toolId);
		if (!$tool) {
			throw new NotFoundException(__('Invalid tool'));
		}

		// what types of booking will this user be able to make?
		$type = array(self::TYPE_NORMAL=>'Normal');

		$userId = $this->_getUserID();
		// is user an inductor on this tool?
		if ($this->ToolsTool->isUserAnInductor($toolId, $userId)) {
			$type[self::TYPE_INDUCTION] = 'Induction';
		}
		// is the user a maintainer?
		if ($this->ToolsTool->isUserAMaintainer($toolId, $userId)) {
			$type[self::TYPE_MAINTAIN] = 'Maintenance';
		}

		$this->set('tool', $tool);
		$this->set('type_options', $type);

		if ($this->request->is('post')) {
			// process the input
			// we know it is dd/mm/yyyy, but PHP can't parse that
			$start_parts = explode('/', $this->request['data']['Tool']['start_date']);
			$end_parts = explode('/', $this->request['data']['Tool']['end_date']);
			
			$start_date = new DateTime($start_parts[2] . '-' . $start_parts[1] . '-' . $start_parts[0] . ' ' . $this->request['data']['Tool']['start_hours'] . ':' . $this->request['data']['Tool']['start_mins'], new DateTimeZone('Europe/London'));
			$end_date = new DateTime($end_parts[2] . '-' . $end_parts[1] . '-' . $end_parts[0] . ' ' . $this->request['data']['Tool']['end_hours'] . ':' . $this->request['data']['Tool']['end_mins'], new DateTimeZone('Europe/London'));

			// the view will need these if there is an error
			$this->set('start_date', $start_date);
			$this->set('end_date', $end_date);

			// set preview based on start date

			// BASIC CHECKS
			// can this user post this event?
			// we know that only inducted users can here, because of isAuthorized
			if ($this->request['data']['Tool']['booking_type'] == self::TYPE_INDUCTION && !$this->ToolsTool->isUserAMaintainer($toolId, $userId)) {
				$this->Session->setFlash(__("Must be an maintainer to book a maintenance slot"));
				return;
			}
			if ($this->request['data']['Tool']['booking_type'] == self::TYPE_MAINTAIN && !$this->ToolsTool->isUserAnInductor($toolId, $userId)) {
				$this->Session->setFlash(__("Must be an inductor to book an induction"));
				return;
			}

			// check start date is in the future (within 30 minutes)
			$now = new DateTime('now -30 minutes', new DateTimeZone('Europe/London'));
			if ($start_date <= $now) {
				$this->Session->setFlash(__("Start date cannot be in the past"));
				return;
			}

			// check the end date is after the start date
			if ($end_date < $start_date) {
				$this->Session->setFlash(__("End date must be after the start date"));
				return;
			}

			// check the end and start date aren't the same?
			if ($end_date == $start_date) {
				$this->Session->setFlash(__("Length of booking must be greater than zero!"));
				return;
			}

			// check length <= max
			$length = ($end_date->getTimestamp() - $start_date->getTimestamp()) / 60;
			if ($length > $tool['Tool']['tool_length_max']) {
				$this->Session->setFlash(__("Maximum booking time is " . $tool['Tool']['tool_length_max'] . " minutes for this tool"));
				return;
			}

			// ADVANCED CHECKS
			// get a list of events from google, from today for a year
			$events = $this->ToolsGoogle->getFutureEvents($tool['Tool']['tool_calendar']);

			// does it clash?
			if ($this->__checkForClash($start_date, $end_date, $events)) {
				$this->Session->setFlash(__("Clashes with another booking"));
				return;
			}

			// how many existing bookings does user have?
			if ($this->request['data']['Tool']['booking_type'] == self::TYPE_INDUCTION || $this->request['data']['Tool']['booking_type'] == self::TYPE_MAINTAIN) {
				// users can have infinite of these
			}
			else {
				$userEvents = $this->__getNormalEvents($this->ToolsGoogle->getEventsForUser($userId, $events));
				if (count($userEvents) + 1 >= $tool['Tool']['tool_bookings_max']) {
					$txt = $tool['Tool']['tool_bookings_max'] > 1 ? "bookings" : "booking";
					$this->Session->setFlash(__("You can only have " . $tool['Tool']['tool_bookings_max'] . " " . $txt . " for this tool"));
					return;
				}
			}

			// Phew!  We can now add the booking
			$now->add(new DateInterval('PT30M'));
			$details = array(
				'type'		=>	$this->request['data']['Tool']['booking_type'],
				'booked'	=>	$now->format(ToolsGoogle::DATETIME_STR),
				'member'	=>	$userId,
				);
			if ($this->ToolsGoogle->saveEvent($start_date, $end_date, $details, $tool['Tool']['tool_calendar'])) {
				$this->Session->setFlash(__("Booking Added"));
			}
			else {
				$this->Session->setFlash(__("Booking Failed"));
			}
			return $this->redirect(array(
					'plugin'		=>	'Tools',
					'controller'	=>	'ToolsTools',
					'action'		=>	'view',
					$toolId,
					));
		}
		else {
			// set preview based on passed parameter
			// look at 2 weeks before and after to show the preview
			$start_date = new DateTime($this->request->query['t'], new DateTimeZone('Europe/London'));
		}
	}

	/**
	 * Returns a datetime object for the monday of this week at midnight (morning)
	 *
	 * @return DateTime monday morning at midnight
	 */
	private function __getMonday() {
		$date = new DateTime('now', new DateTimeZone('Europe/London'));

		$dayOfWeek = $date->format('N');

		// set the time to midnight
		$date->setTime(0,0,0);

		$offset = $dayOfWeek - 1;

		$date->sub(new DateInterval('P' . $offset . 'D'));

		return $date;
	}

	/**
	 * Checks to see if the event in question clashes with any others
	 *
	 * @param DateTime the start date/time of the event
	 * @param DateTime the end date/time of the event
	 * @param Array an array of future events, sorted by start date
	 * @return Boolean true is the new event clashes with an existing event
	 */
	private function __checkForClash($start, $end, $events) {
		foreach ($events as $event) {
			// first possibility, the new event <= existing events
			// If the start or end are within an existing event, there is a clash

			if ($start > $event['start'] && $start < $event['end']) {
				return true;
			}
			if ($end > $event['start'] && $end < $event['end']) {
				return true;
			}

			// second possibility, the new event < existing events
			// If the existing event start is within the new event, there is a clash
			if ($event['start'] > $start && $event['start'] < $end) {
				return true;
			}

		}
		return false;
	}

	/**
	 * Checks to see if the event in question clashes with any others
	 *
	 * @param array events to check
	 * @return array only normal events
	 */
	private function __getNormalEvents($events) {
		$normalEvents = array();
		foreach ($events as $event) {
			if ($event['type'] = self::TYPE_NORMAL) {
				$normalEvents[] = $event;
			}
		}
		return $normalEvents;
	}
}
?>