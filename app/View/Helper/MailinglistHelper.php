<?php

	App::uses('AppHelper', 'View/Helper');

	// Helper class to display mailing lists
	class MailinglistHelper extends AppHelper {

		public $helpers = array('Form', 'Html');

		public function output($mailingListDetails)
		{
			$mailingListOptions = array();
			$selectedMailingLists = array();

			foreach ($mailingListDetails['data'] as $mailingList) 
			{
				$listId = $mailingList['id'];
				$mailingListOptions[$listId] = $mailingList['name'];
				if(Hash::get($mailingList, 'subscribed'))
				{
					array_push($selectedMailingLists, $listId);
				}
			}

			return $this->Form->input('MailingLists.MailingLists',
				array(
			        'label' => __(' ',true),
			        'type' => 'select',
			        'multiple' => 'checkbox',
			        'options' => $mailingListOptions,
			        'selected' => $selectedMailingLists,
			    )
			); 
		}

		public function outputList($mailingListDetails)
		{
			$output = array();
			foreach ($mailingListDetails['data'] as $mailingList) 
			{
				$listId = $mailingList['id'];
				if(Hash::get($mailingList, 'subscribed'))
				{
					array_push($output, $this->Html->link($mailingList['name'], array('controller' => 'Mailinglists', 'action' => 'view', $listId)));
				}
			}

			if(count($output) > 0)
			{
				return String::toList($output);
			}

			return 'None';
		}
	}

?>
