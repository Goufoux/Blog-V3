<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Module\Mail;
use Service\Response;

class Application extends Core
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        $routeur = new Routeur($this);
        $response = new Response;

        if ($routeur->getRoute()->getModule() === 'Backend') {
            $this->AdminAccess($response);
        }

        if (!$routeur->match()) {
            $response->redirectTo('/');
        }

        $page = new Page();
        $page->setController($routeur->getControllerClass());
        $page->generatePage($this, $routeur->getControllerMethod(), $routeur);
    }

    private function AdminAccess(Response $response)
    {
        if (!$this->authentification()->isAuthentificated()) {
            return $response->connect();
        }

        if (!($this->authentification()->hasRole('ROLE_SUPER_ADMIN') || $this->authentification()->hasRole('ROLE_ADMIN') || $this->authentification()->hasRole('ROLE_MODERATEUR'))) {
            return $response->disconnect();
        }
    }
}
