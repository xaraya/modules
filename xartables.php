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
        $xartable['foo_posts']         = xarDB::getPrefix() . '_foo_posts';
        $xartable['foo_users']         = xarDB::getPrefix() . '_foo_users';
        $xartable['foo_tags_posts']    = xarDB::getPrefix() . '_foo_tags_posts';
        $xartable['foo_subscriptions'] = xarDB::getPrefix() . '_foo_subscriptions';
        $xartable['foo_visits']        = xarDB::getPrefix() . '_foo_visits';

        // Return the table information
        return $xartable;
    }
?>
