<?php
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
