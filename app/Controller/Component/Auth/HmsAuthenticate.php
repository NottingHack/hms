<?php

App::uses('FormAuthenticate', 'Controller/Component/Auth');
App::uses('KrbComponent', 'Controller/Component');

class HmsAuthenticate extends FormAuthenticate {

    public $components = array( 'Krb' );

    public function authenticate(CakeRequest $request, CakeResponse $response) {

        # Need to use the members model
        $memberModel = ClassRegistry::init("Member");

        # Find the member
        $memberInfo = $memberModel->find('first', array( 'conditions' => array( 'Member.username' => $request->data['User']['username'] ) ) );

        if( isset($memberInfo) &&
            $memberInfo != null)
        {
            $this->Krb = new KrbComponent($this->_Collection);
            return $this->Krb->checkPassword($request->data['User']['username'], $request->data['User']['password']) ? $memberInfo : false;
        }

    	# Login failed
        return false;
    }


    public static function make_hash($salt, $pass)
    {
    	# Hash is the sha-1 of th salt + pass
    	# Pass is plaintext atm...
    	return sha1($salt . $pass);
    }

    public static function make_salt()
    {
    	# Return a random 16 char salt (adapted from nh_instrumentation code)

    	$chars = str_split('0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz');
    
		$newSalt = '';
		for ($i = 0; $i < 16; $i++) 
		{
			$newSalt .= $chars[rand(0, count($chars) - 1)];
		}

		return $newSalt;
    }

}

?>
