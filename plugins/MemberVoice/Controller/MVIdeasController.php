<?php
/**
 * Controller for Ideas in MemberVoice
 *
 *
 * @package       plugin.MemberVoice.Controller
 */
class MVIdeasController extends MemberVoiceAppController {

	public $helpers = array('Html', 'Form', 'Paginator', 'Tinymce');

	public $components = array('RequestHandler');

	public $paginate = array(
							'limit' => 5,
							'order' => array(
											'Idea.votes' => 'desc'
											)
							);

	//! Main view. Shows either all ideas or just ideas from a category
	/*!
		@param integer $id ID of category to show.  If null, show all
	*/
	public function index($id = null) {
		// If an array is passed, restrict to that category
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
			$category = $this->MVIdea->Category->find('first', array('conditions' => array('Category.id' => $id)));
			$this->set('category', $category);
		}
		// Get the ideas based on the conditions set above
		$ideas = $this->paginate('MVIdea', $conditions);
		// Get a list of all categories for the nav bar in view
		$categories = $this->MVIdea->Category->find('all', array('order' => 'Category.category'));

		// Set the view variables
		$this->set('ideas', $ideas);
		$this->set('categories', $categories);
		$this->set('user', $this->_getUserID());
		$this->set('voteurl', $this->_getVoteUrl());
	}

	//! Show a single idea
	/*!
		@param integer $id ID of idea to show
	*/
	public function idea($id = null) {
		// Throw an error if an id is not passed
		if (!$id) {
			throw new NotFoundException(__('Invalid idea'));
		}

		// Locate the idea, throw an error if not found
		$idea = $this->MVIdea->find('first', array('conditions' => array('Idea.id' => $id)));
		if (!$idea) {
			throw new NotFoundException(__('Invalid idea'));
		}

		// Get a list of all categories for the nav bar in view
		$categories = $this->MVIdea->Category->find('all', array('order' => 'Category.category'));
		// Get the comments for this idea
		$comments = $this->MVIdea->Comment->find('all', array('conditions' => array('Comment.idea_id' => $id)));

		// Set the view variables
		$this->set('idea', $idea);
		$this->set('categories', $categories);
		$this->set('comments', $comments);
		$this->set('firstname', $this->mvFirstName);
		$this->set('lastname', $this->mvLastName);
		$this->set('user', $this->_getUserID());
		$this->set('voteurl', $this->_getVoteUrl());
	}

	//! Vote for an idea. This does not have a view, it is accessed via AJAX
	/*!
		@param integer $id ID of idea to vote for
		Rest of the input comes via a post
	*/
	public function vote($id = null) {
		/* This is the array that will be JSON'd and sent back
		   We'll populate it using a big if statement */
		$return = array();

		// Send an error if no idea is set
		if (!$id) {
			$return['responseid'] = MVVote::VOTE_NO_ID;
			$return['response'] = 'No ID provided';
		}
		else {
			// Look up the idea
			$idea = $this->MVIdea->find('first', array('conditions' => array('Idea.id' => $id)));

			// Send an error if the idea is not found
			if (!$idea) {
				$return['responseid'] = MVVote::VOTE_NOT_FOUND;
				$return['response'] = 'Idea not found';
			}
			else {
				// Ok, we defintely have an idea, so set the id in the return
				$return['id'] = $id;

				// Save vote to local var for testing
				if (isset($this->request->data['vote'])) {
					$vote = $this->request->data['vote'];
				}
				else {
					$vote = null;
				}

				// Now check the votes - are they null?
				if ($vote == null) {
					$return['responseid'] = MVVote::VOTE_MISSING;
					$return['response'] = 'No vote provided';
				}
				// Is the vote in the expected range?  can only be -1, 0 or 1
				elseif ($vote < -1 && $vote > 1) {
					$return['responseid'] = MVVote::VOTE_INVALID;
					$return['response'] = 'Vote not valid';
				}	
				else {
					// Vote is valid, save the vote and put the return value in the return array
					$return['votes'] = $this->MVIdea->saveVote($id, $this->_getUserID(), $vote);
					// Did it save?
					if ($return['votes'] !== false) {
						$return['responseid'] = MVVote::VOTE_VALID;
						$return['voted'] = $vote;
					}
					else {
						$return['responseid'] = MVVote::VOTE_NOT_SAVED;
						$return['response'] = 'Vote failed';
					}
				}
			}
		}

		// Send the JSON back
		$this->set('return', $return);
		$this->set('_serialize', 'return');
	}

	//! Returns the URL for voting
	/*!
		Used for the javascript to set up the AJAX
	*/
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