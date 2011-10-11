<?php
/**
 * Ephemerids Module
 *
 * @package modules
 * @subpackage ephemerids module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */

function ephemerids_xartables()
{
    // Initialise table array
    $xartable = array();

    // Name for ephemerids database entities
    $ephem = xarDB::getPrefix() . '_ephem';

    // Table name
    $xartable['ephem'] = $ephem;

    return $xartable;
}
?>