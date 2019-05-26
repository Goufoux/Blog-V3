<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Module;

use Core\Core;
use Core\Managers;

class AjaxCore extends Core
{
    protected $action;
    protected $manager;
    protected $content;
    protected $error;
    protected $autoload;
    protected $instance;

    const err_template = '<span class="badge badge-danger p-3 mx-auto">%s</span>';

    public function __construct(string $action, array $options = [])
    {
        parent::__construct();
        $this->action = $action;
        $this->autoload = $options['autoload'] ?? true;
        $this->instance = $options['instance'] ?? false;
        $this->manager = new Managers($this->getDatabase()->bdd());
        $this->run($this->instance, $this->autoload);
    }

    public function run(bool $instance = false, $autoLoad = true)
    {
        if (in_array($this->action, ["notifications"])) {
            $instance = true;
        }
        $class = '\\Module\\'.ucfirst($this->action);
        if (class_exists($class)) {
            if ($instance) {
                $class = new $class;
                if ($autoLoad) {
                    $this->content = $class->display();
                    return;
                }
                $this->content = $class;
                return;
            }
            $module = new $class($this->manager);
            if ($autoLoad) {
                $this->content = $module->getTemplate();
                return;
            }
            $this->content = $module;
            return;
        } else {
            $this->setError("Class $class not found.");
            return false;
        }
    }

    public function getError()
    {
        return $this->error;
    }

    private function setError(string $error) :AjaxCore
    {
        $this->error = sprintf($this::err_template, $error);
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }
}
