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
 * @package       plugins.Tools.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppModel', 'Model');
App::uses('Member', 'Model');
App::uses('PhpReader', 'Configure');

/**
 * Model to access google calendars.
 */
class ToolsGoogle extends ToolsAppModel {

	/**
	 * The date format google expects.  Also useful for passing around datetime values
	 */
	const DATETIME_STR = 'Y-m-d\TH:i:sP';

	/**
	 * Specify the table to use.
	 * @var string
	 */
	public $useTable = 'google';

	/**
	 * Specify a nicer alias for this model.
	 * @var string
	 */
	public $alias = 'Google';

	/**
	 * The google identity we are using
	 * @var string
	 */
	private $identity;

	/**
	 * The client object for authorising to the api
	 * @var object
	 */
	private $__client;

	/**
	 * The service object for connecting to the calendars
	 * @var object
	 */
	private $__service;

	/**
	 * Constructor
	 *
	 * @param mixed $id The id to start the model on.
	 * @param string $table The table to use for this model.
	 * @param string $ds The connection name this model is connected to.
	 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		Configure::config('default', new PhpReader(ROOT . '/plugins/Tools/Config/'));
		Configure::load('google', 'default');

		set_include_path(ROOT . '/plugins/Tools/Lib/' . PATH_SEPARATOR . get_include_path());
		require_once 'Google/Client.php';
		require_once 'Google/Service/Calendar.php';

		$this->__client = new Google_Client();
		$this->__client->setClientId(Configure::read('google_client_id'));
		$this->__client->setClientSecret(Configure::read('google_client_secret'));
		$this->__client->setRedirectUri(Configure::read('google_redirect_url'));
		$this->__client->setAccessType('offline');
		$this->__client->addScope("https://www.googleapis.com/auth/calendar");
		
		$this->__service = new Google_Service_Calendar($this->__client);
	}

	/**
	 * Set the identity so we can store the refresh token
	 *
	 * @param string $identity the email address of the google account we are going to connect to
	 */
	public function setIdentity($identity) {
		$this->identity = $identity;
	}

	/**
	 * Checks to see if the refresh token is valid, and if so generate an access token.
	 *
	 * @return boolean true if authorised, false if not
	 */
	public function authorised() {
		$token = $this->__getRefreshToken();
		if (!is_null($token)) {
			// Send it to google and get an access token
			try {
				$this->__client->refreshToken($token);

				// see if it worked
				return (!$this->__client->isAccessTokenExpired());
			} catch (Exception $e) {
				return false;
			}
		}
		else {
			// no token, so not authorised at all
			return false;
		}
	}

	/**
	 * Returns the authorisation URL
	 *
	 * @return string Google URL to authorise the service
	 */
	public function getAuthUrl() {
		return $this->__client->createAuthUrl();
	}

	/**
	 * Second stage of authentication. Takes the code given by google and exchanges
	 * it for an access token and refresh token
	 *
	 * @param string the code returned by google in the first exchange
	 * @return array token information
	 */
	public function authenticate($code) {
		$this->__client->authenticate($code);
		$token = $this->__client->getAccessToken();
		return json_decode($token, true);
	}

	/**
	 * Deletes all refresh tokens stored in the database for our id
	 */
	public function deleteAllRefreshTokens() {
		$tokens = $this->__getRefreshTokens();
		foreach ($tokens as $token) {
			$this->delete($token['Google']['id']);
		}
	}

	/**
	 * Save the refresh token for our identity
	 *
	 * @param string the actual refresh token
	 */
	public function saveRefreshToken($refresh_token) {
		$data = array(
			'Google' => array(
				'identity'		=>	$this->identity,
				'refresh_token'	=>	$refresh_token,
				)
			);
		$this->save($data);
	}

