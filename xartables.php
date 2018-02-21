<?php
/**
 * Cacher Module
 *
 * @package modules
 * @subpackage cacher
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2014 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 *
 * Table information
 *
 */

function cacher_xartables()
{
    // Initialise table array
    $xartable = array();

    $xartable['cacher_caches']          = xarDB::getPrefix() . '_cacher_caches';

    // Return the table information
    return $xartable;
}
?>