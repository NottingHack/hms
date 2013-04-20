<?php
	//! An exception thrown by certain Member methods if they are called on a with data that is invalid from an auth point of view.
	/*
		This is it's own exception as such results are usually handled differently from the calling code, for example the
		displaying a flash message if the Member method returned true or false but redirecting if this exception is thrown.
	*/
	class NotAuthorizedException extends CakeException
	{
		public function __construct($message)
		{
			parent::__construct($message);
		}
	}

?>