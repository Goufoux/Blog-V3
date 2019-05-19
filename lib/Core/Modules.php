<?php

namespace Core;

class Modules
{
    protected $userRoles;
    protected $managers;

    private $message;
    
    protected $modules = array();
    protected $activeModules;

    public function __construct(array $userRoles = array(), Database $db, $autoRegister = true)
    {
        $this->userRoles = $userRoles;
        $this->managers = new Managers($db->bdd());
        if ($autoRegister) {
            $this->register();
        }
    }
    
    public function register()
    {
        $modulesManager = $this->managers->getManagerOf("module");
        $modulesList = array();
        
        foreach ($this->userRoles as $key => $role) {
            $modulesForRole = $modulesManager->findByRole($role);
            foreach ($modulesForRole as $key => $module) {
                if (!in_array($module->getModule(), $modulesList)) {
                    $tempModule = $modulesManager->findById($module->getModule(), true);
                    if ($tempModule === null) {
                        continue;
                    }
                    $modulesList[$module->getModule()] = $tempModule;
                }
            }
        }

        $this->modules = $modulesList;
    }

    public function launch()
    {
        if (empty($this->modules)) {
            $this->message = "Aucun module.";
            return null;
        }

        foreach ($this->modules as $key => $module) {
            $className = '\\Module\\'.ucfirst($module->getName());
            if (!class_exists($className)) {
                $this->setMessage("Module not found : " . $className);
            }
            $this->modules[$key] = new $className($module, $this->managers);
        }
    }

    /**
     * Get the value of modules
     */ 
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Set the value of modules
     *
     * @return  self
     */ 
    public function setModules($modules)
    {
        $this->modules = $modules;

        return $this;
    }

    /**
     * Get the value of message
     */ 
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of message
     *
     * @return  self
     */ 
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}