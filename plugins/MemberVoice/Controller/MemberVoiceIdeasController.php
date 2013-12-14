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
 * Controller for Ideas, allows a user to view, add, and vote on ideas.
 */
class MemberVoiceIdeasController extends MemberVoiceAppController {

/**
 * Views rendered from this controller will have access to the following helpers.
 * @var array
 */
	public $helpers = array('Html', 'Form', 'Paginator', 'Tinymce');

/**
 * List of components this controller uses.
 * @var array
 */
	public $components = array('RequestHandler');

/**
 * Options used when paginating ideas.
 * @var array
 */
	public $paginate = array(
		'limit' => 5,
		'order' => array(
			'Idea.votes' => 'desc'
		)
	);

/**
 * Show a list of all ideas or all ideas in a category.
 * @param  int|null $categoryId If an integer, only show ideas in this category, if null show all ideas.
 */
	public function index($categoryId = null) {
		// If an category ID is passed, restrict to that category
		if (!$categoryId) {
			$conditions = array(
				'Status.status !='	=> array('Complete', 'Cancelled'),
			);
		} else {
			$conditions = array(
				'Status.status !='	=> array('Complete', 'Cancelled'),
				'CategoriesIdeas.category_id'		=>	$categoryId,
			);
			// Add join details to the paginate array
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
			// Get the category details and send to view
			$category = $this->MemberVoiceIdea->Category->find('first', array('conditions' => array('Category.id' => $categoryId)));
			$this->set('category', $category);
		}
		// Get the ideas based on the conditions set above
		$ideas = $this->paginate('MemberVoiceIdea', $conditions);
		// Get a list of all categories for the nav bar in view
		$categories = $this->MemberVoiceIdea->Category->find('all', array('order' => 'Category.category'));

		// Set the view variables
		$this->set('ideas', $ideas);
		$this->set('categories', $categories);
		$this->set('user', $this->_getUserID());
		$this->set('voteurl', $this->__getVoteUrl());
	}

/**
 * Show a single idea.
 * @param  int|null $id The id of the idea to show.
 * @throws NotFoundException if id is null.
 * @throws NotFoundException if no idea could be found with a matching id.
 */
	public function idea($id = null) {
		// Throw an error if an id is not passed
		if (!$id) {
			throw new NotFoundException(__('No idea was specified'));
		}

		// Locate the idea, throw an error if not found
		$idea = $this->MemberVoiceIdea->find('first', array('conditions' => array('Idea.id' => $id)));
		if (!$idea) {
			throw new NotFoundException(__('Specified Idea was not found'));
		}

		// Get a list of all categories for the nav bar in view
		$categories = $this->MemberVoiceIdea->Category->find('all', array('order' => 'Category.category'));
		// Get the comments for this idea
		$comments = $this->MemberVoiceIdea->Comment->find('all', array('conditions' => array('Comment.idea_id' => $id)));

		// Set the view variables
		$this->set('idea', $idea);
		$this->set('categories', $categories);
		$this->set('comments', $comments);
		$this->set('firstname', $this->_mvFirstName);
		$this->set('lastname', $this->_mvLastName);
		$this->set('user', $this->_getUserID());
		$this->set('voteurl', $this->__getVoteUrl());
	}

/**
 * Vote for an idea. This does not have a view, it is accessed via AJAX.
 * @param  int|null $id The id of the idea to vote for.
 */
	public function vote($id = null) {
		/* This is the array that will be JSON'd and sent back
		   We'll populate it using a big if statement */
		$return = array();

		// Send an error if no idea is set
		if (!$id) {
			$return['responseid'] = MemberVoiceVote::VOTE_NO_ID;
			$return['response'] = 'No ID provided';
		} else {
			// Look up the idea
			$idea = $this->MemberVoiceIdea->find('first', array('conditions' => array('Idea.id' => $id)));

			// Send an error if the idea is not found
			if (!$idea) {
				$return['responseid'] = MemberVoiceVote::VOTE_NOT_FOUND;
				$return['response'] = 'Idea not found';
			} else {
				// Ok, we defintely have an idea, so set the id in the return
				$return['id'] = $id;

				// Save vote to local var for testing
				if (isset($this->request->data['vote'])) {
					$vote = $this->request->data['vote'];
				} else {
					$vote = null;
				}

				// Now check the votes - are they null?
				if ($vote == null) {
					$return['responseid'] = MemberVoiceVote::VOTE_MISSING;
					$return['response'] = 'No vote provided';
				} elseif ($vote < -1 && $vote > 1) {
					$return['responseid'] = MemberVoiceVote::VOTE_INVALID;
					$return['response'] = 'Vote not valid';
				} else {
					// Vote is valid, save the vote and put the return value in the return array
					$return['votes'] = $this->MemberVoiceIdea->saveVote($id, $this->_getUserID(), $vote);
					// Did it save?
					if ($return['votes'] !== false) {
						$return['responseid'] = MemberVoiceVote::VOTE_VALID;
						$return['voted'] = $vote;
					} else {
						$return['responseid'] = MemberVoiceVote::VOTE_NOT_SAVED;
						$return['response'] = 'Vote failed';
					}
				}
			}
		}

		// Send the JSON back
		$this->set('return', $return);
		$this->set('_serialize', 'return');
	}

/**
 * Get the URL for voting.
 * @return string The URL for voting.
 */
	private function __getVoteUrl() {
		if (method_exists("Router", "baseUrl")) {
			$url = Router::baseUrl();
		} else {
			$url = FULL_BASE_URL;
		}
		$url .= Router::url(array('plugin' => 'MemberVoice', 'controller' => 'MemberVoiceIdeas', 'action' => 'vote'));
		return $url;
	}
}