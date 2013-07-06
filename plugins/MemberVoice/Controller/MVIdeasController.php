<?php

class MVIdeasController extends MemberVoiceAppController {

	public function index() {
		$ideas = $this->MVIdea->find('all');
		$this->set('ideas', $ideas);
	}
}


?>