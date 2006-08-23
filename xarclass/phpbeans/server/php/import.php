<?php
/**
 * Safe and fast once inclusion of php classes based on a dotted path notation.
 *
 * Simplified copy from XARAYA xarPreCore.php
 * this version takes the include path into account (means PEAR classes get imported properly too)
**/
final class php
{
    private static $has = array();

    private function __construct() 
    {
        // no objects can be made out of this.
    }     
    
    public static function import($dp)
    {
        if(!isset(self::$has[$dp])) 
        {
            // set this *before* the include below
            self::$has[$dp] = true; 
            return include(str_replace('.', '/', $dp) . '.php');
        }
        return true;
    }
}
?>
