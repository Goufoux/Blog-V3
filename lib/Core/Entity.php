<?php

/**
 * @author Genarkys <quentin.roussel@genarkys.fr>
 */

namespace Core;

use Service\MyArray;
use Module\Notifications;
use Service\Response;

abstract class Entity implements \ArrayAccess
{
    public function __construct($donnees = [], $rewriteData = false)
    {
        if ($rewriteData) {
            $donnees = $this->rewriteData($donnees);
        }
        if (!empty($donnees)) {
            $this->hydrate($donnees);
        }
    }

    private function getReflectionClass($data)
    {
        $reflectionClass = new \ReflectionClass($data);
        if (!$reflectionClass) {
            $this->returnError('Impossible d\'instancier la réflection de classe.');
        }
        return $reflectionClass;
    }

    private function returnError($error)
    {
        $notif = Notifications::getInstance();
        $reponse = new Response;

        $notif->addDanger($error);

        return $reponse->referer();
    }

    private function createSingleKey($singleKey)
    {
        if (preg_match("#_#", $singleKey)) {
            $compositionSingleKey = explode('_', $singleKey);
            for ($i = 1; $i < count($compositionSingleKey); $i++) {
                $compositionSingleKey[$i] = ucfirst($compositionSingleKey[$i]);
            }
            $singleKey = implode('', $compositionSingleKey);
        }

        return $singleKey;
    }

    private function getAssociationClass($data, $className)
    {
        $class_assoc = [];
        $class_attribut = [];
        $class_assoc_attribut = [];

        foreach ($data as $key => $value) {
            $className[0] = strtolower($className[0]);
            if (preg_match("#^".$className."_#", $key)) {
                $singleKey = $this->createSingleKey(explode($className.'_', $key)[1]);
                $class_attribut[$singleKey] = $value;
                continue;
            }
            
            $tmpClassAssocName = explode("_", $key)[0];
            if (!in_array($tmpClassAssocName, $class_assoc)) {
                $class_assoc[] = $tmpClassAssocName;
            }
            $singleKey = $this->createSingleKey(explode($tmpClassAssocName.'_', $key)[1]);
            $class_assoc_attribut[$tmpClassAssocName][$singleKey] = $value;
        }

        $final = [
            'classAssoc' => $class_assoc,
            'classAssocAttribut' => $class_assoc_attribut,
            'classAttribut' => $class_attribut
        ];
        
        return $final; 
    }

    private function entityMapping($class_assoc, $class_assoc_attribut)
    {
        $ready_assoc = [];

        foreach ($class_assoc as $key => $assoc) {
            $class = 'Entity\\'.ucfirst($assoc);
            if (!class_exists($class)) {
                $this->returnError("La classe n'existe pas : $class");
            }

            $ready_assoc[$assoc] = new $class($class_assoc_attribut[$assoc]);
        }

        return $ready_assoc;
    }

    private function rewriteData($data)
    {
        $reflectionClass = $this->getReflectionClass($data);
        
        $className = explode('Entity\\', $reflectionClass->getName())[1];
        
        $loadAssociationClass = $this->getAssociationClass($data, $className); 

        /* Attribut de l'entité principale */
        $class_attribut = $loadAssociationClass['classAttribut'];
        /* Attribut des classes mappées */
        $class_assoc_attribut = $loadAssociationClass['classAssocAttribut'];
        /* Nom des classes mappées */
        $class_assoc = $loadAssociationClass['classAssoc'];
        /* Classe finale */
        $ready_assoc = $this->entityMapping($class_assoc, $class_assoc_attribut);


        foreach ($ready_assoc as $n => $v) {
            $class_attribut[$n] = $v;
        }

        return $class_attribut;
    }
    
    public function isNew()
    {
        return empty($this->id);
    }
    
    public function getErreurs()
    {
        return isset($this->erreurs) ? $this->erreurs : false;
    }
    
    public function getEntityId()
    {
        return $this->entityId;
    }
    
    public function setEntityId($id)
    {
        $this->entityId = (int) $id;
    }
    
    public function hydrate($donnees)
    {
        foreach ($donnees as $attribut => $valeur) {
            $methode = 'set'.ucfirst($attribut);
            if (is_callable([$this, $methode])) {
                $this->$methode($valeur);
            }
        }
    }
    
    public function offsetGet($var)
    {
        if (isset($this->$var) && is_callable([$this, $var])) {
            return $this->$var();
        } else {
            if (!isset($this->$var)) {
				return null;
			}
			return $this->$var;
        }
    }
    
    public function offsetSet($var, $value)
    {
        $method = 'set'.ucfirst($var);
        if (isset($this->$var) && is_callable([$this, $method])) {
            $this->$method($value);
        }
    }
    
    public function offsetExists($var)
    {
        return isset($this->$var) && is_callable([$this, $var]);
    }
    
    public function offsetUnset($var)
    {
        throw new \Exception("Impossible de supprimer une valeur.");
    }
}
