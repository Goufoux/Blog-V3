<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Service;

class Error
{
    protected static $instance;
    
    public static function getInstance(): Error
    {
        if (!isset(self::$instance)) {
            self::$instance = new Error();
        }
        return self::$instance;
    }
    protected $messageWrapper = '<div class="alert alert-%s alert-dismissible fade show col-12" role="alert">
                                    <strong>%s</strong>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>';
    public function __construct()
    {
        if (!array_key_exists('errors', $_SESSION)) {
            $_SESSION['errors'] = array();
        }
        return $this;
    }
    public function addSuccess($message, $iconClass = 'fa fa-check-circle'): Error
    {
        $this->add($message, 'success', $iconClass);
        return $this;
    }
    public function addInfo($message, $iconClass = 'fa fa-info-circle'): Error
    {
        $this->add($message, 'info', $iconClass);
        return $this;
    }
    public function addWarning($message, $iconClass = 'fa fa-exclamation-triangle'): Error
    {
        $this->add($message, 'warning', $iconClass);
        return $this;
    }
    public function addDanger($message, $iconClass = 'fa fa-times'): Error
    {
        $this->add($message, 'danger', $iconClass);
        return $this;
    }
    public function add($message, $type = 'info', $iconClass = null): Error
    {
        if ($iconClass !== null) {
            $message = "<i class=\"$iconClass\"></i> $message";
        }
        if (!array_key_exists($type, $_SESSION['errors'])) {
            $_SESSION['errors'][$type] = array();
        }
        $_SESSION['errors'][$type][] = $message;
        return $this;
    }
    public function getMessages($type = null): array
    {
        if ($type === null) {
            $data = $_SESSION['errors'];
        } else {
            $data = $_SESSION['errors'][$type] ?? array();
        }
        return $data;
    }
    public function hasMessages($type = null)
    {
        return (count($this->getMessages($type)));
    }
    public function clearMessages($type = null): Error
    {
        if ($type === null) {
            $_SESSION['errors'] = array();
        } else {
            unset($_SESSION['errors'][$type]);
        }
        return $this;
    }
    public function display($type = null): string
    {
        $output = '';
        $messages = $this->getMessages($type);
        if ($type !== null) {
            foreach ($messages as $message) {
                $output .= $this->formatMessage($message, $type);
            }
        } else {
            foreach ($messages as $msgType => $msgs) {
                foreach ($msgs as $message) {
                    $output .= $this->formatMessage($message, $msgType);
                }
            }
        }
        
        $this->clearMessages($type);
        return $output;
    }
    protected function formatMessage($message, $type): string
    {
        return sprintf(
            $this->messageWrapper,
            $type,
            $message
        );
    }
}
