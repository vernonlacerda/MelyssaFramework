<?php
namespace Melyssa;

/**
 * Interface para classes que devem ser instanciadas somente uma vez.
 *
 * @package		Melyssa Framework
 * @subpackage          Singleton
 * @category            Interface
 * @author		Jhonathas Cavalcante
 * @link		http://melyssaframework.com/user_guide/singleton.php
 *
 */

interface Singleton
{
    public static function &getInstance();
}
