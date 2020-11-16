<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 *
 * Table information
 *
 */

    function realms_xartables()
    {
        // Initialise table array
        $xartable = array();

        $xartable['realms_realms']          = xarDB::getPrefix() . '_realms_realms';
        $xartable['realms_members']         = xarDB::getPrefix() . '_realms_members';

        // Return the table information
        return $xartable;
    }
