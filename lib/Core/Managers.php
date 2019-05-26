<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Module\Notifications;
use Service\Response;

class Managers extends Manager
{
    protected $managers;

    public function getManagerOf($module)
    {
        $module = ucfirst($module);
        if (!isset($this->managers[$module])) {
            $manager = 'Manager\\'.$module.'Manager';
            try {
                if (!class_exists($manager)) {
                    throw new \Exception("Le manager {".$manager."} n'existe pas");
                }
                if (!$this->managers[$module] = new $manager($this->bdd)) {
                    throw new \Exception("Impossible d'instancier la classe " . $manager);
                }
            } catch (\Exception $e) {
                $notif = Notifications::getInstance();
                $notif->addWarning($e->getMessage());
                $response = new Response;
                $response->referer();
                exit;
            }
            return $this->managers[$module];
        }
    }

    public function fetchAll(string $table, array $flags = [])
    {
        $entity = "\Entity\\".ucfirst($table);
        $response = new Response;
        $notif = new Notifications;

        if (!class_exists($entity)) {
            $notif->addDanger("Entité non trouvée : " . $entity);
            $response->referer();
        }

        $sql = 'SELECT * FROM ' . $table;
        
        if (!empty($flags)) {
            foreach ($flags as $key => $flag) {
                switch ($key) {
                    case 'WHERE':
                        $sql .= ' WHERE ' . $flag;
                            break;
                    case 'ORDER BY':
                        $sql .= ' ORDER BY ' . $flag;
                            break;
                    case 'LIMIT':
                        if (!is_int($flag)) {
                            break;
                        }
                        $sql .= ' LIMIT ' . $flag;
                            break;
                    default:
                        break;
                }
            }
        }
        
        try {
            $req = $this->bdd->prepare($sql);
            $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\\'.ucfirst($table));
            $req->execute();
            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }
            $res = $req->fetchAll();
            foreach ($res as $key => $data) {
                $res[$key] = new $entity($data, true);
            }
    
            return $res;
        } catch (\PDOException $e) {
            $notif->addDanger($e->getMessage());
            return false;
        }
    }

    /**
     * @author Genarkys <quentin.roussel@genarkys.fr>
     *
     * @param string $table
     * @param string $data
     * @param boolean $autoPrefix
     * @param string $by
     * @return void
     */
    public function findBy($table = '', string $by = '', $data = '', bool $autoPrefix = true, bool $oneResult = false, array $flags = array())
    {
        $entity = "\Entity\\".ucfirst($table);

        if (empty($table) || empty($data)) {
            return null;
        }
        
        $prefix = '';
        if ($autoPrefix) {
            $prefix = $table.'_';
        }

        $sql = 'SELECT * FROM ' . $table . ' WHERE ' . $prefix.$by . ' = :' . $prefix.$by;
        if (!empty($flags)) {
            foreach ($flags as $key => $flag) {
                switch ($key) {
                    case 'WHERE':
                        $sql .= ' AND ' . $flag;
                            break;
                    case 'LIMIT':
                        if (!is_int($flag)) {
                            break;
                        }
                        $sql .= ' LIMIT ' . $flag;
                        break;
                    default:
                        break;
                }
            }
        }

        $req = $this->bdd->prepare($sql);
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $entity);
        $req->bindValue(':'.$prefix.$by, $data);
        try {
            $req->execute();
            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }
            
            if ($oneResult) {
                $res = $req->fetch();

                if (!$res) {
                    return array();
                }

                $res = new $entity($res, true);
            } else {
                $res = $req->fetchAll();
                
                foreach ($res as $key => $value) {
                    $res[$key] = new $entity($value, true);
                }
            }


            return $res;
        } catch (\PDOException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function update(string $table, array $data, bool $autoPrefix = true)
    {
        if (empty($table) || empty($data)) {
            return null;
        }
        $prefix = '';
        if ($autoPrefix) {
            $prefix = $table.'_';
        }

        $tempKey = array();
        $tempValues = array();

        $id = $data['id'] ?? false;
        $now = new \DateTime();
        $data['updated_at'] = $now->format('Y-m-d H:i:s');

        if (!$id) {
            $this->setError("No key defined for update row");
            return false;
        }

        unset($data['id']);

        foreach ($data as $key => $value) {
            $tempKey[] = $prefix.$key;
            if ($key === 'password') {
                $value = password_hash($value, PASSWORD_BCRYPT);
            }
            $tempValues[] = $value;
        }

        $str = array();

        for ($i = 0; $i < count($tempKey); $i++) {
            $str []= $tempKey[$i] . ' = :' . $tempKey[$i];
        }

        $str = implode(', ', $str);
        $req = $this->bdd->prepare('UPDATE ' . $table . ' SET ' . $str . ' WHERE ' . $table.'_id = :id');
        for ($i = 0; $i < count($tempKey); $i++) {
            $req->bindValue(':'.$tempKey[$i], $tempValues[$i]);
        }
        $req->bindValue(':id', $id);
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

    public function add(string $table, array $data, $tablePrefixe = true)
    {
        if (empty($table)) {
            return false;
        }

        if (empty($data)) {
            return null;
        }

        $prefix = '';
        if ($tablePrefixe) {
            $prefix = $table.'_';
        }

        $now = new \DateTime();
        $data['created_at'] = $now->format('Y-m-d H:i:s');

        $insert = array();
        $values = array();
        $bindKey = array();
        $bindValue = array();
        
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $value = password_hash($value, PASSWORD_BCRYPT);
            }
            $insert []= $prefix.$key;
            $values []= ':'.$key;
            $bindKey[]= $key;
            $bindValue []= $value;
        }

        $SQLinsert = implode(', ', $insert);
        $values = implode(', ', $values);

        $sql = 'INSERT INTO ' . $table . '('.$SQLinsert.') VALUES('.$values.')';
        
        $req = $this->bdd->prepare($sql);
        if (count($bindValue) <= 0) {
            return null;
        }

        for ($i = 0; $i < count($bindValue); $i++) {
            $req->bindValue(':'.$bindKey[$i], $bindValue[$i]);
        }
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

    public function remove(string $table, string $column = 'id', $value, $autoPrefix = true)
    {
        $prefix = '';
        if ($autoPrefix) {
            $prefix = $table.'_';
        }
        $sql = 'DELETE FROM ' . $table . ' WHERE ' . $prefix.$column . ' = :' . $column;
        $req = $this->bdd->prepare($sql);
        $req->bindValue(':'.$column, $value);
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

    public function truncate(string $table)
    {
        $sql = 'TRUNCATE ' . $table;
        $req = $this->bdd->prepare($sql);
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
}
