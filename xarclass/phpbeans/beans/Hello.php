<?php
/**
 * Annotated Hello world bean for demonstration purposes
 *
 * @copyright HS-Development BV, 2006
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://hsdev.com
 * @author Marcel van der Boom <mrb@hsdev.com>
**/
/*
    Each Bean inherits from the PHP_Bean class and has a prefix of 'Bean_'
    in its classname. The part after that should be the basename of the file
    the Bean is contained in. (this will be changed later on to make it mor
    intelligent)
*/
class Bean_Hello extends PHP_Bean 
{
    /* 
        Each bean declares a namespace by which it can be called from the 
        client side. From the client's point of view this object is named
        'hello' So either:
            $tmp = $client->getObject('hello'));
            $tmp->world();
        or 
            $client->call(hello/world')
        will calle the world method on this Bean.
    */
    public $namespace = 'hello';
    
    /*
        Some beans may have the ability to save information in them. (by using
        the save() resp. the restore() methods). Typically, such information
        is coded in object properties to give them a place to live in.
    */
    public $saveme    = '';
    
    /*
        The rules for which methods are visible to a client are as follows:
        1. The constructor is never visible.
        2. private and protected methods are never visible
        3. public methods are only visible if the @access public line is
           present in the phpdoc comments, unless the parent class implements
           the same method (and is visible) This means that a method can 
           not be hidden if the parent exposes it (but reimplementing is
           possible obviously)
           @todo this rule sucks
    */
    
    /**
     * The obligatory hello world
     *
     * @access public
     * @return string
    **/
    function world($saveme = '')
    {
        /*
            This silly variable is here to show that you can save (and restore)
            a certain state of an object. Suppose you would call this object with
            parameter $saveme = 'something', then issue the save() method on the
            object. The save method will return a unique id to the client by 
            which the object state can be restored at any given time by calling
            restore(<the unique id>) on the object. The object will then be 
            restored to the state from the time at which the save() method was
            issued. Currently the objectserver artificially limits the storage
            of objects to one state to keep things simpler to test, but there is
            no reason could not store hundreds of states of the object.
        */
        if($saveme)
            $this->saveme = $saveme;
        /*
            You can return anything you like from a bean, the client will 
            receive the same PHP variable, object or value you return here.
        */
        return "Hello, world (" . $this->saveme . ")";
    }
}
?>