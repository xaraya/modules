<?php
/**
 *
 * Table information
 *
 */

    function mailer_xartables()
    {
        // Initialise table array
        $xartable = array();

        $xartable['mailer_mails']          = xarDB::getPrefix() . '_mailer_mails';
        $xartable['mailer_footers']        = xarDB::getPrefix() . '_mailer_footers';
        $xartable['mailer_history']        = xarDB::getPrefix() . '_mailer_history';

        // Return the table information
        return $xartable;
    }
?>
