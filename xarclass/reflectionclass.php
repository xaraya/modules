<?php
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
?>