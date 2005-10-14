<?php
/**
 * File: $Id$
 *
 * Table information for blacklist utility module
 *
 * @package Modules
 * @copyright (C) 2002-2005 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage BlackList
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @access private
 * @returns array Array of table information for this module
 */

function blacklist_xartables()
{
    // Initialise table array
    $xartable = array();

    // Name for template database entities
    $blacklist_table = xarDBGetSiteTablePrefix() . '_blacklist';

    // Table name
    $xartable['blacklist'] = $blacklist_table;

    // Column names
    $xartable['blacklist_column'] = 
		array(
			'id'  => $blacklist_table . '.xar_id',
            'pid' => $blacklist_table . '.xar_domain'
        );

    // Return table information
    return $xartable;
}

?>
