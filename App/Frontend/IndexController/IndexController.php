<?php

namespace App\Frontend\IndexController;

use Core\AbstractController;

class IndexController extends AbstractController
{
    public function index()
    {
        return $this->render();
    }

    public function deconnect()
    {
        session_destroy();
        $this->notifications->addSuccess("Vous avez était déconnecté.");
        return $this->response->redirectTo("/");
    }
}
