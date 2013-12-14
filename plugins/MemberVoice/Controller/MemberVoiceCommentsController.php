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
 * @package       plugins.MemberVoice.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Controller for comments, allows addid of comments
 */
class MemberVoiceCommentsController extends MemberVoiceAppController {

/**
 * Allow a use to add a comment.
 * Expects a post request. Redirects to the idea on success.
 */
	public function add() {
		if ($this->request->is('post')) {
			// If the form data can be validated and saved...
			if ($this->MemberVoiceComment->save($this->request->data)) {
				// Set a session flash message and redirect.
				$this->Session->setFlash('Comment Saved!');
				return $this->redirect(array( 'plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'idea', $this->request->data['Comment']['idea_id'] ));
			} else {
				$this->Session->setFlash('Save Failed');
				return $this->redirect(array( 'plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'idea', $this->request->data['Comment']['idea_id'] ));
			}
		} else {
			$this->Session->setFlash('Comment not saved');
			return $this->redirect(array( 'plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'index' ));
		}
	}
}