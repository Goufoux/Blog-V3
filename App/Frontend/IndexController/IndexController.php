<?php

namespace App\Frontend\IndexController;

use Core\AbstractController;
use Form\ContactForm;
use Module\Mail;

class IndexController extends AbstractController
{
    public function index()
    {
        $flags = [
            'INNER JOIN' => [
                'table' => 'user',
                'sndTable' => 'post',
                'firstTag' => 'id',
                'sndTag' => 'user'
            ]
        ];

        $posts = $this->manager->fetchAll('post', $flags);

        return $this->render([
            'posts' => $posts
        ]);
    }

    public function contact()
    {
        if ($this->request->hasPost()) {
            $form = new ContactForm();
            $datas = $this->request->getAllPost();
            $form->verif($datas);
            $_SESSION['datas'] = $datas;
            $_SESSION['form'] = $form;
            if ($form->isValid()) {
                $mailModule = new Mail();
                $content = $mailModule->templateContactForm($datas);
                $mailModule->send('quentin.roussel@genarkys.fr', 'Formulaire de contact', $content);
                $this->notifications->addInfo('Votre message a été envoyé avec succès !');
            }
        }


        return $this->response->referer('#contact');
    }

    public function deconnect()
    {
        session_destroy();
        $this->notifications->addSuccess('Vous avez était déconnecté, à bientôt !');
        return $this->response->redirectTo('/');
    }
}
