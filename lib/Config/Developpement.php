<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Config;

class Developpement extends Config
{
    public function __construct()
    {
        $this->setName('developpement');
        $this->run();
    }
}