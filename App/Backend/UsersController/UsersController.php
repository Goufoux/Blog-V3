<?php

namespace App\Backend\UsersController;

use Core\AbstractController;
use Form\UserForm;

class UsersController extends AbstractController
{
    public function index()
    {
        $users = $this->manager->fetchAll("user");
        
        return $this->render([
            'users' => $users
        ]);
    }

    public function add()
    {
        $roles = $this->manager->fetchAll("role");

        $form = new UserForm;

        if ($this->request->hasPost()) {
            $datas = $this->request->getAllPost();
            $form->verif($datas, true);
            if ($form->isValid()) {
                $roles = [];
                foreach ($datas as $key => $val) {
                    if (preg_match("#role_#", $key)) {
                        $roles[] = $val;
                        unset($datas[$key]);
                    }
                }

                if ($this->manager->add("user", $datas)) {
                    $this->notifications->addSuccess("Utilisateur créé");
                    $userId = $this->manager->getLastInsertId();
                    foreach ($roles as $key => $roleId) {
                        $role = $this->manager->findBy("role", "id", $roleId, true, true);
                        if (!$role) {
                            $this->notifications->addWarning("Rôle non trouvé.");
                            continue;
                        }
                        $roleData = [
                            'role' => $role->getId(),
                            'user' => $userId
                        ];
                        if ($this->manager->add("userRole", $roleData)) {
                            $this->notifications->addSuccess("Rôle ajouté");
                        } else {
                            $this->notifications->default("500", $this->manager->getError(), "danger", true);
                        }
                    }
                    $this->response->referer();
                }

                $this->notifications->default("500", $this->manager->getError(), "danger", true);
            } else {
                $this->notifications->addDanger("Le formulaire n'est pas valide.");
            }
        }

        return $this->render([
            'roles' => $roles,
            'form' => $form
        ]);
    }

    public function update()
    {
        $userId = $this->request->getData('id');

        if (!$userId) {
            $this->notifications->default("500", "Aucun identifiant fourni", "danger", true);
            $this->response->referer();
        }

        $userManager = $this->manager->getManagerOf("User");
        $user = $userManager->findById($userId);

        if (!$user) {
            $this->notifications->addWarning("Utilisateur non trouvé");
            $this->response->referer();
        }

        $roles = $this->manager->fetchAll("role");

        $form = new UserForm;

        if ($this->request->hasPost()) {
            $datas = $this->request->getAllPost();
            $form->verif($datas);
            if ($form->isValid()) {
                $roles = [];
                foreach ($datas as $key => $val) {
                    if (preg_match("#role_#", $key)) {
                        $roles[] = $val;
                        unset($datas[$key]);
                    }
                }
                $datas['id'] = $userId;

                foreach ($roles as $key => $roleId) {
                    $role = $this->manager->findBy("role", "id", $roleId, true, true);
                    if (!$role) {
                        $this->notifications->addWarning("Rôle non trouvé.");
                        continue;
                    }
                    if ($this->app->user()->hasRole($role->getName())) {
                        continue;
                    }
                    $roleData = [
                        'role' => $role->getId(),
                        'user' => $userId
                    ];
                    if ($this->manager->add("userRole", $roleData)) {
                        $this->notifications->addSuccess("Rôle ajouté");
                    } else {
                        $this->notifications->default("500", $this->manager->getError(), "danger", true);
                    }
                }

                if ($this->manager->update("user", $datas)) {
                    $this->notifications->addSuccess("Utilisateur mis à jour");
                    $this->response->referer();
                }

                $this->notifications->default("500", $this->manager->getError(), "danger", true);
            } else {
                $this->notifications->addDanger("Le formulaire n'est pas valide.");
            }
        }

        return $this->render([
            'user' => $user,
            'roles' => $roles,
            'current' => 'update',
            'form' => $form
        ]);
    }

    public function view()
    {
        $userId = $this->request->getData('id');

        if (!$userId) {
            $this->notifications->default("500", "Aucun identifiant fourni", "danger", true);
            $this->response->referer();
        }

        $userManager = $this->manager->getManagerOf("User");
        $user = $userManager->findById($userId);

        $userRoleManager = $this->manager->getManagerOf("userRole");
        $userRoles = $userRoleManager->findByUser($userId);

        if (!$user) {
            $this->notifications->addWarning("Utilisateur non trouvé");
            $this->response->referer();
        }

        return $this->render([
            'user' => $user,
            'userRoles' => $userRoles,
            'current' => 'view'
        ]);
    }

    public function delete()
    {
        $userId = $this->request->getData('id');

        if (!$userId) {
            $this->notifications->default("500", "Aucun identifiant fourni", "danger", true);
            $this->response->referer();
        }

        if ($this->manager->remove("user", "id", $userId)) {
            $this->notifications->addSuccess("Utilisateur supprimé");
            if ($this->manager->remove("userRole", "user", $userId)) {
                $this->notifications->addSuccess("Rôle(s) supprimé(s)");
            } else {
                $this->notifications->default("500", $this->manager->getError(), "danger", true);
            }
        } else {
            $this->notifications->default("500", $this->manager->getError(), "danger", true);
        }

        $this->response->referer();
    }
}
