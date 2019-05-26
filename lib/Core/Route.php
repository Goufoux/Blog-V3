<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Service\Request;
use Service\MyArray;

class Route
{
    protected $request;
    protected $module;
    protected $controller;
    protected $view;
    protected $controllerDir;
    protected $param;

    private $controllerPos;
    private $indexAccess;

    public function __construct()
    {
        $request = new Request;
        $temp = explode('/', $request->getRequestUri());
        $this->setRequest(MyArray::clearArray(MyArray::stringToArray($request->getRequestUri())));
        $this->setIndexAccess($request->getRequestUri());
        $this->run();
    }

    public function run()
    {
        /* setModule */
        if (empty($this->getRequest())) {
            $this->setModule('public');
            $this->setControllerDir('IndexController\\');
            $this->setController('IndexController');
            $this->setView('index');
            $this->setControllerPos(0);
            return;
        }
        $this->setModule($this->getRequest()[0]);
        
        /* setController */
        if (empty($this->getRequest()[$this->getControllerPos()])) {
            $this->setControllerDir('IndexController\\');
            $this->setController('IndexController');
            $this->setView('index');
            return;
        }
        $j = 1;
        if ($this->getIndexAccess()) {
            $j = 0;
            $controllerDir = '';
            for ($i = $this->getControllerPos(); $i < (count($this->getRequest())-$j); $i++) {
                $controllerDir .= ucfirst($this->getRequest()[$i]).'Controller\\';
            }
            $this->setControllerDir($controllerDir);
            $x = count($this->getRequest())-$j-1;
            $this->setController(ucfirst($this->getRequest()[$x]).'Controller');

            /* setView */
            if ($this->getIndexAccess()) {
                $this->setView('index');
                return;
            }

            $tempRequest = $this->getRequest();
            $view = end($tempRequest);
            $param = explode('?', $view);
            $this->setView($param[0]);
            if (!empty($param[1])) {
                $this->setParam($param[1]);
            }
        } else {
            $request = new Request;
            $temp = explode('/', $request->getRequestUri());
            $tempCountRequest = count($temp);
            switch ($tempCountRequest) {
                case 1:
                    echo 'error';
                        break;
                case 2:
                    $this->setController('IndexController');
                    $this->setControllerDir('IndexController\\');
                    $tempRequest = $this->getRequest();
                    $view = end($tempRequest);
                    $param = explode('?', $view);
                    $this->setView($param[0]);
                    if (!empty($param[1])) {
                        $this->setParam($param[1]);
                    }
                        break;
                case 3:
                    if ($this->getControllerPos()) {
                        $this->setController('IndexController');
                        $this->setControllerDir('IndexController\\');
                        $tempRequest = $this->getRequest();
                        $view = end($tempRequest);
                        $view = explode('?', $view);
                        $view = MyArray::clearArray($view, '');
                        if (preg_match("#=#", $view[0])) {
                            $this->setView('index');
                            $this->setParam($view[0]);
                        } else {
                            $this->setView($view[0]);
                            if (!empty($view[1])) {
                                $this->setParam($view[1]);
                            }
                        }
                    } else {
                        $this->setController(ucfirst($temp[1]).'Controller');
                        $this->setControllerDir(ucfirst($temp[1]).'Controller\\');
                        $tempRequest = $this->getRequest();
                        $view = end($tempRequest);
                        $param = explode('?', $view);
                        $this->setView($param[0]);
                        if (!empty($param[1])) {
                            $this->setParam($param[1]);
                        }
                    }
                        break;
                default:
                    $controllerDir = '';
                    for ($i = $this->getControllerPos(); $i < (count($this->getRequest())-$j); $i++) {
                        $controllerDir .= ucfirst($this->getRequest()[$i]).'Controller\\';
                    }
                    $this->setControllerDir($controllerDir);
                    $x = count($this->getRequest())-$j-1;
                    $this->setController(ucfirst($this->getRequest()[$x]).'Controller');
                    $tempRequest = $this->getRequest();
                    $view = end($tempRequest);
                    $param = explode('?', $view);
                    $this->setView($param[0]);
                    if (!empty($param[1])) {
                        $this->setParam($param[1]);
                    }
                        break;
            }
        }
    }

    public function setParam(string $param)
    {
        $array = MyArray::stringToAssocArray($param);
        $this->param = $array;
    }

    public function setIndexAccess($request)
    {
        $char = substr($request, -1);
        if ($char === '/') {
            $this->indexAccess = true;
        } else {
            $this->indexAccess = false;
        }
    }

    public function getIndexAccess() :bool
    {
        return $this->indexAccess;
    }

    public function setControllerDir(string $path)
    {
        $this->controllerDir = $path;
        return $this;
    }

    public function getControllerDir()
    {
        return $this->controllerDir;
    }

    public function setRequest(array $request)
    {
        $this->request = $request;
        return $this;
    }

    public function getRequest(?string $key = null)
    {
        if ($key !== null) {
            if (!isset($this->request[$key])) {
                return null;
            }
            return $this->request[$key];
        }
        return $this->request;
    }

    public function setView(string $view)
    {
        $this->view = $view;
    }

    public function getView()
    {
        return $this->view;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function setModule(string $module)
    {
        switch ($module) {
            case 'public':
                $this->module = 'Frontend';
                $this->setControllerPos(1);
                    break;
            case 'admin':
                $this->module = 'Backend';
                $this->setControllerPos(1);
                    break;
            case 'portail':
                $this->module = 'Portail';
                $this->setControllerPos(1);
                    break;
            default:
                $this->module = 'Frontend';
                $this->setControllerPos(0);
                    break;
        }
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setController(string $controller)
    {
        $this->controller = $controller;
    }

    private function setControllerPos($pos)
    {
        $this->controllerPos = $pos;
        return $this;
    }

    private function getControllerPos()
    {
        return $this->controllerPos;
    }
}
