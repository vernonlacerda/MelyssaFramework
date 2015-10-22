<?php

namespace Melyssa\Validation;

use Melyssa\Loader;
use Melyssa\Input;
use Melyssa\Exception;
use Melyssa\Session;

/**
 * Classe de validação de dados:
 * 
 * Recebe valores e valida de acordo com as regras aplicadas.
 *
 * @package		Melyssa Framework
 * @subpackage          iValidate
 * @category            Library
 * @author		Jhonathas Cavalcante
 * @link		http://melyssaframework.com/user_guide/ivalidatelibrary.php
 * 
 */
class Validator
{

    private $session;
    private $loader;
    private $hydrator;
    private $fields = array();
    private $errors = array();
    private $data = array();
    private $messages = array();
    private $formName;
    private $keepDataStorage = true;

    public function __construct($configs = null)
    {
        $this->hydrator = new Input();
        $this->loader = new Loader();
        $this->session = Session::getInstance();
        $this->loadConfigs($configs);
        $this->loadMessages();
    }

    private function loadConfigs($configs)
    {
        if (is_string($configs)) {
            $configs = $this->loader->loadConfigs(APP_PATH . 'Configs/' . ENVIRONMENT . '/Validation/' . $configs . '.php');
            $this->fields = $configs['fields'];
            $this->formName = $configs['form-name'];
            $this->keepDataStorage = $configs['keep-data-storage'];
            $this->hydrateFields();
        } elseif (is_array($configs)) {
            $this->fields = $configs;
            $this->hydrateFields();
        } else {
            return true;
        }
    }

    private function hydrateFields()
    {
        foreach ($this->fields as $field) {
            $this->fields[$field['name']]['value'] = $this->hydrator->getPost($this->fields[$field['name']]['from']);
        }
    }

    private function loadMessages()
    {
        // Pegando com o carregador de arquivos:
        $this->messages = $this->loader->loadConfigs(LANGUAGE . DEFAULT_LANG . '/Validation.php');
    }

    public function setMessages($rule, $message)
    {
        $this->messages[$rule] = $message;
        return $this;
    }

    public function translateFieldName($field, $name)
    {
        $this->fields[$field]['real_name'] = $name;
        return $this;
    }

    private function valid($name, $value, $rules)
    {
        //Sem regras de validação, setar somente os valores dos campos:
        if (!empty($rules)) {
            // Explodindo valores das regras de validação:
            $rules = explode('|', $rules);
            // Se o valor estiver vazio mas não for obrigatório:
            if ($value == '' AND ! in_array('Required', $rules)) {
                $rules = array();
            } else {
                foreach ($rules as $rule) {
                    //Se a função for de comparação entre valores:
                    if (strpos($rule, ':')) {
                        list($function, $baseValue) = explode(':', $rule);
                        $namespace = 'Melyssa\\Validation\\' . $function;
                        if (!$namespace::isValid($baseValue, $value)) {
                            $this->errors[$name] = sprintf($this->messages[$function], $name, $baseValue);
                            $this->data[$name] = $value;
                            return $this;
                        } else {
                            $this->data[$name] = $value;
                        }
                    } else {
                        //Se for uma função padrão:
                        $namespace = 'Melyssa\\Validation\\' . $rule;
                        if (!$namespace::isValid($value)) {
                            $this->errors[$name] = sprintf($this->messages[$rule], $name);
                            $this->data[$name] = $value;
                            return $this;
                        } else {
                            $this->data[$name] = $value;
                        }
                    }
                }
                return $this;
            }
        } else {
            $this->data[$name] = $value;
            return $this;
        }
    }

    private function initialize()
    {
        if(empty($this->fields)){
            throw new Exception("There is no fields to validate");
        }
        // Iniciando rotina de validação:
        foreach ($this->fields as $field) {
            $this->valid($field['name'], $field['value'], $field['rules']);
        }
        if (count($this->errors) > 0) {
            $this->keepDataStorage = true; // Existem erros, setamos a variavel de armazenamento para true.
            $this->saveErrors();
            $this->saveData();
            return false;
        } else {
            $this->saveData(); // Validação foi bem sucedida, setamos os valores se preciso, e retornamos sucesso.
            return true;
        }
    }

    private function saveData()
    {
        if (true === $this->keepDataStorage) {
            foreach ($this->fields as $field) {
                $this->session->makeSession($this->formName . ':' . $field['name'], $field['value']);
            }
        }
    }

    private function saveErrors()
    {
        foreach ($this->errors as $campo => $resultado){
            $this->session->makeSession($this->formName . ':' . $campo . '-error', $resultado);
        }
    }

    public function isValid()
    {
        try {
            return $this->initialize();
        } catch (Exception $e) {
            $e->getError();
        }
    }

}
