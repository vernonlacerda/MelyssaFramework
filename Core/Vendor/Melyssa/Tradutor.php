<?php

namespace Melyssa;

/**
 * Classe de internacionalizaÃ§Ã£o do sistema:
 *
 * Traduz strings, textos e mais.
 *
 * @package		Melyssa Framework
 * @category            Library
 * @author		Jhonathas Cavalcante
 * @link		http://melyssaframework.com/user_guide/
 *
 */
class Tradutor
{
    private $strings = array();

    public function __construct()
    {
        // Inicializando a variável de linguagens para evitar problemas futuros:
        $language = null;
        try {
            if (defined('LANGUAGE') and is_dir(LANGUAGE . DEFAULT_LANG . '/')) {
                include(LANGUAGE . DEFAULT_LANG . '/' . 'Main.php');
                $this->strings = $language;
            } else {
                throw new \Exception("The language folder is not set correctly !");
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    public function getString($string)
    {
        if (isset($this->strings[$string])) {
            return $this->strings[$string];
        } else {
            return $string;
        }
    }
}
