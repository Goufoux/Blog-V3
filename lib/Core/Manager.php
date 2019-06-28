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

    public function executeRequest($req)
    {
        try {
            $req->execute();
            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }
            return true;
        } catch (\PDOException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function fetchAllRequest(string $sql, string $table, string $entity)
    {
        return $this->fetchRequest($sql, $table, $entity, true);
    }

    public function fetchRequest(string $sql, string $table, string $entity, $all = false)
    {
        try {
            $req = $this->bdd->prepare($sql);
            $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\\'.ucfirst($table));
            $req->execute();
            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req), $req);
            }
               
            if ($all) {
                $res = $req->fetchAll();
                foreach ($res as $key => $data) {
                    $res[$key] = new $entity($data, true);
                }

                return $res;
            }

            $res = $req->fetch();
            
            if ($res === false) {
                return false;
            }

            $res = new $entity($res, true);
            
            return $res;
        } catch (\PDOException $e) {
            $this->notifications->default('500', $e->getMessage(), 'danger', $this->isDev());
            
            return false;
        }
    }
}
