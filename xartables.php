<?php
/**
 * Foo Module
 *
 * @package modules
 * @subpackage foo module
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 *
 * Table information
 *
 */

    function foo_xartables()
    {
        // Initialise table array
        $xartable = array();

        $xartable['foo_tags']          = xarDB::getPrefix() . '_foo_tags';

        // Return the table information
        return $xartable;
    }
?>
