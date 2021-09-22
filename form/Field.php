<?php

namespace app\core\form;

use app\core\Model;

class Field extends BaseField
{
    public const TYPE_TEXT= 'text';
    public const TYPE_EMAIL= 'email';
    public const TYPE_PASSWORD= 'password';

    public string $type;
    public Model $model;
    public string $attribute;

    public function __construct($model, $attribute)
    {
        $this->type= self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }

    public function passwordField()
    {
        $this->type= self::TYPE_PASSWORD;
        return $this;
    }

    public function emailField()
    {
        $this->type= self::TYPE_EMAIL;
        return $this;
    }

    public function renderInput()
    {
        return sprintf('<input type="%s" name="%s" value="%s" class="form-control %s">',
        $this->type, 
        $this->attribute,
        $this->model->{$this->attribute},
        $this->model->hasErrors($this->attribute) ? ' is-invalid' : '',
    );
    }
}