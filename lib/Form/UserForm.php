<?php

namespace Form;

use Core\Form;
use Entity\User;

class UserForm extends Form
{
    public function verif(array $datas, bool $new = false)
    {
        $name = isset($datas['name']);
        $first_name = isset($datas['first_name']);
        $email = isset($datas['email']);
        $role = false;

        if (!$name) {
            $this->addErrors("name", "Champs invalide");
        }

        if (!$first_name) {
            $this->addErrors("first_name", "Champs invalide");
        }

        if (!$email) {
            $this->addErrors("email", "Champs invalide");
        }

        foreach ($datas as $key => $val) {
            if (preg_match("#role_#", $key)) {
                $role = true;
                break;
            }
        }

        if (!$role) {
            $this->addErrors("role", "Attribuer au moins un rôle");
        }

        if ($new) {
            $password = (isset($datas['password'])) ? true : false;
            if (!$password) {
                $this->addErrors("password", "Champs invalide");
            }
        }
    }

    public function profilVerif(array $datas)
    {
        $name = isset($datas['name']);
        $first_name = isset($datas['first_name']);
        $email = isset($datas['email']);

        if (!$name) {
            $this->addErrors("name", "Champs invalide");
        }

        if (!$first_name) {
            $this->addErrors("first_name", "Champs invalide");
        }

        if (!$email) {
            $this->addErrors("email", "Champs invalide");
        }
    }

    public function updatePass(array $datas, User $user)
    {
        $password = isset($datas['password']);
        $new_password = isset($datas['new_password']);
        $confirm_password = isset($datas['confirm_password']);

        if (!$password) {
            $this->addErrors("password", "Champs obligatoire");
        }
        if (!password_verify($datas['password'], $user->getPassword())) {
            $password = false;
        }
        if (!$password) {
            $this->addErrors("password", "Mot de passe invalid");
        }

        if (!$new_password) {
            $this->addErrors("new_password", "Champs obligatoire");
        }

        if (!$confirm_password) {
            $this->addErrors("confirm_password", "Champs obligatoire");
        }

        if ($confirm_password && $new_password) {
            if ($datas['confirm_password'] !== $datas['new_password']) {
                $this->addErrors("confirm_password", "Le mot de passe ne correspond pas");
            }
        }
    }
}