	/**
	 * Returns an array containing the access and refresh tokens.
	 * We will already be authenticated and authorised by this point
	 * Will look something like:
	 * {"access_token":"TOKEN", "refresh_token":"TOKEN", "token_type":"Bearer",
	 *  "expires_in":3600,"id_token":"TOKEN", "created":1320790426}
	 * 
	 * @return array token information
	 */
	public function getAccessToken() {
		$token = json_decode($this->__client->getAccessToken(), true);
		if (!isset($token['refresh_token'])) {
			$token['refresh_token'] = $this->__getRefreshToken();
		}
		return $token;
	}

	/**
	 * Creates a new calendar
	 *
	 * @param string calendar name
	 * @return string calendar ID (or false on failure)
	 */
	public function createCalendar($calendarName) {
		if ($this->authorised()) {
			// set up the calendar and save
			$calendar = new Google_Service_Calendar_Calendar();
			$calendar->setSummary($calendarName);
			try {
				$calendar = $this->__service->calendars->insert($calendar);
			} catch (Exception $e) {
				return false;
			}

			// make it public, delete if this fails
			try {
				$acl = new Google_Service_Calendar_AclRule();
				$acl->setRole('reader');
				$scope = new Google_Service_Calendar_AclRuleScope();
				$scope->setType('default');
				$acl->setScope($scope);

				$this->__service->acl->insert($calendar->getId(), $acl);
			} catch (Exception $e) {
				$this->__service->calendars->delete($calendar->getId());
				return false;
			}

			return $calendar->getId();
		}
		return false;
	}

	/**
	 * Query google for the next booking
	 *
	 * @param string calendar Id
	 * @return DateTime of next booking, or false on failure
	 */
	public function getNextBooking($calendarId) {
		if ($this->authorised()) {
			$params = array(
				'maxResults'	=>	1,
				'orderBy'		=>	'startTime',
				'singleEvents'	=>	true,
				'timeMin'		=>	date(self::DATETIME_STR),
				);

			$events = $this->__service->events->listEvents($calendarId, $params)->getItems();

			if (count($events) > 0) {
				return new DateTime($events[0]['start']['dateTime'], new DateTimeZone('Europe/London'));
			}
			else {
				return false;
			}
		}
		return false;
	}

	/**
	 * Returns an array of public addresses of a calendar
	 *
	 * @param string calender id
	 * @return array addresses
	 */
	public function getPublicAddresses($calendarId) {
		$addresses = array(
			'email'	=>	$calendarId,
			'xml'	=>	'https://www.google.com/calendar/feeds/' . urlencode($calendarId) . '/public/basic',	
			'ical'	=>	'https://www.google.com/calendar/ical/' . urlencode($calendarId) . '/public/basic.ics',
			'html'	=>	'https://www.google.com/calendar/embed?src=' . urlencode($calendarId) . '&ctz=Europe/London',
			);
		
		return $addresses;
	}

	/**
	 * Returns the events for the week starting from the provided DateTime
	 *
	 * @param DateTime the start of the week
	 * @param string calender id
	 * @return array all events in the week
	 */
	public function getWeeksEvents($weekStart, $calendarId) {
		if ($this->authorised()) {
			$weekEnd = clone $weekStart;
			$weekEnd->add(new DateInterval('P7D'));

			$params = array(
				'orderBy'		=>	'startTime',
				'singleEvents'	=>	true,
				'timeMin'		=>	$weekStart->format(self::DATETIME_STR),
				'timeMax'		=>	$weekEnd->format(self::DATETIME_STR),
				);

			$googleEvents = $this->__service->events->listEvents($calendarId, $params)->getItems();
			if (count($googleEvents) == 0) {
				return $googleEvents;
			}

			return $this->__parseEvents($googleEvents);

		}
		return array();
	}

