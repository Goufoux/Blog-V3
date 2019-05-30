<?php

namespace App\Backend\IndexController;

use Core\AbstractController;

class IndexController extends AbstractController
{
    public function index()
    {
        return $this->render();
    }
}
