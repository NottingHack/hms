<?php
	//! An exception thrown by certain Member methods if they are called on a member id who has the wrong status.
	/*
		This is it's own exception as such results are usually handled differently from the calling code, for example the
		displaying a flash message if the Member method returned true or false but redirecting if this exception is thrown.
	*/
	class InvalidStatusException extends CakeException
	{
		public function __construct($message)
		{
			parent::__construct($message);
		}
	}

?>