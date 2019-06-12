<?php

namespace Core;

abstract class Form
{
    private $errors;

    public function isEmail($str)
    {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    public function isValid()
    {
        return !isset($this->errors);
    }

    public function hasError($key)
    {
        return !empty($this->errors[$key]);
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