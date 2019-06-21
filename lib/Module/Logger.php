<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Module;

use Core\Managers;

class Logger
{
    protected $successLoading;
    protected $file;
    protected $bdd;

    const SAUT = "\n";

    public function __construct($bdd = null)
    {
        if ($bdd !== null) {
            $this->bdd = $bdd;
        }
        $this->loadLogger();
    }

    public function addLogs(string $message = '', int $code = 400, string $file = '', int $line = 0)
    {
        $data = array(
            'code' => $code ?? 400,
            'message' => $message ?? null,
            'fichier' => $file ?? null,
            'ligne' => $line ?? 0
        );
        $manager = new Managers($this->bdd);
        if (!$manager->add("logs", $data)) {
            $this->setLogs("Impossible d'enregistrer le log en bdd : " . $manager->getError() ."\nCode : ".$data['code']."\nMessage :".$data['message']
                            ."\nFichier : ".$data['fichier']."\nLigne : ".$data['ligne']);
        }
    }

    public function setLogs(string $str)
    {
        if ($this->successLoading) {
            $handle = fopen($this->file, 'a+');
            fwrite($handle, $str.self::SAUT);
            fclose($handle);
        }
    }

    public function loadLogger()
    {
        $logsDir = __DIR__.'/../../GenarkysLogs/';
        $logsfile = $logsDir.'logs.txt';
        if (!is_dir($logsDir)) {
            if (!mkdir($logsDir)) {
                echo "Impossible de créer le dossier de logs";
                exit;
            }
        }
        if (!file_exists($logsfile)) {
            $handle = fopen($logsfile, 'w+');
            if (!$handle) {
                echo "impossible de créer le fichier de logs";
                exit;
            }
            $str = "Old logs file not found but a new logs file was created at " . date('d-m-Y H:i:s') . self::SAUT;
            fwrite($handle, $str);
            fclose($handle);
            return $this;
        } else {
            $handle = fopen($logsfile, 'a');
            $str = "Application launch at " . date('d-m-Y H:i:s') . self::SAUT;
            fwrite($handle, $str, strlen($str));
            fclose($handle);
        }
        $this->successLoading = true;
        $this->file = $logsfile;
        return $this;
    }
}
