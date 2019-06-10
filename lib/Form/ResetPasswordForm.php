<?php

namespace Form;

use Core\Form;

class ResetPasswordForm extends Form
{
    public function verif(array $datas)
    {
        $password = isset($datas['password']);
        $confirm_password = isset($datas['confirm_password']);

        if (!$password) {
            $this->addErrors("password", "Champs obligatoire");
        }

        if (!$confirm_password) {
            $this->addErrors("confirm_password", "Champs obligatoire");
        }

        if ($confirm_password && $password) {
            if ($datas['confirm_password'] !== $datas['password']) {
                $this->addErrors("confirm_password", "Le mot de passe ne correspond pas");
            }
        }
    }
}