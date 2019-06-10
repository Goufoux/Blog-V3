<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Module;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    protected $phpMailer;
    protected $error;

    public function __construct()
    {
        $this->init();
    }

    public function send(string $destinataire = '', string $sujet = '', string $content = '')
    {
        if (empty($destinataire) || empty($content)) {
            $this->setError('Destinataire ou contenu vide');
            return false;
        }

        try {
            $this->phpMailer->setFrom('contact@genarkys.fr', 'Blog');
            $this->phpMailer->addAddress($destinataire);

            $this->phpMailer->isHTML(true);
            $this->phpMailer->CharSet = 'UTF-8';
            $this->phpMailer->Subject = $sujet;
            $this->phpMailer->Body = $content;
        
            if (!$this->phpMailer->send()) {
                throw new Exception($this->phpMailer->ErrorInfo);
            }

            return true;
        } catch (Exception $e) {
            $logger = new Logger;
            $logger->setLogs($e->getMessage());

            return false;
        }
    }

    public function init()
    {
        $this->setPhpMailer();
        try {
            $this->phpMailer->isSMTP();
            $this->phpMailer->Host = 'smtp.gmail.com';
            $this->phpMailer->SMTPAuth = true;
            $this->phpMailer->Username = "quentin.oc.mail@gmail.com";
            $this->phpMailer->Password = "Klest54$36rt";
            $this->phpMailer->SMTPSecure = 'tls';
            $this->phpMailer->Port = 587;
        } catch (\Exception $e) {
            $logger = new Logger;
            $logger->setLogs('Error at Module\\Mail : ' . $this->phpMailer->ErrorInfo);
        }
    }

    public function setPhpMailer()
    {
        $phpMailer = new PHPMailer();
        $this->phpMailer = $phpMailer;
    }

    public function template($str)
    {
        $body = "<h1>Genarkys</h1><p>%s</p>";
        $body = sprintf($body, $str);

        return $body;
    }

    public function templateForgotPassword($user)
    {
        $file = __DIR__."/../../public/template/mail/forgotpassword.html.twig";

        ob_start();
        require $file;
        $page = ob_get_contents();
        ob_end_clean();
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
