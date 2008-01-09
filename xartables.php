<?php
/**
 * Xaraya Autolinks
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Autolinks Module
 * @author Jim McDonald
*/

function autolinks_xartables()
{
    // Initialise table array
    $xartable = array();

    // Set the table names
    $xartable['autolinks'] = xarDBGetSiteTablePrefix() . '_autolinks';
    $xartable['autolinks_types'] = xarDBGetSiteTablePrefix() . '_autolinks_types';

    // Return the table information
    return $xartable;
}

?>
