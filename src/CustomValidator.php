<?php

namespace JiJiHoHoCoCo\IchiValidation;

abstract class CustomValidator
{
    private $attribute,$value;

    abstract public function rule();

    abstract public function showErrorMessage();

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }

    public function getAttribute()
    {
        return $this->attribute;
    }
}
