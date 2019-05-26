<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Module;

class Notifications
{
    protected static $instance;
    protected $created_at;
    const CODE = [
        '404' => ['danger', 'Page non trouvée'],
        '403' => ['warning', 'Accès refusé'],
        '500' => ['danger', 'Une erreur est survenue'] 
    ];

    public static function getInstance(): Notifications
    {
        if (!isset(self::$instance)) {
            self::$instance = new Notifications();
        }
        return self::$instance;
    }

    protected $wrapper = '<div class="alert alert-%s alert-dismissible fade show my-2 col-12" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <p class="col-12">
                            <strong>%s</strong>
                            </p>
                        </div>';

    public function __construct()
    {
        if(!array_key_exists('notifications', $_SESSION)) {
            $_SESSION['notifications'] = array();
            $now = new \DateTime();
            $this->created_at = $now->format('d-m-Y H:i:s');
        }
        return $this;
    }

    public function default(string $code, ?string $message = null, ?string $type = null, ?bool $dev = null)
    {
        if (!isset(self::CODE[$code])) {
            return false;
        }

        if ($message === null || $dev === false) {
            $message = self::CODE[$code][1];
        }

        if ($type === null || $dev === false) {
            $type = self::CODE[$code][0];
        }
        
        $this->add($message, $type);
        return $this;
    }

    /**
     * add()
     *
     * @param string $message
     * @param string $type
     * @param string $icon
     * @return Notifications
     */
    public function add($message, $type = 'info', $icon = null): Notifications
    {
        if($icon !== null) {
            $message = "<i class=\"$icon\"></i> $message";
        }
        if(!array_key_exists($type, $_SESSION['notifications'])) {
            $_SESSION['notifications'][$type] = array();
        }
        $_SESSION['notifications'][$type][] = $message;
        return $this;
    }

    public function addSuccess($message, $type = 'success', $icon = 'fas fa-check-circle')
    {
        $this->add($message, $type, $icon);
        return $this;
    }

    public function addWarning($message, $type = 'warning', $icon = 'fas fa-exclamation-triangle')
    {
        $this->add($message, $type, $icon);
        return $this;
    }

    public function addDanger($message, $type = 'danger', $icon = 'fas fa-times')
    {
        $this->add($message, $type, $icon);
        return $this;
    }

    public function addInfo($message, $type = 'info', $icon = 'fas fa-info')
    {
        $this->add($message, $type, $icon);
        return $this;
    }

    public function getMessages($type = null): array
    {
        if ($type === null) {
            $data = $_SESSION['notifications'];
        } else {
            $data = $_SESSION['notifications'][$type] ?? array();
        }
        return $data;
    }

    public function hasMessages($type = null)
    {
        return (count($this->getMessages($type)) > 0);
    }

    public function clearMessages($type = null)
    {
        if ($type === null) {
            $_SESSION['notifications'] = array();
        } else {
            unset($_SESSION['notifications'][$type]);
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
            $this->wrapper,
            $type,
            $message
        );
    }
}