<?php
/*
 * File: $Id: $
 *
 * Import Issue Area Publication (issueareapub) data 
 * into Newsletter tables
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * Run this script to import Issue Area Publication (issueareapub) 
 * data into Newsletter tables
 * @public
 * @author Richard Cave
 * @returns array
 * @return $data
 */
function newsletter_admin_importissueareapub()
{
    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Get the admin menu
    $data = xarModAPIFunc('newsletter', 'admin', 'menu');

    // Initialize output array
    $data['output'] = array();

    // Initialize error to false
    $data['error'] = '';

    // Make sure the Newsletter module has been activated
    $modinfo = xarModGetInfo(1655);
    if ($modinfo['state'] != XARMOD_STATE_ACTIVE) {
        $data['error'] = xarML('Error: The Newsletter module has not been activated.  Please activate the module and run the import script again.');
        return $data;
    }

    // Load table maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Get database setup
    $dbconn =& xarDBGetConn();
    $nwsltrTables =& xarDBGetTables();

    // Move all Issue Area Publication categories under Newsletter category
    $iapCategory = xarModGetVar('issueareapub', 'mastercid'); 
    $nwsltrCategory = xarModGetVar('newsletter', 'mastercid'); 
    
    // Get direct child categories
    $iapCategories = xarModAPIFunc('categories',
                                   'user',
                                   'getchildren',
                                   array('cid' => $iapCategory,
                                         'return_itself' => false));
    
    if (!empty($iapCategories)) {
        foreach ($iapCategories as $category) {
            if (!xarModAPIFunc('categories',
                               'admin',
                               'updatecat',
                               array('cid'         => $category['cid'],
                                     'name'        => $category['name'],
                                     'description' => $category['description'],
                                     'image'       => $category['image'],
                                     'moving'      => true,
                                     'refcid'      => $nwsltrCategory,
                                     'inorout'     => 'in',
                                     'rightorleft' => 'right' 
                                 ))) {
                $data['error'] = xarML('Error: Could not move category #(1).', $category['name']);
                return $data;
            }
        }
    }

    // Set all modules variables
    xarModSetVar('newsletter', 'number_of_categories', xarModGetVar('issueareapub', 'number_of_categories'));
    xarModGetVar('newsletter', 'mastercid', xarModGetVar('issueareapub', 'mastercid'));
    xarModGetVar('newsletter', 'creategroups', xarModGetVar('issueareapub', 'creategroups'));
    xarModGetVar('newsletter', 'publishername', xarModGetVar('issueareapub', 'publishername'));
    xarModGetVar('newsletter', 'information', xarModGetVar('issueareapub', 'information'));
    xarModGetVar('newsletter', 'privacypolicy', xarModGetVar('issueareapub', 'privacypolicy'));
    xarModGetVar('newsletter', 'itemsperpage', xarModGetVar('issueareapub', 'itemsperpage'));
    xarModGetVar('newsletter', 'categorysort', xarModGetVar('issueareapub', 'categorysort'));
    xarModGetVar('newsletter', 'linkexpiration', xarModGetVar('issueareapub', 'linkexpiration'));
    xarModGetVar('newsletter', 'linkregistration', xarModGetVar('issueareapub', 'linkregistration'));
    xarModGetVar('newsletter', 'templateHTML', xarModGetVar('issueareapub', 'templateHTML'));
    xarModGetVar('newsletter', 'templateText', xarModGetVar('issueareapub', 'templateText'));
    xarModGetVar('newsletter', 'publisher', xarModGetVar('issueareapub', 'publisher'));
    xarModGetVar('newsletter', 'editor', xarModGetVar('issueareapub', 'editor'));
    xarModGetVar('newsletter', 'writer', xarModGetVar('issueareapub', 'writer'));
    xarModGetVar('newsletter', 'previewbrowser', xarModGetVar('issueareapub', 'previewbrowser'));
    xarModGetVar('newsletter', 'commentarysource', xarModGetVar('issueareapub', 'commentarysource'));
    xarModGetVar('newsletter', 'SupportShortURLs', xarModGetVar('issueareapub', 'SupportShortURLs'));
    

    // Get Issue Area Publiation tables
    $iapTables = get_issueareapub_tables();
    if (empty($iapTables)) {
        $data['error'] = xarML('Could not retrieve Issue Area Publiation tables');
        return $data;
    }

    // Import the owners data
    $nwsltrOwners = $nwsltrTables['nwsltrOwners'];
    $iapOwners = $iapTables['iapOwners'];
    $data['output'][] = xarML('Importing the #(1) table.', $iapOwners);

    // Get the iap owners
    $query = "SELECT xar_uid,
                     xar_rid,
                     xar_signature
              FROM $iapOwners";

    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) { 
        $data['error'] = xarML('Error retrieving data from #(1) table.', $iapOwners);
        return $data;
    }

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($uid,
             $rid,
             $signature) = $result->fields;

        $ownerID = xarModAPIFunc('newsletter',
                                 'admin',
                                 'createowner',
                                  array('id' => $uid,
                                        'rid' => $rid,
                                        'signature' => $signature));

        // Check for an error
        if (!$ownerID) { 
            // Skip if the owner already exists
            $data['output'][] = xarML('Owner ID #(1) already exists in #(2) table - skipping.', $uid, $nwsltrOwners);
        } else {
            $data['output'][] = xarML('Inserting data into #(1) table. Owner ID = #(2)', $nwsltrOwners, $uid);
        }
    }

    // Close result set
    $result->Close();

    // Import the disclaimers table
    $nwsltrDisclaimers = $nwsltrTables['nwsltrDisclaimers'];
    $iapDisclaimers = $iapTables['iapDisclaimers'];
    $data['output'][] = xarML('Importing the #(1) table.', $iapDisclaimers);
    $oldDisclaimers = array();

    // Get the iap disclaimers
    $query = "SELECT xar_id,
                     xar_title,
                     xar_text
              FROM $iapDisclaimers";

    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) { 
        $data['error'] = xarML('Error retrieving data from #(1) table.', $iapDisclaimers);
        return $data;
    }

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($id,
             $title,
             $text) = $result->fields;

        $disclaimerID = xarModAPIFunc('newsletter',
                                      'admin',
                                      'createdisclaimer',
                                       array('title' => $title,
                                             'disclaimer' => $text));

        // Check for an error
        if (!$disclaimerID) { 
            $data['error'] = xarML('Error inserting data into #(1) table.', $nwsltrDisclaimers);
            return $data;
        } else {
            // Set old id to the new id 
            $oldDisclaimers[$id] = $disclaimerID;
            $data['output'][] = xarML('Inserting data into #(1) table.  Old ID = #(2) New ID = #(3)', $nwsltrDisclaimers, $id, $disclaimerID);
        }
    }

    // Close result set
    $result->Close();
    
    // Import the publication data
    $nwsltrPublications = $nwsltrTables['nwsltrPublications'];
    $iapPublications = $iapTables['iapPublications'];
    $data['output'][] = xarML('Importing the #(1) table.', $iapPublications);
    $oldPublications = array();

    // Get items
    $query = "SELECT xar_id,
                     xar_cid,
                     xar_altcids,
                     xar_ownerid,
                     xar_template_html,
                     xar_template_text,
                     xar_title,
                     xar_logo,
                     xar_linkexpiration,
                     xar_linkregistration,
                     xar_description,
                     xar_disclaimerid,
                     xar_introduction,
                     xar_private
              FROM   $iapPublications";

    $result = $dbconn->Execute($query);

    // Check for an error
    if (!$result) { 
        $data['error'] = xarML('Error retrieving data from #(1) table.', $iapPublications);
        return $data;
    }

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($id,
             $cid,
             $altcids,
             $ownerId,
             $templateHTML,
             $templateText,
             $title,
             $logo,
             $linkExpiration,
             $linkRegistration,
             $description,
             $disclaimerId,
             $introduction,
             $private) = $result->fields;

        // Set new field subject to 0
        $subject = 0;

        // Insert new publication
        $pubID = xarModAPIFunc('newsletter',
                               'admin',
                               'createpublication',
                               array('ownerId' => $ownerId,
                                     'categoryId' => $cid,
                                     'altcids' => $altcids,
                                     'title' => $title,
                                     'introduction' => $introduction,
                                     'templateHTML' => $templateHTML,
                                     'templateText' => $templateText,
                                     'logo' => $logo,
                                     'linkExpiration' => $linkExpiration,
                                     'linkRegistration' => $linkRegistration,
                                     'disclaimerId' => $oldDisclaimers[$disclaimerId],
                                     'description' => $description,
                                     'private' => $private,
                                     'subject' => $subject));
        // Check for an error
        if (!$pubID) { 
            $data['error'] = xarML('Error inserting data into #(1) table.', $nwsltrPublications);
            return $data;
        } else {
            // Set old id to the new id 
            $oldPublications[$id] = $pubID;
            $data['output'][] = xarML('Inserting data into #(1) table.  Old ID = #(2) New ID = #(3)', $nwsltrPublications, $id, $pubID);
        }
    }

    // Close result set
    $result->Close();

    // Import the issues table
    $nwsltrIssues = $nwsltrTables['nwsltrIssues'];
    $iapIssues = $iapTables['iapIssues'];
    $data['output'][] = xarML('Importing the #(1) table.', $iapIssues);
    $oldIssues = array();

    // Get the owners
    $query = "SELECT xar_id,
                     xar_pid,
                     xar_title,
                     xar_ownerid,
                     xar_external,
                     xar_editornote,
                     xar_datepublished
              FROM  $iapIssues";

    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) { 
        $data['error'] = xarML('Error retrieving data from #(1) table.', $iapIssues);
        return $data;
    }

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($id,
             $pid,
             $title,
             $ownerId,
             $external,
             $editorNote,
             $timestamp) = $result->fields;

        $issueID = xarModAPIFunc('newsletter',
                                 'admin',
                                 'createissue',
                                 array('publicationId' => $oldPublications[$pid],
                                       'ownerId' => $ownerId,
                                       'title' => $title,
                                       'editorNote' => $editorNote,
                                       'external' => $external,
                                       'tstmpDatePublished' => $timestamp));

        // Check for an error
        if (!$issueID) { 
            $data['error'] = xarML('Error inserting data into #(1) table.', $nwsltrIssues);
            return $data;
        } else {
            // Set old id to the new id 
            $oldIssues[$id] = $issueID;
            $data['output'][] = xarML('Inserting data into #(1) table. Old ID = #(2) New ID = #(3)', $nwsltrIssues, $id, $issueID);
        }
    }

    // Close result set
    $result->Close();
    
    // Import the stories table
    $nwsltrStories = $nwsltrTables['nwsltrStories'];
    $iapStories = $iapTables['iapStories'];
    $data['output'][] = xarML('Importing the #(1) table.', $iapStories);
    $oldStories = array();

    // Get the iap stories 
    $query = "SELECT xar_id,
                     xar_ownerid,
                     xar_pid,
                     xar_cid,
                     xar_title,
                     xar_source,
                     xar_content,
                     xar_priority,
                     xar_storydate,
                     xar_altdate,
                     xar_datepublished,
                     xar_fulltextlink,
                     xar_registerlink,
                     xar_commentary,
                     xar_commentarysrc
              FROM $iapStories";

    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) { 
        $data['error'] = xarML('Error retrieving data from #(1) table.', $iapStories);
        return $data;
    }

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($id,
             $ownerId,
             $pid,
             $cid,
             $title,
             $source,
             $content,
             $priority,
             $tstmpStoryDate,
             $altDate,
             $tstmpDatePublished,
             $fullTextLink,
             $registerLink,
             $commentary,
             $commentarySource) = $result->fields;

        // Set linkExpiration to default
        $linkExpiration = xarModGetVar('newsletter', 'linkexpiration');

        // Create story
        $storyID = xarModAPIFunc('newsletter',
                                 'admin',
                                 'createstory',
                                  array('ownerId' => $ownerId,
                                        'publicationId' => $oldPublications[$pid],
                                        'categoryId' => $cid,
                                        'title' => $title,
                                        'source' => $source,
                                        'content' => $content,
                                        'priority' => $priority,
                                        'tstmpStoryDate' => $tstmpStoryDate,
                                        'altDate' => $altDate,
                                        'fullTextLink' => $fullTextLink,
                                        'registerLink' => $registerLink,
                                        'linkExpiration' => $linkExpiration,
                                        'commentary' => $commentary,
                                        'commentarySource' => $commentarySource,
                                        'tstmpDatePublished' => $tstmpDatePublished));

        // Check for an error
        if (!$storyID) { 
            $data['error'] = xarML('Error inserting data into #(1) table.', $nwsltrStories);
            return $data;
        } else {
            // Set old id to the new id 
            $oldStories[$id] = $storyID;
            $data['output'][] = xarML('Inserting data into #(1) table. Old ID = #(2) New ID = #(3)', $nwsltrStories, $id, $storyID);
        }
    }

    // Close result set
    $result->Close();
    
    // Import the topics table
    $nwsltrTopics = $nwsltrTables['nwsltrTopics'];
    $iapTopics = $iapTables['iapTopics'];
    $data['output'][] = xarML('Importing the #(1) table.', $iapTopics);
    
    // Get the iap topics
    $query = "SELECT xar_issueid,
                     xar_storyid,
                     xar_cid,
                     xar_order
              FROM $iapTopics";

    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) { 
        $data['error'] = xarML('Error retrieving data from #(1) table.', $iapTopics);
        return $data;
    }

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($issueId,
             $storyId,
             $cid,
             $order) = $result->fields;
        
        // Create topic 
        $topic =xarModAPIFunc('newsletter',
                              'admin',
                              'createtopic',
                               array('issueId' => $oldIssues[$issueId],
                                     'storyId' => $oldStories[$storyId],
                                     'cid' => $cid,
                                     'order' => $order));

        // Check for an error
        if (!$topic) { 
            $data['error'] = xarML('Error inserting data into #(1) table.', $nwsltrTopics);
            return $data;
        } else {
            // Set old id to the new id 
            $data['output'][] = xarML('Inserting data into #(1) table. Issue/Story IDs = #(2)-#(3)', $nwsltrTopics, $issueId, $storyId);
        }
    }

    // Close result set
    $result->Close();

    // Import the subscriptions table
    $nwsltrSubscriptions = $nwsltrTables['nwsltrSubscriptions'];
    $iapSubscriptions = $iapTables['iapSubscriptions'];
    $data['output'][] = xarML('Importing the #(1) table.', $iapSubscriptions);

    // Get the iap subscriptions
    $query = "SELECT xar_uid,
                     xar_pid,
                     xar_htmlmail
              FROM $iapSubscriptions";

    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) { 
        $data['error'] = xarML('Error retrieving data from #(1) table.', $iapSubscriptions);
        return $data;
    }

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($uid,
             $pid,
             $htmlmail) = $result->fields;
        
        // Create alt subscription
        $subID = xarModAPIFunc('newsletter',
                               'admin',
                               'createsubscription',
                                array('uid' => $uid,
                                      'pid' => $oldPublications[$pid],
                                      'htmlmail' => $htmlmail));

        // Check for an error
        if (!$subID) { 
            $data['error'] = xarML('Error inserting data into #(1) table.', $nwsltrSubscriptions);
            return $data;
        } else {
            $data['output'][] = xarML('Inserting data into #(1) table. Subscription ID = #(2)', $nwsltrSubscriptions, $uid);
        }
    }

    // Close result set
    $result->Close();

    // Import the alternative subscriptions table
    $nwsltrAltSubscriptions = $nwsltrTables['nwsltrAltSubscriptions'];
    $iapAltSubscriptions = $iapTables['iapAltSubscriptions'];
    $data['output'][] = xarML('Importing the #(1) table.', $iapAltSubscriptions);

    // Get the iap alternative subscriptions
    $query = "SELECT xar_id,
                     xar_name,
                     xar_email,
                     xar_pid,
                     xar_htmlmail
              FROM $iapAltSubscriptions";

    $result =& $dbconn->Execute($query);

    // Check for an error
    if (!$result) { 
        $data['error'] = xarML('Error retrieving data from #(1) table.', $iapAltSubscriptions);
        return $data;
    }

    // Put items into result array
    for (; !$result->EOF; $result->MoveNext()) {
        list($id,
             $name,
             $email,
             $pid,
             $htmlmail) = $result->fields;
        
        // Create alt subscription
        $altID = xarModAPIFunc('newsletter',
                               'admin',
                               'createaltsubscription',
                                array('name' => $name,
                                      'email' => $email,
                                      'pid' => $oldPublications[$pid],
                                      'htmlmail' => $htmlmail));

        // Check for an error
        if (!$altID) { 
            $data['error'] = xarML('Error inserting data into #(1) table.', $nwsltrAltSubscriptions);
            return $data;
        } else {
            $data['output'][] = xarML('Inserting data into #(1) table. Alt Subscription ID = #(2)', $nwsltrAltSubscriptions, $altID);
        }
    }

    // Close result set
    $result->Close();
 
    // Done!
    $data['output'][] = xarML('Finished importing data');

    // Return the template variables defined in this function
    return $data;
}

