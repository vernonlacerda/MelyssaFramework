<?php

namespace Melyssa;

use Melyssa\Exception;
use Melyssa\Tradutor;

/**
 * Classe de carregamento de arquivos do sistema:
 *
 * Responsável por procurar e carregar arquivos, views e bibliotecas.
 *
 * @package		Melyssa Framework
 * @subpackage          Loader
 * @category            Library
 * @author		Jhonathas Cavalcante
 * @link		http://melyssaframework.com/user_guide/loader.php
 *
 */
class Loader
{
    /**
     * Instância da classe de internacionalização.
     * @var object
     */
    private $tradutor;

    /**
     * Contrutor da classe.
     *
     * @access public
     */
    public function __construct()
    {
        $this->tradutor = new Tradutor();
    }

    /**
     * Método responsável por carregar arquivos
     *
     * @access public
     * @param string $filename O arquivo a ser carregado
     * @throws Melyssa\Exception se o arquivo não for encontrado
     * */
    public function getFile($filename)
    {
        try {
            if (file_exists($filename)) {
                return $filename;
            } else {
                throw new Exception($this->tradutor->getString('Unable to load file ') . $filename);
            }
        } catch (Exception $e) {
            $e->getError();
        }
    }

    public function loadConfigs($filepath)
    {
        return include($this->getFile($filepath));
    }
}
