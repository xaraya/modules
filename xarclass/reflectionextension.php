<?php
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
        sys::import(ReflectionInfo::fromhere('function'));
        foreach($funcs as $index => $function) {
            $functions[] = new ad_ReflectionFunction($function->getName());
        }
        return $functions;
    }

    function &getClasses()
    {
        $classs = parent::getClasses();
        $classes = array();
        sys::import(ReflectionInfo::fromhere('class'));
        foreach($classs as $index => $class) {
            $classes[] = new ad_ReflectionClass($class->getName());
        }
        return $classes;
    }

}
?>