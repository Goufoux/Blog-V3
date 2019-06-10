<?php

namespace App\Frontend\ConnectController;

use Core\AbstractController;
use Form\ConnectForm;
use Form\ForgotPasswordForm;
use Module\Mail;

class ConnectController extends AbstractController
{
    public function index()
    {
        $form = new ConnectForm;
        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();
            $form->verif($data);
            if ($form->isValid()) {
                $userManager = $this->manager->getManagerOf("user");
                if ($user = $userManager->connect($data['email'], $data['password'])) {
                    $_SESSION['user'] = $user;
                    $this->notifications->addSuccess("Bonjour " . $user->getFirstName());
                    $this->response->redirectTo('/');
                } else {
                    $this->notifications->addDanger($userManager->getError());
                }
            }
        }

        return $this->render([
            'form' => $form
        ]);
    }

    public function forgotPassword()
    {
        $form = new ForgotPasswordForm();

        if ($this->request->hasPost()) {
            $datas = $this->request->getAllPost();
            $form->verif($datas);
            if ($form->isValid()) {
                $user = $this->manager->findBy('user', 'email', $datas['email'], true, true);
                if (!empty($user)) {
                    $mail = new Mail;
                    $content = $mail->templateForgotPassword($user);
                    $mail->send($user->getEmail(), 'Réinitialisation du mot de passe', $content);
                    var_dump($mail);
                    // return $this->response->referer();
                }
                $this->notifications->addDanger("Aucun utilisateur avec cette adresse email.");
            } else {
                $this->notifications->addDanger('Formulaire invalide.');
            }
        }

        return $this->render([
            'form' => $form
        ]);
    }
}
