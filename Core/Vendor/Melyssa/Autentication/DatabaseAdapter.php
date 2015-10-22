<?php
namespace Melyssa\Autentication;

class DatabaseAdapter
{
    protected $tableName;
    protected $formName;
    protected $usernameColumn;
    protected $passwordColumn;
    protected $usernameField;
    protected $passwordField;
    protected $usernameFails;
    protected $passwordFails;

    protected $toRoute = true;
    protected $successRoute = 'teste';
    protected $errorRoute = 'teste';

    protected $authLevel = 'user';

    protected $needsActivation = true;
    protected $activationColumn = 'status';
    protected $activationField = 'email';

    protected $returnFunction = null; // Deve ser definido no adaptador para caso de autenticacao nao permitida

    public function setMessages($field = 'username', $message = 'Invalid Username')
    {
        $for = $field . 'Fails';
        $this->$for = $message;
    }

    public function getMessage($which)
    {
        $for = $which . 'Fails';
        return $this->$for;
    }

    public function __get($variable)
    {
        if (isset($this->$variable)) {
            return $this->$variable;
        } else {
            return null;
        }
    }

    public function getField($name)
    {
        $field = $name . 'Field';
        return $this->$field;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getUserColumn()
    {
        return $this->usernameColumn;
    }

    public function getPasswordColumn()
    {
        return $this->passwordColumn;
    }

    public function getActivationColumn()
    {
        return $this->activationColumn;
    }

    public function beforeAutenticate($userInfo)
    {
        // TODO implementar no adaptador filho
        return true;
    }

    public function getFormName()
    {
        return $this->formName;
    }

    public function getReturnFunction()
    {
        return $this->returnFunction;
    }
}
