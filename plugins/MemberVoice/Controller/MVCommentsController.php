<?php

class MVCommentsController extends MemberVoiceAppController {

	public function add() {
		if ($this->request->is('post')) {
		// If the form data can be validated and saved...
			if ($this->MVComment->save($this->request->data)) {
				// Set a session flash message and redirect.
				$this->Session->setFlash('Comment Saved!');
				$this->redirect(array( 'plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'idea', $this->request->data['Comment']['idea_id'] ));
			}
			else {
				var_dump("no save");
			}
		}
		else {
			var_dump("no post");
		}
	}
}


?>