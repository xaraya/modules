<?php

// Function performs the same function as var_export() but without
// the fancy formatting and indentation, and also supported by earlier
// versions of PHP.

/**
 * $Id$
 * expand a variable into executable declaritive PHP code
 * @param $a value or array of any type or types
 * @returns string
 * @return string of $var expanded into a PHP definition using a recursive method
 */
function autolinks_userapi__varexport($a, $inside_array = 0) 
{
    switch (gettype($a))
    {
        case 'array':
            $i = 1;
            $len = count($a);
            reset($a);
            $result = 'array(';
            while (list($k, $v) = each($a)) {
                $result .= '\'' . str_replace('\'', '\\\'', $k) . '\'' . '=>' . autolinks_userapi__varexport($v, $i-$len);
                $i += 1;
            }
            $result .= ')';
            break;
        case 'string':
            $result = '\'' . str_replace('\'', '\\\'', $a) . '\'';
            break; 
        case 'boolean': 
            $result = ($a) ? 'true' : 'false'; 
            break; 
        default: 
            $result = $a; 
            break; 
    } 
    if ($inside_array) {
        return $result . ', ';
    } else {
        return $result;
    }
} 

/**
 * $Id$
 * expand a variable into executable declaritive PHP code
 * @param $var value or array of any type or types
 * @returns string
 * @return string of $var expanded into a PHP definition
 */
function autolinks_userapi_varexport($var)
{
    // Performs much the same function as var_export() but will work
    // on any version of PHP

    if (function_exists('var_export')) {
        // Use the PHP version if available (from 4.2.0)
        return var_export($var, true);
    } else {
        return autolinks_userapi__varexport($var);
    }
}

?>