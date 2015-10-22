<?php
namespace Melyssa\Forms;

class Select extends Element{
    private $defaultValue = null;
    private $options = array();
    private $previousValue = null;
    private $repopulate = null;
    private $prefix = null;
    private $suffix = null;
    
    public function __construct($attributes, $formName, $keepValues = false)
    {
        parent::__construct($attributes, $formName, $keepValues);
        // Setando os options do select através do banco de dados:
        $this->setOptions();
        $this->setPrefixAndSuffix();
    }
    
    public function setValue() 
    {
        // Pegando o valor que o usuario tinha selecionado anteriormente:
        $sessionName = $this->formName .':'. $this->attributes['name'];
        if ($this->sessionHandler->checkSession($sessionName)) {
            // Temos um valor para o select atual:
            $this->previousValue = $this->sessionHandler->getSession($sessionName);
            if(isset($this->attributes['repopulate'])){
                // O usuário disse que quer o select repopulado no próximo carregamento de página:
                $this->repopulate = true;
                unset($this->attributes['repopulate']);
            }
            if(false === $this->keepValues){
                $this->sessionHandler->destroySession($sessionName);
            }
        }
    }
    
    private function setPrefixAndSuffix()
    {
        if(isset($this->attributes['prefix'])){
            $this->prefix = $this->attributes['prefix'];
            unset($this->attributes['prefix']);
        }
        
        if(isset($this->attributes['suffix'])){
            $this->suffix = $this->attributes['suffix'];
            unset($this->attributes['suffix']);
        }
    }
    
    private function setOptions()
    {
        // Verificando o valor padr�o a ser selecionado:
        if(isset($this->attributes['default-value'])){
            $this->defaultValue = $this->attributes['default-value'];
            unset($this->attributes['default-value']);
        }
        // Op��o para criar as op��es atrav�s de uma sequencia:
        if (isset($this->attributes['from-to'])) {
            if(!is_array($this->attributes['from-to'])){
                list($start, $end) = explode(':', $this->attributes['from-to']);
                if(isset($this->attributes['steps'])){
                    for($i = $start;$i <= $end;$i += $this->attributes['steps']){
                        $options[$i] = $i;
                    }
                }else{
                    if(!isset($this->attributes['inverse'])){
                        for($i = $start;$i <= $end;$i++){
                            $options[$i] = $i;
                        }
                    }else{
                        for($i = $end;$i >= $start;$i--){
                            $options[$i] = $i;
                        }
                        unset($this->attributes['inverse']);
                    }
                }
            }else{
                foreach($this->attributes['from-to'] as $range){
                    list($start, $end, $steps) = explode(':', $range);
                    for($i = $start;$i <= $end;$i += $steps){
                        $options[$i] = $i;
                    }
                }
            }
            $this->options = $options;
            unset($this->attributes['from-to'], $this->attributes['steps']);
        }else{
            if( ! isset($this->attributes['options']) ){
                throw new \Melyssa\Exception('You have to define at least one option to the select element', 0);
            } else {
                $this->options = $this->attributes['options'];
                unset($this->attributes['options']);
            }
        }
    }
        
    public function parseElement()
    {
        $element = sprintf('<select%s>%s</select>', $this->parseAttributes($this->attributes), $this->parseOptions());
        return $element;
    }
    
    private function parseOptions()
    {
        $prefix = (null !== $this->prefix) ? $this->prefix : '';
        $suffix = (null !== $this->suffix) ? $this->suffix : '';
        $options = '';
        if(null !== $this->defaultValue){
            $options .= '<option value="'. $this->defaultValue['value'] .'">' . $this->defaultValue['text'] . '</option>';
        }
        foreach( $this->options as $value => $text ){
            if(true === $this->repopulate AND $value == $this->previousValue){
                $options .= '<option value="'. $value . $suffix . '" selected="selected">' . $prefix . $text . $suffix . '</option>';
            }else{
                $options .= '<option value="'. $value . $suffix . '">' . $prefix . $text . $suffix . '</option>';
            }
        }
        return $options;
    }
}