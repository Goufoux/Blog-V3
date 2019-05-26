<?php

namespace Core;

abstract class Form
{
    private $errors;

    public function isEmail($str)
    {
        if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    public function isValid()
    {
        return (isset($this->errors)) ? false : true;
    }

    public function hasError($key)
    {
        return (isset($this->errors[$key])) ? true : false;
    }

    public function getErrors($key = null)
    {
        if ($key !== null) {
            if (!isset($this->errors[$key])) {
                return null;
            }
            return $this->errors[$key];
        }
        return $this->errors;
    }
    
    public function addErrors(string $label, string $value)
    {
        $this->errors[$label] = $value;
    }
}