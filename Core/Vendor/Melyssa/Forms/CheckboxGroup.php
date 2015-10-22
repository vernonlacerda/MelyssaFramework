<?php
namespace Melyssa\Forms;

class CheckboxGroup extends Element
{
    public $type = 'checkbox';
    private $options = null;
    private $checkeds = array();

    public function __construct($attributes, $formName, $keepValues = false)
    {
        parent::__construct($attributes, $formName, $keepValues);
        $this->setOptions();
        $this->setCheckeds();
    }

    private function setCheckeds()
    {
        if ($this->sessionHandler->checkSession($this->formName . ':' . substr($this->attributes['name'], 0, -2))) {
            $this->checkeds = $this->sessionHandler->getSession($this->formName . ':' . substr($this->attributes['name'], 0, -2));
        }
    }

    private function setOptions()
    {
        if (isset($this->attributes['from-db'])) {
            // Devemos pegar os valores do banco de dados:
            $this->setValuesFromDatabase();
        } elseif (isset($this->attributes['options'])) {
            $this->options = $this->attributes['options'];
            unset($this->attributes['options']);
        } else {
            throw new Exception("You have to define at least one option !");
        }
    }

    private function setValuesFromDatabase()
    {
        // Precisamos saber de qual tabela do banco devemos pegar os optionsa:
        $tablename  = $this->attributes['options']['table-name'];
        $valuefield = $this->attributes['options']['value-field'];
        $textfield  = $this->attributes['options']['text-field'];
        $condition  = (isset($this->attributes['options']['condition'])) ? $this->attributes['options']['condition'] : '';
        $order  = (isset($this->attributes['options']['order'])) ? $this->attributes['options']['order'] : '';
        unset($this->attributes['options'], $this->attributes['from-db']);
        // Pegando os dados do banco:
        $model = new \Melyssa\Model();
        $model->tableName = $tablename;
        $options = $model->Read($condition, $order);
        foreach ($options as $option) {
            $this->options[$option[$valuefield]] = $option[$textfield];
        }
        return true;
    }

    public function parseElement()
    {
        if (null !== $this->options) {
            $this->hasLabel = true;
            $elements = '';
            foreach ($this->options as $val => $text) {
                $this->attributes['value'] = $val;
                if (in_array($val, $this->checkeds)) {
                    $actualEl = sprintf('<input type="%s"%s checked>', $this->type, $this->parseAttributes($this->attributes));
                } else {
                    $actualEl = sprintf('<input type="%s"%s>', $this->type, $this->parseAttributes($this->attributes));
                }
                $this->labelText = $actualEl . $text;
                $elements .= $this->parseLabel();
            }
            // Limpando a sess�o de valor do checkbox:
            if (false === $this->keepValues) {
                $this->sessionHandler->destroySession($this->formName . ':' . substr($this->attributes['name'], 0, -2));
            }
            return $elements;
        } else {
            // Pra inputs do tipo radio, a label, cont�m o elemento e a label:
            $element = sprintf('<input type="%s"%s>', $this->type, $this->parseAttributes($this->attributes));
            $labelText = $this->labelText;
            $this->labelText = $element . $labelText;
            $newElement = $this->parseLabel();
            return $newElement;
        }
    }

    public function getElement()
    {
        return $this->parseElement();
    }
}
