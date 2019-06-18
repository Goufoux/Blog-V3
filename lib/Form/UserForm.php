<?php

namespace Form;

use Core\Form;
use Entity\User;

class UserForm extends Form
{
    const data = [
        'name' => [
            'required' => true,
            'length' => [3, 15],
            'text' => 'default'
        ],
        'first_name' => [
            'required' => true,
            'length' => [3, 20],
            'text' => 'default'
        ],
        'email' => [
            'required' => true,
            'email' => null
        ]
    ];

    public function verif(array $data, bool $new = false)
    {
        if ($new) {
            $array['password'] = [
                'required' => true,
                'text' => 'password',
                'length' => [8, 15]
            ];
            $this->requiredControl($array, $data);
            $this->launch($array, $data);
        }
        
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }

    public function profilVerif(array $data)
    {
        $name = isset($$data['name']);
        $first_name = isset($$data['first_name']);
        $email = isset($$data['email']);

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

    public function updatePass(array $data, User $user)
    {
        $password = isset($$data['password']);
        $new_password = isset($$data['new_password']);
        $confirm_password = isset($$data['confirm_password']);

        if (!$password) {
            $this->addErrors("password", "Champs obligatoire");
        }
        if (!password_verify($$data['password'], $user->getPassword())) {
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
            if ($$data['confirm_password'] !== $$data['new_password']) {
                $this->addErrors("confirm_password", "Le mot de passe ne correspond pas");
            }
        }
    }
}
