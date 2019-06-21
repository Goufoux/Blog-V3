<?php

namespace Form;

use Core\Form;

class ResetPasswordForm extends Form
{
    const data = [
        'password' => [
            'required' => true,
            'text' => 'password',
            'length' => [8, 15],
            'translate' => 'Mot de passe'
        ],
        'confirm_password' => [
            'required' => true,
            'equals' => 'password'
        ]
    ];

    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}