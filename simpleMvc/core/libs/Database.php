<?php
/**
 * SimpleMvc
 *
 * @copyright 2016-2017 Jamie Kim
 */
namespace SimpleMvc;

/**
 * Database Class
 */
class Database
{
    private $debug;
    
    private $pdo;
    private $error;
    private $stmt;
    
    public static function getCurrentDatabaseName() {
        
        return (isset($_SESSION['Schema']) && !empty($_SESSION['Schema'])) ?
                $_SESSION['Schema'] : (self::$defaultDbName);
    }

    public function __construct($host, $databaseName, $userName, $password, $debug) {

        try {
            //set debug mode
            $this->debug = $debug;
            $this->pdo = new \PDO("mysql:host=$host;dbname=$databaseName", $userName, $password);

            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('SET NAMES "utf8"');
            
        } catch (\PDOException $e) {

            $this->pdo = null;
            echo 'Unable to connect to the database server.';
            $this->error = $e->getMessage();
            exit();
        }
    }
    
    public function query($query) {
        $this->stmt = $this->pdo->prepare($query);
    }    
    
    public function bind($param, $value, $type = null) {
        
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = \PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = \PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = \PDO::PARAM_NULL;
                    break;
                default:
                    $type = \PDO::PARAM_STR;
            }
        }
        
        $this->stmt->bindValue($param, $value, $type);
    }
    
    public function executeNormal() {
        
        try {
            $this->stmt->execute();
            return $this->stmt->rowCount();
    
        } catch (\PDOException $e) {
            return 0;
        }
    }
    
    public function executeDebug() {
        $ret = 0;
        $debugString = '';
        $start = 0;

        try {
            //get query string and start time
            $debugString = $this->stmt->queryString;
            $start = microtime(true);
            $this->stmt->execute();
            $ret = $this->stmt->rowCount();
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
        
        $excutedTime = round((microtime(true) - $start), 5);
        echo $debugString . "    [Excuted Time :$excutedTime] <br>";

        return $ret;
    }
    
    public function execute() {

        if ($this->debug) {
            return $this->executeDebug();
        } else {
            return $this->executeNormal();
        }
    }    
    
    public function resultSet() {
        
        $this->execute();
        return $this->stmt->fetchAll(\PDO::FETCH_BOTH);
    }
    
    public function resultSetByAssoc() {
        
        $this->execute();
        return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function resultSetByIndex() {

        $this->execute();
        return $this->stmt->fetchAll(\PDO::FETCH_NUM);
    }
    
    public function resultColumnSet($index = 0) {
    
        $this->execute();
        return $this->stmt->fetchAll(\PDO::FETCH_COLUMN, $index);
    }
    
    public function single() {
        
        $this->execute();
        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function executeSelectSingle($query) {
        
        $s = $this->pdo->prepare($query);
        $s->execute();
        
        return $s->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function executeSelectMultiple($query) {
        
        $s = $this->pdo->prepare($query);
        $s->execute();
        
        return $s->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function executeScalar($query) {

        $s = $this->pdo->prepare($query);
        try {
            $s->execute();
        }
        catch (Exception $e) {
            throw $e;
        }
        
        return $s->fetchColumn();
    }
    
    public function debugDumpParams() {
        
        return $this->stmt->debugDumpParams();
    }    
    
    public function rowCount() {

        return $this->stmt->rowCount();
    }
    
    public function lastInsertId() {
        
        return $this->pdo->lastInsertId();
    }    
    
    public function beginTransaction() {
        
        return $this->pdo->beginTransaction();
    }    
    
    public function endTransaction() {
        
        return $this->pdo->commit();
    }    
    
    public function cancelTransaction(){
        
        return $this->pdo->rollBack();
    }    
    
    public function insert($tableName, $fields, $countAsReturn = false) {

        $cols = array();
        $colVals = array();
        
        foreach ($fields as $name => $val) {
            $cols[] = $name;
            $colVals[] = ($name == 'password') ? "password(:$name)" : ":$name";
        }
        
        $query = "INSERT INTO $tableName (" . join(',', $cols) . ") VALUES (" . join(',', $colVals) . ")";
        $this->query($query);

        foreach ($fields as $name => $val) {
            $this->bind(":" . $name, $val);
            
            if (self::$queryDebug)
                echo "<br/>$name: $val<br/>";
        }
        
        $cnt = $this->execute();
        if ($countAsReturn == true)
            return $cnt;
            
        return $cnt ? $this->pdo->lastInsertId() : 0;
    }

    public function replace($tableName, $fields, $countAsReturn = false) {

        $cols = array();
        $colVals = array();
        
        foreach ($fields as $name => $val) {
            $cols[] = $name;
            $colVals[] = ($name == 'password') ? "password(:$name)" : ":$name";
        }
        
        $query = "REPLACE INTO $tableName (" . join(',', $cols) . ") VALUES (" . join(',', $colVals) . ")";
        $this->query($query);

        foreach ($fields as $name => $val) {
            $this->bind(":" . $name, $val);
        }
        
        $cnt = $this->execute();
        if ($countAsReturn == true)
            return $cnt;
            
        return $cnt ? $this->pdo->lastInsertId() : 0;
    }

    public function update($tableName, $columns, $idColName, $idColVal) {
        
        $cols = array();
        
        foreach ($columns as $name => $val) {
            $cols[] = ($name == 'password') ? "$name=password(:$name)" : "$name=:$name";
        }

        $query = "UPDATE $tableName SET " . join(',', $cols) . " WHERE $idColName='$idColVal'";
        $this->query($query);
        
        foreach ($columns as $name => $val) {
            $this->bind(":" . $name, $val);
        }

        $res = $this->execute();
        
        return $res;
    }
    
    public function updateEx($tableName, $columns, $conditionalColumns) {
        
        $cols = array();
        $condCols = array();
        
        foreach ($columns as $name => $val) {
            $cols[] = ($name == 'password') ? "$name=password(:$name)" : "$name=:$name";
        }
        
        foreach ($conditionalColumns as $name => $val) {
            $condCols[] = "$name='$val'";
        }

        $query = "UPDATE $tableName SET " . join(',', $cols) . " WHERE " . join(' AND ', $condCols);
        $this->query($query);
        
        foreach ($columns as $name => $val) {
            $this->bind(":" . $name, $val);
        }

        $res = $this->execute();
        
        return $res;
    }

    public function delete($tableName, $idColumnName, $idValue) {
        
        $query = "DELETE FROM $tableName WHERE $idColumnName='$idValue'";
        $this->query($query);

        return $this->execute();
    }
}
