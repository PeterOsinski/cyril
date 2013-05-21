<?php

namespace Core\Session;

class User {
    
    protected $user;
    protected $isAuthenticated = false;
    protected $session;
    
    public function __construct(\Symfony\Component\HttpFoundation\Request $request) {
//            $this->user = $request->getSession()->has('user') ? $userObj;
    }
    
    public function __sleep() {
        serialize($this->user);
    }
    
    public function __wakeup() {
        unserialize($this);
    }
    
    public function setUser($userObject){
        if($this->session)
            $this->sesion->set('user', serialize($userObject));
        
        return $this;
    }
    
    public function getUser(){
        if($this->session && $this->session->has('user'))
            return unserialize($this->user);
        else return false;
    }
    
    public function isAuthenticated(){
        return $this->isAuthenticated;
    }
    
    public function setAuthenticated($bool){
        if(is_bool($bool)) $this->isAuthenticated = $bool;
        
        return $this;
    }
    
    
}

?>
