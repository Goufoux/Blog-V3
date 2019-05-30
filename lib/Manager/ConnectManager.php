<?php

namespace Manager;

use Core\Manager;

class ConnectManager extends Manager
{
    public function connect(string $email, string $password)
    {
        $req = $this->bdd->prepare('SELECT user.*, userRole.* FROM user
                                    LEFT JOIN userRole ON userRole.userRole_user = user.user_id
                                    WHERE user_email = :email');
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');
        try {
            $req->bindValue(':email', $email, \PDO::PARAM_STR);
            $req->execute();

            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }

            $user = $req->fetch();
            var_dump($user);

            if ($user === false) {
                throw new \PDOException("No user with this email");
            }

            if (!password_verify($password, $user['user_password'])) {
                throw new \PDOException("Password is not valid");
            }

        } catch (\PDOException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }
}