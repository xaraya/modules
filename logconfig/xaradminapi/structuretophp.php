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
    elseif (is_string($structure) && !defined($structure))
    {
        //using "" (not '') to allow hacks... Not sure if it's a good idea
        $return = "'". addcslashes($structure, '$\\\'') ."'";
    }
//    elseif (is_numeric($structure) || is_bool($structure))
    else
    {
        $return = $structure;
    }
    
     return $return;
}

?>
