<?php

class MVIdeasController extends MemberVoiceAppController {

	public function index() {
		$ideas = $this->MVIdea->find('all', array('conditions' => array('Status.status !=' => array('Complete','Cancelled'))));
		$this->set('ideas', $ideas);
	}

	public function idea($id = null) {
		if (!$id) {
            throw new NotFoundException(__('Invalid post'));
        }
		$idea = $this->MVIdea->find('first', array('conditions' => array('Idea.id' => $id)));
		if (!$idea) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->set('idea', $idea);
	}
}


?>