<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Service;

class Request
{
    protected $redirect_status;
    protected $host;
    protected $connection;
    protected $cache_control;
    protected $user_agent;
    protected $server_name;
    protected $server_addr;
    protected $server_port;
    protected $remote_addr;
    protected $request_scheme;
    protected $request_uri;
    protected $http_referer;

    public function __construct()
    {
        $this->setRedirectStatus();
        $this->setHost();
        $this->setConnection();
        $this->setCacheControl();
        $this->setUserAgent();
        $this->setServerName();
        $this->setServerAddr();
        $this->setServerPort();
        $this->setRemoteAddr();
        $this->setRequestcheme();
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
            if (!empty($ignore)) {
                $isIgnored = false;
                foreach ($ignore as $name) {
                    if ($name == $key) {
                        $isIgnored = true;
                        continue;
                    }
                }
                if ($isIgnored) {
                    continue;
                }
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
            if ($setEmpty) {
                if (empty($value)) {
                    continue;
                }
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

    public function getRedirectStatus()
    {
        return $this->redirect_status;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function getCacheControl()
    {
        return $this->cache_control;
    }

    public function getUserAgent()
    {
        return $this->user_agent;
    }

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

    public function getRequestScheme()
    {
        return $this->request_scheme;
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

    public function setRedirectStatus()
    {
        $this->redirect_status = $_SERVER['REDIRECT_STATUS'] ?? null;
        return $this;
    }

    public function setHost()
    {
        $this->host = $_SERVER['HTTP_HOST'] ?? null;
    }

    public function setConnection()
    {
        $this->connection = $_SERVER['HTTP_CONNECTION'] ?? null;
    }

    public function setCacheControl()
    {
        $this->cache_control = $_SERVER['HTTP_CACHE_CONTROL'] ?? null;
    }

    public function setUserAgent()
    {
        $this->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

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

    public function setRequestcheme()
    {
        $this->request_scheme = $_SERVER['REQUEST_SCHEME'] ?? null;
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
