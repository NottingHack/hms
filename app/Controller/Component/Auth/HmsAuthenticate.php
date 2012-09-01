<?php

App::uses('FormAuthenticate', 'Controller/Component/Auth');

class HmsAuthenticate extends FormAuthenticate {

    public function authenticate(CakeRequest $request, CakeResponse $response) {

    	# Need to use the members model
    	$memberModel = ClassRegistry::init("Member");

    	# Find the member
    	$memberInfo = $memberModel->find('first', array( 'conditions' => array( 'Member.email' => $request->data['User']['username'] ) ) );

    	print_r(HmsAuthenticate::make_salt());

    	if(	isset($memberInfo) &&
    		$memberInfo != null)
    	{
    		# We have a member!
    		# Grab their salt
    		$memberSalt = $memberInfo['MemberAuth']['salt'];
    		if(	isset($memberSalt) &&
    			$memberSalt != null &&
    			strlen($memberSalt) === 16)
    		{
    			# Grab the actual hash
    			$actualHash = $memberInfo['MemberAuth']['passwd'];
    			if( isset($actualHash) &&
	    			$actualHash != null &&
	    			strlen($actualHash) === 40)
    			{
    				# Check it
    				$attemptHash = HmsAuthenticate::make_hash($memberSalt, $request->data['User']['password']);
    				if( $attemptHash === $actualHash )
    				{
    					return true;
    				}
    			}
    		}
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