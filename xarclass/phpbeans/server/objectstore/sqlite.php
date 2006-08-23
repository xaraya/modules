<?php
/**
 * About the thinnest object store i could come up with
 * without any middle ware or anything.
 *
 * @copyright HS-Development BV, 2006-08-20
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://hsdev.com
 * @author Marcel van der Boom <mrb@hsdev.com>
**/

class SQLiteObjectStore implements IObjectStore
{
    private $db;
    
    private $sql_create = 'CREATE TABLE object_store(id char(40) primary key, object text)';
    private $sql_insert = 'INSERT INTO object_store(id,object) VALUES ';
    private $sql_fetch  = 'SELECT object FROM object_store WHERE id = ';
    private $sql_delete = 'DELETE FROM object_store WHERE id = ';
    
    function __construct(SQLiteDatabase &$db)
    {
        $this->db =& $db;
        // if table !exists, create it
        if(!@$this->db->queryExec($this->sql_create,$err))
        {
            // @todo Well, erm, yes.
            if($err != 'table object_store already exists') 
                echo "WARNING: $err\n";
        }
    }
    
    public function store(&$object = null)
    {
        $o = serialize($object);
        $id = sha1($o);
        // Insert the object, if that fails because of a primary key violation
        // we dont need to do anything since we use a hash on the content.
        if(@!$this->db->queryExec(
            $this->sql_insert . $this->values($id, $o), 
            $err
        )) 
        {
            // @todo Well, erm, yes.
            if($err != 'column id is not unique') 
            {
                echo "WARNING: $err\n";
                return false;
            }
        }
        return $id;
    }
    
    public function &fetch($id)
    {
        $sql = $this->sql_fetch . $this->quote($id);
        $res = $this->db->query($sql, SQLITE_ASSOC, $err);
         if(!$res) 
            return false;
        $raw = $res->fetchSingle();
        $object = unserialize($raw);
        return $object;
    }
    
    public function delete($id)
    {
        $sql = $this->sql_delete . $this->quote($id);
        $res = $this->db->queryExec($sql, $err);
        if(!$res)
            return false;
        return true;
    }
    
    /* Private methods */    
    private function quote($val) 
    {
        $val = trim($val);
        if(is_numeric($val)) 
            return $val;
        return '\'' . sqlite_escape_string($val) . '\'';
    }
    
    private function values($i, $o)
    {
        return '(' . $this->quote($i) . ',' . $this->quote($o) . ')';
    }
}

?>