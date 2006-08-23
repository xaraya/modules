<?php
/**
 * Base class for a simple object store 
 *
 * @copyright HS-Development BV, 2006-08-19
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://hsdev.com
 * @author Marcel van der Boom <mrb@hsdev.com>
**/

/* 
    Objects should be forced really to implement __sleep() and __wakeup()
    before they are allowed to be stored, but that is perhaps for later.
    We should define the interface and declare the store method as only accepting
    objects which implement this interface, like: 
        function store(IStorableObject &$object = null)

    For now, people will get what they bargained for.
*/
interface IStorableObject
{
    function __sleep();
    function __wakeup();
}

interface IObjectStore
{
    function   store(&$object = null);
    function  &fetch($id);
    function  delete($id);
}

class ObjectStore implements IObjectStore
{
    private $delegate = null; 
    
    public function __construct(&$type)
    {
        // See if we can figure out what the caller wants 
        if($type instanceof SQLiteDatabase)
        {
            php::import('server.objectstore.sqlite');
            $this->delegate = new SQLiteObjectStore($type);
        //} elseif($type instanceof Connection)
        //{
        //    php::import('server.objectstore.creole');
        //    $this->delegate = new CreoleObjectStore($type);
        } else
            throw Exception("Unknown Object Store type ($type)");
    }
    
    /* ObjectStore interface satisfaction */
    
    public function store(&$object = null)
    {
        return $this->delegate->store($object);
    }
    
    public function &fetch($id)
    {
        return $this->delegate->fetch($id);
    }
    
    public function delete($id)
    {
        return $this->delegate->delete($id);
    }
}
?>