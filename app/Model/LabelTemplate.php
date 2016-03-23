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
 * @package       app.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppModel', 'Model');

/**
 * Model for label templates
 */
class LabelTemplate extends AppModel {

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'label_templates';

/**
 * Specify the primary key.
 * @var string
 */
	public $primaryKey = 'template_name';
    
/**
 * Get a single project and return formated or not
 *
 * @param int $memberProjectId
 * @param bool $format 
 * @return array
 */
    public function getTemplate($templateName) {
        $findOptions = array(
			'conditions' => array(
				'LabelTemplate.template_name' => $templateName,
			),
			'fields' => array('LabelTemplate.*'),
		);

		$template = $this->find( 'first', $findOptions );
        $template = Hash::get($template, 'LabelTemplate.template');
        
        return $template;
    }

}