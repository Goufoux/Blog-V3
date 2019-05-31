<?php

namespace App\Frontend\RegisterController;

use Core\AbstractController;
use Form\RegisterForm;

class RegisterController extends AbstractController
{
    public function index()
    {
        $form = new RegisterForm;

        if ($this->request->hasPost()) {
            $datas = $this->request->getAllPost();
            $form->verif($datas);
            if ($form->isValid()) {
                $emailExist = $this->managers->findBy("user", "email", $datas['email'], true, true);
                if (!$emailExist) {
                    unset($datas['confirm_password'], $datas['cgu']);
                    if ($this->managers->add("user", $datas)) {
                        $userId = $this->managers->getLastInsertId();
                        $roleData = [
                            'role' => 3,
                            'user' => $userId
                        ];
                        $this->managers->add("userRole", $roleData);
                        $this->notifications->addSuccess("Votre compte a bien été créé");
                        $this->response->redirectTo("/register/welcome");
                    } else {
                        $this->notifications->default("500", $this->managers->getError(), "danger", true);
                    }
                } else {
                    $form->addErrors("email", "Cette adresse email existe déjà");
                    $this->notifications->addDanger("Formulaire invalide");
                }
            } else {
                $this->notifications->addDanger("Formulaire invalide");
            }
        }

        return $this->render([
            "form" => $form
        ]);
    }

    public function welcome()
    {
        return $this->render();
    }
}
