<?php
namespace Melyssa\Forms;

class Radio extends Element
{
    public $type = 'radio';
    private $options = null;
    private $checked;

    public function __construct($attributes, $formName, $keepValues = false)
    {
        parent::__construct($attributes, $formName, $keepValues);
        // Setando os grupos de radiobuttons:
        $this->setOptions();
        $this->setChecked();
    }

    private function setChecked()
    {
        if (isset($this->attributes['checked'])) {
            $this->checked = $this->attributes['checked'];
            unset($this->attributes['checked']);
        }
    }

    private function setOptions()
    {
        if (isset($this->attributes['options'])) {
            $this->options = $this->attributes['options'];
            unset($this->attributes['options']);
        }
    }

    public function parseElement()
    {
        if (null !== $this->options) {
            $this->hasLabel = true;
            // Temos um array de opções para criar os radiobuttons:
            $elements = '';
            foreach ($this->options as $val => $text) {
                $this->attributes['value'] = $val;
                if ($this->checked === $this->attributes['value']) {
                    $actualEl = sprintf('<input type="%s"%s checked>', $this->type, $this->parseAttributes($this->attributes));
                } else {
                    $actualEl = sprintf('<input type="%s"%s>', $this->type, $this->parseAttributes($this->attributes));
                }
                $this->labelText = $actualEl . $text;
                $elements .= $this->parseLabel();
            }
            return $elements;
        } else {
            // Pra inputs do tipo radio, a label, contém o elemento e a label:
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
