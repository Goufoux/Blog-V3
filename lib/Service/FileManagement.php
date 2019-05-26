<?php

namespace Service;

class FileManagement
{
    protected const PATH = __DIR__.'/../../docs';
    protected const MAX_SIZE = 2000000;
    protected const ALL_EXT = [
        'png', 'jpeg', 'jpg', 'gif'
    ];
    protected const IMG_EXT = [
        'png', 'jpeg', 'jpg', 'gif'
    ];
    protected const TYPE = [
        'img', 'pdf'
    ];
    private $error;
    private $filename;

    public function deleteFile($filename, $type)
    {
        if (!$this->dirExist(self::PATH)) {
            return false;
        }
        if (!in_array($type, self::TYPE)) {
            $this->setError("Le type de fichier {".$type."} n'est pas pris en charge.");
            return false;
        }

        $path = self::PATH.'/'.$type.'/'.$filename;

        if (!file_exists($path)) {
            return true;
        }

        if (!unlink($path)) {
            $this->setError("Impossible de supprimer le fichier.");
            return false;
        }
        return true;
    }

    public function uploadFile($file, $name, $type)
    {
        if (!$this->dirExist(self::PATH)) {
            return false;
        }

        if (empty($file) || !is_array($file)) {
            $this->setError("Aucun fichier ou fichier invalide.");
            return false;
        }

        
        if (!in_array($type, self::TYPE)) {
            $this->setError("Le type de fichier {".$type."} n'est pas pris en charge.");
            return false;
        }
        
        switch ($file['error']) {
            case 4:
                $this->setError("Aucun fichier");
                return false;
            default:
                break;
        }

        if ($file['size'] > self::MAX_SIZE) {
            $this->setError("Le fichier est trop volumineux, il doit être inférieur à 2Mo.");
            return false;
        }

        switch ($type) {
            case 'img':
                $ext = $this->getExtension($file['type']);
                if (!$ext) {
                    return false;
                }
                if (!in_array($ext, self::IMG_EXT)) {
                    $ext_autorise = implode(", ", self::IMG_EXT);
                    $this->setError("Extension de l'image non prise en charge {".$ext."}<br/>Extension autorisée : " . $ext_autorise);
                    return false;
                }
                break;
            default:
                $this->setError("Le type de fichier n'est pas défini.");
                return false;
        }

        $this->setFilename($name.'.'.$ext);
        $dir = self::PATH."/".$type."/";
        
        if (!$this->dirExist($dir)) {
            return false;
        }

        if (!move_uploaded_file($file['tmp_name'], $dir.$this->getFilename())) {
            $this->setError("Impossible de télécharger le fichier");
            return false;
        }
        return true;
    }

    public function getExtension(string $type)
    {
        if (!preg_match("#/#", $type)) {
            $this->setError("Impossible de récupérer l'extension.");
            return false;
        }
        
        $temp = explode("/", $type);

        if (empty($temp[1]) || !isset($temp[1])) {
            $this->setError("Format d'extension non reconnu.");
            return false;
        }

        return $temp[1];
    }

    public function dirExist(string $dir)
    {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777)) {
                $this->setError("Impossible de créé le dossier cible {".$dir."}");
                return false;
            } else {
                return true;
            }
        }
        return true;
    }

    /**
     * Get the value of error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set the value of error
     *
     * @return  self
     */
    private function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get the value of filename
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set the value of filename
     *
     * @return  self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }
}
