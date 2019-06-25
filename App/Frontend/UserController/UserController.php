<?php

namespace App\Frontend\UserController;

use Core\AbstractController;
use Form\UserForm;
use Entity\User;

class UserController extends AbstractController
{
    public function profil()
    {
        $userId = $this->get('id');

        $userManager = $this->manager->getManagerOf('User');
        $user = $userManager->findById($userId);

        if (!$user) {
            $this->notifications->addWarning('L\'utilisateur n\'a pas été trouvé.');
            $this->response->referer();
        }

        $posts = $this->manager->findBy('post', ['WHERE' => "user = $userId"]);
    
        return $this->render([
            'user' => $user,
            'current' => 'profil',
            'posts' => $posts,
            'nb_posts' => count($posts)
        ]);
    }

    public function edit()
    {
        if (!$this->app->authentification()->isAuthentificated()) {
            $this->response->redirectTo('/');
        }

        $userId = $this->get('id');

        $userManager = $this->manager->getManagerOf('User');
        $user = $userManager->findById($userId);

        if (!$user) {
            $this->notifications->addWarning('Utilisateur non trouvé');
            $this->response->referer();
        }

        $form = new UserForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();
            $type = $this->get('type');

            if ($this->checkForm($type, $data, $user, $form)) {
                $this->notifications->addSuccess('Données mis à jour.');
                $this->response->referer();
            }
        }

        return $this->render([
            'user' => $user,
            'form' => $form
        ]);
    }

    private function checkForm(string $type, $data, User $user, UserForm $userForm)
    {
        $check = ($type == 'user_data') ? $userForm->verif($data) : $userForm->updatePass($data, $user);
        
        if (!$userForm->isValid()) {
            $this->notifications->addDanger('Formulaire invalide.');
            
            return false;
        }

        $data['id'] = $user->getId();
        if ($type == 'user_pass') {
            $data['password'] = $data['new_password'];
        }

        if (!$this->manager->update('user', $data)) {
            $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
            
            return false;
        }

        return true;
    }
}
