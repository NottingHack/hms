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
 * Model for working with CSV uplaod files
 */
class CsvUpload extends AppModel {
    
/**
 * Use the csvupload DB config
 * @var string
 */
    public $useDbConfig = 'csvupload';

// Not much hear as all the lifting is done by either the CSVSource or a CSVBehavior
}