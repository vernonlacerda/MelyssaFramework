<?php
namespace Melyssa\Forms;

use Melyssa\Model;

class DbSelect extends Element
{
    private $options = array();
    private $defaultValue = null;
    private $previousValue = null;
    private $repopulate = null;

    public function __construct($attributes, $formName, $keepValues = false)
    {
        parent::__construct($attributes, $formName, $keepValues);
        // Setando os options do select através do banco de dados:
        $this->setDefaultValue();
        $this->setOptions();
    }

    public function setValue()
    {
        // Pegando o valor que o usuario tinha selecionado anteriormente:
        $sessionName = $this->formName .':'. $this->attributes['name'];
        if ($this->sessionHandler->checkSession($sessionName)) {
            // Temos um valor para o select atual:
            $this->previousValue = $this->sessionHandler->getSession($sessionName);
            if (isset($this->attributes['repopulate'])) {
                // O usuário disse que quer o select repopulado no próximo carregamento de página:
                $this->repopulate = true;
                unset($this->attributes['repopulate']);
            }
            if (false === $this->keepValues) {
                $this->sessionHandler->destroySession($sessionName);
            }
        }
    }

    private function setDefaultValue()
    {
        if (isset($this->attributes['default-value']) and is_array($this->attributes['default-value'])) {
            // Setando o valor padrão (o que aparece no topo da lista):
            $this->defaultValue = $this->attributes['default-value'];
            unset($this->attributes['default-value']);
        }

        return true;
    }

    private function setOptions()
    {
        if (! isset($this->attributes['options'])) {
            throw new \Melyssa\Exception('You have to define at least one option to the select element', 0);
        } else {
            $this->options = $this->attributes['options'];
            unset($this->attributes['options']);
        }
    }

    public function parseElement()
    {
        $element = sprintf('<select%s>%s</select>', $this->parseAttributes($this->attributes), $this->parseOptions());
        return $element;
    }

    private function parseOptions()
    {
        // Pegando a tabela do banco de dados:
        $db = new Model();
        $db->tableName = $this->options['table-name'];
        // Setando a condição de pesquisa:
        $where = (isset($this->options['condition'])) ? $this->options['condition'] : null;
        // Setando ordem de exibiçao dos resultados:

        // Executando query:
        $options = $db->Read($where, $this->options['text-field']." ASC");
        $fields = '';
        if (null !== $this->defaultValue) {
            // Temos um valor padrão a ser definido:
            $fields .= '<option value="'. $this->defaultValue['value'] .'">' . $this->defaultValue['text'] . '</option>';
        }
        foreach ($options as $option) {
            if (true === $this->repopulate and $option[$this->options['value-field']] === $this->previousValue) {
                $fields .= '<option value="'. $option[$this->options['value-field']] .'" selected="selected">' . $option[$this->options['text-field']] . '</option>';
            } else {
                $fields .= '<option value="'. $option[$this->options['value-field']] .'">' . $option[$this->options['text-field']] . '</option>';
            }
        }
        return $fields;
    }
}
