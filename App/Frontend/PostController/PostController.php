<?php

namespace App\Frontend\PostController;

use Core\AbstractController;
use Form\PostForm;
use Service\FileManagement;

class PostController extends AbstractController
{
    public function new()
    {
        if (!$this->app->user()->hasRole('ROLE_USER') && !$this->app->user()->hasRole('ROLE_SUPER_ADMIN')) {
            $this->notifications->addWarning("Zone réservée.");
            $this->response->referer();
        }

        $form = new PostForm;

        if ($this->request->hasPost()) {
            $datas = $this->request->getAllPost();
            $form->verifAdd($datas);
            if ($form->isValid()) {
                if (!empty($_FILES)) {
                    $fileManagement = new FileManagement;
                    $fileName = uniqid(); 
                    if ($fileManagement->uploadFile($_FILES['image'], $fileName, "img")) {
                        $datas['image'] = $fileManagement->getFilename();
                        $datas['format'] = 1;
                    } else {
                        $form->addErrors("image", $fileManagement->getError());
                    }
                }
                $datas['user'] = $this->app->user()->getUser()->getId();
                if ($this->manager->add("post", $datas)) {
                    $this->notifications->addSuccess("Post ajouté");
                    $this->response->referer();
                } else {
                    $this->notifications->default("500", $this->manager->getError(), "danger", true);
                }
            } else {
                $this->notifications->addDanger("Formulaire invalide");
            }
        }

        return $this->render([
            'form' => $form
        ]);
    }

    public function update()
    {
        $postId = $this->request->getData('id');

        if (!$postId) {
            $this->notifications->default("500", "Identifiant non fourni");
            $this->response->referer();
        }

        $postManager = $this->manager->getManagerOf("Post");
        $post = $postManager->findById($postId);

        if (!$post) {
            $this->notifications->addWarning("Post non trouvé.");
            $this->response->referer();
        }

        return $this->render([
            'post' => $post 
        ]);
    }

    public function view()
    {
        $postId = $this->request->getData('id');

        if (!$postId) {
            $this->notifications->default("500", "Identifiant non fourni");
            $this->response->referer();
        }

        $postManager = $this->manager->getManagerOf("Post");
        $post = $postManager->findById($postId);

        if (!$post) {
            $this->notifications->addWarning("Post non trouvé.");
            $this->response->referer();
        }

        return $this->render([
            'post' => $post 
        ]);
    }

    public function delete()
    {
        $postId = $this->request->getData('id');

        if (!$postId) {
            $this->notifications->default("500", "Aucun identifiant.");
            $this->response->referer();
        }

        $post = $this->manager->findBy("post", "id", $postId, true, true);

        if (!$post) {
            $this->notifications->addWarning("Post non trouvé.");
            $this->response->referer();
        }

        if ($post->getUser() != $this->app->user()->getUser()->getId()) {
            $this->notifications->addDanger("Vous n'avez pas l'autorisation.");
            $this->response->referer();
        }

        if ($this->manager->remove("post", "id", $postId)) {
            $this->notifications->addSuccess("Post supprimé.");
        } else {
            $this->notifications->default("500", $this->manager->getError(), "danger", true);
        }
        $this->response->referer();
    }
}
