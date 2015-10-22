<?php

namespace Melyssa;

use Melyssa\Database\Conector;

/**
 * Model padrão do sistema:
 *
 * Responsável por transações e persistência de dados.
 *
 * @package		Melyssa Framework
 * @category            Library
 * @author		Jhonathas Cavalcante
 * @link		http://melyssaframework.com/user_guide/model
 *
 */
class Model
{
    /**
     * Instância do conector de banco de dados:
     * @var object
     */
    protected $db;

    /**
     * Nome da tabela, deve ser setado nos models filhos. (TableDataGateway)
     * @var string
     */
    public $tableName;

    /**
     * �ltimo registro inserido
     * @var string
     */
    public $lastInsertId;

    public function __construct()
    {
        $this->db = & Conector::getInstance();
    }

    public function save(array $dados)
    {
        $this->beforeSave();
        $indices = implode(", ", array_keys($dados));
        $valores = "'" . implode("', '", array_values($dados)) . "'";
        $q = $this->db->query("INSERT INTO `{$this->tableName}` ({$indices}) VALUES ({$valores})");

        $this->lastInsertId = $this->db->lastInsertId();
    }

    public function Read($where = null, $order = null, $limit = null, $offset = null)
    {
        $where  = ($where == null ? $where = null : $where = "WHERE " . $where);
        $order  = ($order == null ? $order = null : $order = "ORDER BY " . $order);
        $limit  = ($limit == null ? $limit = null : $limit = "LIMIT " . $limit);
        $offset = ($offset == null ? $offset = null : $offset = "OFFSET " . $offset);

        $q = $this->db->query("SELECT * FROM `{$this->tableName}` {$where} {$order} {$limit} {$offset}");
        $q->setFetchMode(\PDO::FETCH_ASSOC);
        $f = $q->fetchAll();

        $this->results = count($f);

        return $f;
    }

    public function getRow($where = null, $order = null, $limit = null, $offset = null)
    {
        $row = $this->Read($where, $order, $limit, $offset);
        if (count($row) != 0) {
            return $row[0];
        } else {
            false;
        }
    }

    public function getById($id, $idColumn = 'id')
    {
        $resultRow = $this->Read("{$idColumn} = '{$id}'");
        if ($this->countResults() > 0) {
            return $resultRow[0];
        } else {
            return array();
        }
    }

    public function countResults()
    {
        return $this->results;
    }

    public function update(array $dados, $where)
    {
        $laco = "";
        foreach ($dados as $campo => $valor) {
            $laco .= $campo . " = '" . $valor . "', ";
        }
        $laco = substr($laco, 0, -2);

        $q = $this->db->query("UPDATE `{$this->tableName}` SET {$laco} WHERE {$where}");
    }

    public function delete($where)
    {
        $q = $this->db->query("DELETE FROM `{$this->tableName}` WHERE {$where}");
        if ($q) {
            return 'Sucesso';
        }
    }

    public function beforeSave()
    {
        // This function must be implemented inside the child classes.
    }
}
