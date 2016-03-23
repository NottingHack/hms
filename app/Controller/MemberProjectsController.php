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
 * @package       app.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');
    
/**
 * Controller to handle Member projects functionality.
 */
class MemberProjectsController extends AppController {
/**
 * Views rendered from this controller will have access to the following helpers.
 * @var array
 */
	public $helpers = array('Html', 'Form');

/**
 * The list of models this Controller relies on.
 * @var array
 */
	public $uses = array('MemberProject', 'Member');
    
/**
 * The list of components this Controller relies on.
 * @var array
 */
    public $components = array('LabelPrinter');

/**
 * Label template name
 */
    public $label_template = 'member_project';

/**
 * Test to see if a user is authorized to make a request.
 *
 * @param array $user Member record for the user.
 * @param CakeRequest $request The request the user is attempting to make.
 * @return bool True if the user is authorized to make the request, otherwise false.
 * @link http://api20.cakephp.org/class/cake-request
 */
	public function isAuthorized($user, $request) {
		// allows full access to see everything
		if (parent::isAuthorized($user, $request)) {
			return true;
		}

		// Get the member_id details have been requested for & the logged in users member_id
		$logMemberId = $this->_getLoggedInMemberId();
        

        
        $memberIsCurrentMember = ($this->Member->getStatusForMember($logMemberId) == Status::CURRENT_MEMBER);
		$memberAdmin = $this->Member->GroupsMember->isMemberInGroup( $logMemberId, Group::MEMBERSHIP_ADMIN);

		switch ($request->action) {
            case 'index':
                if (isset($request->params['pass'][0])) {
                    $reqProjectId = $request->params['pass'][0];
                    $reqProjectMemberId = $this->MemberProject->getMemberIDforProject($reqProjectId);
                    return $memberAdmin || ($reqProjectMemberId == $logMemberId);
                } else {
                    return false;
                }
            case 'listProjects':                // takes memberId (or if null shows loggedInMemberId
                if (isset($request->params['pass'][0])) {
                    $reqMemberId = $request->params['pass'][0];
                } else {
                    $reqMemberId = $logMemberId;
                }
                return $memberAdmin || ($reqMemberId == $logMemberId);
            case 'view':                // rest take memberProjcetId
            case 'markComplete':
                if (isset($request->params['pass'][0])) {
                    $reqProjectId = $request->params['pass'][0];
                } else {
                    return false;
                }
                $reqProjectMemberId = $this->MemberProject->getMemberIDforProject($reqProjectId);
                return $memberAdmin || ($reqProjectMemberId == $logMemberId);
			case 'edit':
            case 'printDNHLabel':
            case 'markAbandoned':
            case 'resume':
                if (isset($request->params['pass'][0])) {
                    $reqProjectId = $request->params['pass'][0];
                } else {
                    return false;
                }
                $reqProjectMemberId = $this->MemberProject->getMemberIDforProject($reqProjectId);
                return $memberIsCurrentMember && ($memberAdmin || ($reqProjectMemberId == $logMemberId));
            case 'add':                 // takes no param
                return $memberIsCurrentMember;
		}
	}

/**
 * Show a project (uses view)
 *
 * @param int|null $memberProjectId The id of the project to view; null redirect to list
 */
	public function index($memberProjectId = null) {
        if ($memberProjectId == null) {
            return $this->redirect(array('controller' => 'memberprojects', 'action' => 'listProjects'));
        }
        
        $this->view($memberProjectId);
        $this->render('view');
        
	}
    
/**
 * Show a list of projects owend by a memberId
 *
 * @param int|null $memberId The member_id to list projects for; null show current members list
 */
	public function listProjects($memberId = null) {
        $this->view = 'list_projects';
        if ($memberId == null) {
            $memberId = $this->_getLoggedInMemberId();
        }
        
        $this->paginate = $this->MemberProject->getProjectsList(true, array('Member.member_id' => $memberId));
        $projectsList = $this->paginate('MemberProject');
        $projectsList = $this->MemberProject->formatProjectsList($projectsList, false);
        // need to add the action buttons for each project
        $projectsList = $this->__addActionsforProjectsList($projectsList);
        
        $this->set('projectsList', $projectsList);
        $this->set('shortDescriptionLength', 40);
        
        
        $member = $this->Member->getMemberSummaryForMember($memberId);
        $this->set('member', $member);
        
        // you can only add your own projects
        if ($memberId == $this->_getLoggedInMemberId() && $this->Member->getStatusForMember($memberId) == Status::CURRENT_MEMBER) {
            $this->Nav->add('Add new project', 'memberprojects', 'add');
        }
	}
    
/**
 * Show a project
 *
 * @param int|null $memberProjectId The id of the project to view; null redirect to list
 */
    public function view($memberProjectId = null) {
        if ($memberProjectId == null) {
            $this->redirect(array('controller' => 'memberprojects', 'action' => 'listProjects'));
        }
        
        $project = $this->MemberProject->getProject($memberProjectId);
        $this->set('project', $project);
        
        $member = $this->Member->getMemberSummaryForMember($project['memberId']);
        $this->set('member', $member);
        
        $project = $this->__addActionsForProject($project, true);
        
        foreach ($project['actions'] as $action) {
            $class = '';
            if (isset($action['class'])) {
                $class = $action['class'];
            }
            $this->Nav->add($action['title'], $action['controller'], $action['action'], $action['params'], $class);
        }
	}
    
/**
 * Edit a project
 *
 * @param int|null $memberProjectId The id of the project to edit; null redirect to list
 */
	public function edit($memberProjectId = null) {
        if ($memberProjectId == null) {
            $this->redirect(array('controller' => 'memberprojects', 'action' => 'listProjects'));
        }
        
        $project = $this->MemberProject->getProject($memberProjectId, false);
        $projectFormated = $this->MemberProject->formatDetails($project);
        $this->set('project', $projectFormated);

        $member = $this->Member->getMemberSummaryForMember($projectFormated['memberId']);
        $this->set('member', $member);
        
        if ( $this->request->is('post') || $this->request->is('put')) {
            $sanitisedData = $this->request->data;

            if ($sanitisedData) {
                $updateResult = $this->MemberProject->save($sanitisedData);
                if (is_array($updateResult)) {
                    $this->Session->setFlash('Details updated.');
                    return $this->redirect(array('action' => 'view', $memberProjectId));
                }
            }
            $this->Session->setFlash('Unable to update project.');
        }
        if (!$this->request->data) {
            $this->request->data = $project;
        }
        
        $this->set('project', $this->MemberProject->formatDetails($project));

	}

    
/**
 * Print Do-Not-Hack label for a project
 *
 * @param int|null $memberProjectId The id of the project to print; null redirect to list
 */
	public function printDNHLabel($memberProjectId = null) {
        if ($memberProjectId == null) {
            $this->redirect(array('controller' => 'memberprojects', 'action' => 'listProjects'));
        }
        
 
        $project = $this->MemberProject->getProject($memberProjectId);
        $member = $this->Member->getMemberSummaryForMember($project['memberId']);
        
        $qrURL = Router::url([
                          'controller' => 'memberprojects',
                          'action' => 'view',
                          $project['memberProjectId'],
                          ], true);
        
        $memberName = $member['firstname'] . ' ' . $member['surname'];
 
        // hack to offset the ID printing and give the look of right justification
        $idOffset = (5 - strlen($project['memberProjectId'])) * 35;

        $substitutions = array(
                               'memberName' => $memberName,
                               'username' => $member['username'],
                               'projectName' => $project['projectName'],
                               'startDate' => $project['startDate'],
                               'memberProjectId' => $project['memberProjectId'],
                               'qrURL' => $qrURL,
                               'lastDate' => date('Y-m-d'),
                               'idOffset' => 220 + $idOffset,
                               );

        if ($this->LabelPrinter->printLabel($this->label_template, $substitutions)) {
            $this->Session->setFlash('Label sent to printer');
        } else {
            $this->Session->setFlash('Unable to print label');
        }
        
        return $this->redirect($this->referer());
	}
    
/**
 * Mark a project as complete (not longer able to print labels for it)
 *
 * @param int|null $memberProjectId The id of the project to view; null redirect to list
 */
	public function markComplete($memberProjectId = null) {
        if ($memberProjectId == null) {
            $this->redirect(array('controller' => 'memberprojects', 'action' => 'listProjects'));
        }
        
        if ($this->MemberProject->changeStateForPorject($memberProjectId, MemberProject::PROJCET_COMPLETE)) {
            $this->Session->setFlash('Project marked Complete');
        } else {
            $this->Session->setFlash('Unable to update project');
        }
        
        return $this->redirect($this->referer());
	}
    
/**
 * Mark a project as Abandoned (not longer able to print labels for it)
 *
 * @param int|null $memberProjectId The id of the project to view; null redirect to list
 */
	public function markAbandoned($memberProjectId = null) {

        if ($memberProjectId == null) {
            $this->redirect(array('controller' => 'memberprojects', 'action' => 'listProjects'));
        }
        
        if ($this->MemberProject->changeStateForPorject($memberProjectId, MemberProject::PROJCET_ABANDONED)) {
            $this->Session->setFlash('Project marked Abandoned');
        } else {
            $this->Session->setFlash('Unable to update project');
        }
        
        return $this->redirect($this->referer());
        
	}
    
/**
 * Resume a currently complete project
 *
 * @param int|null $memberProjectId The id of the project to view; null redirect to list
 */
	public function resume($memberProjectId = null) {

        if ($memberProjectId == null) {
            $this->redirect(array('controller' => 'memberprojects', 'action' => 'listProjects'));
        }
        
        if ($this->MemberProject->changeStateForPorject($memberProjectId, MemberProject::PROJCET_ACTIVE)) {
            $this->Session->setFlash('Project marked active');
        } else {
            $this->Session->setFlash('Unable to update project');
        }
        
        return $this->redirect($this->referer());
        
	}
    
/**
 * Add a new project
 *
 */
    public function add() {
        $memberId = $this->_getLoggedInMemberId();
        $member = $this->Member->getMemberSummaryForMember($memberId);
        $this->set('member', $member);
        
        // if this is a POST/PUT:
        if ($this->request->is('post') || $this->request->is('put')) {
            // sanitise!
            $sanitisedData = $this->request->data;
            
            if ($sanitisedData) {
                // clean the data
                // create a new projectformember
                $result = $this->MemberProject->newProjectForMember($memberId,
                                                                    $sanitisedData['MemberProject']['project_name'],
                                                                    $sanitisedData['MemberProject']['description']
                                                                    );
            
                if ($result) {
                    // pass redirect to list
                    $this->Session->setFlash('Project created');
                    return $this->redirect(array('controller' => 'memberprojects', 'action' => 'listProjects'));
                } else {
                    // fail set flash and show page again
                    $this->Session->setFlash('Unable to create project');
                }
            }
        }
        
    }
    
    
/**
 * add actions with dispay in the view in to a list of projects
 *
 * @param array $projectsList
 * @return array A list of projects now with added actions 
 */
    private function __addActionsForProjectsList($projectsList) {
        $actionedList = array();
        foreach($projectsList as $project) {
            array_push($actionedList, $this->__addActionsForProject($project));
        }
        return $actionedList;
    }
    
/**
 * Add actions for a project based on the viewing members permissions and IP
 *
 * @param array $project formated project object
 * @param bool $edit Is set show edit action over view action
 * @return project object now with added actions
 */
    private function __addActionsForProject($project, $edit = false) {
        $actions = array();
        $memberId = $this->_getLoggedInMemberId();
        $memberAdmin = $this->Member->GroupsMember->isMemberInGroup($memberId, Group::MEMBERSHIP_ADMIN);
        
        // view or edit, always add this
        if ($edit) {
            array_push($actions,
                       array(
                             'title' => 'Edit Project',
                             'controller' => 'memberprojects',
                             'action' => 'edit',
                             'params' => array(
                                               $project['memberProjectId'],
                                               )
                             )
                       );
        } else {
            array_push($actions,
                       array(
                             'title' => 'View Project',
                             'controller' => 'memberprojects',
                             'action' => 'view',
                             'params' => array(
                                               $project['memberProjectId'],
                                               )
                             )
                       );
        }
        
        // print DHN, only if on space IP
        if ($this->isRequestLocal() && $project['stateId'] == MemberProject::PROJCET_ACTIVE) {
            array_push($actions,
                       array(
                             'title' => 'Print Do-Not-Hack Label',
                             'controller' => 'memberprojects',
                             'action' => 'printDNHLabel',
                             'params' => array(
                                               $project['memberProjectId'],
                                               )
                             )
                       );
        }
        
        
        // mark complete
        if ($project['stateId'] == MemberProject::PROJCET_ACTIVE &&
            ($memberId == $project['memberId'] || !$memberAdmin)) {
            array_push($actions,
                       array(
                             'title' => 'Mark Complete',
                             'controller' => 'memberprojects',
                             'action' => 'markComplete',
                             'params' => array(
                                               $project['memberProjectId'],
                                               )
                             )
                       );
        }
        // mark abandonded
        if ($memberAdmin && $memberId != $project['memberId'])
        array_push($actions,
                   array(
                         'title' => 'Mark Abandoned',
                         'controller' => 'memberprojects',
                         'action' => 'markAbandoned',
                         'params' => array(
                                           $project['memberProjectId'],
                                           )
                         )
                   );
        // resume
        if ($project['stateId'] != MemberProject::PROJCET_ACTIVE) {
        array_push($actions,
                   array(
                         'title' => 'Resume Project',
                         'controller' => 'memberprojects',
                         'action' => 'resume',
                         'params' => array(
                                           $project['memberProjectId'],
                                           )
                         )
                   );
        }
        
        $project['actions'] = $actions;

        return $project;
    }
    
/**
 * Test to see if a request is coming from within the hackspace.
 *
 * @return bool True if the request is coming from with in the hackspace, false otherwise.
 */
	public function isRequestLocal() {
		return preg_match('/10\.0\.0\.\d+/', $this->getRequestIpAddress());
	}

/**
 * Get the ip address of the request.
 *
 * @return string The IP address of the request.
 */
	public function getRequestIpAddress() {
		// We might have a debug config here to force requests to be local
		App::uses('PhpReader', 'Configure');
		Configure::config('default', new PhpReader());

		try {
			Configure::load('debug', 'default');
			$configIp = Configure::read('forceRequestIp');
			if (isset($configIp)) {
				return $configIp;
			}
		} catch(ConfigureException $ex) {
			// We don't care.
		}

		return $_SERVER["REMOTE_ADDR"];
	}
    
}