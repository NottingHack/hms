<?php

class krb5_auth 
{
  public function __construct($krb_username, $keytab, $realm) 
  {
  }
  
  public function add_user($username, $password)
  {
    return true;
  }
        
  public function delete_user($username)
  {
    return true;
  }
        
  public function check_password($username, $password)
  {
    return true;
  }
  
  public function change_password($username, $newpassword)
  {
    return true;
  }

  public function user_exists($username)
  {
    return true;
  }
} 

?>