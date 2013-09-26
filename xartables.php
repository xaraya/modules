<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 *
 * Table information
 *
 */

    function eav_xartables()
    {
        // Initialise table array
        $xartable = array();

        $xartable['eav_attributes']        = xarDB::getPrefix() . '_eav_attributes';
        $xartable['eav_entities']          = xarDB::getPrefix() . '_eav_entities';

        // Return the table information
        return $xartable;
    }
?>
