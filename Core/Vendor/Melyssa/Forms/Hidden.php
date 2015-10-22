<?php
namespace Melyssa\Forms;

class Hidden extends Element
{
    protected $type = 'hidden';
    
    public function getElement()
    {
        return $this->parseElement();
    }
}