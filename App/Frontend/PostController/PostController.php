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
            $data = $this->request->getAllPost();
            
            if ($this->checkForm($data, $form) === false) {
                goto out;
            } 

            if ($image = $this->fileGestion($_FILES, $form)) {
                $data['image'] = $image;
            }
            $data['user'] = $this->app->user()->getId();
            
            if ($this->manager->add('post', $data)) {
                $this->notifications->addSuccess('Post ajouté');
                $this->response->referer();
            }
            $this->notifications->default('500', $this->manager->getError(), 'danger', true);
        }

        out:

        return $this->render([
            'form' => $form
        ]);
    }

    public function update()
    {
        $postId = $this->get('id');

        $postManager = $this->manager->getManagerOf('Post');
        $post = $postManager->findById($postId);

        if (!$post) {
            $this->notifications->addWarning('Post non trouvé.');
            $this->response->referer();
        }

        $form = new PostForm();

        if ($this->request->hasPost()) {
            $data = $this->request->getAllPost();
            
            if ($this->checkForm($data, $form) === false) {
                goto out;
            }
            if ($image = $this->fileGestion($_FILES, $form)) {
                $data['image'] = $image;
            }

            $data['id'] = $postId;
            if ($this->manager->update('post', $data)) {
                $this->notifications->addSuccess('Post mis à jour');
                $this->response->referer();
            }
            $this->notifications->default('500', $this->manager->getError(), 'danger', true);
        }

        out:

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
        $postId = $this->get('id');

        $postFlags = [
            'INNER JOIN' => [
                'table' => 'user',
                'sndTable' => 'post',
                'firstTag' => 'id',
                'sndTag' => 'user'
            ]
            ];

        $post = $this->manager->findOneBy('post', ['WHERE' => "id = $postId"], $postFlags);

        if (!$post) {
            $this->notifications->addWarning('Post non trouvé.');
            $this->response->referer();
        }

        $commentFlags = [
            'INNER JOIN' => [
                'table' => 'comment',
                'sndTable' => 'post',
                'firstTag' => 'post',
                'sndTag' => 'id'
            ]
        ];

        $comments = $this->manager->findBy('comment', ['WHERE' => "post = $postId"]);

        $form = new CommentForm();

        if ($this->request->hasPost() && $this->app->authentification()->isAuthentificated()) {
            $data = $this->request->getAllPost();
            
            if ($this->checkCommentForm($data, $form)) {
                goto out;
            }

            $data['user'] = $this->app->user()->getId();
            $data['post'] = $postId;
            if ($this->manager->add('comment', $data)) {
                $this->notifications->addSuccess('Votre commentaire a été envoyé et est en attente de validation.');
                $this->response->referer();
            }
            $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
        }

        out:

        return $this->render([
            'post' => $post,
            'form' => $form,
            'comments' => $comments
        ]);
    }

    private function checkCommentForm(array $data, CommentForm $commentForm)
    {
        $commentForm->verif($data);

        if (!$commentForm->isValid()) {
            $this->notifications->addDanger('Formulaire invalide.');
            
            return false;
        }

        return true;
    }

    public function delete()
    {
        $postId = $this->get('id');

        $post = $this->manager->findOneBy('post', ['WHERE' => "id = $postId"]);

        if (!$post) {
            $this->notifications->addWarning('Post non trouvé.');
            $this->response->referer();
        }

        if ($post->getUser() != $this->app->user()->getId() && !$this->app->authentification()->hasRole('ROLE_SUPER_ADMIN')) {
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

        $post = $this->manager->findOneBy('post', ['WHERE' => "id = $postId"]);

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

    /**
     * 
     * @param array $data
     * @param PostForm $postForm
     * @return bool
     */
    private function checkForm(array $data, PostForm $postForm)
    {
        $postForm->verif($data);

        if (!$postForm->isValid()) {
            $this->notifications->addDanger('Formulaire invalide.');
            
            return false;
        }

        return true;
    }

    /**
     * @param array $files
     * @param PostForm $postForm
     * @return bool|string
     */
    private function fileGestion(array $files, PostForm $postForm)
    {
        if (empty($_FILES)) {
            return false;
        }

        $fileManagement = new FileManagement();
        $fileName = uniqid();

        if ($fileManagement->uploadFile($_FILES['image'], $fileName, 'img')) {
            return $fileManagement->getFilename();
        }
        $postForm->addErrors('image', $fileManagement->getError());
        
        return false;
    }
}
