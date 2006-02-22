<?php
class ReflectionInfo
{
    const FNC = 1;
    const CLS = 2;
    const EXT = 3;
    const CON = 4;
    const INT = 5;

    static function &GetInfo($name, $type = ReflectionInfo::FNC)
    {
        switch($type) {
            case ReflectionInfo::FNC: // Function
                $clazz = 'ad_ReflectionFunction';
                break;
            case ReflectionInfo::CLS: // Class
            case 5: // Interface
                $clazz = 'ad_ReflectionClass';
                break;
            case ReflectionInfo::EXT: // Extension
                $clazz = 'ad_ReflectionExtension';
                break;
            case ReflectionInfo::CON: // Constants
                $clazz = 'ad_ReflectionConstant';
                break;
        }
        $object = new $clazz($name);
        return $object;
    }

    static function GetId($name,$type)
    {
        return sha1($type.$name);
    }
}

class ad_ReflectionFunction extends ReflectionFunction
{
    function toArray()
    {
        $frParams = $this->getParameters();
        $params = array();
        foreach($frParams as $frParam) {
            $params[] = $frParam->toArray();
        }
        
        $info = array (
                       'id'         => ReflectionInfo::GetId($this->getName(),ReflectionInfo::FNC),
                       'type'       => 'function',
                       'name'       => $this->getName(),
                       'file'       => $this->getFileName(),
                       'start'      => $this->getStartLine(),
                       'end'        => $this->getEndLine(),
                       'returnsref' => $this->returnsReference(),
                       'params'     => $params,
                       'statics'    => $this->getStaticVariables(),
                       'doc'        => $this->getDocComment(),
                       'raw'        => $this->export($this->getName(),true)
                       );
        
        return $info;
    }

    function &getParameters()
    {
        // This gets an array of ReflectionParameter objects
        $params =& parent::getParameters();
        $pars = array();
        foreach($params as $key => $param) {
            if($param->getName()) {
                $pars[$key] = new ad_ReflectionParameter($this->getName(),$param->getName());
            }
        }
        return $pars;
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


class ad_ReflectionClass extends ReflectionClass
{
    function toArray()
    {
        // Properties
        $properties = array();
        $p = $this->getProperties();
        foreach($p as $key => $property) {
            $properties[] = $property->toArray();
        }

        // Methods
        $methods = array();
        $m = $this->getMethods();
        foreach($m as $key => $method) {
            $methods[] = $method->toArray();
        }

        // Parent
        $parent = $this->getParentClass();
        if($parent) {
            $parent = $parent->getName();
            $parentid = ReflectionInfo::GetID($parent,ReflectionInfo::CLS);
        }
        // Interfaces
        $ifs = $this->getInterfaces();
        $interfaces = array();
        if(!empty($ifs)) {
            foreach($ifs as $interface) {
                $interfaces[] = array('id' => ReflectionInfo::GetID($interface->getName(),ReflectionInfo::INT),
                                      'name'=>$interface->getName());
            }
        }
        $typeId = $this->isInterface() ? ReflectionInfo::INT : ReflectionInfo::CLS;
        $info = array (
                       'id'        => ReflectionInfo::GetID($this->getName(),$typeId),
                       'type'      => $this->isInterface() ? 'interface' : 'class',
                       'name'      => $this->getName(),
                       'file'      => $this->getFileName(),
                       'start'     => $this->getStartLine(),
                       'end'       => $this->getEndLine(),
                       'doc'       => $this->getDocComment(),
                       'constants' => $this->getConstants(),
                       'abstract'  => $this->isAbstract(),
                       'final'     => $this->isFinal(),
                       'methods'   => $methods,
                       'parent'    => $parent,
                       'parentid'  => isset($parentid) ? $parentid : null,
                       'interfaces'=> $interfaces,
                       'properties'=> $properties,
                       'raw'       => $this->export($this->getName(),true),
                       'extension' => $this->getExtensionName(),
                       'extensionid' => ReflectionInfo::GetID($this->getExtensionName(),ReflectionInfo::EXT),
                       'interface' => $this->isInterface()
                       );
        return $info;
    }

    function getConstants()
    {
        return parent::getConstants();
    }
    
    function &getMethods()
    {
        $methods = parent::getMethods();
        $meths = array();
        foreach($methods as $key => $method) {
            $meths[$key] = new ad_ReflectionMethod($this->getName(),$method->getName());
        }
        return $meths;
    }
    
    function &getProperties()
    {
        $properties = parent::getProperties();
        $props = array();
        foreach($properties as $key => $property) {
            $props[$key] = new ad_ReflectionProperty($this->getName(),$property->getName());
        }
        return $props;
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
        $params =& parent::getParameters();
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
        $dc =& parent::getDeclaringClass();
        $declaringClass = new ad_ReflectionClass($dc->getName());
        return $declaringClass;
    }
}

class ad_ReflectionExtension extends ReflectionExtension
{
    function &toArray()
    {
        // Functions
        $funcs = $this->getFunctions();
        $functions = array();
        foreach($funcs as $index => $function) {
            $functions[] = $function->toArray();
        }

        // Classes
        $classs = $this->getClasses();
        $classes = array();
        foreach($classs as $index => $class) {
            $classes[] = $class->toArray();
        }

        $info = array(
                      'id'        => ReflectionInfo::GetID($this->getName(),ReflectionInfo::EXT),
                      'type'      => 'extension',
                      'name'      => $this->getName(),
                      'version'   => $this->getVersion(),
                      'functions' => $functions,
                      'constants' => $this->getConstants(),
                      'classes'   => $classes,
                      'inientries'=> $this->getINIEntries(),
                      'raw'       => $this->export($this->getName(),true)

                      );
        return $info;
    }

    function &getFunctions()
    {
        $funcs = parent::getFunctions();
        $functions = array();
        foreach($funcs as $index => $function) {
            $functions[] = new ad_ReflectionFunction($function->getName());
        }
        return $functions;
    }

    function &getClasses()
    {
        $classs = parent::getClasses();
        $classes = array();
        foreach($classs as $index => $class) {
            $classes[] = new ad_ReflectionClass($class->getName());
        }
        return $classes;
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

class ad_ReflectionConstant 
{
    private $name = '';

    function __construct($name)
    {
        $this->name = $name;
        $tmp = get_defined_constants();
        $this->value = $tmp[$name];
    }

    function toArray()
    {
        $info = array(
                      'type' => 'constant',
                      'name' => $this->name,
                      'value'=> $this->value,
                      );
        return $info;
    }
}
?>