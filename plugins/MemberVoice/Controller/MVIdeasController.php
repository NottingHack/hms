<?php

class MVIdeasController extends MemberVoiceAppController {

	public $components = array('RequestHandler');

	public $paginate = array(
							'limit' => 5,
							'order' => array(
											'Idea.votes' => 'desc'
											)
							);

	public function index($id = null) {
		/* If an array is passed, restrict to that category */
		if (!$id) {
			$conditions = array(
								'Status.status !='	=> array('Complete','Cancelled'),
								);
		}
		else {
			$conditions = array(
								'Status.status !='	=> array('Complete','Cancelled'),
								'CategoriesIdeas.category_id'		=>	$id,
								);
			$this->paginate['joins'] = array(
											array(
												'table' => 'mv_categories_ideas',
												'alias' => 'CategoriesIdeas',
												'type' => 'INNER',
												'conditions' => array(
													'Idea.id = CategoriesIdeas.idea_id',
												)
											),
										);
			$category = $this->MVIdea->Category->find('first', array('conditions' => array('Category.id' => $id)));
			$this->set('category', $category);
		}
		$ideas = $this->paginate('MVIdea', $conditions);
		$categories = $this->MVIdea->Category->find('all', array('order' => 'Category.category'));

		$this->set('ideas', $ideas);
		$this->set('categories', $categories);
		$this->set('user', $this->_getUserID());
		$this->set('voteurl', $this->_getVoteUrl());
	}

	public function idea($id = null) {
		if (!$id) {
			throw new NotFoundException(__('Invalid post'));
		}
		$idea = $this->MVIdea->find('first', array('conditions' => array('Idea.id' => $id)));
		if (!$idea) {
			throw new NotFoundException(__('Invalid post'));
		}
		$categories = $this->MVIdea->Category->find('all', array('order' => 'Category.category'));

		$this->set('idea', $idea);
		$this->set('categories', $categories);
		$this->set('user', $this->_getUserID());
		$this->set('voteurl', $this->_getVoteUrl());
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

	private function _getVoteUrl() {
		if (method_exists("Router", "baseUrl")) {
			$url = Router::baseUrl();
		}
		else {
			$url = FULL_BASE_URL;
		}
		$url .= Router::url(array('plugin' => 'MemberVoice', 'controller' => 'MVIdeas', 'action' => 'vote'));;
		return $url;
	}
}


?>