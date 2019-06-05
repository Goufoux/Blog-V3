<?php

namespace App\Frontend\ConnectController;

use Core\AbstractController;
use Form\ConnectForm;

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
}
