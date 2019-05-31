<?php

namespace App\Backend\PostController;

use Core\AbstractController;

class PostController extends AbstractController
{
    public function delete()
    {
        if (!$this->app->user()->hasRole('ROLE_SUPER_ADMIN')) {
            $this->notifications->addWarning("Zone réservée.");
            $this->response->referer();
        }

        $postId = $this->request->getData('id');

        if (!$postId) {
            $this->notifications->default("500", "Aucun identifiant.");
            $this->response->referer();
        }

        if ($this->managers->remove("post", "id", $postId)) {
            $this->notifications->addSuccess("Post supprimé.");
        } else {
            $this->notifications->default("500", $this->managers->getError(), "danger", true);
        }
        $this->response->referer();
    }
}
