<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
    specifies module tables namees

    @param   none

    @return  $xartable array
*/
function security_xartables()
{
    // Initialise table array
    $xartable = array();

    // Name for ratings database entities
    $table = xarDBGetSiteTablePrefix() . '_security';

    // Table name
    $xartable['security'] = $table;
    $xartable['security_group_levels'] = $table . '_group_levels';
    $xartable['security_roles'] = $table . '_roles';

    // Return table information
    return $xartable;
}

?>