<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');
App::uses('Member', 'Model');
App::uses('Group', 'Model');
App::uses('AuthComponent', 'Controller/Auth');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    const VERSION_MAJOR = 0;
    const VERSION_MINOR = 3;
    const VERSION_BUILD = 3;

	public $helpers = array('Html', 'Form', 'Nav');

    //! Email object, for easy mocking.
    public $email = null;

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

    private $mainNav = array();

    public function __construct($request = null, $response = null) {
    	parent::__construct($request, $response);

    	// Add the main nav
    	$this->AddMainNav('Members', array( 'plugin' => null, 'controller' => 'members', 'action' => 'index' ), array(Group::FULL_ACCESS, Group::MEMBERSHIP_ADMIN));
    	$this->AddMainNav('Groups', array( 'plugin' => null, 'controller' => 'groups', 'action' => 'index' ), array(Group::FULL_ACCESS, Group::MEMBERSHIP_ADMIN));
    	$this->AddMainNav('Mailing Lists', array( 'plugin' => null, 'controller' => 'mailinglists', 'action' => 'index' ), array(Group::FULL_ACCESS, Group::MEMBERSHIP_ADMIN));
    	$this->AddMainNav('MemberVoice', array( 'plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'index' ), '');

    }

    public function AddMainNav($title, $link, $access) {
    	$this->mainNav[] = array(
    							'title'		=>	$title,
    							'link'		=>	$link,
    							'access'	=>	$access,
    							);
    }

    public function getMainNav($userId) {
		Controller::loadModel('Member');

		$navLinks = array();

		foreach ($this->mainNav as $nav) {
			$allowed = false;
			if ($nav['access'] == '') {
				$allowed = true;
			}
			elseif (is_array($nav['access'])) {
				foreach ($nav['access'] as $access) {
					if ($this->Member->GroupsMember->isMemberInGroup($userId, $access)) {
						$allowed = true;
						break;
					}
				}
			}
			else {
				if ($this->Member->GroupsMember->isMemberInGroup($userId, $nav['access'])) {
					$allowed = true;
				}
			}
			if ($allowed) {
				$navLinks[$nav['title']] = $nav['link'];
			}
		}

		return $navLinks;
    }

    public function beforeFilter() {
        $this->Auth->authenticate = array('Hms');
    	$this->Auth->authorize = array('Hms');
    }

    public function beforeRender() {
        // Send any links added to the NavComponent to the view
        $this->set('navLinks', $this->Nav->get_allowed_actions());

        Controller::loadModel('Member');

        $loggedInMemberId = $this->Member->getIdForMember($this->Auth->user());
        if($loggedInMemberId)
        {
            $adminLinks = $this->getMainNav($loggedInMemberId);

            $userMessage = array();
            if( $this->Member->getStatusForMember($loggedInMemberId) == Status::PRE_MEMBER_1 )
            {
                $userMessage = array('Click here to enter your contact details!' => array( 'controller' => 'members', 'action' => 'setupDetails', $loggedInMemberId ) );
            }

            $this->set('adminNav', $adminLinks);
            $this->set('userMessage', $userMessage);
            $this->set('memberId', $loggedInMemberId);
            $this->set('username', $this->Member->getUsernameForMember($this->Auth->user()));
        }

        $jsonData = json_decode(file_get_contents('http://lspace.nottinghack.org.uk/status/status.php'));

        $this->set('jsonData', $jsonData);
        $this->set('navLinks', $this->Nav->get_allowed_actions());

        $this->set('version', $this->getVersionString());
    }

    public function isAuthorized($user, $request)
    {
        Controller::loadModel('Member');

        if($this->Member->GroupsMember->isMemberInGroup( $this->Member->getIdForMember($user), Group::FULL_ACCESS ))
        {
            return true;
        }
        
        return false;
    }

    public function getVersionString()
    {
        return sprintf('%d.%d.%d', self::VERSION_MAJOR, self::VERSION_MINOR, self::VERSION_BUILD);
    }

    //! Send an e-mail to one or more email addresses.
    /*
        @param mixed $to Either an array of email address strings, or a string containing a singular e-mail address.
        @param string $subject The subject of the email.
        @param string $template Which email view template to use.
        @param array $viewVars Array containing all the variables to pass to the view, Defaults to empty array.
        @param bool $record If true then the sending of this e-mail we be recorded. Defaults to true.
        @retval bool True if e-mail was sent, false otherwise.
    */
    protected function _sendEmail($to, $subject, $template, $viewVars = array(), $record = true)
    {
        if($this->email == null)
        {
            $this->email = new CakeEmail();
        }

        if($record)
        {
            Controller::loadModel('Member');
            Controller::loadModel('EmailRecord');

            $memberIdList = $this->Member->emailToMemberId($to);
            $this->EmailRecord->createNewRecord($memberIdList, $subject);
        }

        $email = $this->email;
        $email->config('smtp');
        $email->from(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
        $email->sender(array('membership@nottinghack.org.uk' => 'Nottinghack Membership'));
        $email->emailFormat('html');
        $email->to($to);
        $email->subject($subject);
        $email->template($template);
        $email->viewVars($viewVars);
        return $email->send();
    }

    //! Get the id of the currently logged in Member.
    /*!
        @retval int The id of the currently logged in Member, or 0 if not found.
    */
    protected function _getLoggedInMemberId()
    {
        Controller::loadModel('Member');
        return $this->Member->getIdForMember($this->Auth->user());
    }
}
