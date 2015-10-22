<?php

namespace Melyssa\Forms;

use Melyssa\Session;

abstract class Element
{
    public $displayErrors = false;
    public $errorContainer = null;
    protected $type = '';
    protected $attributes = array();
    protected $hasLabel = false;
    protected $labelText;
    protected $hasLabelAttributes = false;
    protected $labelAttributes = array();
    protected $error = null;
    protected $keepValues = false;
    protected $formName;

    public function __construct($attributes, $formName, $keepValues = false)
    {
        $this->formName = $formName;
        $this->keepValues = $keepValues;
        $this->sessionHandler = Session::getInstance();
        $this->attributes = $attributes;
        $this->setValue();
        $this->setError();
        $this->setLabelAttributes();
        $this->cleanUpAttributes();
    }

    protected function setValue()
    {
        // Formato da sessão:
        // FormName:FieldName:Value
        // FormName:FieldName:Error
        $sessionName = $this->formName .':'. $this->attributes['name'];
        if ($this->sessionHandler->checkSession($sessionName)) {
            $this->attributes['value'] = $this->sessionHandler->getSession($sessionName);
            if (false === $this->keepValues) {
                $this->sessionHandler->destroySession($sessionName);
            }
        }
    }

    private function setError()
    {
        $sessionName = $this->formName .':'. $this->attributes['name'] . '-error';
        if ($this->sessionHandler->checkSession($sessionName)) {
            $this->error = $this->sessionHandler->getSession($sessionName);
            $this->sessionHandler->destroySession($sessionName);
            if (isset($this->attributes['error-container'])) {
                $this->error = sprintf($this->attributes['error-container'], $this->error);
                unset($this->attributes['error-container']);
            } elseif (null !== $this->errorContainer) {
                $this->error = sprintf($this->errorContainer, $this->error);
            }
        }
    }

    private function setLabelAttributes()
    {
        if (isset($this->attributes['label'])) {
            $this->hasLabel = true;
            if (is_array($this->attributes['label'])) {
                $this->hasLabelAttributes = true;
                $this->labelAttributes = $this->attributes['label'];
                $this->labelText = $this->labelAttributes['text'];
                unset($this->labelAttributes['text']);
            } else {
                $this->labelText = $this->attributes['label'];
            }
            unset($this->attributes['label']);
        }
    }
    
    private function cleanUpAttributes()
    {
        foreach (array('error-container', 'type') as $attribute) {
            if (isset($this->attributes[$attribute])) {
                unset($this->attributes[$attribute]);
            }
        }
    }

    public function parseAttributes(array $attributes)
    {
        $return = '';
        foreach ($attributes as $key => $val) {
            $return .= ' ' . $key . '="' . $val . '"';
        }
        return $return;
    }

    public function parseLabel()
    {
        if ($this->hasLabel === true and $this->hasLabelAttributes === true) {
            $label = sprintf('<label%s>%s</label>', $this->parseAttributes($this->labelAttributes), $this->labelText);
            return $label;
        } elseif ($this->hasLabel === true) {
            return '<label>' . $this->labelText . '</label>';
        } else {
            return '';
        }
    }

    public function parseElement()
    {
        return sprintf('<input type="%s"%s />', $this->type, $this->parseAttributes($this->attributes));
    }

    private function parseErrors()
    {
        if (true === $this->displayErrors and $this->error !== null) {
            return $this->error;
        }
        return '';
    }

    public function getElement()
    {
        $element = $this->parseLabel() . $this->parseElement() . $this->parseErrors();
        return $element;
    }
    
    public function getErrors()
    {
        return $this->error;
    }
}
