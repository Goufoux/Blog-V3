<?php

namespace App\Backend\CommentController;

use Core\AbstractController;

class CommentController extends AbstractController
{
    public function index()
    {
        $commentsFlags = [
            'INNER JOIN' => [
                'table' => 'user',
                'sndTable' => 'comment',
                'firstTag' => 'id',
                'sndTag' => 'user' 
            ]
        ];

        $comments = $this->manager->findBy('comment', ['WHERE' => 'state = 0'] , $commentsFlags);

        return $this->render([
            'title' => 'Gestion des commentaires',
            'comments' => $comments
        ]);
    }

    public function validate()
    {
        $commentId = $this->get('id');

        
        if (!$this->app->authentification()->hasRole('ROLE_SUPER_ADMIN') && !$this->app->authentification()->hasRole('ROLE_ADMIN') && !$this->app->authentification()->hasRole('ROLE_MODERATEUR')) {
            $this->notifications->addWarning('Vous n\'avez pas l\'autorisation.');
            $this->response->referer();        
        }
        
        $datas = [
            'id' => $commentId,
            'state' => 1
        ];

        if ($this->manager->update('comment', $datas)) {
            $this->notifications->addSuccess('Commentaire validé.');
        } else {
            $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
        }

        $this->response->referer();
    }

    public function delete()
    {
        $commentId = $this->get('id');

        if (!$this->app->authentification()->hasRole('ROLE_SUPER_ADMIN') && !$this->app->authentification()->hasRole('ROLE_ADMIN') && !$this->app->authentification()->hasRole('ROLE_MODERATEUR')) {
            $this->notifications->addWarning('Vous n\'avez pas l\'autorisation.');

            return $this->response->referer();
        }

        if ($this->manager->remove('comment', 'id', $commentId)) {
            $this->notifications->addSuccess('Commentaire supprimé.');
        } else {
            $this->notifications->default('500', $this->manager->getError(), 'danger', $this->isDev());
        }

        $this->response->referer();
    }
}
