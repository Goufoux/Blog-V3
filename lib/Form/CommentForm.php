<?php

namespace Form;

use Core\Form;

class CommentForm extends Form
{
    public function verif(array $datas)
    {
        $comment = isset($datas['content']);

        if (!$comment) {
            $this->addErrors('content', 'Veuillez laisser un commentaire.');
        }

        if ($comment) {
            $comment = (strlen($datas['content']) > 10 && $datas['content'] <= 150) ? true : false;

            if (!$comment) {
                $this->addErrors('content', 'Votre commentaire doit être compris entre 10 et 150 caractères.');
            }
        }
    }    
}
