<?php 
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information for the Newsletter tables
 */
function newsletter_xartables()
{
    // Set module prefix for tables
    $issuesPrefix = "_nwsltr";

    // Initialise table array
    $xartable = array();

    // Name for publications
    $nwsltrPublications = xarDBGetSiteTablePrefix() . $issuesPrefix . '_publications';

    // Set the table name for publications
    $xartable['nwsltrPublications'] = $nwsltrPublications;

    // Name for issues
    $nwsltrIssues = xarDBGetSiteTablePrefix() . $issuesPrefix . '_issues';

    // Set the table name for issues
    $xartable['nwsltrIssues'] = $nwsltrIssues;

    // Name for topics
    $nwsltrTopics = xarDBGetSiteTablePrefix() . $issuesPrefix . '_topics';

    // Set the table name for topics
    $xartable['nwsltrTopics'] = $nwsltrTopics;

    // Name for owners
    $nwsltrOwners = xarDBGetSiteTablePrefix() . $issuesPrefix . '_owners';

    // Set the table name for owners
    $xartable['nwsltrOwners'] = $nwsltrOwners;

    // Name for disclaimers
    $nwsltrDisclaimers = xarDBGetSiteTablePrefix() . $issuesPrefix . '_disclaimers';

    // Set the table name for disclaimers
    $xartable['nwsltrDisclaimers'] = $nwsltrDisclaimers;

    // Name for stories
    $nwsltrStories = xarDBGetSiteTablePrefix() . $issuesPrefix . '_stories';

    // Set the table name for stories
    $xartable['nwsltrStories'] = $nwsltrStories;

    // Name for subscriptions
    $nwsltrSubscriptions = xarDBGetSiteTablePrefix() . $issuesPrefix . '_subscriptions';

    // Set the table name for subscriptions
    $xartable['nwsltrSubscriptions'] = $nwsltrSubscriptions;

    // Name for alternative subscriptions
    $nwsltrAltSubscriptions = xarDBGetSiteTablePrefix() . $issuesPrefix . '_altsubscriptions';

    // Set the table name
    $xartable['nwsltrAltSubscriptions'] = $nwsltrAltSubscriptions;

    // Return the table information
    return $xartable;
}

?>
