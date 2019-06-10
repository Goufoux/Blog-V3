<?php

namespace Form;

use Core\Form;

class PostForm extends Form
{
    public function verifAdd(array $datas)
    {
        $title = isset($datas['title']);
        $chapo = isset($datas['chapo']);
        $seo_title = isset($datas['seo_title']);
        $seo_description = isset($datas['seo_description']);
        $content = isset($datas['content']);
        $image_alt = isset($datas['image_alt']);

        if (!$title) {
            $this->addErrors("title", "Champs obligatoire");
        }

        if (!$content) {
            $this->addErrors("content", "Champs obligatoire");
        }
    }

    public function verifUpdate(array $datas)
    {
        $title = isset($datas['title']);
        $chapo = isset($datas['chapo']);
        $seo_title = isset($datas['seo_title']);
        $seo_description = isset($datas['seo_description']);
        $content = isset($datas['content']);
        $image_alt = isset($datas['image_alt']);

        if (!$title) {
            $this->addErrors("title", "Champs obligatoire");
        }

        if (!$content) {
            $this->addErrors("content", "Champs obligatoire");
        }    
    }
}
