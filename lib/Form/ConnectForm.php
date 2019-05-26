<?php

namespace Form;

use Core\Form;

class ConnectForm extends Form
{
    public function verif(array $datas)
    {
        $email = (isset($datas['email'])) ? ($this->isEmail($datas['email'])) ? true : false : false;
        if (!$email) {
            $this->addErrors("email", "Email invalid");
        }

        $password = (isset($datas['password'])) ? true : false;

        if (!$password) {
            $this->addErrors("password", "Password required");
        }
    }
}