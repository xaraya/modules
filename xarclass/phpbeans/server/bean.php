<?php
/**
 * PHP Bean base class
 *
 * All available objects for remote exposure should inherit from this
 * class.
 *
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
**/
 
php::import('server.objectstore');

interface IPHP_Bean
{
    function listMethods();
    function   hasMethod($name = '');
    function  methodInfo($name);
    function  objectInfo(); 
}

/*  
    For now a simple save/restore, with the addition of fetch/delete etc. we
    can easily add support for saving multiple states of an object which is
    obviously very useful. In other words: this is a stack of depth one.
*/
interface IObjectState
{
    function     save();        // returns an $id for the saved state
    function &restore($id);     // restore the object as it was in state $id and removes it from storage again
}

class PHP_Bean implements IPHP_Bean, IObjectState
{
    public         $namespace     = null;
    public         $publicMethods = array();
    protected      $server        = null;
    private static $classes       = array();
    
    function __construct(&$server) 
    {
        $this->server =& $server;
        
        // Add both this and the callee to the dispatch map, but only once
        if(!isset(self::$classes[__CLASS__])) 
            $this->addMethods(__CLASS__);
        if(!isset(self::$classes[get_class($this)]))
            $this->addMethods(get_class($this));
    }

    /* IPHP_Bean interface satisfaction */

    /**
     * Retrieves the list of methods accessible from the current object.
     *
     * @access    public
     * @return    array
    **/
    function listMethods() 
    {
        return array_keys($this->publicMethods);
    }

    /**
     * Determines whether the specified method is provided by the current object.
     *
     * @access  public
     * @param   string
     * @return  boolean
    **/
    function hasMethod($name = '') 
    {
        if(!isset($this->publicMethods[$name])) 
        {
            // Note: no exception here.
            return false;
        }
        return true;
    }

    /**
     * Returns a hash with the name, description, parameters array, and return type of the current object.
     *
     * @access  public
     * @param   string
     * @return  array
    **/
    function methodInfo($name) 
    {
        if(!$this->hasMethod($name)) 
            return new ObjectServerException('Method not found',0);

        return $this->publicMethods[$name];
    }

    /**
     * Returns a hash with all of the public methods.
     *
     * @access    public
     * @return    array
    **/
    function objectInfo() 
    {
        return $this->publicMethods;
    }

    /* IObjectState interface satisfaction */
    
    /**
     * Saves the state of the current object
     *
     * @access   public
     * @param    string
     * @return   boolean
    **/
    function save() 
    {
        return $this->server->store($this);
    }

    /**
     * Restores the state of the current object from storage
     *
     * @access   public
     * @param    string
     * @return   boolean
    **/
    function &restore($id) 
    {
        $res =& $this->server->fetch($id);
        if(!$res) 
            return false;
        $named = array();
        foreach(get_object_vars($res) as $k => $v) 
            if(!is_object($v)) 
            {
                $this->{$k} = $v;
                $named[] = $k;
            }
        foreach(get_object_vars($this) as $k => $v) 
            if(!is_object($v) && !in_array($k, $named)) 
                unset($this->{$k});
        // Restored, remove from storage
        $this->server->delete($id);
        return $this;
    }
    
    /* Private methods */
    
    // @todo split this off into a reflection based class.
    private function determineMethods($class) 
    {
        $objReflect = new ReflectionClass($class);
        foreach($objReflect->getMethods() as $index => $methodInfo) 
        {
            // Return only the public methods and also leave the constructor 
            // behind.
            if(!$methodInfo->isPublic() or $methodInfo->isConstructor()) 
            {
                //fwrite(STDOUT,"$class : Not adding ". $methodInfo->getName() . "\n");
                continue;
            }
        
            
            // Description is taken from doc comment, if any
            $text= $methodInfo->getDocComment();
            // fetch description.
            $description = 'No description available.';
            if(preg_match('|/\*\*[\r\n\t ]+\*[\t ]+?([^@][^\r\n]+)|s', $text, $regs)) 
                $description = $regs[1];
            
            // Match on the @param something lines to gather some fuzzy info
            if(preg_match_all('/@([a-zA-Z0-9_-]+)[\t ]+([^\n\r]+)/s', $text, $regs, PREG_SET_ORDER)) 
            {
                $info = array();
                foreach($regs as $reg) 
                {
                    if(isset($info[$reg[1]])) 
                    {
                        if(!is_array($info[$reg[1]])) 
                            $info[$reg[1]] = array($info[$reg[1]]);
                        $info[$reg[1]][] = $reg[2];
                    } else 
                        $info[$reg[1]] = $reg[2];
                }
            }
            // If no access specifier was there, or there was something else than public, do not include the method
            if(!isset($info['access']) || $info['access'] != 'public') {
                //fwrite(STDOUT,"$class: Not adding ". $methodInfo->getName() . "\n");
                continue;
            }

            $params = array();
            foreach($methodInfo->getParameters() as $i => $paramInfo) 
            {
                $clazz = $paramInfo->getClass();
                if(!is_null($clazz)) 
                    $type = $clazz->getName();
                elseif($paramInfo->isArray()) 
                    $type = 'array';

                // Otherwise we'll use the fuzzy info
                if(!isset($type)) 
                    if(isset($info['param'][$i]))
                        $type = $info['param'][$i];
                    else
                        $type = 'unknown';
                $params[$paramInfo->getName()] = $type;
            }
            $methods[$methodInfo->getName()] = array(
                'description' => $description, 
                'parameters'  => $params,
                'return'      => isset($info['return']) ? $info['return'] : 'mixed'
            );
        }
        self::$classes[$class] = true;
        return $methods;
    }

    private function addMethods($class) 
    {
        $new = $this->determineMethods($class);
        $this->publicMethods = array_merge($this->publicMethods, $new);
    }

}

?>