<?php

/**
 * generate the common admin menu configuration
 */
function logconfig_adminapi_structuretophp($args)
{
    $structure = $args['structure'];
    
    if (is_array($structure))
    {
        $return = ' array(';
        foreach ($structure as $key => $value)
        {
            if (is_int($key)) {
                $return .= $key . '=>'; 
            } else {
                $return .= "'".addcslashes($key, '$\\\'')."'=>"; 
            }
            $array = array('structure' => $value); 
            $return .= logconfig_adminapi_structuretophp($array) .", \n";
        }
        $return .= ')';
    }
    //PHP/Xaraya Constants should be OK.
    elseif (is_string($structure))
    {
        $php_operators = array("||", "|", "&", "&&", "<<", ">>", "xor", "XOR", "and", "AND", "or", "OR");
        $constants_with_operator = str_replace($php_operators, "", $structure);
        
        $array = explode(' ', $constants_with_operator);
        $constants = true;
        foreach ($array as $test_value) {
                if (!empty($test_value) && !defined(trim($test_value))) {
                        $constants = false;
                }
        } 
        
        if  ($constants) {
            $return = $structure;
        } else {
            $return = "'". addcslashes($structure, '$\\\'') ."'";
        }
    }
//    elseif (is_numeric($structure) || is_bool($structure))
    else
    {
        $return = $structure;
    }
    
     return $return;
}

?>