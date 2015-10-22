<?php

namespace Melyssa;

use Melyssa\Request;

/**
 * Classe de uri do sistema:
 * 
 * Oferece opções para redirecionamento, resgate de parâmetros e segmentos, etc.
 *
 * @package		Melyssa Framework
 * @category            Library
 * @author		Jhonathas Cavalcante
 * @link		http://melyssaframework.com/user_guide/
 * 
 */
class Uri
{

    private $segments = array();
    private $request;
    private $actualController;
    public $actualAction;

    public function __construct()
    {
        $this->request = Request::getInstance();
        $this->actualController = $this->request->getSegment(0);
        $this->actualAction = $this->request->getSegment(1);
    }

    public function getParam($param = null)
    {
        $this->segments = $this->request->getUrlParams();
        if (null === $param) {
            return $this->segments;
        } else {
            if (!isset($this->segments[$param])) {
                return FALSE;
            } else {
                return $this->segments[$param];
            }
        }
    }

    public function redirect($destiny = null)
    {
        if (null === $destiny) {
            return;
        } else {
            header('Location: ' . $destiny);
        }
    }

    public function goControllerAction($controller, $action)
    {
        $route = '/' . $controller . '/' . $action . '/';
        if ($this->actualController === $controller AND $this->actualAction === $action) {
            return;
        }
        $this->redirect($route);
    }

    public function goController($controller)
    {
        if ($this->actualController !== $controller) {
            $this->redirect('/' . $controller . '/');
        } else {
            return;
        }
    }

    public function goAction($action)
    {
        try {
            if ($this->actualController !== '') {
                $this->goControllerAction($this->actualController, $action);
            } else {
                throw new Exception("There are no controller set for action redirect !");
            }
        } catch (Exception $e) {
            $e->getError();
        }
    }

    public function goHome()
    {
        $this->redirect(BASE_URL);
    }

    public static function __callStatic($name, $arguments)
    {
        $var = & $this;
        if (method_exists($var, $name)) {
            $var->$name($arguments);
        } else {
            return false;
        }
    }
    
    public function getCurrentUri()
    {
        return $this->request->getUri();
    }

}
