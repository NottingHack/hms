<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
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

App::uses('AppController', 'Controller');
App::uses('Xml', 'Utility');
App::uses('PhpReader', 'Configure');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * The list of components this Controller relies on.
 * @var array
 */
	public $components = array('Auth');

/**
 * The list of models this Controller relies on.
 *
 * @var array
 */
	public $uses = array('Meta');

/**
 * Displays a view
 *
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */
	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
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

		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
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

/**
 * The credits page.  Pulls from the config files, which plugins can contribute to
 */
	public function credits() {
		Configure::config('default', new PhpReader());
		Configure::load('credits', 'default');

		$media = Configure::read('hms_media');

		$this->set('media', $media);
	}

/** 
 * Links Pages
 */
	public function links() {

		$options = array(
			'conditions' => array('Meta.name LIKE' => '%link_%'),

			);

		$results = $this->Meta->find('all', $options);
		$links = array();
		foreach($results as $link) {
			$link['Meta']['name'] = str_replace('link_', '', $link['Meta']['name']);
			array_push($links, $link['Meta']);
		}

		$this->set('links', $links);
	}

}
