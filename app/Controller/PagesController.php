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
 * @package       app.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


App::uses('AppController', 'Controller');
App::uses('Xml', 'Utility');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * The list of components this Controller relies on.
 * @var array
 */
	public $components = array('Auth');

/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'Pages';

/**
 * Displays a view.
 *
 */
	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}

		// Dynamic content on a 'static' page? Why not.
		if ( method_exists($this, $page)) {
			call_user_func( array($this, $page) );
		}

		$this->set(compact('page', 'subpage', 'title_for_layout'));
		$this->render(implode('/', $path));
	}

/**
 * Perform any actions that should be performed before any controller action.
 *
 * @link http://api20.cakephp.org/class/controller#method-ControllerbeforeFilter
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('display');
	}

/**
 * Display the home page.
 */
	public function home() {
		Controller::loadModel('Member');

		$loggedInMemberId = $this->Member->getIdForMember($this->Auth->user());

		if ($loggedInMemberId) {
			if ($this->referer() == Router::url(array('controller' => 'members', 'action' => 'login'), true)) {
				// Redirect if the user wants to be elsewhere
				switch ($this->Member->getStatusForMember($loggedInMemberId)) {
					case Status::PRE_MEMBER_1:
						return $this->redirect(array('controller' => 'members', 'action' => 'setupDetails', $loggedInMemberId));
				}
			}

			$parsedXml = Xml::build('http://nottinghack.org.uk/?feed=rss2');
			$this->set('rssData', $parsedXml);
		} else {
			$this->Nav->add('Register Now!', 'members', 'register', array(), 'big_button');
		}
	}
}
