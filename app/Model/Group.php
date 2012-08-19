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
            )
    );
	}
?>