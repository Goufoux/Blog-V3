<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Module\Notifications;
use Service\Response;

class Page
{
    protected $controller;

    public function generatePage(Application $app, string $action, Routeur $routeur)
    {
        $controller = $this->getController();
        $notif = Notifications::getInstance();
        $response = new Response;

        if (!class_exists($controller)) {
            if ($app->config()->isDev()) {
                $notif->addDanger("Controller not found : " . $controller);
            } else {
                $notif->addDanger("Une erreur est survenue. La page n'existe pas, ou plus.");
            }
            $response->referer();
            return;
        }
        
        $controller = new $controller($app);
        if (method_exists($controller, $action)) {
            echo $controller->$action();
        } else {
            if (preg_match("#ico#", $action)) {
                return;
            }
            $app->logger()->addLogs("Méthode not found : " . $action);
            if ($app->config()->isDev()) {
                $notif->addDanger("Méthode non trouvée : " . $action);
            } else {
                $notif->addDanger("Une erreur est survenue. La page n'existe pas, ou plus.");
            }
            $response->referer();
            return;
        }
    }

    public function setController(string $controller) :Page
    {
        $this->controller = $controller;
        return $this;
    }

    public function getController() :string
    {
        return $this->controller;
    }
}
