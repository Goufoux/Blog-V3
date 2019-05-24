<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Manager\UserRoleManager;

class Authentification
{
    protected $authentificated;
    protected $role;
    protected $user;

    public function __construct()
    {
        $this->setAuthentificated();
    }

    public function isAuthentificated() :bool
    {
        if($this->authentificated === true) {
            return true;
        } else {
            return false;
        }
    }

    public function getRole()
    {
        return $this->role;
    }

    public function isAdmin()
    {
        return $this->hasRole('ROLE_SUPER_ADMIN');
    }

    public function hasRole(string $role)
    {
        if (empty($this->role[$role])) {
            return false;
        } else {
            return true;
        }
    }

    private function setRole()
    { 
        
    }

    public function getUser()
    {
        return $this->user;
    }

    private function setUser($user)
    {
        $this->user = $user;
    }

    private function setAuthentificated() :Authentification
    {
        if(isset($_SESSION['user'])) {
            $this->authentificated = true;
        } else {
            $this->authentificated = false;
        }
        return $this;
    }
}