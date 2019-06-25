<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Module\Notifications;
use Service\Response;
use Service\Request;

abstract class Manager
{
    protected $bdd;
    protected $notifications;
    protected $error;
    protected $response;
    protected $request;

    public function __construct($bdd)
    {
        $this->bdd = $bdd;
        $this->notifications = Notifications::getInstance();
        $this->response = new Response();
        $this->request = new Request();
    }

    public function isDev()
    {
        if ($this->request->getServerAddr() == '::1') {
            return true;
        }
        
        return false;
    }


    public function successRequest($request)
    {
        return ($request->errorCode() != '00000') ? false : true;
    }

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function errorCode($request)
    {
        return $request->errorInfo()[2];
    }

    public function getLastInsertId()
    {
        return $this->bdd->lastInsertId();
    }

    public function flagsToSql(array $flags)
    {
        $sql = '';
        foreach ($flags as $key => $flag) {
            switch ($key) {
                case 'WHERE':
                    $sql .= $flag;
                    break;
                case 'ORDER BY':
                    $sql .= ' ORDER BY ' . $flag;
                    break;
                case 'LIMIT':
                    $sql .= ' LIMIT '. $flag;
                    break;
                default:
                    $this->setError("Flag : $key not defined.");
                    return false;
                    break;
            }
        }
        return $sql;
    }
}
