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

	    public $validate = array(
	        'grp_description' => array(
	            'rule' => 'notEmpty'
	        ),
	    );

	    #public function beforeSave($options = array()) {
	    #	# Need to update the permissions table
#
#	    	$newPermissions = array();
#
#	    	#print_r($this->data['Group']);
#
#	    	foreach ($this->data['Group'] as $possiblePermission => $active) {
#	    		# Check the permission exists
#	    		if($active)
#	    		{
#	    			#echo $possiblePermission;
#
#		    		$permissionExists = false;
#
#		    		foreach ($this->Permission->find('all') as $permission) {
#		    			
#		    			if($permission['Permission']['permission_code'] === $possiblePermission)
#		    			{
#		    				$permissionExists = true;
#		    			}
#		    		}
#
#		    		if($permissionExists === true)
#		    		{
#		    			array_push($newPermissions, $possiblePermission);
#		    		}
#	    		}
#	    	}
#
#	    	print_r($newPermissions);
#	    	$this->data['Group']['Permission'] = $newPermissions;
#
#	    	return true;
#		}
	}
?>