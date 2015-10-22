<?php
namespace Melyssa;

/**
 * Classe de entrada de dados:
 *
 * Carrega e sanitiza os dados enviados pelo usuário através de qualquer método.
 *
 * @package		Melyssa Framework
 * @subpackage          Input
 * @category            Library
 * @author		Jhonathas Cavalcante
 * @link		http://melyssaframework.com/user_guide/
 *
 */
class Input
{
    /**
     * Dados recebido através do método POST:
     * @var string
     */
    private $postValues = array();

    /**
     * Contrutor da classe, guarda os dados recebidos dentro da variável $postValues.
     *
     * @access public
     */
    public function __construct()
    {
        $this->fetchPost();
    }

    public function fetchPost()
    {
        foreach ($_POST as $name => $value) {
            if (!is_array($value)) {
                $this->postValues[$name] = strip_tags(trim($value));
            } else {
                foreach ($value as $val) {
                    $this->postValues[$name][] = strip_tags(trim($val));
                }
            }
        }
    }

    public function hasPost($name)
    {
        return isset($this->postValues[$name]);
    }

    public function getPost($name = null)
    {
        if (null === $name) {
            return $this->postValues;
        } else {
            if ($this->hasPost($name)) {
                return $this->postValues[$name];
            } else {
                return null;
            }
        }
    }
}
