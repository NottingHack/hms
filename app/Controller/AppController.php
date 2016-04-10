<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
App::uses('Member', 'Model');
App::uses('Group', 'Model');
App::uses('AuthComponent', 'Controller/Auth');
App::uses('Status', 'Model');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	const VERSION_MAJOR = 0;
	const VERSION_MINOR = 4;
	const VERSION_BUILD = 1;

/**
 * List of helpers views rendered from this controller will have access to.
 * @var array
 */
	public $helpers = array('Html', 'Form', 'Nav');

/**
 * Email object, used for easy mocking.
 * @var class
 */
	public $email = null;

/**
 * List of components this controller uses.
 * @var array
 */
	public $components = array(
		'Session',
		'Auth' => array(
			'loginAction' => array(
				'plugin' => null,
				'controller' => 'members',
				'action' => 'login',
			),
			'loginRedirect' => array('plugin' => null, 'controller' => 'pages', 'action' => 'index'),
			'logoutRedirect' => array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home'),
			'Hms' => array(
				'fields' => array('username' => 'email'),
				'userModel' => 'Member',
			),
			'authorize' => array('Hms'),
		),
		'Nav',
		'AuthUtil',
	);

/**
 * List of link information that will be used to render the main navigation element.
 * @var array
 */
	private $__mainNav = array();

