<?php

namespace Manager;

use Core\Manager;
use Entity\User;

class UserManager extends Manager
{
    public function fetchAll()
    {
        $req = $this->bdd->prepare('SELECT user.*, userRole.*, role.* FROM user
                                    INNER JOIN userRole ON userRole.userRole_user = user.user_id
                                    INNER JOIN role ON role.role_id = userRole.userRole_role');
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\\User');
        try {
            $req->execute();
            
            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }

            $users = $req->fetchAll();

            foreach ($users as $key => $user) {
                $users[$key] = new User($user, true);
            }
            return $users;

        } catch(\PDOException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function findById(int $id)
    {
        $req = $this->bdd->prepare('SELECT user.*, userRole.* FROM user
                                    INNER JOIN userRole ON userRole.userRole_user = user.user_id
                                    WHERE user_id = :id');
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\\User');
        $req->bindValue(':id', $id ,\PDO::PARAM_INT);
        try {
            $req->execute();
            
            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }

            $res = $req->fetch();
            
            $user = new User($res, true);
            return $user;

        } catch(\PDOException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function connect(string $email, string $password)
    {
        $req = $this->bdd->prepare('SELECT user.*, userRole.* FROM user
                                    LEFT JOIN userRole ON userRole.userRole_user = user.user_id
                                    WHERE user_email = :email');
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\\User');
        try {
            $req->bindValue(':email', $email,\PDO::PARAM_STR);
            $req->execute();
            
            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }

            $user = $req->fetch();
            
            if ($user === false) {
                throw new \PDOException("Identifiant incorrects");
            }
            if (!password_verify($password, $user['user_password'])) {
                throw new \PDOException("Identifiants incorrects");
            }

            $user = new User($user, true);
            return $user;

        } catch(\PDOException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }
}