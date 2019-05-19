<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Config;

class Production extends Config
{
    public function __construct()
    {
        $this->setName('production');
        $this->run();
    }
}