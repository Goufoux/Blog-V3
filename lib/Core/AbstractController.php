<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Module\Notifications;
use Service\Request;
use Service\Response;
use Module\Logger;

abstract class AbstractController
{
    protected $app;
    protected $twig;
    protected $manager;
    protected $notifications;
    protected $path;
    protected $base_link;
    protected $request;
    protected $response;

    public function __construct($app)
    {
        $this->app = $app;
        $this->loadTwig();
        $this->notifications = Notifications::getInstance();
        $this->manager = new Managers($app->getDatabase()->bdd());
        $this->request = new Request;
        $this->response = new Response;
    }

    public function setBaseLink(Route $route)
    {
        $request = $route->getRequest();
        if (!$route->getIndexAccess()) {
            array_pop($request);
        }
        $temp = implode('/', $request);
        $this->base_link = '/'.$temp.'/';

        return $this;
    }

    public function render(array $array = [], string $path = null)
    {
        $this->setPath($path);
        $data = array();
        foreach ($array as $key => $val) {
            $data[$key] = $val;
        }
        $data['app'] = $this->app;
        $data['request'] = $this->request;
        $data['base_url'] = $this->base_link;

        if (!empty($_SESSION['datas'])) {
            $data['datas'] = $_SESSION['datas'];
            unset($_SESSION['datas']);
        }

        if (!empty($_SESSION['form'])) {
            $data['form'] = $_SESSION['form'];
            unset($_SESSION['form']);
        }

        return $this->twig->render($this->getPath(), $data);
    }

    public function loadTwig()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../../public/template/');
        $this->twig = new \Twig_Environment($loader, [
            'cache' => 'cache/twig-cache',
            'debug' => true
        ]);
        $this->twig->addExtension(new \Twig_Extension_Debug());
    }

    public function getPath()
    {
        return $this->path;
    }

    private function setPath(string $path = null)
    {
        $route = new Route();
        $this->setBaseLink($route);
        if ($path === null) {
            if (count($route->getRequest()) <= 2) {
                $module = strtolower($route->getModule());
                $controller = explode("Controller", $route->getController())[0];
                $controller[0] = strtolower($controller[0]);
                $view = explode("?", $route->getView());
                $path = $module.'/'.$controller.'/'.$view[0].'.html.twig';
            } else {
                $request = $route->getRequest();
                $adminKey = array_search('admin', $request);
                if ($adminKey !== false) {
                    $request[$adminKey] = 'backend';
                }
                $view = end($request);
                $view = explode("?", $view);
                $request[count($request)-1] = $view[0];
                $temp = implode('/', $request);
                $path = $temp.'.html.twig';
                if ($route->getIndexAccess()) {
                    $path = $temp.'/index.html.twig';
                }
            }
        }
        
        $logger = new Logger;
        if (!file_exists(__DIR__.'/../../public/template/'.$path)) {
            if ($this->isDev()) {
                $this->notifications->addDanger("Template not found : " . $path);
            } else {
                $this->notifications->addDanger("Une erreur est survenue, page non trouvée");
            }
            $logger->setLogs("Template not found:".$path);
            $this->response->referer();
        }
        $this->path = $path;
    }

    public function isDev()
    {
        return ($this->app->config()->isDev() === true);
    }

    public function getAll(string $data = '', string $method)
    {
        return $this->get($data, $method, true);
    }

    public function get(string $data = '', string $method = 'GET', bool $all = false, bool $autoRedirect = true)
    {
        switch ($method) {
            case 'GET':
                $result = $all ? $this->request->getAllData() : $this->request->getData($data);
                break;
            case 'POST':
                $result = $all ? $this->request->getAllPost() : $this->request->getPost($data);
                break;
        }
        
        if ($autoRedirect && ($result === false || $result === null || empty($result))) {
            $this->notifications->default('500', "$data n'a pas été trouvé. Méthode $method", 'danger', $this->isDev());

            $this->response->referer();
        }

        return $result;
    }
}
