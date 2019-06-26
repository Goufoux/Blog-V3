<?php

namespace App\Backend\UserController;

use Core\AbstractController;
use Form\UserForm;
use Entity\User;

class UserController extends AbstractController
{
    public function index()
    {
        if (!$this->app->authentification()->hasRole('ROLE_SUPER_ADMIN') && !$this->app->authentification()->hasRole('ROLE_ADMIN')) {
            $this->notifications->addWarning('Zone réservée.');
            $this->response->referer();
        }

        $users = $this->manager->fetchAll('user');
        
        return $this->render([
            'users' => $users
        ]);
    }

    public function add()
    {
        if (!$this->app->authentification()->hasRole('ROLE_SUPER_ADMIN') && !$this->app->authentification()->hasRole('ROLE_ADMIN')) {
            $this->notifications->addWarning('Zone réservée.');
            $this->response->referer();
        }

        $roles = $this->manager->fetchAll('role');

        $form = new UserForm;

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();

            if (!$this->checkForm($data, $form, 'add')) {
                goto out;
            }

            $tempData = $data;
            $data = $this->clearDataOfRole($data);
            if ($this->manager->add('user', $data)) {
                $this->notifications->addSuccess('Utilisateur créé');
                $userId = $this->manager->getLastInsertId();
                $this->addRoles($tempData, $userId, true);
                return $this->response->referer();
            }

            $this->notifications->default('500', $this->manager->getError(), 'danger', true);
        }

        out:

        return $this->render([
            'roles' => $roles,
            'form' => $form
        ]);
    }

    public function update()
    {
        if (!$this->app->authentification()->hasRole('ROLE_SUPER_ADMIN') && !$this->app->authentification()->hasRole('ROLE_ADMIN')) {
            $this->notifications->addWarning('Zone réservée.');
            $this->response->referer();
        }

        $userId = $this->get('id');
        $user = $this->manager->findOneBy('user', ['WHERE' => "id = $userId"]);

        if (!$user) {
            $this->notifications->addWarning('Utilisateur non trouvé');
            $this->response->referer();
        }

        $roles = $this->manager->fetchAll('role');

        $userRoles = $this->manager->findBy('userRole', ['WHERE' => "user = $userId"]);
        
        $form = new UserForm;

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();
            
            if (!$this->checkForm($data, $form, 'update')) {
                goto out;
            }
            $this->addRoles($data, $user->getId());
            $data = $this->clearDataOfRole($data);

            $data['id'] = $userId;

            if ($this->manager->update('user', $data)) {
                $this->notifications->addSuccess('Utilisateur mis à jour');
                return $this->response->referer();
            }

            $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
        }

        out:

        return $this->render([
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles,
            'current' => 'update',
            'form' => $form
        ]);
    }

    public function clearDataOfRole(array $data)
    {
        foreach ($data as $key => $value) {
            if (preg_match('#role#', $key)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    public function addRoles(array $data, int $userId, bool $isNew = false)
    {
        $roles = $this->returnRole($data);

        if (empty($roles)) {
            $userRoles = $this->getUserRoles($userId);
            foreach ($userRoles as $key => $role) {
                $this->removeRole($role->getRole(), $userId);
            }
        }

        foreach ($roles as $key => $roleId) {
            $role = $this->manager->findOneBy('role', ['WHERE' => "id = $roleId"]);
            if (!$role) {
                $this->notifications->addWarning('Rôle non trouvé.');
                continue;
            }
            
            if (!$isNew && $this->checkForRolesUpdate($userId, $roleId, $roles)) {
                continue;
            }

            $roleData = [
                'role' => $role->getId(),
                'user' => $userId
            ];

            if (!$this->manager->add('userRole', $roleData)) {
                $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
            }
            $this->notifications->addSuccess('Rôle ajouté');
        }
    }

    public function checkForRolesUpdate(int $userId, int $roleId, array $roles)
    {
        if ($userRoles = $this->manager->findBy('userRole', ['WHERE' => "user = $userId"])) {
            foreach ($userRoles as $key => $userRole) {
                /* exist role */
                if ($userRole->getRole() == $roleId) {
                    return true;
                }
                
                /* role to remove */
                if (!in_array($userRole->getRole(), $roles)) {
                    $this->removeRole($userRole->getRole(), $userId);
                    return true;
                }
            }
        }
        return false;
    }

    private function getUserRoles(int $userId)
    {
        return $this->manager->findBy('userRole', ['WHERE' => "user = $userId"]);
    }

    public function removeRole(int $roleId, int $userId)
    {
        $userRoleManager = $this->manager->getManagerOf('userRole');

        if ($userRoleManager == null) {
            $this->notifications->addWarning('Impossible d\'instancier');
            return false;
        }

        if ($userRoleManager->removeRoleOfUser($roleId, $userId)) {
            $this->notifications->addSuccess('Rôle supprimé');
            return true;
        }

        $this->notifications->default('500', $userRoleManager->getError(), 'danger', $this->isDev());

        return false;
    }

    public function returnRole(array $data)
    {
        $roles = [];
        foreach ($data as $key => $val) {
            if (preg_match('#role_#', $key)) {
                $roles[] = $val;
            }
        }

        return $roles;
    }

    public function checkForm(array $data, UserForm $userForm, string $type)
    {
        $check = ($type == 'add') ? $userForm->verif($data, true) : $userForm->verif($data);

        if (!$userForm->isValid()) {
            return false;
        }

        return true;
    }

    public function view()
    {
        if (!$this->app->authentification()->hasRole('ROLE_SUPER_ADMIN') && !$this->app->authentification()->hasRole('ROLE_ADMIN')) {
            $this->notifications->addWarning('Zone réservée.');
            $this->response->referer();
        }
        
        $userId = $this->get('id');

        $user = $this->manager->findOneBy('user', ['WHERE' => "id = $userId"]);

        if (!$user) {
            $this->notifications->addWarning('Utilisateur non trouvé');
            $this->response->referer();
        }

        $userRolesFlags = [
            'INNER JOIN' => [
                'table' => 'role',
                'sndTable' => 'userRole',
                'firstTag' => 'id',
                'sndTag' => 'role'
            ]
        ];

        $userRoles = $this->manager->findBy('userRole', ['WHERE' => "user = $userId"], $userRolesFlags);

        $posts = $this->manager->findBy('post', ['WHERE' => "user = $userId"]);

        return $this->render([
            'user' => $user,
            'userRoles' => $userRoles,
            'current' => 'view',
            'posts' => $posts,
            'nb_posts' => count($posts)
        ]);
    }

    public function delete()
    {
        if (!$this->app->authentification()->hasRole('ROLE_SUPER_ADMIN') && !$this->app->authentification()->hasRole('ROLE_ADMIN')) {
            $this->notifications->addWarning('Zone réservée.');
            $this->response->referer();
        }
        
        $userId = $this->get('id');

        if (!$this->manager->remove('user', 'id', $userId)) {
            $this->notifications->default('500', $this->manager->getError(), 'danger', true);
        }
        
        if (!$this->manager->remove('userRole', 'user', $userId)) {
            $this->notifications->default('500', $this->manager->getError(), 'danger', true);
        }

        $this->notifications->addSuccess('L\'utilisateur et ses rôles ont étaient supprimés.');

        $this->response->referer();
    }
}
