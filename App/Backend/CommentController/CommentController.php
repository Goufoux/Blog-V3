<?php

namespace App\Backend\CommentController;

use Core\AbstractController;

class CommentController extends AbstractController
{
    public function index()
    {
        $commentManager = $this->manager->getManagerOf('comment');
        $comments = $commentManager->findByState();

        return $this->render([
            'title' => 'Gestion des commentaires',
            'comments' => $comments
        ]);
    }

    public function validate()
    {
        $commentId = $this->request->getData('id');

        if (!$commentId) {
            $this->notifications->addDanger('Commentaire non trouvé.');
            $this->response->referer();
        }

        if ($this->app->authentification()->hasRole('ROLE_SUPER_ADMIN') || $this->app->authentification()->hasRole('ROLE_ADMIN') ||$this->app->authentification()->hasRole('ROLE_MODERATEUR')) {
            $datas = [
                'id' => $commentId,
                'state' => 1
            ];
            if ($this->manager->update('comment', $datas)) {
                $this->notifications->addSuccess('Commentaire validé.');
            } else {
                $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
            }
        } else {
            $this->notifications->addWarning('Vous n\'avez pas l\'autorisation.');
        }

        $this->response->referer();
    }

    public function delete()
    {
        $commentId = $this->request->getData('id');

        if (!$commentId) {
            $this->notifications->addDanger('Commentaire non trouvé.');
            $this->response->referer();
        }

        if ($this->app->authentification()->hasRole('ROLE_SUPER_ADMIN') || $this->app->authentification()->hasRole('ROLE_ADMIN') ||$this->app->authentification()->hasRole('ROLE_MODERATEUR')) {
            if ($this->manager->remove('comment', 'id', $commentId)) {
                $this->notifications->addSuccess('Commentaire supprimé.');
            } else {
                $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
            }
        } else {
            $this->notifications->addWarning('Vous n\'avez pas l\'autorisation.');
        }

        $this->response->referer();
    }
}
