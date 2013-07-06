<?php

class MVCommentsController extends MemberVoiceAppController {

	public function index() {
		$comments = $this->Comment->find('all');
		$this->set('comments', $comments);
	}
}


?>