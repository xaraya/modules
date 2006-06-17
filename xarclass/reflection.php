<?php
/**
 * Reflection wrapper to make the reflection info easily accessible
 *
 * @package autodoc
 * @copyright HS-Development BV, 2006-06-17
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://hsdev.com
 * @author Marcel van der Boom <mrb@hsdev.com>
 **/

/**
 * Factory class for reflection info
 *
 * Class constructs objects based on the parameters in the getInfo class method.
 *
 * @author Marcel van der Boom <mrb@hsdev.com>
 **/
class ReflectionInfo
{
    const FNC = 1;
    const CLS = 2;
    const EXT = 3;
    const CON = 4;
    const INT = 5;

    /**
     * Factory method to construct a reflection object of a certain type
     *
     * @return object
     * @author Marcel van der Boom
     **/
    static function &GetInfo($name, $type = ReflectionInfo::FNC)
    {
        switch($type) {
            case ReflectionInfo::FNC: // Function
                include_once(self::fromhere('function'));
                $clazz = 'ad_ReflectionFunction';
                break;
            case ReflectionInfo::CLS: // Class
                include_once(self::fromhere('class'));
                $clazz = 'ad_ReflectionClass';
                break;
            case ReflectionInfo::INT: // Interface
                include_once(self::fromhere('interface'));
                $clazz = 'ad_ReflectionInterface';
                break;
            case ReflectionInfo::EXT: // Extension
                include_once(self::fromhere('extension'));
                $clazz = 'ad_ReflectionExtension';
                break;
            case ReflectionInfo::CON: // Constants
                include_once(self::fromhere('constant'));
                $clazz = 'ad_ReflectionConstant';
                break;
        }
        $object = new $clazz($name);
        return $object;
    }

    /**
     * Get the unique ID of a named item
     *
     * @return string
     * @author Marcel van der Boom
     **/
    static function GetId($name,$type)
    {
        return sha1($type.$name);
    }
    /**
     * Helper method for easy inclusion
     *
     * @return void
     * @author Marcel van der Boom <mrb@hsdev.com>
     **/
    static function fromhere($type)
    {
        return dirname(__FILE__).'/reflection'.$type.'.php';
    }
}

class ad_ReflectionParameter extends ReflectionParameter
{
    function toArray()
    {
        // See if we can get some type hinting
        $clazz = $this->getClass();
        if(!is_null($clazz)) {
            $type = $clazz->getName();
        } elseif($this->isArray()) {
            $type = 'array';
        } 
            
        // Deal with the default value
        $default = null;
        if($this->isDefaultValueAvailable()) {
            $default = $this->getDefaultValue();
            if(!isset($type)) $type = gettype($default);
            if(is_null($default)) {
                $default = 'null';
                $type = '';
            } elseif(is_string($default)) {
                $default ="'$default'";
                $type = 'string';
            } elseif(is_bool($default)) {
                $default = empty($default) ? 'false' : 'true';
            } 
        }

        $info =  array (
                        'type'     => 'parameter',
                        'name'     => $this->getName(),
                        'type'    =>  !isset($type) ? '' : $type,
                        'byref'    => $this->isPassedByReference(),
                        'default'  => $default,
                        'optional' => $this->isOptional(),
                        //'raw'      => $this->export(,$this->getName(),true) // how do we get the function name here?
                        );
        return $info;
    }
}

class ad_ReflectionMethod extends ReflectionMethod
{
    function toArray()
    {
        $frParams = $this->getParameters();
        $params = array();
        foreach($frParams as $frParam) {
          $params[] = $frParam->toArray();
        }
        $dc = $this->getDeclaringClass();
        $declaringclass = $dc->getName();
        $info = array (
                       'type'       => 'method',
                       'name'       => $this->getName(),
                       'file'       => $this->getFileName(),
                       'start'      => $this->getStartLine(),
                       'end'        => $this->getEndLine(),
                       'returnsref' => $this->returnsReference(),
                       'params'     => $params,
                       'statics'    => $this->getStaticVariables(),
                       'doc'        => $this->getDocComment(),
                       'final'      => $this->isFinal(),
                       'abstract'   => $this->isAbstract(),
                       'public'     => $this->isPublic(),
                       'private'    => $this->isPrivate(),
                       'protected'  => $this->isProtected(),
                       'static'     => $this->isStatic(),
                       'declaringclass' => $declaringclass,
                       'declaringclassid' => ReflectionInfo::GetID($declaringclass,ReflectionInfo::CLS)
                       );
        
        return $info;
    }
    
    function &getParameters()
    {
        // This gets an array of ReflectionParameter objects
        $params = parent::getParameters();
        $pars = array();
        foreach($params as $key => $param) {
            if($param->getName()) {
                $pars[$key] = new ad_ReflectionParameter(array($this->class,$this->getName()), $param->getName());
            }
        }
        return $pars;
    }
    
    function &getDeclaringClass()
    {
        $dc = parent::getDeclaringClass();
        $declaringClass = new ad_ReflectionClass($dc->getName());
        return $declaringClass;
    }
}


class ad_ReflectionProperty extends ReflectionProperty
{
    function toArray()
    {
        $dc = $this->getDeclaringClass();
        $declaringclass = $dc->getName();
        $info = array(
                      'type'      => 'property',
                      'name'      => $this->getName(),
                      'public'    => $this->isPublic(),
                      'private'   => $this->isPrivate(),
                      'protected' => $this->isProtected(),
                      'static'    => $this->isStatic(),
                      'default'   => $this->isDefault(),
                      'declaringclass' => $declaringclass,
                      'declaringclassid' => ReflectionInfo::GetID($declaringclass,ReflectionInfo::CLS)
                       );
        return $info;
    }
}


?>