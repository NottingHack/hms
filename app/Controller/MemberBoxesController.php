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
 * Controller to handle Member boxes.
 */
class MemberBoxesController extends AppController {
/**
 * Views rendered from this controller will have access to the following helpers.
 * @var array
 */
	public $helpers = array('Html', 'Form');

/**
 * The list of models this Controller relies on.
 * @var array
 */
	public $uses = array('MemberBox', 'Meta', 'Member', 'Transaction');
    
/**
 * The list of components this Controller relies on.
 * @var array
 */
    public $components = array('LabelPrinter');

/**
 * Label template name
 */
    private $labelTemplate = 'member_box';
    
    private $individualLimitKey = 'member_box_individual_limit';
    private $maxLimitKey = 'member_box_limit';
    private $boxCostKey = 'member_box_cost';
    
    
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
                    $reqBoxId = $request->params['pass'][0];
                    $reqBoxMemberId = $this->MemberBox->getMemberIDforBox($reqBoxId);
                    return $memberAdmin || ($reqBoxMemberId == $logMemberId);
                } else {
                    return false;
                }
            case 'listBoxes':                // takes memberId (or if null shows loggedInMemberId
                if (isset($request->params['pass'][0])) {
                    $reqMemberId = $request->params['pass'][0];
                } else {
                    $reqMemberId = $logMemberId;
                }
                return $memberAdmin || ($reqMemberId == $logMemberId);
            case 'view':                // rest take memberProjcetId
            case 'markRemoved':
                if (isset($request->params['pass'][0])) {
                    $reqBoxId = $request->params['pass'][0];
                } else {
                    return false;
                }
                $reqBoxMemberId = $this->MemberBox->getMemberIDforBox($reqBoxId);
                return $memberAdmin || ($reqBoxMemberId == $logMemberId);
            case 'printBoxLabel':
            case 'markAbandoned':
            case 'markInuse':
                if (isset($request->params['pass'][0])) {
                    $reqBoxId = $request->params['pass'][0];
                } else {
                    return false;
                }
                $reqBoxMemberId = $this->MemberBox->getMemberIDforBox($reqBoxId);
                return $memberIsCurrentMember && ($memberAdmin || ($reqBoxMemberId == $logMemberId));
            case 'buy':                 // takes no param
                return $memberIsCurrentMember;
            case 'issue':               // only member Admins can free issue a box
                if (isset($request->params['pass'][0])) {
                    return $memberAdmin;
                } else {
                    return false;
                }
		}
	}

/**
 * Show a Box (uses view)
 *
 * @param int|null $memberBoxId The id of the Box to view; null redirect to list
 */
	public function index($memberBoxId = null) {
        if ($memberBoxId == null) {
            return $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes'));
        }
        
        $this->view($memberBoxId);
        $this->render('view');
        
	}
    
/**
 * Show a list of boxes owend by a memberId
 *
 * @param int|null $memberId The member_id to list boexs for; null show current members list
 */
	public function listBoxes($memberId = null) {
        $this->view = 'list_boxes';
        if ($memberId == null) {
            $memberId = $this->_getLoggedInMemberId();
        }
        
        $this->paginate = $this->MemberBox->getBoxesList(true, array('Member.member_id' => $memberId));
        $boxesList = $this->paginate('MemberBox');
        $boxesList = $this->MemberBox->formatBoxesList($boxesList, false);
        // need to add the action buttons for each box
        $boxesList = $this->__addActionsforBoxesList($boxesList);
        
        $this->set('boxesList', $boxesList);
        $this->set('shortDescriptionLength', 40);
        
        
        $member = $this->Member->getMemberSummaryForMember($memberId);
        $this->set('member', $member);
        
        // you can only add your own boxes
        if ($memberId == $this->_getLoggedInMemberId() && $this->Member->getStatusForMember($memberId) == Status::CURRENT_MEMBER) {
            $this->Nav->add('Buy new box', 'memberBoxes', 'buy');
        }
        
        if ($this->Member->GroupsMember->isMemberInGroup( $this->_getLoggedInMemberId(), Group::MEMBERSHIP_ADMIN) && $memberId != $this->_getLoggedInMemberId()) {
            $this->Nav->add('Issue new box', 'memberBoxes', 'issue', array($memberId));
        }
	}
    
