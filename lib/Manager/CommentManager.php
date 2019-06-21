<?php

namespace Manager;

use Core\Manager;
use Entity\Comment;

class CommentManager extends Manager
{
    public function fetchAll()
    {

    }

    public function findByState(int $state = 0)
    {
        $req = $this->bdd->prepare('SELECT comment.*, user.* FROM comment
                                    INNER JOIN user ON user.user_id = comment.comment_user
                                    WHERE comment_state = :state');
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\\Comment');
        $req->bindValue(':state', $state ,\PDO::PARAM_INT);
        try {
            $req->execute();
            
            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }

            $comments = $req->fetchAll();

            foreach ($comments as $key => $comment) {
                $comments[$key] = new Comment($comment, true);

            }
            
            return $comments;

        } catch(\PDOException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function findByPost(int $postId)
    {
        $req = $this->bdd->prepare('SELECT comment.*, user.* FROM comment
                                    INNER JOIN user ON user.user_id = comment.comment_user
                                    WHERE comment_post = :post AND comment_state = 1 ORDER BY comment_created_at DESC');
        $req->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\\Comment');
        $req->bindValue(':post', $postId ,\PDO::PARAM_INT);
        try {
            $req->execute();
            
            if (!$this->successRequest($req)) {
                throw new \PDOException($this->errorCode($req));
            }

            $comments = $req->fetchAll();

            foreach ($comments as $key => $comment) {
                $comments[$key] = new Comment($comment, true);

            }
            
            return $comments;

        } catch(\PDOException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }
}
