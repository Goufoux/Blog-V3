<?php

namespace Form;

use Core\Form;

class ForgotPasswordForm extends Form
{
    public function verif(array $datas)
    {
        $email = isset($datas['email']) ? ($this->isEmail($datas['email'])) : false;

        if (!$email) {
            $this->addErrors('email', 'Adresse email invalide.');
        }
    }
}