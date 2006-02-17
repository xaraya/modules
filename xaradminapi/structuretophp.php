<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
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
        $php_operators = array("||", "|", "&", "&&", "<<", ">>", "xor", "XOR", "and", "AND", "or", "OR", "^");
        $constants_with_operator = str_replace($php_operators, "", $structure);

        $array = explode(' ', $constants_with_operator);
        $constants = true;
        foreach ($array as $test_value) {
                if (!empty($test_value) && !defined(trim($test_value))) {
                        $constants = false;
                }
        }

        if  ($constants || is_numeric($structure)) {
            $return = $structure;
        } else {
            $return = "'". addcslashes($structure, '$\\\'') ."'";
        }
    }
    // is_bool($structure)
    elseif (is_bool($structure))
    {
        $return = $structure;
    } else {
        //Maybe log an error here? I dont know what more could appear...
        //Gonna play safe.
        $return = "'". addcslashes($structure, '$\\\'') ."'";
    }

     return $return;
}

?>