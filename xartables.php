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
 * This function is called internally by the core whenever the module is
 * loaded.
 *
 * @return array with tablenames (none for this module)
 */
function logconfig_xartables()
{
    // Initialise table array
    $xartable = array();

    // There are no module-specific tables here...

    // Return the table information
    return $xartable;
}

?>