/**
 * Show a box
 *
 * @param int|null $memberBoxId The id of the box to view; null redirect to list
 */
    public function view($memberBoxId = null) {
        if ($memberBoxId == null) {
            $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes'));
        }
        
        $box = $this->MemberBox->getBox($memberBoxId);
        $this->set('box', $box);
        
        $member = $this->Member->getMemberSummaryForMember($box['memberId']);
        $this->set('member', $member);
        
        $box = $this->__addActionsForBox($box, true);
        
        foreach ($box['actions'] as $action) {
            $class = '';
            if (isset($action['class'])) {
                $class = $action['class'];
            }
            $this->Nav->add($action['title'], $action['controller'], $action['action'], $action['params'], $class);
        }
	}

/**
 * Print Members Box label
 *
 * @param int|null $memberBoxId The id of the box to print; null redirect to list
 */
	public function printBoxLabel($memberBoxId = null) {
        if ($memberBoxId == null) {
            $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes'));
        }
        
 
        $box = $this->MemberBox->getBox($memberBoxId);
        $member = $this->Member->getMemberSummaryForMember($box['memberId']);
        
        $qrURL = Router::url([
                          'controller' => 'memberBoxes',
                          'action' => 'view',
                          $box['memberBoxId'],
                          ], true);
        
        $memberName = $member['firstname'] . ' ' . $member['surname'];
 
        // hack to offset the ID printing and give the look of right justification
        $idOffset = (5 - strlen($box['memberBoxId'])) * 35;

        $substitutions = array(
                               'memberName' => $memberName,
                               'username' => $member['username'],
                               'memberBoxId' => $box['memberBoxId'],
                               'qrURL' => $qrURL,
                               'idOffset' => 220 + $idOffset,
                               );

        if ($this->LabelPrinter->printLabel($this->labelTemplate, $substitutions)) {
            $this->Session->setFlash('Label sent to printer');
        } else {
            $this->Session->setFlash('Unable to print label');
        }
        
        return $this->redirect($this->referer());
	}
    
/**
 * Mark a box as removed (not longer able to print labels for it)
 *
 * @param int|null $memberBoxId The id of the box to mark removed; null redirect to list
 */
	public function markRemoved($memberBoxId = null) {
        if ($memberBoxId == null) {
            $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes'));
        }
        
        if ($this->MemberBox->changeStateForBox($memberBoxId, MemberBox::BOX_REMOVED)) {
            $this->Session->setFlash('Box marked as removed');
        } else {
            $this->Session->setFlash('Unable to update box');
        }
        
        return $this->redirect($this->referer());
	}
    
/**
 * Mark a box as Abandoned (not longer able to print labels for it)
 *
 * @param int|null $memberBoxId The id of the box to mark abandoned; null redirect to list
 */
	public function markAbandoned($memberBoxId = null) {

        if ($memberBoxId == null) {
            $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes'));
        }
        
        if ($this->MemberBox->changeStateForBox($memberBoxId, MemberBox::BOX_ABANDONED)) {
            $this->Session->setFlash('Box marked Abandoned');
        } else {
            $this->Session->setFlash('Unable to update box');
        }
        
        return $this->redirect($this->referer());
        
	}
    
