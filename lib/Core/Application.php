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
            $response->connect();
            return;
        }
        
        if ($routeur->getRoute()->getModule() === 'Backend' && !($this->authentification()->hasRole('ROLE_SUPER_ADMIN')
                                                                || $this->authentification()->hasRole('ROLE_ADMIN')
                                                                || $this->authentification()->hasRole('ROLE_MODERATEUR'))) {
            $response->disconnect();
            return;
        }
        
        if ($routeur->match()) {
            $page = new Page();
            $page->setController($routeur->getControllerClass());
            $page->generatePage($this, $routeur->getControllerMethod(), $routeur);
        } else {
            echo 'routeur don\'t match()';
        }
    }
}
