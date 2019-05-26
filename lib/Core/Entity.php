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

    private function rewriteData($data)
    {
        $notif = Notifications::getInstance();
        $reponse = new Response;
        $reflectionClass = new \ReflectionClass($data);
        if (!$reflectionClass) {
            $notif->addDanger("Impossible d'instancier la réflection de classe.");
            $reponse->referer();
        }
        
        $className = explode('Entity\\', $reflectionClass->getName())[1];
        
        /* Attribut de l'entité principale */
        $class_attribut = array();
        /* Attribut des classes mappées */
        $class_assoc_attribut = array();
        /* Nom des classes mappées */
        $class_assoc = array();
        /* Classe finale */
        $ready_assoc = array();

        foreach ($data as $key => $value) {
            $className[0] = strtolower($className[0]);
            if (preg_match("#^".$className."_#", $key)) {
                $singleKey = explode($className.'_', $key)[1];
                if (preg_match("#_#", $singleKey)) {
                    $compositionSingleKey = explode('_', $singleKey);
                    for ($i = 1; $i < count($compositionSingleKey); $i++) {
                        $compositionSingleKey[$i] = ucfirst($compositionSingleKey[$i]);
                    }
                    $singleKey = implode('', $compositionSingleKey);
                }
                $class_attribut[$singleKey] = $value;
            } else {
                $tmpClassAssocName = explode("_", $key);
                $tmpClassAssocName = $tmpClassAssocName[0];
                if (!in_array($tmpClassAssocName, $class_assoc)) {
                    $class_assoc[] = $tmpClassAssocName;
                }
                $singleKey = explode($tmpClassAssocName.'_', $key)[1];
                if (preg_match("#_#", $singleKey)) {
                    $compositionSingleKey = explode('_', $singleKey);
                    for ($i = 1; $i < count($compositionSingleKey); $i++) {
                        $compositionSingleKey[$i] = ucfirst($compositionSingleKey[$i]);
                    }
                    $singleKey = implode('', $compositionSingleKey);
                }
                $class_assoc_attribut[$tmpClassAssocName][$singleKey] = $value;
            }
        }
        /* Parcours des entités mappés */

        foreach ($class_assoc as $key => $assoc) {
            $class = 'Entity\\'.ucfirst($assoc);
            if (!class_exists($class)) {
                $notif->addDanger("La classe " . ucfirst($class) . " n\'a pas été trouvée pour l'association d'entitée.");
                $reponse->referer();
                exit;
            }

            foreach ($ready_assoc as $rClass => $value) {
                if (preg_match("#".ucfirst($assoc)."#", ucfirst($rClass))) {
                    $method = 'set'.ucfirst($assoc);
                    if (is_callable($rClass, $method)) {
                        if (method_exists($ready_assoc[$rClass], $method)) {
                            $ready_assoc[$rClass]->$method(new $class($class_assoc_attribut[$assoc]));
                            continue;
                        } else {
                            for ($i = 0; $i < count($class_assoc); $i++) {
                                if (!in_array($class_assoc[$i], [$rClass, ucfirst($assoc)])) {
                                    $t = 'get'.ucfirst($class_assoc[$i]);
                                    if (method_exists($ready_assoc[$rClass], $t)) {
                                        $sClass = $ready_assoc[$rClass]->$t();
                                        if (method_exists($sClass, $method)) {
                                            $sClass->$method(new $class($class_assoc_attribut[$assoc]));
                                        }
                                    } else {
                                        $notif->addWarning("Méthode d'une entité non trouvée : " . $t);
                                    }
                                }
                            }
                        }
                    } else {
                        $notif->addWarning("Méthode d'une entité non trouvée : " . $t);
                    }
                }
            }
            $ready_assoc[$assoc] = new $class($class_assoc_attribut[$assoc]);
        }

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
