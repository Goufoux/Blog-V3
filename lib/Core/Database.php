<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Module\Logger;
use Module\Mail;

class Database
{
    protected $devMode;
    protected $params;
    protected $bdd;
    protected $connected = null;

    public function __construct($config)
    {
        $this->setParams($config->getDbParam());
        $this->setDevMode($config->getName());
        $this->connect();
    }

    public function connect()
    {
        $host = $this->getParams('host');
        $driver = $this->getParams('driver');
        $dbName = $this->getParams('dbName');
        $user = $this->getParams('user');
        $password = $this->getParams('password');
        $logger = new Logger;

        if($host === null) {
            if($this->isDevMode()) {
                $host = '127.0.0.1';
            } else {
                $logger->setLogs("Host not found in production.ini.".Logger::SAUT);
                $this->setConnected(false);
            }
        }

        if($driver === null) {
            if($this->isDevMode()) {
                $driver = 'mysql';
            } else {
                $logger->setLogs("Driver not found in production.ini.".Logger::SAUT);
                $this->setConnected(false);
            }
        }
        
        if($dbName === null) {
            $logger->setLogs("DbName not found in " . (($this->devMode == true) ? 'developpement' : 'production') . ".ini.".Logger::SAUT);
            $this->setConnected(false);
        }

        if($user === null) {
            if($this->isDevMode()) {
                $user = 'root';
            } else {
                $logger->setLogs("User not found in production.ini".Logger::SAUT);                    
                $this->setConnected(false);
            }
        }
        if($password === null) {
            if($this->isDevMode()) {
                $password = '';
            } else {
                $logger->setLogs("Password not found in production.ini".Logger::SAUT);
                $this->setConnected(false);
            }
        }

        if($this->isConnected() === false) {
            // $mail = new Mail;
            // $mail->send("quentin.roussel@genarkys.fr", "Erreur fatale.", "Des informations sont manquantes dans le production.ini consulter les logs.");
            $logger->setLogs("La connexion avec la base de donnée n'a pas été tentée car il manque des informations essentielles.");
            return false;
        }
        
        $connecting_string = $driver.':host='.$host.';dbname='.$dbName;
        // var_dump($connecting_string);
        // exit;
        try
        {
            $this->bdd = new \PDO($connecting_string, $user, $password, array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
            ));
            $this->setConnected(true);
            $logger->setLogs("Connexion réussi avec la bdd");
        }
        catch(\PDOException $e)
        {
            $this->setConnected(false);
            $logger->setLogs("Une erreur est survenue est la connexion avec la base de donnée n'a pas été établie. Détails:".Logger::SAUT.$e->getMessage());
            return false;
        }
    }

    public function isConnected() {
        if($this->connected !== null) {
            if($this->connected) {
                return true;
            } 
            return false;
        }
        return null;
    }

    public function isDevMode() :bool
    {
        if($this->devMode === true) {
            return true;
        } else {
            return false;
        }
    }

    private function setConnected(bool $bool)
    {
        $this->connected = $bool;
        return $this;
    }

    public function bdd()
    {
        return $this->bdd;
    }

    public function setDevMode($mode)
    {
        if($mode == 'production') {
            $this->devMode = false;
        } else {
            $this->devMode = true;
        }
    }

    public function setParams(array $param)
    {
        $this->params = $param;
    }

    protected function getParams(string $param = '')
    {
        if(empty($param)) {
            return $this->params;
        }

        if(isset($this->params[$param])) {
            return $this->params[$param];
        } else {
            return null;
        }
    }
}