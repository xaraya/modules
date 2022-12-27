<?php
/**
 * Karma Module
 *
 * @package modules
 * @subpackage karma
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 *
 * Table information
 *
 */

function karma_xartables()
{
    // Initialise table array
    $xartable = [];

    $xartable['karma_tags']          = xarDB::getPrefix() . '_karma_tags';
    $xartable['karma_posts']         = xarDB::getPrefix() . '_karma_posts';
    $xartable['karma_users']         = xarDB::getPrefix() . '_karma_users';
    $xartable['karma_tags_posts']    = xarDB::getPrefix() . '_karma_tags_posts';
    $xartable['karma_subscriptions'] = xarDB::getPrefix() . '_karma_subscriptions';
    $xartable['karma_visits']        = xarDB::getPrefix() . '_karma_visits';

    // Return the table information
    return $xartable;
}
