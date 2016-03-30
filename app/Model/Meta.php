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
 * Model for meta data (key/valeue store)
 */
class Meta extends AppModel {
/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'hms_meta';

/**
 * Specify the primary key.
 * @var string
 */
	public $primaryKey = 'name';

/**
 * get value for name
 * 
 * @param string $name
 * @return mixed
 */
    public function getValueFor($name = null) {
        if ($name == null) {
            return false;
        }
        $meta = $this->findByName($name);
        
        return Hash::get($meta, 'Meta.value');
    }
    
}