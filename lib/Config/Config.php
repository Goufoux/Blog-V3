<?php

namespace Config;

abstract class Config
{
    protected $name;
    protected $path;
    protected $error;
    protected $dev;

    public function getError()
    {
        return $this->error;
    }

    private function setError(string $error, int $code = 0)
    {
        $this->error = ['error' => $error, 'code' => $code];
        
        return $this;
    }

    public function isDev()
    {
        if ($this->getName() === 'developpement') {
            return true;
        }

        return false;
    }

    protected function setName(string $name)
    {
        $this->name = $name;
    }

    protected function run()
    {
        if (!$this->setConfigPath()) {
            die($this->getError()['error']);
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDbParam()
    {
        $dbParam = array(
            'user' => $this->readConfigFile('user') ?? null,
            'password' => $this->readConfigFile('password') ?? null,
            'host' => $this->readConfigFile('host') ?? null,
            'dbName' => $this->readConfigFile('dbName') ?? null,
            'driver' => $this->readConfigFile('driver') ?? 'mysql'
        );

        return $dbParam;
    }

    protected function readConfigFile($data)
    {
        if (empty($this->getConfigPath())) {
            return false;
        }
        $file = parse_ini_file($this->getConfigPath());
        if ($file) {
            if (isset($file[$data])) {
                return $file[$data];
            }
            return null;
        }
    }

    protected function getConfigPath(): string
    {
        return $this->path;
    }

    protected function setConfigPath(): bool
    {
        $path = __DIR__ . '/../../config/' . $this->name . '.ini';
        if (!file_exists($path)) {
            $this->path = false;
            $this->setError("Fichier de configuration not found");
            return false;
        }
        $this->path = $path;
        return true;
    }
}
