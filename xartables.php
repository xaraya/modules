<?php
/**
 * Mailer Module
 *
 * @package modules
 * @subpackage mailer module
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
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
        $xartable['mailer_headers']        = xarDB::getPrefix() . '_mailer_headers';
        $xartable['mailer_footers']        = xarDB::getPrefix() . '_mailer_footers';
        $xartable['mailer_history']        = xarDB::getPrefix() . '_mailer_history';

        // Return the table information
        return $xartable;
    }
?>
