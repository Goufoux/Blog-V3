<?php

namespace App\Frontend\ConnectController;

use Core\AbstractController;
use Form\ConnectForm;
use Form\ForgotPasswordForm;
use Module\Mail;
use Form\ResetPasswordForm;

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
                    /* Prepare datas */
                    $today = new \DateTime();
                    $today->add(New \DateInterval("P2D"));
                    $datas = [
                        'id' => $user->getId(),
                        'token_renewal' => $today->format('Y-m-d H:i:s'),
                        'token' => bin2hex(random_bytes(16))
                    ];
                    if (!$this->manager->update('user', $datas)) {
                        $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
         
                        return $this->response->referer();
                    }
                    $mail = new Mail;
                    $content = $mail->templateForgotPassword($user, $datas['token']);
                    $mail->send($user->getEmail(), 'Réinitialisation du mot de passe', $content);
                    $this->notifications->addInfo('Vérifier votre boîte mail.');
         
                    return $this->response->referer();
                }
                $this->notifications->addDanger('Aucun utilisateur avec cette adresse email.');
            } else {
                $this->notifications->addDanger('Formulaire invalide.');
            }
        }

        return $this->render([
            'form' => $form
        ]);
    }

    public function reset()
    {
        if (empty($_SESSION['resetPassword'])) {
            $this->response->redirectTo('/');
        }

        $form = new ResetPasswordForm();

        if ($this->request->hasPost()) {
            $datas = $this->request->getAllPost();
            $form->verif($datas);

            if ($form->isValid()) {
                $datas = [
                    'token' => NULL,
                    'password' => $datas['password'],
                    'id' => $_SESSION['resetPassword']->getId()
                ];
                if ($this->manager->update('user', $datas)) {
                    $this->notifications->addSuccess('Données de connexion mis à jour');
                    unset($_SESSION['resetPassword']);
                    $this->response->redirectTo('/');
                } else {
                    $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
                }
            } else {
                $this->notifications->addDanger('Formulaire invalide.');
            }
        }

        return $this->render([
            'form' => $form
        ]);
    }

    public function verifToken()
    {
        $token = $this->request->getData('token');

        if (!$token) {
            $this->notifications->addDanger('Le lien est incorrect.');
            $this->response->redirectTo('/');
        }

        $user = $this->manager->findBy('user', 'token', $token, true, true);

        if (!$user) {
            $this->response->redirectTo('/');
        }

        $today = new \DateTime();

        $diff = $today->diff(new \DateTime($user->getTokenRenewal()));

        if ($diff->invert == 1) {
            $this->notifications->addInfo('Le lien a expiré.');
            $this->response->redirectTo('/');
        }
        $_SESSION['resetPassword'] = $user;
        $this->response->redirectTo('/connect/reset');
    }
}
