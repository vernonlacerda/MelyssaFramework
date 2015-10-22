<?php
namespace Melyssa\Database;

use Melyssa\Singleton;
use Melyssa\Loader;

class Conector implements Singleton{
    
    private $dsn;

    private $username;

    private $password;

    private $initConfigs = array();

    private static $instance = null;
    
    public function __construct() {
        self::$instance = new \PDO('dsn', 'username', 'password');
        $logger =& \Melyssa\Logger\Log::getInstance();
        $logger->debugMessage("Database class initialized!");
    }
    
    public static function &getInstance(){
        if( !is_object(self::$instance)){
            new self;
        }
        return self::$instance;
    }

    /**
    * Carregamento das configurações de banco de dados:
    */

    private function loadConfigs()
    {
        $loader = new Loader();
        $configs = $loader->loadConfigs("Application/Configs/" . ENVIRONMENT . "Database");
    }
}