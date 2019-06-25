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
                $this->notifications->default('500', $e->getMessage(), 'danger', $this->isDev());
                $this->response->referer();
                exit;
            }

            return $this->managers[$module];
        }
    }

    public function prepareRequest(string $table, array $flags)
    {
        $entity = "\Entity\\".ucfirst($table);
        
        if (!$this->checkEntity($entity)) {
            return false;
        }

        $sql = "SELECT $table.* FROM $table";

        if (!empty($flags)) {
            $sql = $this->transformFlags($table, $flags);
        }

        return ['request' => $sql, 'entity' => $entity];
    }

    public function transformFlags(string $table, array $flags)
    {
        $data = [
            'table' => [],
            'request' => []
        ];

        foreach ($flags as $key => $flag) {
            switch ($key) {
                case 'INNER JOIN':
                    $data['request']['INNER JOIN'][] = "{$flag['table']} ON {$flag['table']}.{$flag['table']}_{$flag['firstTag']} = {$flag['sndTable']}.{$flag['sndTable']}_{$flag['sndTag']}";
                    $data['table'][] = [$flag['table'],$flag['sndTable']];
                    break;
                case 'WHERE':
                    $data['request']['WHERE'][] = "AND {$flag['table']}.{$flag['tag']}";
                    break;
                case 'LIMIT':
                    $data['request']['LIMIT'][] = "AND {$flag['table']}.{$flag['tag']}";
                    break;
                default:
                    return false;
            }
        }

        $data['table'] = $this->analyseTableData($table, $data['table']);
        $data['request'] = $this->analyseTableRequest($data['request']);
        
        $final = $data['table'] . $data['request'];

        return $final;
    }

    private function analyseTableRequest(array $flags)
    {
        $final = [];

        foreach ($flags as $key => $value) {
            foreach ($value as $k => $v) {
                $final[] = " $key $v";
            }
        }

        $final = implode(', ', $final);
        
        return $final;
    }

    private function analyseTableData(string $primaryTable, array $secondaryTable)
    {
        $sql = "SELECT $primaryTable.*";

        $temp = [];

        foreach ($secondaryTable as $value) {
            $temp[] = $this->deleteSameTable($primaryTable, $value);
        }

        $final = '';

        foreach ($temp as $key => $value) {
            foreach ($value as $k => $v) {
                $v = $v.'.*';
                $value[$k] = $v;
            }
            $final .= implode(', ', $value);            
        }

        $sql .= ", $final FROM $primaryTable";

        return $sql;
    }

    private function deleteSameTable(string $origin, array $array)
    {
        foreach ($array as $key => $value) {
            if ($value == $origin) {
                unset($array[$key]);
            }
        }

        return array_values($array);
    }

    public function checkEntity($entity)
    {
        if (!class_exists($entity)) {
            $this->notifications->default('500', "$entity non trouvée.", 'danger', $this->isDev());    
            
            return false;
        }

        return true;
    }

    public function whereCondition(string $table, array $conditions)
    {
        $final = [];

        foreach ($conditions as $key => $condition) {
            $final[] = $table.'_'.$condition;
        }

        $final = implode(', AND', $final);

        return "WHERE $final";
    }

    public function fetchAll(string $table, array $flags = [])
    {
        $data = $this->prepareRequest($table, $flags);
        $sql = $data['request'];
        $entity = $data['entity'];
        
        if ($sql === false) {
            return $this->response->referer();
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
            $this->notifications->default('500', $e->getMessage(), 'danger', $this->isDev());
            
            return false;
        }
    }

    public function findOneBy(string $table, array $where, array $flags = [])
    {
        return $this->findBy($table, $where, $flags, true);
    }

    /**
     * @author Genarkys <quentin.roussel@genarkys.fr>
     *
     * @param string $table
     * @param string $data
     * @param boolean $autoPrefix
     * @param string $by
     */
    public function findBy(string $table, array $where, array $flags = [], bool $oneResult = false)
    {
        
        $whereCondition = $this->whereCondition($table, $where);
        $data = $this->prepareRequest($table, $flags);
        $sql = $data['request'].' '.$whereCondition;
        // var_dump($sql);
        $entity = $data['entity'];
        // var_dump($sql, $entity);
        // return $data;
        // exit;
// 
        $req = $this->bdd->prepare($sql);
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $entity);
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
