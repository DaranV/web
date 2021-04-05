<?php
namespace Database;

use PDO;
use Stan\Stan;
use PDOException;
use PDOStatement;

class Database extends PDO
{
    /**
     * Return Dababase Instances
     * @var array
     */
    protected static $instances = array();


    /**
     * Get an Instance of Database
     *
     * @param bool $group
     * @return Database|mixed
     */
    public static function get(bool $group = false) : Database
    {
        $stan = Stan::getInstance();
        
        $group = !$group ? array(
            'type' => $stan->configs->database->get("DB_TYPE"),
            'host' => $stan->configs->database->get("DB_HOST"),
            'name' => $stan->configs->database->get("DB_NAME"),
            'user' => $stan->configs->database->get("DB_USER"),
            'pass' => $stan->configs->database->get("DB_PASS")
        ) : $group;
		
        $type = $group['type'];
        $host = $group['host'];
        $name = $group['name'];
        $user = $group['user'];
        $pass = $group['pass'];
        
        $id = "$type.$host.$name.$user.$pass";
        
        if (isset(self::$instances[$id])) {
            return self::$instances[$id];
        } //isset(self::$instances[$id])
        
        try {
            $instance = new Database("$type:host=$host;dbname=$name;charset=utf8", $user, $pass);
            $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            self::$instances[$id] = $instance;
            
            return $instance;
        }
        catch (PDOException $e) {
            $e->getTraceAsString();
            return null;
        }
    }

    /**
     * Raw Query
     *
     * @param string $sql
     * @return PDOStatement
     */
    public function raw(string $sql) : PDOStatement
    {
        return $this->query($sql);
    }

    /**
     * Select Query
     *
     * @param string $sql
     * @param array $array
     * @param int $fetchMode
     * @param string $class
     * @return array
     */
    public function select(string $sql, array $array = array(), $fetchMode = PDO::FETCH_OBJ, string $class = '') : array
    {
        $stmt = $this->prepare($sql);
        foreach ($array as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue("$key", $value, PDO::PARAM_INT);
            } //is_int($value)
            else {
                $stmt->bindValue("$key", $value);
            }
        } //$array as $key => $value
        
        $stmt->execute();
        
        if ($fetchMode === PDO::FETCH_CLASS) {
            return $stmt->fetchAll($fetchMode, $class);
        } //$fetchMode === PDO::FETCH_CLASS
        else {
            return $stmt->fetchAll($fetchMode);
        }
    }

    /**
     * Insert Query
     *
     * @param string $table
     * @param array $data
     * @return string
     */

    public function insert(string $table, array $data) : string
    {
        ksort($data);
        
        $fieldNames  = implode(',', array_keys($data));
        $fieldValues = ':' . implode(', :', array_keys($data));
        $stmt = $this->prepare("INSERT INTO $table ($fieldNames) VALUES ($fieldValues)");
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        } //$data as $key => $value

        $stmt->execute();
        return $this->lastInsertId();
    }

    /**
     * Update Query
     *
     * @param string $table
     * @param array $data
     * @param array $where
     * @return int
     */

    public function update(string $table, array $data, array $where, $code = "=") : int
    {
        ksort($data);
        
        $fieldDetails = null;
        foreach ($data as $key => $value) {
            $fieldDetails .= "$key = :field_$key,";
        } //$data as $key => $value
        $fieldDetails = rtrim($fieldDetails, ',');
        
        $whereDetails = null;
        $i            = 0;
        foreach ($where as $key => $value) {
            if ($i == 0) {
                $whereDetails .= "$key $code :where_$key";
            } //$i == 0
            else {
                $whereDetails .= " AND $key $code :where_$key";
            }
            $i++;
        } //$where as $key => $value
        $whereDetails = ltrim($whereDetails, ' AND ');
        
        $stmt = $this->prepare("UPDATE $table SET $fieldDetails WHERE $whereDetails ");
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":field_$key", $value);
        } //$data as $key => $value
        
        foreach ($where as $key => $value) {
            $stmt->bindValue(":where_$key", $value);
        } //$where as $key => $value
        
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Delete Query
     *
     * @param string $table
     * @param array $where
     * @param int $limit
     * @param string $code
     * @return int
     */

    public function delete(string $table, array $where, int $limit = 1, string $code = "=") : int
    {
        ksort($where);
        
        $whereDetails = null;
        $i            = 0;
        foreach ($where as $key => $value) {
            if ($i == 0) {
                $whereDetails .= "$key $code :$key";
            } //$i == 0
            else {
                $whereDetails .= " AND $key $code :$key";
            }
            $i++;
        } //$where as $key => $value
        $whereDetails = ltrim($whereDetails, ' AND ');

        $uselimit = "";
        if (is_numeric($limit)) {
            $uselimit = "LIMIT $limit";
        } //is_numeric($limit)
        
        $stmt = $this->prepare("DELETE FROM $table WHERE $whereDetails $uselimit");
        
        foreach ($where as $key => $value) {
            $stmt->bindValue(":$key", $value);
        } //$where as $key => $value
        
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Truncate Query
     *
     * @param string $table
     * @return bool
     */

    public function truncate(string $table) : bool
    {
        return $this->exec("TRUNCATE TABLE $table");
    }

    /**
     * Drop Query
     *
     * @param string $table
     * @return bool
     */

    public function drop(string $table) : bool
    {
        return $this->exec("DROP TABLE $table");
    }
}
