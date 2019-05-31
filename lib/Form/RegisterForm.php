<?php

namespace Form;

use Core\Form;

class RegisterForm extends Form
{
    public function verif(array $datas)
    {
        $name = isset($datas['name']) ? true : false;
        $first_name = isset($datas['first_name']) ? true : false;
        $email = isset($datas['email']) ? true : false;
        $password = isset($datas['password']) ? true : false;
        $confirm_password = isset($datas['confirm_password']) ? true : false;
        $cgu = isset($datas['cgu']) ? true : false;
        
        if (!$name) {
            $this->addErrors("name", "Champs obligatoire");
        }

        if (!$first_name) {
            $this->addErrors("first_name", "Champs obligatoire");
        }

        if (!$email) {
            $this->addErrors("email", "Champs obligatoire");
        } else {
            if (!$this->isEmail($datas['email'])) {
                $this->addErrors("email", "Cette adresse email ne semble pas valide.");
            }
        }

        if (!$password) {
            $this->addErrors("password", "Champs obligatoire");
        }

        if (!$confirm_password) {
            $this->addErrors("confirm_password", "Champs obligatoire");
        } else {
            if ($datas['confirm_password'] != $datas['password']) {
                $this->addErrors("confirm_password", "Le mot de passe ne correspond pas");
            }
        }

        if (!$cgu) {
            $this->addErrors("cgu", "Veuillez accepter nos conditions générales d'utilisation pour vous inscrire.");
        }
    }
}
