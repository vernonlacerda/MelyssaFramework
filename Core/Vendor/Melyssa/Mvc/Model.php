<?php

namespace Melyssa\Mvc;

use Melyssa\Database\Conector;

class Model
{
    protected $db;
    public $_tabela;
    public $tableName;

    public function __construct()
    {
        $this->db = & Conector::getInstance();
    }

    public function create(array $dados)
    {
        $indices = implode(", ", array_keys($dados));
        $valores = "'" . implode("', '", array_values($dados)) . "'";
        $q = $this->db->query("INSERT INTO `{$this->_tabela}` ({$indices}) VALUES ({$valores})");
        return true;
    }

    public function Read($where = null, $order = null, $limit = null, $offset = null)
    {
        $where = ($where == null ? $where = null : $where = "WHERE " . $where);
        $order = ($order == null ? $order = null : $order = "ORDER BY " . $order);
        $limit = ($limit == null ? $limit = null : $limit = "LIMIT " . $limit);
        $offset = ($offset == null ? $offset = null : $offset = "OFFSET " . $offset);

        $q = $this->db->query("SELECT * FROM `{$this->tableName}` {$where} {$order} {$limit} {$offset}");
        $q->setFetchMode(\PDO::FETCH_ASSOC);
        $f = $q->fetchAll();

        $this->results = count($f);

        return $f;
    }

    public function countResults()
    {
        return $this->results;
    }

    public function make_query($query)
    {
        $q = $this->db->query($query);
        $q->setFetchMode(\PDO::FETCH_ASSOC);
        $result_set = $q->fetchAll();
        return $result_set;
    }

    public function update(array $dados, $where)
    {
        $laco = "";
        foreach ($dados as $campo => $valor) {
            $laco .= $campo . " = " . $valor . ", ";
        }
        $laco = substr($laco, 0, -2);

        $q = $this->db->query("UPDATE `{$this->_tabela}` SET {$laco} WHERE {$where}");
    }

    public function delete($where)
    {
        $q = $this->db->query("DELETE FROM `{$this->_tabela}` WHERE {$where}");
        if ($q) {
            return 'Sucesso';
        }
    }
}
