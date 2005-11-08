<?php 
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */
 
/*
 * Table information for hitcount module
 *
 * Original Author of file: Jim McDonald
 */
function hitcount_xartables()
{
    // Initialise table array
    $xartable = array();

    // Name for hitcount database entities
    $hitcount = xarDBGetSiteTablePrefix() . '_hitcount';

    // Table name
    $xartable['hitcount'] = $hitcount;

    // Return table information
    return $xartable;
}

?>