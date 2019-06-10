<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Service\Request;
use Config\Developpement;
use Config\Production;
use Module\Logger;

abstract class Core
{
    protected $config;
    protected $database;
    protected $user;
    protected $authentification;
    protected $logger;

    public function __construct()
    {
        $this->setConfig();
        $this->setDatabase();
        $this->setAuthentification();
        $this->setLogger();
    }

    public function logger(): Logger
    {
        return $this->logger;
    }

    public function setLogger(): self
    {
        $this->logger = new Logger($this->getDatabase()->bdd());

        return $this;
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function authentification()
    {
        return $this->authentification;
    }

    public function user()
    {
        return $this->user;
    }

    public function config()
    {
        return $this->config;
    }
    
    abstract public function run();

    public function setAuthentification()
    {
        $this->authentification = new Authentification($this->database->bdd());

        if ($this->authentification->isAuthentificated()) {
            $this->setUser($this->authentification->getUser());
        }

        return $this;
    }
    
    private function setUser($user = null): Core
    {
        $this->user = $user;
        
        return $this;
    }

    private function setDatabase(): Core
    {
        $this->database = new Database($this->config);

        return $this;
    }

    private function setConfig()
    {
        $request = new Request;
        $this->config = new Production;
        if ($request->getServerAddr() == '::1') {
            $this->config = new Developpement;
        }
    }
}
