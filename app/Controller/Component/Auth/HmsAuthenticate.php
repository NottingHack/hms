<?php

    App::uses('FormAuthenticate', 'Controller/Component/Auth');

    class HmsAuthenticate extends FormAuthenticate 
    {

        public function authenticate(CakeRequest $request, CakeResponse $response) {

            # Need to use the members model
            $memberModel = ClassRegistry::init("Member");

            # Find the member
            # Try username first
            $memberInfo = $memberModel->find('first', array( 'conditions' => array( 'Member.username' => $request->data['User']['username'] ) ) );

            if( isset($memberInfo) &&
                $memberInfo != null)
            {
                return $memberModel->krbCheckPassword($request->data['User']['username'], $request->data['User']['password']) ? $memberInfo : false;
            }

            # See if they used their email address instead
            $memberInfo = $memberModel->find('first', array( 'conditions' => array( 'Member.email' => $request->data['User']['username'] ) ) );
            if( isset($memberInfo) &&
                $memberInfo != null)
            {
                return $memberModel->krbCheckPassword($memberInfo['Member']['username'], $request->data['User']['password']) ? $memberInfo : false;
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
