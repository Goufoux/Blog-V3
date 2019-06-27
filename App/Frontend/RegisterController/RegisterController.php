<?php

namespace App\Frontend\RegisterController;

use Core\AbstractController;
use Form\RegisterForm;

class RegisterController extends AbstractController
{
    public function index()
    {
        $form = new RegisterForm;

        if (!$this->request->hasPost()) {
            goto out;
        }
        $datas = $this->request->getAllPost();
        $form->verif($datas);

        if (!$form->isValid()) {
            $this->notifications->addDanger("Formulaire invalide");
            goto out;
        }

        $emailExist = $this->manager->findOneBy('user', ['WHERE' => "email = {$datas['email']}"]);

        if ($emailExist) {
            $form->addErrors('email', 'Cette adresse email existe déjà.');
            goto out;
        }

        unset($datas['confirm_password'], $datas['cgu']);


        if ($this->manager->add("user", $datas)) {
            $this->addRoles(3, $this->manager->getLastInsertId());
            $this->notifications->addSuccess("Votre compte a bien été créé");
            $this->response->redirectTo("/register/welcome");
        }
        $this->notifications->default("500", $this->manager->getError(), "danger", true);

        out:

        return $this->render([
            "form" => $form
        ]);
    }

    private function addRoles(int $roles, int $userId)
    {
        $this->manager->add('userRole', ['role' => 3, 'user' => $userId]);
    }

    public function welcome()
    {
        return $this->render();
    }
}
