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
    protected $logger;

    public function __construct()
    {
        $this->setConfig();
        $this->setDatabase();
        $this->setUser();
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

    public function user(): Authentification
    {
        return $this->user;
    }

    public function config()
    {
        return $this->config;
    }
    
    abstract public function run();

    
    private function setUser(): Core
    {
        $this->user = new Authentification();
        
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
