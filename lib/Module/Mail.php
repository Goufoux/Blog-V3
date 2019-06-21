<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Module;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    protected $error;

    public function send(string $destinataire = '', string $sujet = '', string $content = '')
    {
        $headers = "From: contact@blog-genarkys.fr\r\n";
        $headers .= "Reply-To: contact@blog-genarkys.fr\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        return mail($destinataire, $sujet, $content, $headers);
    }

    public function template($str)
    {
        $body = "<h1>Genarkys</h1><p>%s</p>";
        $body = sprintf($body, $str);

        return $body;
    }

    public function templateForgotPassword($user, $token)
    {
        $file = __DIR__."/../../public/template/mail/forgotPassword.php";
        ob_start();
        include_once $file;
        $page = ob_get_contents();
        ob_end_clean();
        ob_end_flush();
        return $page;
    }

    public function templateContactForm(array $datas)
    {
        $file = __DIR__."/../../public/template/mail/contact.php";
        ob_start();
        include_once $file;
        $page = ob_get_contents();
        ob_end_clean();
        ob_end_flush();
        return $page;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(string $error): Mail
    {
        $this->error = $error;

        return $this;
    }
}
