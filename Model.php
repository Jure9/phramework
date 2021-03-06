<?php

namespace app\core;

abstract class Model
{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';

    public $errors= [];

    public function loadData($data)
    {
        foreach($data as $key => $value)
        {
            if(property_exists($this, $key))
            {
                $this->$key= $value;
            }
        }
    }

    public function attributes()
    {
        return [];
    }

    public function labels()
    {
        return [];
    }

    abstract public function rules();

    public function validate()
    {
        foreach($this->rules() as $attribute => $rules)
        {
            $value= $this->$attribute;
            foreach($rules as $rule)
            {
                $ruleName= $rule;
                if(!is_string($rule))
                {
                    $ruleName= $rule[0];
                }
                
                if($ruleName === self::RULE_REQUIRED && !$value)
                {
                    $this->addError($attribute, self::RULE_REQUIRED);
                }

                if($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL))
                {
                    $this->addError($attribute, self::RULE_EMAIL);
                }

                if($ruleName === self::RULE_MIN && strlen($value) < $rule['min'])
                {
                    $this->addError($attribute, self::RULE_MIN, $rule);
                }

                if($ruleName === self::RULE_MAX && strlen($value) > $rule['max'])
                {
                    $this->addError($attribute, self::RULE_MAX, $rule);
                }

                if($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']})
                {
                    $this->addError($attribute, self::RULE_MATCH);
                }

                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $db = Application::$app->db;
                    $statement = $db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :$uniqueAttr");
                    $statement->bindValue(":$uniqueAttr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        // $this->addErrorByRule($attribute, self::RULE_UNIQUE);
                        $this->addError($attribute, self::RULE_UNIQUE);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    public function addError($attribute, $rule, $params= [])
    {
        $message= $this->errorMessage()[$rule] ?? '';
        
        foreach($params as $key => $value)
        {
            $message= str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function errorMessage()
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This must be a valid email',
            self::RULE_MIN => 'Minimum length of this field must be {min}',
            self::RULE_MAX => 'Maximum length of this field must be {max}',
            self::RULE_MATCH => 'The passwords does not match',
            self::RULE_UNIQUE => 'User with this email already exists'
        ];
    }

    public function hasErrors($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError($attribute)
    {
        if($this->hasErrors($attribute))
        { 
           return $this->errors[$attribute][0]; 
        }

        return '';
    }
}