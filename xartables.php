<?php
/**
 *
 * Table information
 *
 */

    function xarayatesting_xartables()
    {
        // Initialise table array
        $xartable = [];

        $xartable['xarayatesting'] = xarDB::getPrefix() . '_xarayatesting_tests';

        // Return the table information
        return $xartable;
    }
