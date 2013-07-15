<?php

class MVIdeasController extends MemberVoiceAppController {

	public $components = array('RequestHandler');

	public function index() {
		$ideas = $this->MVIdea->find('all', array('conditions' => array('Status.status !=' => array('Complete','Cancelled'))));
		$this->set('ideas', $ideas);
		$this->set('user', $this->_getUserID());
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

	public function vote($id = null) {
		$return = array();
		if (!$id) {
			$return['responseid'] = 201;
			$return['response'] = 'No ID provided';
		}
		$idea = $this->MVIdea->find('first', array('conditions' => array('Idea.id' => $id)));
		if (!$idea) {
			$return['responseid'] = 202;
			$return['response'] = 'Idea not found';
		}

		$return['id'] = $id;
		if ($this->request->data['vote'] == null) {
			$return['responseid'] = 203;
			$return['response'] = 'No vote provided';
		}
		if ($this->request->data['vote'] != 1 and $this->request->data['vote'] != -1 and $this->request->data['vote'] != 0) {
			$return['responseid'] = 204;
			$return['response'] = 'Vote not valid';
		}		
		
		$return['votes'] = $this->MVIdea->saveVote($id, $this->_getUserID(), $this->request->data['vote']);
		if ($return['votes'] !== false) {
			$return['responseid'] = 200;
			$return['voted'] = $this->request->data['vote'];
		}
		else {
			$return['responseid'] = 205;
			$return['response'] = 'Vote failed';
		}

		$this->set('return', $return);
		$this->set('_serialize', 'return');
	}
}


?>