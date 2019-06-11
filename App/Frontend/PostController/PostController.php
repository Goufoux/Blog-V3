<?php

namespace App\Frontend\PostController;

use Core\AbstractController;
use Form\PostForm;
use Service\FileManagement;
use Form\CommentForm;

class PostController extends AbstractController
{
    public function index()
    {
        $postManager = $this->manager->getManagerOf('Post');
        $posts = $postManager->fetchAll();

        return $this->render([
            'title' => 'Liste des posts',
            'posts' => $posts
        ]);
    }

    public function new()
    {
        if (!$this->app->authentification()->hasRole('ROLE_USER') && !$this->app->authentification()->hasRole('ROLE_SUPER_ADMIN')) {
            $this->notifications->addWarning('Zone réservée.');
            $this->response->referer();
        }

        $form = new PostForm();

        if ($this->request->hasPost()) {
            $datas = $this->request->getAllPost();
            $form->verifAdd($datas);
            if ($form->isValid()) {
                if (!empty($_FILES)) {
                    $fileManagement = new FileManagement;
                    $fileName = uniqid(); 
                    if ($fileManagement->uploadFile($_FILES['image'], $fileName, 'img')) {
                        $datas['image'] = $fileManagement->getFilename();
                    } else {
                        $form->addErrors('image', $fileManagement->getError());
                    }
                }
                $datas['user'] = $this->app->user()->getId();
                if ($this->manager->add('post', $datas)) {
                    $this->notifications->addSuccess('Post ajouté');
                    $this->response->referer();
                } else {
                    $this->notifications->default('500', $this->manager->getError(), 'danger', true);
                }
            } else {
                $this->notifications->addDanger('Formulaire invalide');
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
            $this->notifications->default('500', 'Identifiant non fourni');
            $this->response->referer();
        }

        $postManager = $this->manager->getManagerOf('Post');
        $post = $postManager->findById($postId);

        if (!$post) {
            $this->notifications->addWarning('Post non trouvé.');
            $this->response->referer();
        }

        $form = new PostForm();

        if ($this->request->hasPost()) {
            $datas = $this->request->getAllPost();
            $form->verifUpdate($datas);
            if ($form->isValid()) {
                if (!empty($_FILES)) {
                    $fileManagement = new FileManagement;
                    $fileName = uniqid(); 
                    if ($fileManagement->uploadFile($_FILES['image'], $fileName, 'img')) {
                        $datas['image'] = $fileManagement->getFilename();
                    } else {
                        $form->addErrors('image', $fileManagement->getError());
                    }
                }
                $datas['id'] = $postId;
                if ($this->manager->update('post', $datas)) {
                    $this->notifications->addSuccess('Post mis à jour');
                    $this->response->referer();
                } else {
                    $this->notifications->default('500', $this->manager->getError(), 'danger', true);
                }
            }
        }

        return $this->render([
            'post' => $post,
            'form' => $form
        ]);
    }

    public function list()
    {
        $postManager = $this->manager->getManagerOf('post');
        $posts = $postManager->findByUser($this->app->user()->getId());

        return $this->render([
            'title' => 'Mes posts',
            'posts' => $posts
        ]);
    }

    public function view()
    {
        $postId = $this->request->getData('id');

        if (!$postId) {
            $this->notifications->default('500', 'Identifiant non fourni');
            $this->response->referer();
        }

        $postManager = $this->manager->getManagerOf('Post');
        $post = $postManager->findById($postId);

        if (!$post) {
            $this->notifications->addWarning('Post non trouvé.');
            $this->response->referer();
        }

        $commentManager = $this->manager->getManagerOf('comment');
        $comments = $commentManager->findByPost($postId);

        $form = new CommentForm();

        if ($this->request->hasPost() && $this->app->authentification()->isAuthentificated()) {
            $datas = $this->request->getAllPost();
            $form->verif($datas);
            if ($form->isValid()) {
                $datas['user'] = $this->app->user()->getId();
                $datas['post'] = $postId;
                if ($this->manager->add('comment', $datas)) {
                    $this->notifications->addSuccess('Votre commentaire a été envoyé et est en attente de validation.');
                    $this->response->referer();
                }
                $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
            }
            $this->notifications->addDanger('Formulaire invalide');

        }

        return $this->render([
            'post' => $post,
            'form' => $form,
            'comments' => $comments
        ]);
    }

    public function delete()
    {
        $postId = $this->request->getData('id');

        if (!$postId) {
            $this->notifications->default('500', 'Aucun identifiant.');
            $this->response->referer();
        }

        $post = $this->manager->findBy('post', 'id', $postId, true, true);

        if (!$post) {
            $this->notifications->addWarning('Post non trouvé.');
            $this->response->referer();
        }

        if ($post->getUser() != $this->app->user()->getUser()->getId()) {
            $this->notifications->addDanger('Vous n\'avez pas l\'autorisation.');
            $this->response->referer();
        }

        if ($this->manager->remove('post', 'id', $postId)) {
            $this->notifications->addSuccess('Post supprimé.');
        } else {
            $this->notifications->default('500', $this->manager->getError(), 'danger', true);
        }
        $this->response->referer();
    }

    public function deleteImage()
    {
        $postId = $this->request->getData('post');

        if (!$postId) {
            return false;
        }

        $post = $this->manager->findBy('post', 'id', $postId, true, true);

        $datas = [
            'id' => $postId,
            'image' => NULL,
            'image_alt' => NULL
        ];

        if ($this->manager->update('post', $datas)) {
            $this->notifications->addSuccess('L\'image a été supprimée.');
            $fm = new FileManagement();
            if (!$fm->deleteFile($post->getImage(), 'img')) {
                $this->app->logger()->addLogs('Impossible de supprimer l\'image ' . $post->getImage(), 500);
            }

            return true;
        }
        $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
        return false;
    }
}
