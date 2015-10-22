<?php

namespace Melyssa\Autentication;

use Melyssa\Session;
use Melyssa\Model;
use Melyssa\Uri;
use Melyssa\Input;

class Service
{
    private $sessionHandler;
    protected $adapter;
    private $database;
    private $userInfo = null;
    private $hydrator;
    private $uri;

    public function __construct(DatabaseAdapter $adapter)
    {
        //Precisamos de um adaptador pra saber com qual tipo de autenticação vamos trabalhar
        $this->adapter = $adapter;
        // Gerenciador de sessões:
        $this->sessionHandler = Session::getInstance();
        // Instancia do banco de dados:
        $this->database = new Model();
        // Hydrator para pegar os dados recebidos do formulário:
        $this->hydrator = new Input();
        // Roteador:
        $this->uri = new Uri;
    }

    public function autenticate()
    {
        // Consultando o MySql para pegar as informações do usuário:
        $this->setUserInfo();
        // Validando usuário:
        if ($this->verifyUser()) {
            // Validando senha:
            if ($this->verifyPassword()) {
                // Matando as sessões já existentes da autenticação (se houver alguma):
                $this->sessionHandler->destroySession($this->adapter->getFormName());
                $this->sessionHandler->destroySession($this->adapter->getFormName());
                if ($this->adapter->beforeAutenticate($this->userInfo)) {
                    session_regenerate_id();
                    $this->sessionHandler->makeSession($this->adapter->authLevel . '_data', $this->encryptData())
                                         ->makeSession($this->adapter->authLevel . '_auth', base64_encode('Authorized'));
                    // Autenticação realizada com sucesso, falta redirecionar o browser para o local adequado:
                    $this->autenticationSuccess();
                } else {
                    // Ocorreu algum erro e o adaptador não permitiu a autenticação do usuário:
                    $this->autenticationFails($this->adapter->getReturnFunction());
                }
            }
        }
    }

    public function encryptData()
    {
        $encryptedData = array();
        foreach ($this->userInfo as $key => $value) {
            // Usar método de encriptação com salt definido no session hash:
            $data = SESSION_HASH . base64_encode($value);
            $encryptedData[$key] = base64_encode($data);
        }
        return $encryptedData;
    }

    private function setUserInfo()
    {
        $whereClause = $this->adapter->getUserColumn() . " = '" . $this->hydrator->getPost($this->adapter->getUserColumn()) . "'"; //
        $this->database->tableName = $this->adapter->getTableName();
        $userQuery = $this->database->Read($whereClause);
        // Guardando as informações do cliente dentro do módulo de autenticação:
        if ($this->database->countResults() > 0) {
            $this->userInfo = $userQuery[0];
        } else {
            $this->userInfo = null;
        }
    }

    private function verifyUser()
    {
        if (null === $this->userInfo) {
            $this->saveData();
            $this->autenticationFails('username');
        } else {
            return $this->verifyActivation();
        }
    }

    private function verifyActivation()
    {
        if ($this->adapter->needsActivation === true and $this->userInfo[$this->adapter->getActivationColumn()] == '0') {
            $this->saveData();
            $this->autenticationFails('activation');
        } else {
            return true;
        }
    }

    private function verifyPassword()
    {
        if ($this->userInfo[$this->adapter->getPasswordColumn()] === md5($this->hydrator->getPost($this->adapter->getPasswordColumn()))) {
            return true;
        } else {
            $this->saveData();
            $this->autenticationFails('password');
        }
    }

    private function saveData()
    {
        $username = $this->adapter->getField('username');
        $password = $this->adapter->getField('password');
        $uData = $this->hydrator->getPost($username);
        $pData = $this->hydrator->getPost($password);
        $this->sessionHandler->makeSession($this->adapter->getFormName().':'.$username, $uData);
        $this->sessionHandler->makeSession($this->adapter->getFormName().':'.$password, $pData);
    }

    private function autenticationFails($motive = 'username')
    {
        // Criando a sessão de acordo com o erro que ocorreu:
        $indicator = $this->adapter->getField($motive);
        $message = $this->adapter->getMessage($motive);
        $this->sessionHandler->makeSession($this->adapter->getFormName().':'.$indicator.'-error', $message);
        if (true === $this->adapter->toRoute) {
            $this->uri->redirect($this->adapter->errorRoute);
        } else {
            die('Redirecionar para controller e action !');
        }
    }

    private function autenticationSuccess()
    {
        if (true === $this->adapter->toRoute) {
            $this->uri->redirect($this->adapter->successRoute);
        } else {
            die('Redirecionar para controller e action !');
        }
    }

    public static function isLogged($level = 'user')
    {
        $s = & Session::getInstance();
        return $s->checkSession($level . '_auth');
    }

    public static function onlyLoggedIn($level, $route)
    {
        if (!self::isLogged($level)) {
            Uri::redirect($route);
        } else {
            return true;
        }
    }

    public static function onlyLoggedOff($level, $route)
    {
        if (self::isLogged($level)) {
            Uri::redirect($route);
        } else {
            return true;
        }
    }

    public static function logoutUser($level, $route)
    {
        self::onlyLoggedIn($level, $route);
        $session = Session::getInstance();
        $session->destroySession($level . '_auth');
        $session->destroySession($level . '_data');

        // Regenerating the session ID, prevent Session Hijacking:
        session_regenerate_id();

        Uri::redirect($route);
    }

    public static function getUserInfo($level, $field = null)
    {
        // Decrypting information:
        $session =& Session::getInstance();
        if (null !== $field) {
            $real_data = base64_decode($session->getSession($level . '_data:' . $field));
            // Tirando o salt do valor criptografado:
            $cleared_data = preg_replace('/('. SESSION_HASH .')/', '', $real_data);
            // Retornando o valor decriptado:
            return base64_decode($cleared_data);
        } else {
            $decryptedData = array();
            foreach ($session->getSession($level . '_data') as $key => $val) {
                $real_data = base64_decode($session->getSession($level . '_data:' . $key));
                // Tirando o salt do valor criptografado:
                $cleared_data = preg_replace('/('. SESSION_HASH .')/', '', $real_data);
                $decryptedData[$key] = base64_decode($cleared_data);
            }
            return $decryptedData;
        }
    }

    public static function changeUserInfo($level, $field, $value)
    {
        $session =& Session::getInstance();
        $data = SESSION_HASH . base64_encode($value);
        $encryptedData = base64_encode($data);
        $session->makeSession($level . '_data:' . $field, base64_encode($encryptedData));
        return true;
    }

    public function logout()
    {
        $this->sessionHandler->destroySession(array('user_data', 'user_auth'));
        $this->uri->redirect($this->adapter->successRoute);
    }
}