/**
 * Mark a box as inuse again
 *
 * @param int|null $memberBoxId The id of the box to mark in use; null redirect to list
 */
	public function markInuse($memberBoxId = null) {

        if ($memberBoxId == null) {
            $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes'));
        }
        // check member is not at limit for number of allowed boxes
        $individualLimit = ($this->Meta->getValueFor($this->individualLimitKey));
        
        $memberBoxCount = $this->MemberBox->boxCountForMemberByBox($memberBoxId);
        
        if ($memberBoxCount == $individualLimit) {
            // all ready got to many boxes
            $this->Session->setFlash('Too many boxes already');
        } else if ($this->MemberBox->changeStateForBox($memberBoxId, MemberBox::BOX_INUSE)) {
            $this->Session->setFlash('Box marked inuse');
        } else {
            $this->Session->setFlash('Unable to update box');
        }
        
        return $this->redirect($this->referer());
        
	}
    
/**
 * buy a new box
 *
 */
    public function buy() {
        $memberId = $this->_getLoggedInMemberId();
        $member = $this->Member->getMemberSummaryForMember($memberId);
        $this->set('member', $member);
        
        $individualLimit = ($this->Meta->getValueFor($this->individualLimitKey));
        $maxLimit = ($this->Meta->getValueFor($this->maxLimitKey));
        $boxCost = ($this->Meta->getValueFor($this->boxCostKey));
        
        $this->set('boxCost', -$boxCost);
        
        // check member does not all ready have max number of boxes
        $memberBoxCount = $this->MemberBox->boxCountForMember($memberId);
        $canBuyBox = true;
        
        if ($memberBoxCount == $individualLimit) {
            // all ready got to many boxes
            $this->Session->setFlash('Too many boxes already');
            $canBuyBox = false;
            
        }
        
        // check we have not hit max limit of boxes
        $spaceBoxCount = $this->MemberBox->boxCountForSpace();
        if ($spaceBoxCount == $maxLimit) {
            $this->Session->setFlash('Sorry we have no room for any more boxes');
            $canBuyBox = false;
            
        }
        
        if (($member['balance'] + $boxCost) < (-1 *$member['creditLimit'])) {
            $this->Session->setFlash('Sorry you do not have enought credit to buy another box');
            $canBuyBox = false;
        }

        $this->set('canBuyBox', $canBuyBox);

        // if this is a POST/PUT: and member has no hit limit or max limit
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!$canBuyBox) {
                $this->Session->setFlash('Unable to buy a box');
                return $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes'));
            }
            
            // charge for box
            if ($this->Transaction->recordTransaction($memberId, $boxCost, Transaction::TYPE_MEMBERBOX, 'Members Box')) {
                // create a new box for member
                $result = $this->MemberBox->newBoxForMember($memberId);
            
                if ($result) {
                    // pass redirect to list
                    $this->Session->setFlash('Box created');
                    return $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes'));
                } else {
                    // TODO: should roll back the payment aswell
                
                    // fail set flash and show page again
                    $this->Session->setFlash('Unable to buy a box, but you have been charged');
                    return $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes'));
                }
            } else {
                // failed to charge for a box
                $this->Session->setFlash('Unable to buy a box, your account has not been charged');
                return $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes'));
            }
        }
    }
    
/**
 * issue a new box
 *
 * @param int $memberId
 */
    public function issue($memberId = null) {
        if ($memberId == null) {
            $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes'));
        }
        
        if ($memberId == $this->_getLoggedInMemberId()) {
            $this->Session->setFlash('You can not issue a box to yourself');
            $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes'));
        }
        $individualLimit = ($this->Meta->getValueFor($this->individualLimitKey));
        $maxLimit = ($this->Meta->getValueFor($this->maxLimitKey));
        $boxCost = ($this->Meta->getValueFor($this->boxCostKey));
        
        $this->set('boxCost', -$boxCost);
        
        // check member does not all ready have max number of boxes
        $memberBoxCount = $this->MemberBox->boxCountForMember($memberId);
        $canBuyBox = true;
        
        if ($memberBoxCount == $individualLimit) {
            // all ready got to many boxes
            $this->Session->setFlash('Too many boxes already');
            $canBuyBox = false;
            
        }
        
        // check we have not hit max limit of boxes
        $spaceBoxCount = $this->MemberBox->boxCountForSpace();
        if ($spaceBoxCount == $maxLimit) {
            $this->Session->setFlash('Sorry we have no room for any more boxes');
            $canBuyBox = false;
            
        }
        
        $this->set('canBuyBox', $canBuyBox);
        
        // check we have not hit max limit of boxes
        
        if ($canBuyBox) {
            // create a new boxformember
            $result = $this->MemberBox->newBoxForMember($memberId);
            
            if ($result) {
                // pass redirect to list
                $this->Session->setFlash('Box created');
                return $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes', $memberId));
            } else {
                // fail set flash and show page again
                $this->Session->setFlash('Unable to issue box');
                return $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes', $memberId));
            }
        } else {
            $this->Session->setFlash('Unable to issue box due to limits');
            return $this->redirect(array('controller' => 'memberBoxes', 'action' => 'listBoxes', $memberId));
        }
    }
    
