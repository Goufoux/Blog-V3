<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

abstract class Manager
{
    protected $bdd;
    protected $error;

    public function __construct($bdd)
    {
        $this->bdd = $bdd;
    }

    public function successRequest($request)
    {
        if($request->errorCode() != '00000') {
            return false;
        }
        return true;
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
                    $this->setError("Flag : " . $key . " not defined.");
                    return false;
                    break;
            }
        }
        return $sql;
    }
}