<?php

namespace Form;

use Core\Form;

class ContactForm extends Form
{
    public function verif(array $datas)
    {
        $name = isset($datas['name']);
        $email = isset($datas['email']);
        $message = isset($datas['message']);

        if (!$name) {
            $this->addErrors('name', 'Champs requis');
        }

        if (!$email) {
            $this->addErrors('email', 'Champs requis');
        }

        if (!$message) {
            $this->addErrors('message', 'Champs requis');
        }
    }
}
