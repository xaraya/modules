<?php
/**
 * Minimal layer to manage access rules in a sqlite database.
 *
 * @copyright HS-Development BV, 2006
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @author Marcel van der Boom <mrb@hsdev.com>
**/

class SqliteAccessRules implements IAccess
{
    public $db;
    
    // @todo Guess ;-)
    private $schema = "
        CREATE table identities (
            identity char(50) primary key,
            pass char(50)
        );
        CREATE table access_rules (
            identity char(50),
            item     char(50),
            type     char(6),
            primary key(identity, item, type)
        );
        CREATE TRIGGER identity_insert
        -- Prevent inserting rules for non existing identities
        BEFORE INSERT ON [access_rules]
        BEGIN
          SELECT RAISE(ROLLBACK, 'there is no identity rule for this user, create one first before adding other access rules')
          WHERE NEW.identity IS NOT NULL AND (SELECT identity FROM identities WHERE identity = NEW.identity) IS NULL;
        END;
        -- Prevent updating identities in the access rules table directly
        CREATE TRIGGER identity_update
        BEFORE UPDATE ON [access_rules] 
        BEGIN
          SELECT RAISE(ROLLBACK, 'you cant update access rules directly, change the identity of the user and all access rules will be updated automatically')
              WHERE NEW.identity IS NOT NULL AND (SELECT identity FROM identities WHERE identity = NEW.identity) IS NULL;
        END;
        -- If an identity is removed, also remove all its access rules.
        CREATE TRIGGER identity_remove
        BEFORE DELETE ON identities
        BEGIN 
            DELETE FROM access_rules WHERE access_rules.identity = OLD.identity;
        END;";
        
    function __construct(SQLiteDatabase &$db)
    {
        $this->db =& $db;
        // if table !exists, create it
        if(!@$this->db->queryExec($this->schema,$err))
        {
            // @todo Well, erm, yes.
            echo "WARNING: $err\n";
        }
    }
    
    function HostRules($user)
    {
        return new SQLiteHostRule($user,$this);
    }
    function ObjectRules($user)
    {
        return new SQLiteObjectRule($user,$this);
    }
    function IdentityRules($user)
    {
        return new SQLiteIdentityRule($user,$this);
    }
    
    function query($sql)
    {
        $res = @$this->db->query($sql, SQLITE_ASSOC, $err);
        //echo "$sql\n";
        if($err)
        {
            throw new Exception($err);
        }
        return $res;
    }
    
    function exec($sql)
    {
        $res = @$this->db->queryExec($sql, $err);
        //echo "$sql\n";
        if($err) {
            throw new Exception($err);
        }
        return true;
    }
}

abstract class SQLiteRule 
{
    protected $connector = null;
    protected $user      = '';
    
    function __construct($user, SQLiteAccessRules &$rules)
    {
        $this->connector =& $rules;
        $this->user = $user;
    }
    
    abstract function canUse($item);
    
    protected function ApplyRule($sql)
    {
        $res = $this->connector->query($sql);
        return $res->numRows() != 0;
    }
    
    protected function quote($val) 
    {
        $val = trim($val);
        if(is_numeric($val)) 
            return $val;
        return '\'' . sqlite_escape_string($val) . '\'';
    }
}

class SQLiteHostRule extends SQLiteRule 
{
    function canUse($item)
    {
        $sql = 
            'SELECT identity FROM access_rules ' . 
            'WHERE identity='. $this->quote($this->user) . ' AND ' .
            "      type='host' AND " . 
            "      (item='*' OR item=" . $this->quote($item) . ')';
        return $this->ApplyRule($sql);
    }
    
    function add($item)
    {
        $sql = 
            'INSERT INTO access_rules(identity,item,type) VALUES (' . 
            $this->quote($this->user) . ',' . $this->quote($item) . ",'host')";
        return $this->connector->exec($sql);
    }
    
    function remove($item)
    {
        $sql = 
            'DELETE FROM access_rules ' . 
            'WHERE identity=' . $this->quote($this->user). ' AND ' .
            "      type='host' AND item=" . $this->quote($item);
        return $this->connector->exec($sql);
    }
    
    function get()
    {
        $sql = 
            'SELECT identity, item as host FROM access_rules ' .
            "WHERE  type='host' AND identity=".$this->quote($this->user);
        $res = $this->connector->query($sql);
        return $res->fetchAll();
    }
}

class SQLiteObjectRule extends SQLiteRule 
{
    function canUse($item)
    {
        $sql = 
            "SELECT identity FROM access_rules WHERE identity=" . 
            $this->quote($this->user) . 
            " AND type='object' AND (item='*' OR item=" . $this->quote($item) . ')';
        return $this->ApplyRule($sql);
    }
    
    function add($item)
    {
        $sql = 
            "INSERT INTO access_rules(identity,item,type) VALUES (" . 
            $this->quote($this->user) . "," . $this->quote($item) . ",'object')";
        return $this->connector->exec($sql);
    }
    
    function remove($item)
    {
        $sql = 
            'DELETE FROM access_rules ' . 
            'WHERE identity=' . $this->quote($this->user) . ' AND ' . 
            "      type='object' AND item=" . $this->quote($item);
        return $this->connector->exec($sql);
    }
    
    function get()
    {
        $sql = 
            "SELECT identity, item as object FROM access_rules " .
            "WHERE type='object' AND identity=".$this->quote($this->user);
        $res = $this->connector->query($sql);
        return $res->fetchAll();
    }

}

class SQLiteIdentityRule extends SQLiteRule 
{
    function canUse($item)
    {
        $sql = 
            'SELECT identity FROM identities ' . 
            'WHERE identity=' . $this->quote($this->user) . ' AND pass=' . $this->quote($item);
        return $this->ApplyRule($sql);
    }
    
    function add($item)
    {
        $sql = 
            "INSERT INTO identities(identity,pass) VALUES (" . 
            $this->quote($this->user) . "," . $this->quote($item) . ")";
        return $this->connector->exec($sql);
    }
    
    function remove($item = 'irrelevant')
    {
        $sql = 
            'DELETE FROM identities ' . 
            'WHERE identity=' . $this->quote($this->user);
        return $this->connector->exec($sql);
    }
    
    function get()
    {
        $sql = "SELECT identity FROM identities";
        $res = $this->connector->query($sql);
        return $res->fetchAll();
    }
}



?>