<?php
/**
 * Table information for encyclopedia module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function ledger_xartables()
{
    // Initialise table array
    $xartable = array();

    $encyclopedia = xarDBGetSiteTablePrefix() . '_encyclopedia';
    $encyclopedia_volumes = xarDBGetSiteTablePrefix() . '_encyclopedia_volumes';

    // Set the table name
    $xartable['encyclopedia'] = $encyclopedia;
    $xartable['encyclopedia_volumes'] = $encyclopedia_volumes;

    // Return the table information
    return $xartable;
}
?>