	/**
	 * Returns the future .  Currently this limits at 2 years
	 *
	 * @param string calender id
	 * @return array events
	 */
	public function getFutureEvents($calendarId) {
		if ($this->authorised()) {
			$now = new DateTime('now', new DateTimeZone('Europe/London'));
			$future = clone $now;
			$future->add(new DateInterval('P2Y'));

			$params = array(
				'orderBy'		=>	'startTime',
				'singleEvents'	=>	true,
				'timeMin'		=>	$now->format(self::DATETIME_STR),
				'timeMax'		=>	$future->format(self::DATETIME_STR),
				);

			$googleEvents = $this->__service->events->listEvents($calendarId, $params)->getItems();
			if (count($googleEvents) == 0) {
				return $googleEvents;
			}

			return $this->__parseEvents($googleEvents);
		}
	}

	/**
	 * Returns all events for a specified user
	 *
	 * @param string member id
	 * @param mixed either an array of events or the calendarId
	 * @return array events
	 */
	public function getEventsForUser($memberId, $mixed) {
		if (is_array($mixed)) {
			$events = $mixed;
		}
		else {
			$events = $this->getFutureEvents($mixed);
		}

		$userEvents = array();
		foreach ($events as $event) {
			if ($event['member'] == $memberId) {
				$userEvents[] = $event;
			}
		}
		return $userEvents;
	}

	/**
	 * Saves event
	 *
	 * @param DateTime booking start
	 * @param DateTime booking end
	 * @param array additional booking details
	 * @param string calendarId of the calendar to save it to
	 * @return boolean success or failure
	 */
	public function saveEvent($start_date, $end_date, $details, $calendarId) {
		if ($this->authorised()) {
			// get memeber details so we can build the title
			$this->Member = new Member();
			$member = $this->Member->getMemberSummaryForMember($details['member'], false);

			// build the parts of the event
			$title = $member['Member']['firstname'] . " " . $member['Member']['surname'];

			$start = new Google_Service_Calendar_EventDateTime();
			$start->setDateTime($start_date->format(self::DATETIME_STR));

			$end = new Google_Service_Calendar_EventDateTime();
			$end->setDateTime($end_date->format(self::DATETIME_STR));

			// set up the event
			$event = new Google_Service_Calendar_Event();
			$event->setSummary($title);
			$event->setDescription(json_encode($details));
			$event->setStart($start);
			$event->setEnd($end);

			// send it to google
			$created = $this->__service->events->insert($calendarId, $event);
			if ($created->getId()) {
				return true;
			}
			return false;
		}
	}

	/**
	 * Return the refresh token iff one is saved
	 *
	 * @return string token if saved, otherwise null
	 */
	private function __getRefreshToken() {
		$tokens = $this->__getRefreshTokens();
		if (count($tokens) == 1) {
			// extract the actual refresh token
			$refresh_token = $tokens[0]['Google']['refresh_token'];

			return $refresh_token;
		}
		else {
			// no token, so not authorised at all
			return null;
		}
	}

	/**
	 * Extract all refresh tokens saved against our identity
	 * 
	 * @return array tolen information from the database
	 */
	private function __getRefreshTokens() {
		$params = array(
			'conditions' => array(
				'Google.identity' => $this->identity,
				),
			);
		$tokens = $this->find('all', $params);

		return $tokens;
	}

	/**
	 * Takes an array of google events and returns an array with the important info
	 *
	 * @param array google events
	 * @return array events
	 */
	private function __parseEvents($googleEvents) {
		$events = array();
		foreach ($googleEvents as $googleEvent) {
			$description = json_decode($googleEvent->getDescription());
			$event = array(
				'id'		=>	$googleEvent->getId(),
				'title'		=>	$googleEvent->getSummary(),
				'type'		=>	$description->type,
				'booked'	=>	new DateTime($description->booked),
				'member'	=>	$description->member,
				'start'		=>	new DateTime($googleEvent->getStart()->getDateTime()),
				'end'		=>	new DateTime($googleEvent->getEnd()->getDateTime()),
				);
			$events[] = $event;
		}

		return $events;
	}
}

?>