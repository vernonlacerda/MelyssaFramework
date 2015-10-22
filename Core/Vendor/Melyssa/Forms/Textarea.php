<?php
namespace Melyssa\Forms;

class Textarea extends Element{
    protected $value;
    
    public function setValue()
    {
        $sessionName = $this->formName .':'. $this->attributes['name'];
        if ($this->sessionHandler->checkSession($sessionName)) {
            $this->value = $this->sessionHandler->getSession($sessionName);
            if(false === $this->keepValues){
                $this->sessionHandler->destroySession($sessionName);
            }
        }
    }
    
    public function parseElement() {
        return sprintf('<textarea%s>%s</textarea>', $this->parseAttributes($this->attributes), $this->value);
    }
}