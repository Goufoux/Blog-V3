<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

class Routeur
{
    protected $route;
    protected $moduleDir;
    protected $controllerDir;
    protected $controllerFile;
    protected $controllerClass;
    protected $controllerMethod;

    public function __construct(Application $app)
    {
        $this->route = new Route;
    }

    public function match()
    {
        $moduleDir = __DIR__.'\\..\\..\\App\\'.$this->route->getModule().'\\';
        $this->setModuleDir($moduleDir);
        $controllerDir = $this->getModuleDir().$this->route->getControllerDir();
        $this->setControllerDir($controllerDir);
        $controllerFile = $this->getControllerDir().$this->route->getController().'.php';
        $this->setControllerFile($controllerFile);
        $controllerClass = '\\App\\'.$this->route->getModule().'\\'.$this->route->getControllerDir().$this->route->getController();
        $this->setControllerClass($controllerClass);
        $this->setControllerMethod($this->route->getView());
        return true;
    }

    public function setControllerMethod(string $method) :Routeur
    {
        $this->controllerMethod = $method;
        return $this;
    }

    public function getControllerMethod() :string
    {
        return $this->controllerMethod;
    }

    public function setModuleDir(string $dir) :Routeur
    {
        $this->moduleDir = $dir;
        return $this;
    }

    public function getModuleDir() :string
    {
        return $this->moduleDir;
    }

    public function setControllerDir(string $dir) :Routeur
    {
        $this->controllerDir = $dir;
        return $this;
    }

    public function getControllerDir() :string
    {
        return $this->controllerDir;
    }

    public function setControllerFile(string $path) :Routeur
    {
        $this->controllerFile = $path;
        return $this;
    }

    public function getControllerFile() :string
    {
        return $this->controllerFile;
    }

    public function setControllerClass(string $namespace) :Routeur
    {
        $this->controllerClass = $namespace;
        return $this;
    }

    public function getControllerClass() :string
    {
        return $this->controllerClass;
    }

    public function getRoute() :Route
    {
        return $this->route;
    }
}
