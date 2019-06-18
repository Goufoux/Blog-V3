<?php

namespace Form;

use Core\Form;

class CommentForm extends Form
{
    const data = [
        'content' => [
            'required' => true,
            'length' => [10, 150]
        ]
    ];

    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }    
}
