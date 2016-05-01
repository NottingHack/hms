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
    
/**
 * Controller to handle Member boxes.
 */
class MetaController extends AppController {
/**
 * Views rendered from this controller will have access to the following helpers.
 * @var array
 */
	public $helpers = array('Html', 'Form');

/**
 * The list of models this Controller relies on.
 * @var array
 */
	public $uses = array('Meta', 'Member');
    
/**
 * Test to see if a user is authorized to make a request.
 *
 * @param array $user Member record for the user.
 * @param CakeRequest $request The request the user is attempting to make.
 * @return bool True if the user is authorized to make the request, otherwise false.
 * @link http://api20.cakephp.org/class/cake-request
 */
	public function isAuthorized($user, $request) {
		// allows full access to see everything
		if (parent::isAuthorized($user, $request)) {
			return true;
		}

        return false; // only full admins can access this
	}

/**
 * Index redirect
 *
 */
	public function index() {
        return $this->redirect(array('controller' => 'meta', 'action' => 'listMetas'));
	}
    
/**
 * Show List of all meta values
 *
 */
	public function listMetas() {
        $this->view = 'list_metas';
        
        $this->paginate = $this->Meta->getMetasList(true);
        $metasList = $this->paginate('Meta');
        $metasList = $this->Meta->formatMetasList($metasList, false);
       
        $this->set('metasList', $metasList);
        $this->set('shortDescriptionLength', 40);
	}
    
/**
 * Edit a Meta
 *
 * @param string $name The name of the meta to edit; null redirect to list
 */
	public function edit($name = null) {
        if ($name == null) {
            $this->redirect(array('controller' => 'meta', 'action' => 'listMetas'));
        }
        
        $meta = $this->Meta->getMeta($name, false);
        $metaFormated = $this->Meta->formatMeta($meta);
        $this->set('meta', $metaFormated);

        if ( $this->request->is('post') || $this->request->is('put')) {
            $sanitisedData = $this->Meta->formatMeta($this->request->data, false);
            
            if ($sanitisedData) {
                $updateResult = $this->Meta->updateValueFor($sanitisedData['name'], $sanitisedData['value']);
                if (is_array($updateResult)) {
                    $this->Session->setFlash('Details updated.');
                    return $this->redirect(array('action' => 'listMetas'));
                }
            }
            $this->Session->setFlash('Unable to update value.');
        }
        if (!$this->request->data) {
            $this->request->data = $meta;
        }
        
        $this->set('meta', $this->Meta->formatMeta($meta));

	}

}