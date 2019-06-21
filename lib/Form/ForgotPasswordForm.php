<?php

namespace Form;

use Core\Form;

class ForgotPasswordForm extends Form
{
    const data = [
        'email' => [
            'required' => true,
            'email' => null
        ]
    ];

    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}