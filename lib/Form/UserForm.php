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

    public function updatePass(array $data, User $user)
    {
        $array = [
            'password' => [
                'required' => true,
            ],
            'new_password' => [
                'required' => true,
                'text' => 'password',
                'length' => [8, 15],
                'translate' => 'Nouveau mot de passe'
            ],
            'confirm_password' => [
                'required' => true,
                'equals' => 'new_password'
            ]
        ];

        $this->requiredControl($array, $data);
        $this->launch($array, $data);

        if (!password_verify($data['password'], $user->getPassword())) {
            $this->addErrors('password', 'Le mot de passe est incorrect.');
        }
        
    }
}
