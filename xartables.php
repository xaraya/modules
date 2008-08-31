<?php
/**
 *
 * Table information
 *
 */

    function xarayatesting_xartables()
    {
        // Initialise table array
        $xartable = array();

        $xartable['xarayatesting'] = xarDB::getPrefix() . '_xarayatesting_tests';

        // Return the table information
        return $xartable;
    }
?>
