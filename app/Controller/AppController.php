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

	public $helpers = array('Html', 'Form', 'Nav');

	public $components = array(
        'Session',
        'Auth' => array(
        	'loginAction' => array(
	            'controller' => 'members',
	            'action' => 'login',
	            #'plugin' => 'users'
	        ),
            'loginRedirect' => array('controller' => 'pages', 'action' => 'index'),
            'logoutRedirect' => array('controller' => 'pages', 'action' => 'display', 'home'),
            'Hms' => array(
                'fields' => array('username' => 'email'),
                'userModel' => 'Member',
            ),
            'authorize' => array('Hms'),
        ),
        'Nav',
    );

    public function beforeFilter() {
        $this->Auth->authenticate = array('Hms');
    	$this->Auth->authorize = array('Hms');
    }

    public function beforeRender() {
        # AT [16/09/2012] Send any links added to the NavComponent to the view
        $this->set('navLinks', $this->Nav->get_allowed_actions());
    }

    public function isAuthorized($user, $request)
    {
        if(Member::isInGroupFullAccess($user))
        {
            return true;
        }
        
        return null;
    }

}
