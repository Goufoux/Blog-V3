<?php

namespace Manager;

use Core\Manager;
use Entity\UserRole;

class UserRoleManager extends Manager
{
    public function findByUser($id)
    {
        $req = $this->bdd->prepare('SELECT userRole.*, role.* FROM userRole
                                    INNER JOIN role ON role.role_id = userRole.userRole_role
                                    WHERE userRole_user = :id');
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\\UserRole');
        $req->bindValue(':id', $id ,\PDO::PARAM_STR);
        try {
            $req->execute();
            
            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }

            $res = $req->fetchAll();

            foreach ($res as $key => $userRole) {
                $res[$key] = new UserRole($userRole, true);
            }
            
            return $res;

        } catch(\PDOException $e) {
            $this->setError($e->getMessage());
            
            return false;
        }
    }

    public function removeRoleOfUser(int $roleId, int $userId)
    {
        $req = $this->bdd->prepare('DELETE FROM userRole WHERE userRole_user = :user_id AND userRole_role = :role_id');
        try {
            $req->bindValue(':user_id', $userId ,\PDO::PARAM_INT);
            $req->bindValue(':role_id', $roleId ,\PDO::PARAM_INT);
            $req->execute();

            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }

            return true;

        } catch(\PDOException $e) {
            $this->setError($e->getMessage());
            
            return false;
        }
    }
}