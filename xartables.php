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

        $xartable['mailer_tags']          = xarDB::getPrefix() . '_mailer_tags';
        $xartable['mailer_posts']         = xarDB::getPrefix() . '_mailer_posts';
        $xartable['mailer_users']         = xarDB::getPrefix() . '_mailer_users';
        $xartable['mailer_tags_posts']    = xarDB::getPrefix() . '_mailer_tags_posts';
        $xartable['mailer_subscriptions'] = xarDB::getPrefix() . '_mailer_subscriptions';
        $xartable['mailer_visits']        = xarDB::getPrefix() . '_mailer_visits';

        // Return the table information
        return $xartable;
    }
?>
