<?php

namespace Melyssa;

use Melyssa\Router;
use Melyssa\Request;
use Melyssa\Logger\Log;

/**
 * Classe de bootstrap do sistema:
 * 
 * Carrega as configurações e classes necessárias e executa as requisições.
 *
 * @package		Melyssa Framework
 * @category            Library
 * @author		Jhonathas Cavalcante
 * @link		http://melyssaframework.com/user_guide/
 * 
 */
class Bootstrap
{

    /**
     * Versão do Framework:
     * @var string
     */
    const MELYSSA_VERSION = '1.0.0';

    /**
     * Status do sistema:
     * @var boolean
     */
    private static $initialized = false;

    /**
     * Inicializador do sistema:
     * @access private
     */
    private static function initialize()
    {
        // Autoloader do Framework:
        
        self::registerAutoloader();
        
        // Carregando o logger:
        
        $log = Log::getInstance();

        // Setando encoding interno do Framework:
        
        header('Content-type: text/html; charset=UTF-8');
        
        self::verifyPhpVersion();
        
        $log->debugMessage('Running Melyssa Framework in PHP Version '.PHP_VERSION);
        
        self::switchEnvironment();
        
        $log->debugMessage('The application environment is set to: '.ENVIRONMENT);

        // Setando a variável de inicialização pra garantir que não teremos mais que um dispatch na mesma requisição:
        
        self::$initialized = true;
        
        $log->debugMessage("System initialized");
    }
    
    private static function registerAutoloader()
    {
        // Devemos registrar o autoloader do Framework aqui dentro pra nao precisar de arquivos externos:
        spl_autoload_register(function($class){
            $filename = str_replace('\\', '/', $class);
            foreach(array(VENDOR_PATH, APP_PATH) as $path){
                if(file_exists($path . $filename . '.php')){
                    require $path . $filename . '.php';
                }
            }
        });
    }
    
    private static function switchEnvironment()
    {

        // Verificando ambiente da aplicação:

        if (defined('ENVIRONMENT')) {
            switch (ENVIRONMENT) {
                case 'Development':
                    error_reporting(E_ALL);
                    break;

                case 'Testing':
                case 'Production':
                    error_reporting(0);
                    break;

                default:
                    exit("Ambiente da aplicação não foi definido corretamente !");
            }
        }
    }
    
    private static function verifyPhpVersion()
    {
        
        // Verificando a versão atual do PHP utilizada no servidor:

        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            exit('The Melyssa Framework only runs at PHP 5.5.0 or higher and your current version is: ' . PHP_VERSION);
        }
    }

    /**
     * Start up da aplicação
     * 
     * Esse método é chamado após o bootstrap ser concluído.<br>
     * Requisitamos e instanciamos o controller definido no bootstrap.<br>
     * E o router se encarrega de todo o trabalho daqui pra frente.<br>
     * Só voltamos ao método dispatch() depois que tudo estiver finalizado
     * 
     */
    public static function dispatch()
    {
        if (self::$initialized === false) {
            self::initialize();
            try{
                $log = Log::getInstance();
                $router = new Router(new Request());
                $controller = $router->getController();
                $action = $router->getAction();
                $controller->initPage();
                call_user_func_array(array($controller, $action), array());
                $controller->closePage();
                $log->saveLog(true);
            } catch (Exception $e){
                $e->getError();
            }
        } else {
            die("System already initialized, second atempt ignored !");
        }
    }

}
