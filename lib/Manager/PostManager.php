<?php

namespace Manager;

use Core\Manager;
use Entity\Post;

class PostManager extends Manager
{
    public function fetchAll(array $flags = array())
    {
        $sql = $this->flagsToSql($flags);
        $req = $this->bdd->prepare('SELECT post.*, user.* FROM post
                                    INNER JOIN user ON user.user_id = post.post_user ORDER BY post_created_at DESC '.$sql);
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
}
