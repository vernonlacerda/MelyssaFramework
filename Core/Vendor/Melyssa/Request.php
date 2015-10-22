<?php

namespace Melyssa;

use Melyssa\Input;
use Melyssa\Logger\Log;

/**
 * Classe de requisições do sistema:
 *
 * Lê a requisição atual, define segmentos, tipo de requisição e afins.
 *
 * @package		Melyssa Framework
 * @category            Library
 * @author		Jhonathas Cavalcante
 * @link		http://melyssaframework.com/user_guide/
 *
 */
class Request
{
    /**
     * Instância singleton da classe:
     * @static
     * @access private
     */
    private static $instance = null;

    /**
     * Uri atual do sistema:
     * @var string
     */
    private $uri;

    /**
     * Segmentos encontrados na Uri:
     * @var array
     */
    private $segments = array();

    /**
     * Parâmetros de url (sem controller e action):
     * @var array
     */
    public $urlParams = array();

    /**
     * Valores recebidos pelo método POST:
     * @var array
     */
    public $postValues = array();

    /**
     * Método de requisição:
     * @var string
     */
    private $requestMethod;
    private $referrer;
    private $logger;
    private $userAgentParser;

    public function __construct()
    {
        self::$instance = & $this;
        $this->logger = Log::getInstance();
        // Classe de user agent:
        $this->userAgentParser =& UserAgent::getInstance();
        // Definindo o tipo de requisição:
        $this->setRequestMethod();
        // Definindo a página anterior do site:
        $this->setReferrer();
        // Definindo a uri (tudo que existe depois do domínio da aplicação)
        $this->setUri();
        // Setando os parâmetros dentro do array de segmentos, aí ainda temos a uri normal guardada !
        $this->explodeSegments();
        // Setando os parâmetros da url (diferente dos segmentos, retiramos o controller e a action, saca?) xD;
        $this->setUrlParams($this->segments);
        // Setando parâmetros recebido via método POST
        $this->setPostValues();
    }

    private function setRequestMethod()
    {
        // Verificando se temos uma requisição enviada via ajax:
        if ($this->isAjax()) {
            $this->requestMethod = 'AJAX';
        } else {
            $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        }
        $this->logger->debugMessage("Request method set to: " . $this->requestMethod);
    }

    // Verificando se temos uma requisição enviada via ajax:
    private function isAjax()
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Recuperando o método utilizado na requisição atual
     *
     * @return string POST ou GET
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    private function setReferrer()
    {
        $this->referrer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : null;
        $this->logger->debugMessage("HTTP Referrer set to: " . $this->referrer);
    }

    public function getReferrer()
    {
        return $this->referrer;
    }

    private function setUri()
    {
        $uri = (isset($_GET[URI_IDENTIFIER])) ? filter_input(INPUT_GET, URI_IDENTIFIER) : '';
        $this->uri = $uri;
        $this->logger->debugMessage("Uri set to: " . $this->uri);
    }

    public function getUri()
    {
        return $this->uri;
    }

    private function explodeSegments()
    {
        $this->segments = explode('/', $this->uri);
    }

    private function setUrlParams($segments = [])
    {
        /** Retirando o Controller e a Action do array recebido **/

        unset($segments[0], $segments[1]);

        /** O último índice do array é vazio? ... Então deleta **/

        if (end($segments) == null) {
            array_pop($segments);
        }

        /** Ainda temos alguma coisa dentro do array? ... Continuamos **/

        if ($segments != null && count($segments) > 1) {
            $i = 0;
            foreach ($segments as $vals) {

                /** Se o índice for ímpar então temos uma chave, caso contrário, um valor. **/

                if ($i % 2 == 0) {
                    $key[] = $vals;
                } else {
                    $value[] = $vals;
                }
                $i++;
            }

            /** Se índices e chaves não contiverem o mesmo número de valores, retiramos o último índice. **/

            if (count($key) !== count($value)) {
                array_pop($key);
            }

            /** Criamos o array final de parâmetros e enviamos para a propriedade da classe. **/

            $arrayFinal = array_combine($key, $value);
            $this->urlParams = $arrayFinal;
        }
    }

    public function setPostValues()
    {
        $hydrator = new Input();
        $this->postValues = $hydrator->getPost();
        return true;
    }

    public function hasPost($name)
    {
        $isset = true;
        if (is_array($name)) {
            foreach ($name as $val) {
                $isset = isset($this->postValues[$val]);
            }
        } else {
            $isset = isset($this->postValues[$name]);
        }

        return $isset;
    }

    public function getUrlParams()
    {
        return $this->urlParams;
    }

    public function is($what = 'POST')
    {
        return ($this->requestMethod === $what);
    }

    public function getSegment($position = 0)
    {
        return (isset($this->segments[$position])) ? $this->segments[$position] : '';
    }

    public function getPost($name = null)
    {
        if (null === $name) {
            return $this->postValues;
        } else {
            return $this->postValues[$name];
        }
    }

    // Late static bind:
    public static function &getInstance()
    {
        return self::$instance;
    }
}
