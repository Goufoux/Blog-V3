<?php

namespace Core;

abstract class Form
{
    private $errors;

    public function requiredControl($waitingData, $data)
    {
        foreach ($waitingData as $key => $value) {
            if (!isset($data[$key]) && (isset($value['required']) && $value['required'] == true)) {
                $this->addErrors($key, 'Champs obligatoire.');
            }
        }
    }

    public function launch($waitingData, $data)
    {
        foreach ($data as $key => $value) {
            if (isset($waitingData[$key])) {
                foreach ($waitingData[$key] as $controlName => $required) {
                    switch ($controlName) {
                        case 'length':
                            if (!$this->lengthControl($value, $required[0], $required[1])) {
                                $this->addErrors($key, 'Le champ doit être compris entre ' . $required[0] . ' et ' . $required[1] . ' caractères.');
                                continue 2;
                            }
                            break;
                        case 'text':
                            if (!$this->isText($value, $required)) {
                                $this->addErrors($key, 'Le champ ne peut être que composé que des caractères suivants : A-Z a-z 0-9.');
                                continue 2;
                            }
                            break;
                        case 'email':
                            if (!$this->isEmail($value)) {
                                $this->addErrors($key, 'Cette adresse email ne semble pas valide.');
                                continue 2;
                            }
                            break;
                        case 'equals':
                            if (!$this->isEquals($value, $data[$required])) {
                                $this->addErrors($key, 'Le champ doit être le même que ' . $waitingData[$required]['translate']);
                                continue 2;
                            }
                            break;
                    }
                }
            }
        }
    }

    public function isEquals($value, $otherValue)
    {
        return $value === $otherValue;
    }

    public function isText(string $str, string $case = 'default')
    {
        switch ($case) {
            case 'password':
                $result = preg_match('/^[a-zA-Z0-9][a-zA-Z0-9]/', $str);
                break;
            default:
                $result = preg_match('/^[a-zA-Z0-9][a-zA-Z0-9]/', $str) == 1;
                break; 
        }

        return $result == 1;
    }

    public function lengthControl(string $str, int $minLength, int $maxLength)
    {
        return (strlen($str) >= $minLength && strlen($str) <= $maxLength);
    }

    public function isEmail($str)
    {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Form is valid if no errors has stock
     *
     * @return boolean
     */
    public function isValid()
    {
        return !isset($this->errors);
    }

    /**
     * return true if errors is not empty, false if is empty
     *
     * @param string $key
     * @return boolean
     */
    public function hasError(string $key)
    {
        return !empty($this->errors[$key]);
    }

    /**
     * get specific error in function of key, not key for all errors
     *
     * @param string $key
     * @return void
     */
    public function getErrors(string $key = null)
    {
        if ($key !== null) {
            if (!isset($this->errors[$key])) {
                return null;
            }
            return $this->errors[$key];
        }
        return $this->errors;
    }
    
    /**
     * add an error with a label
     *
     * @param string $label
     * @param string $value
     * @return void
     */
    public function addErrors(string $label, string $value)
    {
        $this->errors[$label] = $value;

        return $this;
    }
}