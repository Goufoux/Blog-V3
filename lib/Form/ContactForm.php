<?php

namespace Form;

use Core\Form;

class ContactForm extends Form
{
    public function verif(array $datas)
    {
        $name = isset($datas['contact_name']);
        $email = isset($datas['contact_email']);
        $message = isset($datas['contact_message']);

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