/**
 * add actions with dispay in the view in to a list of boxes
 *
 * @param array $boxesList
 * @return array A list of boxes now with added actions 
 */
    private function __addActionsForBoxesList($boxesList) {
        $actionedList = array();
        foreach($boxesList as $box) {
            array_push($actionedList, $this->__addActionsForBox($box));
        }
        return $actionedList;
    }
    
/**
 * Add actions for a box based on the viewing members permissions and IP
 *
 * @param array $box formated box object
 * @param bool $edit Is set show edit action over view action
 * @return box object now with added actions
 */
    private function __addActionsForBox($box, $edit = false) {
        $actions = array();
        $memberId = $this->_getLoggedInMemberId();
        $memberAdmin = $this->Member->GroupsMember->isMemberInGroup($memberId, Group::MEMBERSHIP_ADMIN);
        
        // view or edit, always add this
        if ($edit) {
//            array_push($actions,
//                       array(
//                             'title' => 'Edit Box',
//                             'controller' => 'memberBoxes',
//                             'action' => 'edit',
//                             'params' => array(
//                                               $box['memberBoxId'],
//                                               )
//                             )
//                       );
        } else {
            array_push($actions,
                       array(
                             'title' => 'View Box',
                             'controller' => 'memberBoxes',
                             'action' => 'view',
                             'params' => array(
                                               $box['memberBoxId'],
                                               )
                             )
                       );
        }
        
        // print box, only if on space IP
        if ($this->isRequestLocal() && $box['stateId'] == MemberBox::BOX_INUSE) {
            array_push($actions,
                       array(
                             'title' => 'Print Box Label',
                             'controller' => 'memberBoxes',
                             'action' => 'printBoxLabel',
                             'params' => array(
                                               $box['memberBoxId'],
                                               )
                             )
                       );
        }
        
        
        // mark complete
        if ($box['stateId'] == MemberBox::BOX_INUSE &&
            ($memberId == $box['memberId'] || !$memberAdmin)) {
            array_push($actions,
                       array(
                             'title' => 'Mark Removed',
                             'controller' => 'memberBoxes',
                             'action' => 'markRemoved',
                             'params' => array(
                                               $box['memberBoxId'],
                                               )
                             )
                       );
        }
        // mark abandonded
        if ($memberAdmin && $memberId != $box['memberId'])
        array_push($actions,
                   array(
                         'title' => 'Mark Abandoned',
                         'controller' => 'memberBoxes',
                         'action' => 'markAbandoned',
                         'params' => array(
                                           $box['memberBoxId'],
                                           )
                         )
                   );
        // mark inuse
        if ($box['stateId'] != MemberBox::BOX_INUSE) {
        array_push($actions,
                   array(
                         'title' => 'Mark In Use',
                         'controller' => 'memberBoxes',
                         'action' => 'markInuse',
                         'params' => array(
                                           $box['memberBoxId'],
                                           )
                         )
                   );
        }
        
        $box['actions'] = $actions;

        return $box;
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