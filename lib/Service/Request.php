<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Service;

class Request
{
    protected $server_name;
    protected $server_addr;
    protected $server_port;
    protected $remote_addr;
    protected $request_uri;
    protected $http_referer;

    public function __construct()
    {
        $this->setServerName();
        $this->setServerAddr();
        $this->setServerPort();
        $this->setRemoteAddr();
        $this->setRequestUri();
        $this->setHttpReferer();
    }

    public function hasData($key = false): bool
    {
        if ($key !== false) {
            return isset($_GET[$key]);
        }

        return !empty($_GET);
    }

    public function getAllData(bool $setEmpty = true, array $ignore = [])
    {
        if (!$this->hasData()) {
            return null;
        }

        $array = array();

        foreach ($_GET as $key => $value) {
            if ($setEmpty && empty($value)) {
                continue;
            }
            if (!empty($ignore) && in_array($key, $ignore)) {
                continue;
            }
            $array[$key] = htmlspecialchars($value);
        }

        return $array;
    }

    public function getAllPost($setEmpty = true)
    {
        if (!$this->hasPost()) {
            return null;
        }

        $array = array();

        foreach ($_POST as $key => $value) {
            if ($setEmpty && empty($value)) {
                continue;
            }
            if (is_array($value)) {
                $array[$key] = $value;
                continue;
            }
            $array[$key] = htmlspecialchars($value);
        }

        return $array;
    }

    public function getData(string $key = null)
    {
        if (!$this->hasData()) {
            return null;
        }

        if ($key != null) {
            if (!isset($_GET[$key]) || empty($_GET[$key])) {
                return null;
            }
            return htmlspecialchars($_GET[$key]);
        }

        return $_GET;
    }

    public function hasPost($key = false)
    {
        if ($key !== false) {
            return isset($_POST[$key]);
        }

        return !empty($_POST);
    }

    public function getPost(string $key = null)
    {
        if (!$this->hasPost()) {
            return null;
        }

        if ($key != null) {
            if (!isset($_POST[$key]) || empty($_POST[$key])) {
                return null;
            }
            return htmlspecialchars($_POST[$key]);
        }

        return $_POST;
    }

    /* Getters */

    public function getServerName()
    {
        return $this->server_name;
    }

    public function getServerAddr()
    {
        return $this->server_addr;
    }

    public function getServerPort()
    {
        return $this->server_port;
    }

    public function getRemoteAddr()
    {
        return $this->remote_addr;
    }

    public function getRequestUri()
    {
        return $this->request_uri;
    }

    public function getHttpReferer()
    {
        return $this->http_referer;
    }

    /* Setters */

    public function setServerName()
    {
        $this->server_name = $_SERVER['SERVER_NAME'] ?? null;
    }

    public function setServerAddr()
    {
        $this->server_addr = $_SERVER['SERVER_ADDR'] ?? null;
    }

    public function setServerPort()
    {
        $this->server_port = $_SERVER['SERVER_PORT'] ?? null;
    }

    public function setRequestUri()
    {
        $this->request_uri = $_SERVER['REQUEST_URI'] ?? null;
    }

    public function setRemoteAddr()
    {
        $this->remote_addr = $_SERVER['REMOTE_ADDR'] ?? null;
    }

    public function setHttpReferer()
    {
        $this->http_referer = $_SERVER['HTTP_REFERER'] ?? null;
    }
}
