<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Manager\UserRoleManager;

class Authentification
{
    protected $authentificated;
    protected $roles;
    protected $user;
    protected $manager;

    public function __construct($bdd)
    {
        $this->manager = new Managers($bdd);
        $this->setAuthentificated();
    }

    public function isAuthentificated(): bool
    {
        if ($this->authentificated === true) {
            return true;
        }    
        return false;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles()
    {
        $flags = [
            'INNER JOIN' => [
                'table' => 'role',
                'sndTable' => 'userRole',
                'firstTag' => 'id',
                'sndTag' => 'role'
            ]
        ];
        
        $roles = $this->manager->findBy('userRole', ['WHERE' => "user = {$this->user->getId()}"], $flags);

        foreach ($roles as $role) {
            $this->roles[$role->getRole()->getName()] = [
                'create' => $role->getRole()->getCreate(),
                'update' => $role->getRole()->getUpdate(),
                'delete' => $role->getRole()->getDelete()
            ];
        }
        
        return $this;
    }

    public function isAdmin()
    {
        return $this->hasRole('ROLE_SUPER_ADMIN');
    }

    public function hasRole(string $role)
    {
        if (empty($this->roles[$role])) {
            return false;
        } else {
            return true;
        }
    }

    public function getUser()
    {
        return $this->user;
    }

    private function setUser($user)
    {
        $this->user = $user;
    }

    private function setAuthentificated()
    {
        // var_dump($_SESSION['user']);
        if (isset($_SESSION['user'])) {
            $this->authentificated = true;
            $this->setUser($_SESSION['user']);
            $this->setRoles();
            // return;
        }
        // $this->authentificated = false;
    }
}
