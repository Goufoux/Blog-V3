<?php

namespace Form;

use Core\Form;

class PostForm extends Form
{
    public function verifAdd(array $datas)
    {
        $title = isset($datas['title']) ? true : false;
        $chapo = isset($datas['chapo']) ? true : false;
        $seo_title = isset($datas['seo_title']) ? true : false;
        $seo_description = isset($datas['seo_description']) ? true : false;
        $content = isset($datas['content']) ? true : false;
        $image_alt = isset($datas['image_alt']) ? true : false;

        if (!$title) {
            $this->addErrors("title", "Champs obligatoire");
        }

        if (!$content) {
            $this->addErrors("content", "Champs obligatoire");
        }
    }

    public function verifUpdate(array $datas)
    {
        $title = isset($datas['title']) ? true : false;
        $chapo = isset($datas['chapo']) ? true : false;
        $seo_title = isset($datas['seo_title']) ? true : false;
        $seo_description = isset($datas['seo_description']) ? true : false;
        $content = isset($datas['content']) ? true : false;
        $image_alt = isset($datas['image_alt']) ? true : false;

        if (!$title) {
            $this->addErrors("title", "Champs obligatoire");
        }

        if (!$content) {
            $this->addErrors("content", "Champs obligatoire");
        }    
    }
}
