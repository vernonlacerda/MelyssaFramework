<?php

namespace Melyssa\Exception;

use Melyssa\Exception;
use Melyssa\Tradutor;

class ControllerException extends Exception
{

    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->tradutor = new Tradutor();
    }

    public function getError()
    {
        $error = $this->tradutor->getString('Controller Exception') . ': ';
        $error .= '<strong>' . $this->message . '</strong>';
        $error .= ' ' . $this->tradutor->getString('on file') . ': ';
        $error .= '<strong>' . $this->file . '</strong> ';
        $error .= $this->tradutor->getString('at line') . ': <strong>' . $this->line . '</strong>.<br><br>';
        die($error);
    }

}
