<?php
	class Member extends AppModel {

		public $primaryKey = 'member_id';

		public $belongsTo =  array(
				"Status" => array(
						"className" => "Status",
						"foreignKey" => "member_status",
						"type" => "inner"
				),
		);

		public $hasOne = array(
	        'Pin' => array(
	            'className'    => 'Pin',
	            'dependent'    => true
	        )
	    );

		public $hasAndBelongsToMany = array(
	        'Group' =>
	            array(
	                'className'              => 'Group',
	                'joinTable'              => 'member_group',
	                'foreignKey'             => 'member_id',
	                'associationForeignKey'  => 'grp_id',
	                'unique'                 => true,
	                'conditions'             => '',
	                'fields'                 => '',
	                'order'                  => '',
	                'limit'                  => '',
	                'offset'                 => '',
	                'finderQuery'            => '',
	                'deleteQuery'            => '',
	                'insertQuery'            => ''
	            )
	    );

		public $validate = array(
	        'name' => array(
	            'rule' => 'notEmpty'
	        ),
	        'email' => array(
	        	'email'
	        ),
	        'handle' => array(
	            'rule' => 'notEmpty'
	        )
	    );
	}
?>