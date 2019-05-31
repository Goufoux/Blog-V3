<?php

namespace App\Frontend\UserController;

use Core\AbstractController;
use Form\UserForm;

class UserController extends AbstractController
{
    public function profil()
    {
        $userId = $this->request->getData('id');

        if (!$userId) {
            $this->notifications->default("500", "Aucun identifiant fourni", "danger", true);
            $this->response->referer();
        }

        $userManager = $this->managers->getManagerOf("User");
        $user = $userManager->findById($userId);

        if (!$user) {
            $this->notifications->addWarning("Utilisateur non trouvé");
            $this->response->referer();
        }

        return $this->render([
            'user' => $user,
            'current' => 'profil'
        ]);
    }

    public function edit()
    {
        $userId = $this->request->getData('id');

        if (!$userId) {
            $this->notifications->default("500", "Aucun identifiant fourni", "danger", true);
            $this->response->referer();
        }

        $userManager = $this->managers->getManagerOf("User");
        $user = $userManager->findById($userId);

        if (!$user) {
            $this->notifications->addWarning("Utilisateur non trouvé");
            $this->response->referer();
        }

        $form = new UserForm;

        if ($this->request->hasPost()) {
            $datas = $this->request->getAllPost();
            $type = $this->request->getData('type');

            if ($type == "user_data") {
                $form->profilVerif($datas);
                if ($form->isValid()) {
                    $datas['id'] = $userId;
                    if ($this->managers->update("user", $datas)) {
                        $this->notifications->addSuccess("Données mise à jour");
                        $this->response->redirectTo("/user/profil?id=".$userId);
                    } else {
                        $this->notifications->default("500", $this->managers->getError(), "danger", false);
                    }
                } else {
                    $this->notifications->addDanger("Formulaire invalid");
                }
            } else if ($type == "user_pass") {
                $datas = $this->request->getAllPost();
                $form->updatePass($datas, $user);
                if ($form->isValid()) {
                    $datas = [
                        'id' => $userId,
                        'password' => $datas['new_password']
                    ];
                    if ($this->managers->update("user", $datas)) {
                        $this->notifications->addSuccess("Données de connexion mise à jour");
                        $this->response->redirectTo("/user/profil?id=".$userId);
                    } else {
                        $this->notifications->default("500", $this->managers->getError(), "danger", true);
                    }
                } else {
                    $this->notifications->addDanger("Formulaire invalid");
                }
            } else {
                $this->notifications->addDanger("Une erreur est survenue.");
                $this->response->referer();
            }
        }

        return $this->render([
            'user' => $user,
            'form' => $form
        ]);
    }
}