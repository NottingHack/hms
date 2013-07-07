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
    const VERSION_BUILD = 2;

	public $helpers = array('Html', 'Form', 'Nav');

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
            $adminLinks = array();
            if( $this->Member->GroupsMember->isMemberInGroup( $loggedInMemberId, Group::FULL_ACCESS ) || 
                $this->Member->GroupsMember->isMemberInGroup( $loggedInMemberId, Group::MEMBER_ADMIN ) )
            {
                $adminLinks = array(
                    'Members' => array( 'plugin' => null, 'controller' => 'members', 'action' => 'index' ),
                    'Groups' => array( 'plugin' => null, 'controller' => 'groups', 'action' => 'index' ),
                    'Mailing Lists' => array( 'plugin' => null, 'controller' => 'mailinglists', 'action' => 'index' ),
                );
            }

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

}
