<?php
/**
 *
 * Table information for roles module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Foo Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  Table information for this module
 *
 * @param to be added
 * @return to be added
 *
 */

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function foo_xartables()
{
    // Initialise table array
    $xartable = array();

//    $foo = xarDBGetSiteTablePrefix() . '_foo';

    // Set the table name
//    $xartable['foo'] = $foo;

    // Return the table information
    return $xartable;
}

?>
