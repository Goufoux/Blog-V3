<?php

namespace App\Frontend\ConnectController;

use Core\AbstractController;
use Form\ConnectForm;
use Form\ForgotPasswordForm;
use Module\Mail;
use Form\ResetPasswordForm;
use Entity\User;

class ConnectController extends AbstractController
{
    public function index()
    {
        $form = new ConnectForm;
        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();
            $form->verif($data);

            if (!$form->isValid()) {
                $this->notifications->addDanger('Formulaire invalide.');
                goto out;
            }

            $userManager = $this->manager->getManagerOf("user");
            if ($user = $userManager->connect($data['email'], $data['password'])) {
                $_SESSION['user'] = $user;
                $this->notifications->addSuccess("Bonjour " . $user->getFirstName());
                return $this->response->redirectTo('/');
            }
            $this->notifications->addDanger('Identifiant incorrect');
        }

        out:

        return $this->render([
            'form' => $form
        ]);
    }

    public function forgotPassword()
    {
        $form = new ForgotPasswordForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();
            $form->verif($data);

            if (!$form->isValid()) {
                $this->notifications->addDanger('Formulaire invalide.');
                goto out;
            }
            $user = $this->manager->findBy('user', 'email', $data['email'], true, true);

            if (empty($user) || $user == false || $user == null) {
                $this->notifications->addDanger('Aucun utilisateur avec cette email.');
                goto out;
            }

            $data = $this->generateForgotPasswordData($user);

            if (!$this->manager->update('user', $data)) {
                $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
                return $this->response->referer();
            }
            $mail = new Mail;
            $content = $mail->templateForgotPassword($user, $data['token']);
            $mail->send($user->getEmail(), 'Réinitialisation du mot de passe', $content);
            $this->notifications->addInfo('Vérifier votre boîte mail.');
    
            return $this->response->referer();
        }

        out:

        return $this->render([
            'form' => $form
        ]);
    }

    private function generateForgotPasswordData(User $user)
    {
        $today = new \DateTime();
        $today->add(New \DateInterval("P2D"));
        $data = [
            'id' => $user->getId(),
            'token_renewal' => $today->format('Y-m-d H:i:s'),
            'token' => bin2hex(random_bytes(16))
        ];

        return $data;
    }

    public function reset()
    {
        if (empty($_SESSION['resetPassword'])) {
            $this->response->redirectTo('/');
        }

        $form = new ResetPasswordForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();
            $form->verif($data);

            if (!$form->isValid()) {
                $this->notifications->addDanger('Formulaire invalide.');
                goto out;
            }

            $data = [
                'token' => NULL,
                'password' => $data['password'],
                'id' => $_SESSION['resetPassword']->getId()
            ];
            if ($this->manager->update('user', $data)) {
                $this->notifications->addSuccess('Données de connexion mis à jour');
                unset($_SESSION['resetPassword']);
                
                return $this->response->redirectTo('/');
            }
            $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
        }

        out:

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
