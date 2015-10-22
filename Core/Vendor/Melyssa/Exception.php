<?php

namespace Melyssa;

use Melyssa\Tradutor;

/**
 * Classe de exceções do sistema:
 *
 * @package		Melyssa Framework
 * @subpackage          Exception
 * @category            Library
 * @author		Jhonathas Cavalcante
 * @link		http://melyssaframework.com/user_guide/routerlibrary.php
 *
 */
class Exception extends \Exception
{
    private $tradutor;

    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->tradutor = new Tradutor();
    }

    public function getError()
    {
        $error = $this->tradutor->getString('Melyssa Exception') . ': ';
        $error .= '<strong>' . $this->message . '</strong>';
        $error .= ' ' . $this->tradutor->getString('on file') . ': ';
        $error .= '<strong>' . $this->file . '</strong> ';
        $error .= $this->tradutor->getString('at line') . ': <strong>' . $this->line . '</strong>.<br><br>';
        die($error);
    }
}
