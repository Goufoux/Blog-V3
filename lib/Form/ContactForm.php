<?php

namespace Form;

use Core\Form;

class ContactForm extends Form
{
    const data = [
        'contact_name' => [
            'required' => true,
            'text' => 'default',
            'length' => [3, 15]
        ],
        'contact_email' => [
            'required' => true,
            'email' => null
        ],
        'contact_message' => [
            'required' => true,
            'length' => [15, 150] 
        ]
    ];
    
    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}
