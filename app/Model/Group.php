<?php
	class Group extends AppModel {
		
		public $useTable = "grp";

		public $primaryKey = 'grp_id';

		public $hasAndBelongsToMany = array(
	        'Permission' =>
	            array(
	                'className'              => 'Permission',
	                'joinTable'              => 'group_permissions',
	                'foreignKey'             => 'grp_id',
	                'associationForeignKey'  => 'permission_code',
	                'unique'                 => true,
	                'conditions'             => '',
	                'fields'                 => '',
	                'order'                  => '',
	                'limit'                  => '',
	                'offset'                 => '',
	                'finderQuery'            => '',
	                'deleteQuery'            => '',
	                'insertQuery'            => ''
	            ),
	        'Member' =>
	            array(
	                'className'              => 'Member',
	                'joinTable'              => 'member_group',
	                'foreignKey'             => 'grp_id',
	                'associationForeignKey'  => 'member_id',
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
	        'grp_description' => array(
	            'rule' => 'notEmpty'
	        ),
	    );
	}
?>