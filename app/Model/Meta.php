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
 * Validation rules.
 * @var array
 */
	public $validate = array(
		'name' => array(
			'length' => array(
				'rule' => array('between', 1, 200),
				'message' => 'Meta name must be between 1 and 200 characters long; can not be empty',
				'allowEmpty' => false,
			),
		),
		'value' => array(
			'length' => array(
				'rule' => array('between', 1, 200),
				'message' => 'Meta value must be between 1 and 200 characters long; can not be empty',
				'allowEmpty' => false,
			),
		),
	);
/**
 * Get a single Meta and return formated or not
 *
 * @param string $name
 * @param bool $format 
 * @return array
 */
    public function getMeta($name, $format = true) {
        $meta = $this->findByName($name);

		if ($format) {
			return $this->formatMeta($meta, false);
		}

		return $meta;
    }
/**
 * Get a list of Meta records
 * 
 * @param bool $paginate If true, return a query to retrieve a page of the data, otherwise return the data.
 * @param array $conditions An array of conditions to decide which records to access.
 * @return array A list of Metas or query to report a list of Metas
 */
	public function getMetasList($paginate, $conditions = array()) {
		$findOptions = array(
			'conditions' => $conditions,
			'fields' => array('Meta.*'),
		);

		if ($paginate) {
			return $findOptions;
		}

		$meta = $this->find( 'all', $findOptions );

		return $meta;
	}
    
/**
 * Format an array of K/Vs
 * 
 * @param array $metaList The array of K/V.
 * @param bool $removeNullEntries If true then entries that have a value of null, false or an empty array won't exist in the final array.
 * @return array A list of formated Metas
 */
    public function formatMetasList($metaList, $removeNullEntries) {
        $formatted = array();
        foreach($metaList as $meta) {
            array_push($formatted, $this->formatMeta($meta, $removeNullEntries));
        }
        return $formatted;
    }

/**
 * Flatten meta details array
 * 
 * @param array $meta raw Meta record from the model
 * @param bool $removeNullEntries strips out null fields from the result
 * @return array Details for the Meta record
 */
  public function formatMeta($meta, $removeNullEntries = true) {
  	/*
  		Data should be presented to the view in an array like so:
        [name] => name
  		[value] => value
  	*/
  		$formatted = array(
            'name' => Hash::get($meta, 'Meta.name'),
	  		'value' => Hash::get($meta, 'Meta.value'),
  		);

  		if (!$removeNullEntries) {
  			return $formatted;
  		}

  		$validValues = array();
  		foreach($formatted as $key => $value) {
  			if (isset($value) != false) {
  				$validValues[$key] = $value;
  			}
  		}

  		return $validValues;
  }
    
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
    
/** 
 * update value for name
 *
 * @param string $name
 * @param string $value
 * @return bool
 */
    public function updateValueFor($name, $value = null) {
        if ($value == null) {
            return false;
        }
        $this->id = $name;
        return $this->saveField('value', $value);
    }
}