/*
 * Retrieve Issue Area Pub tables
 */
function get_issueareapub_tables()
{
    // Load table maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Set module prefix for tables
    $issuesPrefix = "_iap";

    // Initialise table array
    $xartable = array();

    // Name for publications
    $iapPublications = xarDBGetSiteTablePrefix() . $issuesPrefix . '_publications';

    // Set the table name for publications
    $xartable['iapPublications'] = $iapPublications;

    // Name for issues
    $iapIssues = xarDBGetSiteTablePrefix() . $issuesPrefix . '_issues';

    // Set the table name for issues
    $xartable['iapIssues'] = $iapIssues;

    // Name for topics
    $iapTopics = xarDBGetSiteTablePrefix() . $issuesPrefix . '_topics';

    // Set the table name for topics
    $xartable['iapTopics'] = $iapTopics;

    // Name for owners
    $iapOwners = xarDBGetSiteTablePrefix() . $issuesPrefix . '_owners';

    // Set the table name for owners
    $xartable['iapOwners'] = $iapOwners;

    // Name for disclaimers
    $iapDisclaimers = xarDBGetSiteTablePrefix() . $issuesPrefix . '_disclaimers';

    // Set the table name for disclaimers
    $xartable['iapDisclaimers'] = $iapDisclaimers;

    // Name for stories
    $iapStories = xarDBGetSiteTablePrefix() . $issuesPrefix . '_stories';

    // Set the table name for stories
    $xartable['iapStories'] = $iapStories;

    // Name for subscriptions
    $iapSubscriptions = xarDBGetSiteTablePrefix() . $issuesPrefix . '_subscriptions';

    // Set the table name for subscriptions
    $xartable['iapSubscriptions'] = $iapSubscriptions;

    // Name for alternative subscriptions
    $iapAltSubscriptions = xarDBGetSiteTablePrefix() . $issuesPrefix . '_altsubscriptions';

    // Set the table name
    $xartable['iapAltSubscriptions'] = $iapAltSubscriptions;

    // Return the table information
    return $xartable;
}

?>
