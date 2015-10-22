<?php

namespace Melyssa\Forms;

use Melyssa\Loader;
use Melyssa\Exception;

class Form
{

    private $inputs = array();
    private $elements = array();
    private $errors = array();
    private $fullForm;
    private $configs = array();
    private $openTag = null;
    private $closeTag = null;
    private $keepValues = false;
    private $template = null;

    public function __construct($file, $keepValues = false, $replaceConfigs = array(), $template = null)
    {
        $this->keepValues = $keepValues;
        $this->template = $template;
        $this->loader = new Loader;
        $this->loadConfigs($file);
        $this->replaceConfigs($replaceConfigs);
        $this->fetchInputs();
    }

    private function loadConfigs($file)
    {
        if (is_string($file)) {
            // Carregando configurações:
            $configs = $this->loader->loadConfigs(APP_PATH . 'Configs/' . ENVIRONMENT . '/Forms/' . $file . '.php');
            // Setando campos do formulário:
            $this->inputs = $configs['inputs'];
            $this->configs = $configs['form'];
        } elseif (is_array($file)) {
            $this->inputs = $file['inputs'];
            $this->configs = $file['form'];
        }
    }
    
    private function replaceConfigs($arrayToReplace)
    {
        if(count($arrayToReplace) > 0){
            foreach($arrayToReplace as $key => $val){
                $this->configs['attributes'][$key] = $val;
            }
        }
        
        return true;
    }

    private function fetchInputs()
    {
        $this->fullForm = $this->openForm();
        foreach ($this->inputs as $input) {
            $class = 'Melyssa\\Forms\\' . $input['type'];
            $element = new $class($input, $this->configs['form-name'], $this->keepValues);
            $this->hydrateConfigs($element);
            $this->elements[$input['name']] = $element->getElement();
            $this->errors[$input['name']] = $element->getErrors();
            $this->fullForm .= $this->elements[$input['name']];
        }
        $this->fullForm .= $this->closeForm();
    }

    private function hydrateConfigs(Element $element)
    {
        if (isset($this->configs['display-errors'])) {
            $element->displayErrors = $this->configs['display-errors'];
        }
    }

    public function openForm()
    {
        if ($this->openTag === null) {
            $this->openTag = '<form';
            if (isset($this->configs['attributes']) AND ! empty($this->configs['attributes'])) {
                foreach ($this->configs['attributes'] as $attribute => $value) {
                    $this->openTag .= ' ' . $attribute . '="' . $value . '"';
                }
            }
            $this->openTag .= '>';
        }
        return $this->openTag;
    }

    public function closeForm()
    {
        if ($this->closeTag === null) {
            $this->closeTag = '</form>';
        }
        return $this->closeTag;
    }

    public function getForm()
    {
        if($this->template !== null){
            // Vamos brincar com a troca de variaveis no template do formulario:
            $form = $this->elements;
            $form['OpenForm'] = $this->openTag;
            $form['CloseForm'] = $this->closeTag;
            $parser = new Templater($this->template, $form);
            return $parser->getParsedForm();
        }
        return $this->fullForm;
    }

    public function getElement($name)
    {
        try {
            if (isset($this->elements[$name])) {
                return $this->elements[$name];
            } else {
                throw new Exception("Invalid field: " . $name);
            }
        } catch (Exception $e) {
            $e->getError();
        }
    }

    public function getError($field)
    {
        try {
            if ($this->configs['display-errors'] !== true) {
                $error = $this->errors[$field];
                return $error;
            } else {
                throw new Exception("When display-errors is enabled, is not possible to retrieve errors with Form->getError()");
            }
        } catch (Exception $e) {
            $e->getError();
        }
    }

}
