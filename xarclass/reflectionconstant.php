<?php

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