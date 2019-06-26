<?php

namespace Manager;

use Core\Manager;
use Entity\UserRole;

class UserRoleManager extends Manager
{
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