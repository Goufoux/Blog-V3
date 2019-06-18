<?php

namespace Form;

use Core\Form;

class PostForm extends Form
{
    const data = [
        'title' => [
            'required' => true,
            'length' => [3, 20]
        ],
        'chapo' => [
            'length' => [0, 50]
        ],
        'seo_title' => [
            'length' => [0, 85]
        ],
        'seo_description' => [
            'length' => [0, 180]
        ],
        'content' => [
            'required' => true,
            'length' => [15, 200]
        ],
        'image_alt' => [
            'length' => [0, 15]
        ]
    ];

    public function verifAdd(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}
