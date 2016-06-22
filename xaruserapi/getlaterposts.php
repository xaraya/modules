<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
 /**
 * Get the IDs of all posts later than a given time
 *
 * @author crisp <marc@luetolf-carroll.com>
 * @return array
 */
function crispbb_userapi_getlaterposts($args)
{
    // If we don't have a topic ID, return nothing
    if (!isset($args['tid'])) return array();
    // Make sure we have a timestamp
    if (!isset($args['ts'])) $args['ts'] = 0;
    // Do we include those posts at the timestamp's time?
    if (!isset($args['include_ts'])) $args['include_ts'] = 0;

    $tables = xarDB::getTables();
    $q = new Query('SELECT', $tables['crispbb_posts']);
    
    // For now, just return the ID and time fields
//    $q->addfield('id');
//    $q->addfield('ptime');
    
    // Only those posts that belong to this topic
    $q->eq('tid', $args['tid']);
    
    // Only posts with timestamps later than or equal to the timestamp passed
    if ($args['include_ts']) {
        $q->ge('ptime', $args['ts']);
    } else {
        $q->gt('ptime', $args['ts']);
    }
    
    // Set an ordering by time
    $q->setorder('ptime', 'ASC');
    
    // Run the query
    $q->run();
    
    
    return $q->output();

}
?>