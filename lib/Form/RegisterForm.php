<?php

namespace Form;

use Core\Form;

class RegisterForm extends Form
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
        ],
        'password' => [
            'required' => true,
            'length' => [8, 15],
            'text' => 'password',
            'translate' => 'Mot de passe'
        ],
        'confirm_password' => [
            'required' => true,
            'equals' => 'password'
        ],
        'cgu' => [
            'required' => true
        ]
    ];

    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}
