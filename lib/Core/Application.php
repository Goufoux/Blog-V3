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

        if ($routeur->getRoute()->getModule() === 'Backend' && !$this->authentification()->isAuthentificated()) {
            if (!$this->authentification()->isAuthentificated()) {
                $response->connect();
                return;
            }

            if (!($this->authentification()->hasRole('ROLE_SUPER_ADMIN') || $this->authentification()->hasRole('ROLE_ADMIN') || $this->authentification()->hasRole('ROLE_MODERATEUR'))) {
                $response->disconnect();
                return;
            }
        }

        if (!$routeur->match()) {
            $response->redirectTo('/');
        }

        $page = new Page();
        $page->setController($routeur->getControllerClass());
        $page->generatePage($this, $routeur->getControllerMethod(), $routeur);
    }
}
