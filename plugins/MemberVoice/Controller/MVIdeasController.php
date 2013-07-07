<?php

class MVIdeasController extends MemberVoiceAppController {

	public function index() {
		$ideas = $this->MVIdea->find('all', array('conditions' => array('Status.status !=' => array('Complete','Cancelled'))));
		$this->set('ideas', $ideas);
	}
}


?>