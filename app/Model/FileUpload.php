<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model to provide validation for a file upload form/view.
	 *
	 *
	 * @package       app.Model
	 */
	class FileUpload extends AppModel 
	{
		public $useTable = false; //!< Don't use any table, this is just a dummy model.

		//! Validation rules.
		/*!
			Filename must not be blank.,
		*/
	    public $validate = array(
	        'filename' => array(
	            'rule' => 'notEmpty'
	        ),
	    );

	    //! Get the temporary name of the file.
	    /*!
	    	@param $data The data from the request.
	    	@retval mixed String containing the temp path name if data is valid, otherwise false.
	    */
	    public function getTmpName($data)
	    {
	    	if(is_array($data))
	    	{
	    		return Hash::get($data, 'FileUpload.filename.tmp_name');
	    	}
	    	return false;
	    }
	}
?>