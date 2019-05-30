<?php

namespace Entity;

use Core\Entity;

class Role extends Entity
{
    private $id;
    private $name;
    private $description;
    private $create;
    private $update;
    private $delete;
    
    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of create
     */ 
    public function getCreate()
    {
        return $this->create;
    }

    /**
     * Set the value of create
     *
     * @return  self
     */ 
    public function setCreate($create)
    {
        $this->create = $create;

        return $this;
    }

    /**
     * Get the value of update
     */ 
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * Set the value of update
     *
     * @return  self
     */ 
    public function setUpdate($update)
    {
        $this->update = $update;

        return $this;
    }

    /**
     * Get the value of delete
     */ 
    public function getDelete()
    {
        return $this->delete;
    }

    /**
     * Set the value of delete
     *
     * @return  self
     */ 
    public function setDelete($delete)
    {
        $this->delete = $delete;

        return $this;
    }
}
