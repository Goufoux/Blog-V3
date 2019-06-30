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
            'length' => [0, 1000]
        ],
        'seo_title' => [
            'length' => [0, 85]
        ],
        'seo_description' => [
            'length' => [0, 180]
        ],
        'content' => [
            'required' => true,
            'length' => [15, 5000]
        ],
        'image_alt' => [
            'length' => [0, 15]
        ]
    ];

    public function verif(array $data)
    {
        $this->requiredControl(self::data, $data);
        $this->launch(self::data, $data);
    }
}
