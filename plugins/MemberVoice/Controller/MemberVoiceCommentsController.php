<?php
/**
 * Controller for Comments.
 *
 *
 * @package       plugin.MemberVoice.Controller
 */
class MemberVoiceCommentsController extends MemberVoiceAppController {

	//! Saves a new comment to the database
	/*!
		Expects a post request. Redirects to the idea on success.
	*/
	public function add() {
		if ($this->request->is('post'))
		{
			// If the form data can be validated and saved...
			if ($this->MemberVoiceComment->save($this->request->data))
			{
				// Set a session flash message and redirect.
				$this->Session->setFlash('Comment Saved!');
				return $this->redirect(array( 'plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'idea', $this->request->data['Comment']['idea_id'] ));
			}
			else
			{
				$this->Session->setFlash('Save Failed');
				return $this->redirect(array( 'plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'idea', $this->request->data['Comment']['idea_id'] ));
			}
		}
		else
		{
			$this->Session->setFlash('Comment not saved');
			return $this->redirect(array( 'plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'index' ));
		}
	}
}


?>