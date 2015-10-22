<?php

namespace Melyssa\Mvc;

use Melyssa\Loader;
use Melyssa\Tradutor;
use Melyssa\Request;
use Melyssa\Html\Utils;
use Melyssa\Uri;
use Melyssa\Session;
use Melyssa\UserAgent;

class Controller
{

    // Instancia da própria classe
    private static $instance;
    protected $loader;
    private $tradutor;
    protected $request = null;
    protected $html;
    protected $uri;
    protected $session;
    protected $userAgent;

    public function __construct()
    {
        // Assinalando instância da classe para pegar quando precisar:
        self::$instance = & $this;
        // Objeto da requisição:
        $this->request =& Request::getInstance();
        $this->session =& Session::getInstance();
        $this->userAgent =& UserAgent::getInstance();

        // Biblioteca de carregamento de arquivos
        $this->loader = new Loader();
        $this->tradutor = new Tradutor();
        $this->html = new Utils();
        $this->uri = new Uri();
    }

    public static function &getInstance()
    {
        return self::$instance;
    }

    /**
     * Método responsável por carregar uma view.
     * 
     * @access protected
     * @param string $file O arquivo de view a ser carregado
     * @param array  $data Um array de dados a serem enviados para a view
     * */
    protected function view($file, array $data = null)
    {
        if ($data != null AND is_array($data)) {
            extract($data);
        }
        // Retirando a parte do namespace:
        $class = substr(get_called_class(), strpos(get_called_class(), '\\') + 1);
        if('404' === $file OR 'Mvc\Controller' === $class){
            $folder = '';
        }else{
            if(!strpos($file, '/')){
                $folder = $class . '/';
            }else{
                $folder = '';
            }
        }
        // Pegando o arquivo com o loader:
        return include($this->loader->getFile(VIEWS . $folder . $file . '.php'));
    }

    /**
     * Método responsável por inicializar as páginas do sistema.<br>
     * É útil para se trabalhar com templates, exemplo, carregar um cabeçalho<br>
     * ou para inicializar funcionalidades do sistema.
     * 
     * @access public
     * @todo implementar dentro dos controllers da aplicação:
     */
    public function initPage()
    {
        // TODO: implementar dentro dos controllers da aplicação
    }

    //Função de fechamento de todo o bloco de template:
    public function closePage()
    {
        // TODO: implementar dentro dos controllers da aplicação
    }

    public function show404($data = null)
    {
        header('HTTP/1.1 404 Not Found');
        $this->view('Errors/404', $data);
    }

    public function translate($string)
    {
        return $this->tradutor->getString($string);
    }
    
    public function setFlash($message = 'This is only a flash message !'){
        $session = \Melyssa\Session::getInstance();
        $session->makeSession('flash_system', $message);
    }
    
    public function getFlash($container = ''){
        $s = \Melyssa\Session::getInstance();
        if($s->checkSession('flash_system')){
            $flash = $s->getSession('flash_system');
            $s->destroySession('flash_system');
            if($container === ''){
                return $flash;
            } else {
                return sprintf($container, $flash);
            }
        } else{
            return '';
        }
    }

}
