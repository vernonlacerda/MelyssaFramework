<?php

namespace Melyssa;

use Melyssa\Mvc\Controller;
use Melyssa\Exception;
use Melyssa\Logger\Log;

/**
 * Classe de roteamento do sistema:
 *
 * Realiza o bootstrap, define controller e action, filtra parâmetros de url<br>
 * e inicializa as páginas da aplicação
 *
 * @package		Melyssa Framework
 * @subpackage          Router
 * @category            Library
 * @author		Jhonathas Cavalcante
 * @link		http://melyssaframework.com/user_guide/routerlibrary.php
 *
 */
class Router
{
    private $routes;
    private $class;
    private $method;
    private $controller;
    private $action;
    private $controllerConfigs = array();
    private $actionConfigs = array();
    private $log;
    private static $instance;

    /**
     * Construtor da classe, utilizado para realizar o bootstrap de forma automática.
     */
    public function __construct(Request $requestObject)
    {
        $this->request = $requestObject;
        $this->log = Log::getInstance();
        $this->defineRoutes();
        $this->setController();
        $this->setAction();
        $this->setUrlParams();
        self::$instance =& $this;
    }

    /**
     * Define Routes
     *
     * Método responsável por carregar as rotas salvas nas configurações.
     *
     * @access private
     */
    private function defineRoutes()
    {
        // Inicializamos a vari�vel $routes pra evitar problemas futuros;

        $routes = array();

        // Procuramos o arquivo de rotas nas pastas poss�veis:

        if (defined('ENVIRONMENT') and is_file(APP_PATH . '/Configs/' . ENVIRONMENT . '/Routes.php')) {
            include(APP_PATH . '/Configs/' . ENVIRONMENT . '/Routes.php');
        } elseif (is_file('Application/Configs/Routes.php')) {
            include(APP_PATH . '/Configs/Routes.php');
        } else {
            throw new Exception("No routes file present, aborting...");
        }

        // Definimos as rotas a partir das rotas definidas pelo usu�rio:

        $this->routes = $routes;
        $this->log->debugMessage("Routes file loaded");
    }

    private function setController()
    {
        if ($this->request->getSegment(0) == null or $this->request->getSegment(0) == '') {
            $this->setDefaultController();
        } else {
            $this->controller = ucfirst($this->request->getSegment(0));
            $this->log->debugMessage("Controller class set to: " . $this->controller);
        }
        // Verificando se o controller chamado na url existe antes de definir a variável:
        if (class_exists('Controllers\\' . $this->controller) and isset($this->routes[$this->controller])) {
            // Namespace full qualified:
            $namespace = 'Controllers\\' . $this->controller;
            $this->class = new $namespace();
            // Redefinindo o array de rotas somente para o controller atual:
            $this->controllerConfigs = $this->routes[$this->controller];
        } else {
            //Instanciando controller padrão:
            $this->class = new Controller();
        }
    }

    private function setDefaultController()
    {
        if (isset($this->routes['default-controller'])) {
            $this->controller = $this->routes['default-controller'];
            $this->log->debugMessage("Default controller set from configuration variable");
            return true;
        } elseif (defined('DEFAULT_CONTROLLER')) {
            $this->controller = DEFAULT_CONTROLLER;
            $this->log->debugMessage("Default controller set from application constant");
            return true;
        } else {
            throw new Exception("Default controller not set !");
        }
    }

    private function setAction()
    {
        if ($this->request->getSegment(1) == null or $this->request->getSegment(1) == '') {
            $this->setDefaultAction();
        } else {
            $this->action = $this->request->getSegment(1);
        }
        $actionRealName = strtolower($this->action) . 'Action';
        $this->log->debugMessage("Action set to: " . $this->action);
        if (!method_exists($this->class, $actionRealName)) {
            $this->method = 'show404';
        } else {
            // o método existe, setamos as configurações da action atual:
            //$this->actionConfigs = $this->controllerConfigs['callables'][$this->action];
            //Verificando se o metodo pode ser chamado diretamente pela url ou é uma função fechada:
            if (array_key_exists($this->action, $this->controllerConfigs['callables']) and
                    in_array($this->request->getRequestMethod(), $this->controllerConfigs['callables'][$this->action]['methods'])) {
                $this->method = $actionRealName;
                $this->actionConfigs = $this->controllerConfigs['callables'][$this->action];
            } else {
                $this->method = 'show404';
            }
        }
    }

    private function setDefaultAction()
    {
        // Primeiro verificamos se existe uma página padrão definida no array de rotas com o nome da view padrão:
        if (isset($this->controllerConfigs['default-action'])) {
            $this->action = $this->controllerConfigs['default-action'];
            // Se não existir, verificamos se a constante foi definida:
        } elseif (isset($this->routes['default-action'])) {
            $this->action = $this->routes['default-action'];
        } elseif (defined('DEFAULT_ACTION')) {
            $this->action = DEFAULT_ACTION;
        } else {
            throw new Exception("No default action present !");
        }
    }

    private function setUrlParams()
    {
        // Método de validação dos parâmetros, apenas params registrados nas rotas e compatíveis com
        // os valores esperados serão indexados no array de parâmetros da requisição.

        $configs = (isset($this->actionConfigs['params'])) ? $this->actionConfigs['params'] : array();
        $params = $this->request->getUrlParams();

        if (!empty($params) and !empty($configs)) {
            foreach ($params as $key => $value) {
                if (array_key_exists($key, $configs) and preg_match($configs[$key], $value)) {
                    // Se o parâmetro não foi registrado na rota, então deletamos:
                    break;
                } else {
                    unset($params[$key]);
                }
            }
        } elseif (!empty($params) and empty($configs)) {
            // Temos o array de configurações vazio, então não é permitido nenhum parâmetro na url:
            $params = array();
        }

        // Parâmetros basicamente filtrados, agora, jogamos eles de volta para a requisição:
        $this->request->urlParams = $params;
    }

    /**
     * Get Controller
     *
     * Retorna a instância do controller que foi definido pelo bootstrap
     *
     * @access public
     */
    public function getController()
    {
        return $this->class;
    }

    /**
     * Get Action
     *
     * Retorna o método requisitado pela url e verificado pelo Router:
     *
     * @access public
     */
    public function getAction()
    {
        return $this->method;
    }
}
