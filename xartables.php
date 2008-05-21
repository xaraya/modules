<?php
/**
 * @package modules
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Recommend Module
 * @author John Cox
 */
/**
 * The tables information
 * @since 9 October 2007
 */
function recommend_xartables()
{
    // Initialise table array
    $xartable = array();
    $prefix = xarDBGetSiteTablePrefix();

    // Set the table name
    $xartable['recommend_recipients'] = $prefix . '_recipients';
    // Return the table information
    return $xartable;
}

?>