/**
 * Constructor.
 *
 * @param CakeRequest $request Request object for this controller. Can be null for testing,
 *  but expect that features that use the request parameters will not work.
 * @param CakeResponse $response Response object for this controller.
 */
	public function __construct($request = null, $response = null) {
		parent::__construct($request, $response);

		// Add the main nav
		$this->__addMainNav('Members', array( 'plugin' => null, 'controller' => 'members', 'action' => 'index' ), array(Group::FULL_ACCESS, Group::MEMBERSHIP_ADMIN), null);
		$this->__addMainNav('Groups', array( 'plugin' => null, 'controller' => 'groups', 'action' => 'index' ), array(Group::FULL_ACCESS, Group::MEMBERSHIP_ADMIN), null);
		$this->__addMainNav('Mailing Lists', array( 'plugin' => null, 'controller' => 'mailinglists', 'action' => 'index' ), array(Group::FULL_ACCESS, Group::MEMBERSHIP_ADMIN), null);
		$this->__addMainNav('Snackspace', array( 'plugin' => null, 'controller' => 'snackspace', 'action' => 'history' ), null, array(Status::CURRENT_MEMBER, Status::EX_MEMBER));
        $this->__addMainNav('Access Codes', array( 'plugin' => null, 'controller' => 'members', 'action' => 'viewAccessCodes' ), null, array(Status::CURRENT_MEMBER));
		$this->__addMainNav('MemberVoice', array( 'plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'index' ), null, null);
		$this->__addMainNav('Tools', array( 'plugin' => 'tools', 'controller' => 'tools', 'action' => 'index' ), null, null);
        $this->__addMainNav('Projects', array( 'plugin' => null, 'controller' => 'memberProjects', 'action' => 'listProjects' ), null, array(Status::CURRENT_MEMBER, Status::EX_MEMBER));
        $this->__addMainNav('Boxes', array( 'plugin' => null, 'controller' => 'memberBoxes', 'action' => 'listBoxes' ), null, array(Status::CURRENT_MEMBER, Status::EX_MEMBER));
	}

/**
 * Add an item to the main nav list.
 * @param  string $title Text to display with the link.
 * @param  string[] $link Array to pass to the HtmlHelper to construct the link when rendering.
 * @param  int[]|null $access Array of group ids that have access to this link, if null, all groups have access to link.
 * @param  int[]|null $statuses A list of status ids, a member must be in one of these statuses for this link to be visible.
 */
	private function __addMainNav($title, $link, $access, $statuses) {
		$this->__mainNav[] = array(
								'title'		=>	$title,
								'link'		=>	$link,
								'access'	=>	$access,
								'statuses'	=>	$statuses
								);
	}

/**
 * Return a list of navigation elements that a user is allowed to access.
 * @param  int $userId Only navigation elements accessible by this user will be returned.
 * @return array A list of link information.
 */
	public function getMainNav($userId) {
		Controller::loadModel('Member');

		$navLinks = array();

		foreach ($this->__mainNav as $nav) {
			$allowed = false;
			if ($nav['access'] == '') {
				$allowed = true;
			} elseif (is_array($nav['access'])) {
				foreach ($nav['access'] as $access) {
					if ($this->Member->GroupsMember->isMemberInGroup($userId, $access)) {
						$allowed = true;
						break;
					}
				}
			} else {
				if ($this->Member->GroupsMember->isMemberInGroup($userId, $nav['access'])) {
					$allowed = true;
				}
			}

			// Apply restrictions based on member status, if applicable
			if ($allowed) {
				if ($nav['statuses'] != '') {
					$allowed = false;
					foreach ($nav['statuses'] as $status) {
						if ($this->Member->getStatusForMember($userId) == $status) {
							$allowed = true;
						}
					}
				}
			}

			if ($allowed) {
				$navLinks[$nav['title']] = $nav['link'];
			}

		}

		return $navLinks;
	}

/**
 * Called before the controller action. Sets up the authentication.
 *
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeFilter() {
		$this->Auth->authenticate = array('Hms');
		$this->Auth->authorize = array('Hms');
	}

/**
 * Called after the controller action is run, but before the view is rendered.
 * Set up the common view variables, navigation links, memberId, etc.
 *
 * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
 */
	public function beforeRender() {
		// Send any links added to the NavComponent to the view
		$this->set('navLinks', $this->Nav->getAllowedActions());

		Controller::loadModel('Member');

		$loggedInMemberId = $this->Member->getIdForMember($this->Auth->user());

		if ($loggedInMemberId) {
			$adminLinks = $this->getMainNav($loggedInMemberId);

			$userMessage = array();
			if ($this->Member->getStatusForMember($loggedInMemberId) == Status::PRE_MEMBER_1) {
				$userMessage = array('Click here to enter your contact details!' => array( 'controller' => 'members', 'action' => 'setupDetails', $loggedInMemberId ) );
			}

			$this->set('adminNav', $adminLinks);
			$this->set('userMessage', $userMessage);
			$this->set('memberId', $loggedInMemberId);
			$this->set('username', $this->Member->getUsernameForMember($this->Auth->user()));
			$this->set('email', $this->Member->getEmailForMember($this->Auth->user()));
		}

		$jsonData = json_decode(file_get_contents('http://lspace.nottinghack.org.uk/status/status.php'));

		$this->set('jsonData', $jsonData);
		$this->set('navLinks', $this->Nav->getAllowedActions());

		$this->set('version', $this->getVersionString());
	}

/**
 * Check if a user is allowed to complete a request.
 * @param  array $user An array of data describing the user attempting to make the request.
 * @param  CakeRequest $request The request the user is trying to make.
 * @return boolean True if user is able to complete request, false otherwise.
 */
	public function isAuthorized($user, $request) {
		Controller::loadModel('Member');

		if ($this->Member->GroupsMember->isMemberInGroup( $this->Member->getIdForMember($user), Group::FULL_ACCESS )) {
			return true;
		}
		return false;
	}

/**
 * Get a string detailing the current HMS version.
 * @return string A string of the current HMS version.
 */
	public function getVersionString() {
		return sprintf('%d.%d.%d', self::VERSION_MAJOR, self::VERSION_MINOR, self::VERSION_BUILD);
	}

/**
 * Send an e-mail to one or more email addresses.
 *
 * @param string[]|string $to Either an array of email address strings, or a string containing a singular e-mail address.
 * @param string $subject The subject of the email.
 * @param string $template Which email view template to use.
 * @param array $viewVars Array containing all the variables to pass to the view, Defaults to empty array.
 * @param bool $record If true then the sending of this e-mail we be recorded. Defaults to true.
 * @return bool True if e-mail was sent, false otherwise.
 */
	protected function _sendEmail($to, $subject, $template, $viewVars = array(), $record = true) {
		if ($this->email == null) {
			$this->email = new CakeEmail();
		}

		if ($record) {
			Controller::loadModel('Member');
			Controller::loadModel('EmailRecord');

			$memberIdList = $this->Member->emailToMemberId($to);
			$this->EmailRecord->createNewRecord($memberIdList, $subject);
		}

		$email = $this->email;
		$email->from(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
		$email->sender(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
		$email->emailFormat('html');
		$email->to($to);
		$email->subject($subject);
		$email->template($template);
		$email->viewVars($viewVars);

		$emailDebugDirectory = null;
		try {
			Configure::load('debug');
			$emailDebugDirectory = Configure::read('emailDebugDirectory');
		} catch(ConfigureException $e) {
			$emailDebugDirectory = null;
		}
		if ($emailDebugDirectory != null) {
			$email->transport('Debug');
			$emailContents = $email->send();
			$this->__writeEmailContents($emailContents, $emailDebugDirectory);
			return true;
		}

		$email->config('smtp');
		return $email->send();
	}

/**
 * Get the id of the currently logged in Member.
 *
 * @return int The id of the currently logged in Member, or 0 if not found.
 */
	protected function _getLoggedInMemberId() {
		Controller::loadModel('Member');
		return $this->Member->getIdForMember($this->Auth->user());
	}

/**
 * Write an e-mail to a file.
 * @param array $data Aray of e-mail data.
 * @param string $toFolder The folder to save to e-mail to.
 */
	private function __writeEmailContents($data, $toFolder) {
		$headers = $this->__splitEmailHeaders($data['headers']);
		$data['headers'] = $headers;
		$subject = $headers['Subject'];

		$numEmailFiles = $this->__countFilesInFolder($toFolder);
		$filepath = sprintf('%s/%03d_%s.js', $toFolder, $numEmailFiles, $subject);
		file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));
	}

/**
 * Given a list of headers, split them into an assoc array.
 * @param string $headers String containing all header info.
 * @return array An assoc array of headers.
 */
	private function __splitEmailHeaders($headers) {
		$arrayHeaders = array();
		foreach (explode("\r\n", $headers) as $header) {
			$headerParts = explode(':', $header);
			$arrayHeaders[$headerParts[0]] = trim($headerParts[1]);
		}

		return $arrayHeaders;
	}

/**
 * Given the path to a directory, return the number of files in said directory
 * @param string $folder Path to folder to count files in.
 * @return int The number of files in the folder.
 */
	private function __countFilesInFolder($folder) {
		$iter = new FilesystemIterator($folder, FilesystemIterator::SKIP_DOTS);
		return iterator_count($iter);
	}
}
