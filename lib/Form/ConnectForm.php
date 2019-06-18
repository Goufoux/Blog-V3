<?php

namespace Form;

use Core\Form;

class ConnectForm extends Form
{
    const data = [
        'email' => [
            'required' => true,
            'email' => null
        ],
        'password' => [
            'required' => true
        ] 
    ];

    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}