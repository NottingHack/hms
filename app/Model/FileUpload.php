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
 * Model to provide validation for a file upload form/view.
 */
class FileUpload extends AppModel {

/**
 * Don't use a table, this model is just for validation.
 * @var boolean
 */
	public $useTable = false;

/**
 * [$validate description]
 * @var array
 */
	public $validate = array(
		'filename' => array(
			'rule' => 'notEmpty'
		),
	);

/**
 * Get the temporary name of the file.
 * 
 * @param $data The data from the request.
 * @return mixed String containing the temp path name if data is valid, otherwise false.
 */
	public function getTmpName($data) {
		if (is_array($data)) {
			return Hash::get($data, 'FileUpload.filename.tmp_name');
		}
		return false;
	}
}