<?php

namespace Manager;

use Core\Manager;
use Entity\Post;

class PostManager extends Manager
{
    public function findById(int $id)
    {
        $req = $this->bdd->prepare('SELECT post.*, user.* FROM post
                                    INNER JOIN user ON user.user_id = post.post_user
                                    WHERE post_id = :id');
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\\Post');
        $req->bindValue(':id', $id ,\PDO::PARAM_INT);
        try {
            $req->execute();
            
            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }

            $res = $req->fetch();
            
            $post = new Post($res, true);
            return $post;

        } catch(\PDOException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function fetchAll()
    {
        $req = $this->bdd->prepare('SELECT post.*, user.* FROM post
                                    INNER JOIN user ON user.user_id = post.post_user ORDER BY post_created_at DESC');
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\\Post');
        try {
            $req->execute();
            
            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }

            $posts = $req->fetchAll();

            foreach ($posts as $key => $post) {
                $posts[$key] = new Post($post, true);

            }
            
            return $posts;

        } catch(\PDOException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function findByUser(int $id)
    {
        $req = $this->bdd->prepare('SELECT post.*, user.* FROM post
                                    INNER JOIN user ON user.user_id = post.post_user
                                    WHERE post_user = :id');
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\\Post');
        $req->bindValue(':id', $id ,\PDO::PARAM_INT);
        try {
            $req->execute();
            
            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }

            $posts = $req->fetchAll();

            foreach ($posts as $key => $post) {
                $posts[$key] = new Post($post, true);

            }
            
            return $posts;

        } catch(\PDOException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }
}
