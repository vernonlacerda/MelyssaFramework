<?php

namespace Melyssa;

/**
 * Classe de sessões do sistema:
 * 
 * Cria, exclui e resgata sessões e cookies.
 *
 * @package		Melyssa Framework
 * @category            Library
 * @author		Jhonathas Cavalcante
 * @link		http://melyssaframework.com/user_guide/
 * 
 */
class Session implements Singleton {

    private static $instance = null;

    public function __construct() {
        try{
            if( ! defined('SESSION_HASH')){
                throw new Exception("In order to use Sessions with the framework, you need to set the encryption key in the config file for you application !");
            }
        } catch( Exception $e ){
            $e->getError();
        }
        self::$instance =& $this;
    }

    public function makeSession($name, $value) {
        if( ! strpos($name, ':')){
            // Precisamos pegar de dentro de um array multidimensional:
            $_SESSION[$name] = $value;
        }else{
            list($main, $child) = explode(':', $name);
            $_SESSION[$main][$child] = $value;
        }
        return $this;
    }

    public function getSession($name) {
        if( ! strpos($name, ':')){
            // Precisamos pegar de dentro de um array multidimensional:
            return $_SESSION[$name];
        }else{
            list($main, $child) = explode(':', $name);
            return $_SESSION[$main][$child];
        }
    }

    public function destroySession($name) {
        if(is_array($name)){
            foreach($name as $session){
                $this->destroySession($session);
            }
            return;
        }elseif( ! strpos($name, ':')){
            // Precisamos pegar de dentro de um array multidimensional:
            unset($_SESSION[$name]);
        }else{
            list($main, $child) = explode(':', $name);
            unset($_SESSION[$main][$child]);
        }
        return $this;
    }

    public function checkSession($name) {
        if( ! strpos($name, ':')){
            // Precisamos pegar de dentro de um array multidimensional:
            return isset($_SESSION[$name]);
        }else{
            list($main, $child) = explode(':', $name);
            return isset($_SESSION[$main][$child]);
        }
    }

    public static function &getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __toString() {
        if (!empty($_SESSION)) {
            return $_SESSION;
        } else {
            return 'There are no sessions to display !';
        }
    }

    public function get_all_sessions() {
        return $_SESSION;
    }

}
