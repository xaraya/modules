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
 * Get all Newsletter items
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['numitems'] the number of items to retrieve (default -1 = all)
 * @param $args['startnum'] start with this item number (default 1)
 * @param $args['phase'] type of item to retreive (ie 'story', 'publcation', etc.)
 * @param $args['display'] display 'published' or 'unpublished' stories/issues
 * @param $args['owner'] get stories/issues for this owner (1 = true, 0 = false)
 * @param $args['sortby'] sort by 'title', 'category', 'publication', 'date' or 'owner'
 * @param $args['orderby'] order by 'ASC' or 'DESC' (default = ASC)
 * @param $args['publicationId'] get items for a specific publication
 * @param $args['issueId'] get items for a specific issue
 * @param $args['external'] get items for a external viewing in archives  (1 = true, 0 =false)
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_get($args)
{
    // Get arguments
    extract($args);

    // Optional arguments.
    if(!isset($startnum)) {
        $startnum = 1;
    }

    if (!isset($numitems)) {
        $numitems = -1;
    }

    if (!isset($phase)) {
        return; // throw back
    }

    if (!isset($publicationId)) {
        $publicationId = 0;
    }

    if (!isset($issueId)) {
        $issueId = 0;
    }

    if (!isset($orderby)) {
        $orderby = 'ASC';
    }

    if (!isset($external)) {
        $external = 0;
    }

    // Argument check
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'get', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $items = array();

    // Security check
    if(!xarSecurityCheck('OverviewNewsletter')) return;

    // Load categories API
    if (!xarModAPILoad('categories', 'user')) {
        $msg = xarML('Unable to load #(1) #(2) API','categories','user');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MODULE_DEPENDENCY', new SystemException($msg));
        return false;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Set roles and categories table
    $rolesTable = $xartable['roles'];
    $categoriesTable = $xartable['categories'];

    // Switch to requested view
    switch(strtolower($phase)) {

        case 'owner':
            // Name the table and column definitions
            $ownersTable = $xartable['nwsltrOwners'];

            // Get items
            $query = "SELECT $ownersTable.xar_uid,
                             $ownersTable.xar_rid,
                             $rolesTable.xar_name,
                             $ownersTable.xar_signature
                      FROM $ownersTable, $rolesTable
                      WHERE $ownersTable.xar_uid = $rolesTable.xar_uid
                      ORDER BY $rolesTable.xar_name";

            $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

            // Check for an error
            if (!$result) return;

            // Put items into result array
            for (; !$result->EOF; $result->MoveNext()) {
                list($uid, 
                     $rid, 
                     $ownerName,
                     $signature) = $result->fields;

                $items[] = array('id' => $uid,
                                 'rid' => $rid,
                                 'name' => $ownerName,
                                 'signature' => $signature);
            }

            // Close result set
            $result->Close();

            break;

        case 'disclaimer':
            // Name the table and column definitions
            $disclaimersTable = $xartable['nwsltrDisclaimers'];

            // Get items
            $query = "SELECT $disclaimersTable.xar_id,
                             $disclaimersTable.xar_title,
                             $disclaimersTable.xar_text
                      FROM $disclaimersTable
                      ORDER BY $disclaimersTable.xar_title";

            $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

            // Check for an error
            if (!$result) return;

            // Put items into result array
            for (; !$result->EOF; $result->MoveNext()) {
                list($id, $title, $disclaimer) = $result->fields;

                 $items[] = array('id' => $id,
                                  'title' => $title,
                                  'disclaimer' => $disclaimer);
            }

            // Close result set
            $result->Close();

            break;

        case 'story':
            // Name the table and column definitions
            $storiesTable = $xartable['nwsltrStories'];
            $publicationsTable = $xartable['nwsltrPublications'];

            $storyDate = array();
            $datePublished = array();

            // Get items
            $query = "SELECT $storiesTable.xar_id,
                             $storiesTable.xar_ownerid,
                             $rolesTable.xar_name,
                             $storiesTable.xar_pid,
                             $publicationsTable.xar_title,
                             $storiesTable.xar_cid,
                             $categoriesTable.xar_name,
                             $storiesTable.xar_title,
                             $storiesTable.xar_source,
                             $storiesTable.xar_content,
                             $storiesTable.xar_priority,
                             $storiesTable.xar_storydate,
                             $storiesTable.xar_altdate,
                             $storiesTable.xar_datepublished,
                             $storiesTable.xar_fulltextlink,
                             $storiesTable.xar_registerlink,
                             $storiesTable.xar_commentary,
                             $storiesTable.xar_commentarysrc,
                             $storiesTable.xar_articleid
                      FROM  $storiesTable, $rolesTable, $categoriesTable, $publicationsTable
                      WHERE $storiesTable.xar_ownerid = $rolesTable.xar_uid
                      AND   $storiesTable.xar_cid = $categoriesTable.xar_cid
                      AND   $storiesTable.xar_pid = $publicationsTable.xar_id";

            if (isset($display)) {
                switch ($display) {
                    case 'published':
                        $query .= " AND $storiesTable.xar_datepublished > 0";
                        break;
                    case 'unpublished':
                        $query .= " AND $storiesTable.xar_datepublished = 0";
                        break;
                }
            }
            
            if (isset($owner)) {
                if ($owner) {
                    // Get current uid
                    $userid = xarSessionGetVar('uid');
                    $query .= " AND $storiesTable.xar_ownerid = " . $userid;
                }
            }

            if ($publicationId) {
                $query .= " AND $storiesTable.xar_pid = " . $publicationId;
            }

            if (isset($sortby)) {
                switch ($sortby) {
                    case 'title':
                        $query .= " ORDER BY $storiesTable.xar_title";
                        break;
                    case 'publication':
                        $query .= " ORDER BY $publicationsTable.xar_title";
                        break;
                    case 'category':
                        $query .= " ORDER BY $categoriesTable.xar_name";
                        break;
                    case 'date':
                        $query .= " ORDER BY $storiesTable.xar_storydate";
                        break;
                    case 'owner':
                        $query .= " ORDER BY $rolesTable.xar_name";
                        break;
                }
            }

            $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

            // Check for an error
            if (!$result) return;

            // Put items into result array
            for (; !$result->EOF; $result->MoveNext()) {

                list($id, 
                     $ownerId, 
                     $ownerName, 
                     $pid,
                     $publicationTitle,
                     $cid,
                     $categoryName,
                     $title, 
                     $source,
                     $content,
                     $priority,
                     $storyDate['timestamp'],
                     $altDate,
                     $datePublished['timestamp'],
                     $fullTextLink,
                     $registerLink,
                     $commentary,
                     $commentarySource,
                     $articleid) = $result->fields;

                // Change date formats from UNIX timestamp to something readable
                if ($storyDate['timestamp'] == 0) {
                    $storyDate['mon'] = "";
                    $storyDate['day'] = "";
                    $storyDate['year'] = "";
                } else {
                    $storyDate['mon'] = date('m', $storyDate['timestamp']);
                    $storyDate['day'] = date('d', $storyDate['timestamp']);
                    $storyDate['year'] = date('Y', $storyDate['timestamp']);
                }

                if ($datePublished['timestamp'] == 0) {
                    $datePublished['mon'] = "";
                    $datePublished['day'] = "";
                    $datePublished['year'] = "";
                } else {
                    $datePublished['mon'] = date('m', $datePublished['timestamp']);
                    $datePublished['day'] = date('d', $datePublished['timestamp']);
                    $datePublished['year'] = date('Y', $datePublished['timestamp']);
                }
                
                $items[] = array('id' => $id,
                                 'ownerId' => $ownerId,
                                 'ownerName' => $ownerName,
                                 'pid' => $cid,
                                 'publicationTitle' => $publicationTitle,
                                 'cid' => $cid,
                                 'categoryName' => $categoryName,
                                 'title' => $title,
                                 'source' => $source,
                                 'content' => $content,
                                 'priority' => $priority,
                                 'storyDate' => $storyDate,
                                 'altDate' => $altDate,
                                 'datePublished' => $datePublished,
                                 'fullTextLink' => $fullTextLink,
                                 'registerLink' => $registerLink,
                                 'commentary' => $commentary,
                                 'commentarySource' => $commentarySource,
                                 'articleid'=>$articleid);
            }
            
            // Close result set
            $result->Close();

            break;

        case 'topic':
            // Name the table and column definitions
            $topicsTable = $xartable['nwsltrTopics'];

            // Get items
            $query = "SELECT xar_issueid,
                             xar_storyid,
                             xar_cid,
                             xar_order
                      FROM $topicsTable";

            if($issueId) {
                $query .= " WHERE xar_issueid = ?";
                $bindvars = array((int) $issueId);
            } else {
                $bindvars = array();
            }

            $query .= " ORDER by xar_order";

            $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);

            // Check for an error
            if (!$result) return;

            // Put items into result array
            for (; !$result->EOF; $result->MoveNext()) {
                list($issueId, $storyId, $cid, $order) = $result->fields;

                 $items[] = array('issueId' => $issueId,
                                  'storyId' => $storyId,
                                  'cid' => $cid,
                                  'order' => $order);
            }

            // Close result set
            $result->Close();

            break;

        case 'issue':
            // Name the table and column definitions
            $issuesTable = $xartable['nwsltrIssues'];
            $publicationsTable = $xartable['nwsltrPublications'];

            $datePublished = array();

            // Get items
            $query = "SELECT $issuesTable.xar_id,
                             $issuesTable.xar_pid,
                             $publicationsTable.xar_title,
                             $issuesTable.xar_title,
                             $issuesTable.xar_ownerid,
                             $rolesTable.xar_name,
                             $issuesTable.xar_external,
                             $issuesTable.xar_editornote,
                             $issuesTable.xar_datepublished,
                             $issuesTable.xar_fromname,
                             $issuesTable.xar_fromemail
                      FROM  $issuesTable, $publicationsTable, $rolesTable
                      WHERE $issuesTable.xar_ownerid = $rolesTable.xar_uid
                      AND   $issuesTable.xar_pid = $publicationsTable.xar_id";

            if (isset($display)) {
                switch ($display) {
                    case 'published':
                        $query .= " AND $issuesTable.xar_datepublished > 0";
                        break;
                    case 'unpublished':
                        $query .= " AND $issuesTable.xar_datepublished = 0";
                        break;
                }
            }

            if (isset($owner)) {
                if ($owner) {
                    // Get current uid
                    $userid = xarSessionGetVar('uid');
                    $query .= " AND $issuesTable.xar_ownerid = " . $userid;
                }
            }
            
            if ($publicationId) {
                $query .= " AND $issuesTable.xar_pid = " . $publicationId;
            }

            // Check if we want to display external issues.  This is only
            // applicable to viewing issue archives.
            if ($external) {
                $query .= " AND $issuesTable.xar_external = 1";
            }

            if (isset($sortby)) {
                switch ($sortby) {
                    case 'title':
                        $query .= " ORDER BY $issuesTable.xar_title $orderby";
                        break;
                    case 'publication':
                        $query .= " ORDER BY $publicationsTable.xar_title $orderby";
                        break;
                    case 'owner':
                        $query .= " ORDER BY $rolesTable.xar_name $orderby";
                        break;
                    case 'datePublished':
                        $query .= " ORDER BY $issuesTable.xar_datepublished $orderby";
                        break;
                }
            }

            $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

            // Check for an error
            if (!$result) return;

            // Put items into result array
            for (; !$result->EOF; $result->MoveNext()) {
                list($id, 
                     $pid,
                     $publicationTitle,
                     $title,
                     $ownerId, 
                     $ownerName,
                     $external,
                     $editorNote,
                     $datePublished['timestamp'],
                     $fromname,
                     $fromemail) = $result->fields;

                // Change date formats from UNIX timestamp to something readable
                if ($datePublished['timestamp'] == 0) {
                    $datePublished['mon'] = "";
                    $datePublished['day'] = "";
                    $datePublished['year'] = "";
                } else {
                    $datePublished['mon'] = date('m', $datePublished['timestamp']);
                    $datePublished['day'] = date('d', $datePublished['timestamp']);
                    $datePublished['year'] = date('Y', $datePublished['timestamp']);
                }

                 $items[] = array('id' => $id,
                                  'pid' => $pid,
                                  'publicationTitle' => $publicationTitle,
                                  'title' => $title,
                                  'ownerId' => $ownerId,
                                  'ownerName' => $ownerName,
                                  'external' => $external,
                                  'editorNote' => $editorNote,
                                  'datePublished' => $datePublished,
                                  'fromname' => $fromname,
                                  'fromemail' => $fromemail);
            }

            // Close result set
            $result->Close();

            break;

        case 'publication':
            // Name the table and column definitions
            $publicationsTable = $xartable['nwsltrPublications'];

            // Get items
            $query = "SELECT $publicationsTable.xar_id,
                             $publicationsTable.xar_cid,
                             $publicationsTable.xar_altcids,
                             $categoriesTable.xar_name,
                             $publicationsTable.xar_ownerid,
                             $rolesTable.xar_name,
                             $publicationsTable.xar_template_html,
                             $publicationsTable.xar_template_text,
                             $publicationsTable.xar_title,
                             $publicationsTable.xar_logo,
                             $publicationsTable.xar_linkexpiration,
                             $publicationsTable.xar_linkregistration,
                             $publicationsTable.xar_description,
                             $publicationsTable.xar_disclaimerid,
                             $publicationsTable.xar_introduction,
                             $publicationsTable.xar_private,
                             $publicationsTable.xar_subject,
                             $publicationsTable.xar_fromname,
                             $publicationsTable.xar_fromemail
                      FROM  $publicationsTable, $categoriesTable, $rolesTable
                      WHERE $publicationsTable.xar_ownerid = $rolesTable.xar_uid
                      AND $publicationsTable.xar_cid = $categoriesTable.xar_cid";

            if (isset($sortby)) {
                switch ($sortby) {
                    case 'title':
                        $query .= " ORDER BY $publicationsTable.xar_title";
                        break;
                    case 'category':
                        $query .= " ORDER BY $categoriesTable.xar_name";
                        break;
                    case 'owner':
                        $query .= " ORDER BY $rolesTable.xar_name";
                        break;
                }
            }

            $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);

            // Check for an error
            if (!$result) return;

            // Put items into result array
            for (; !$result->EOF; $result->MoveNext()) {
                list($id, 
                     $cid,
                     $altcids,
                     $categoryName,
                     $ownerId, 
                     $ownerName,
                     $templateHTML,
                     $templateText,
                     $title,
                     $logo,
                     $linkExpiration,
                     $linkRegistration,
                     $description, 
                     $disclaimerId,
                     $introduction,
                     $private,
                     $subject,
                     $fromname,
                     $fromemail) = $result->fields;

                // The user API function is called.
                $userData = xarModAPIFunc('roles',
                                  'user',
                                  'get',
                                   array('uid' => $ownerId));

                if ($userData == false) {
                    $ownerEmail = "none@none.com";
                } else {
                    $ownerEmail = $userData['email'];
                }
                
                // Unserialize the altcids
                if (is_string($altcids)) {
                    $altcids = unserialize($altcids);
                }

                $items[] = array('id' => $id,
                                 'cid' => $cid,
                                 'altcids' => $altcids,
                                 'categoryName' => $categoryName,
                                 'ownerId' => $ownerId,
                                 'ownerName' => $ownerName,
                                 'ownerEmail' => $ownerEmail,
                                 'templateHTML' => $templateHTML,
                                 'templateText' => $templateText,
                                 'title' => $title,
                                 'logo' => $logo,
                                 'linkExpiration' => $linkExpiration,
                                 'linkRegistration' => $linkRegistration,
                                 'description' => $description,
                                 'disclaimerId' => $disclaimerId,
                                 'introduction' => $introduction,
                                 'private' => $private,
                                 'subject' => $subject,
                                 'fromname' => $fromname,
                                 'fromemail' => $fromemail);
            }

            // Close result set
            $result->Close();

            break;

        case 'subscription':
            // Name the table and column definitions
            $subscriptionsTable = $xartable['nwsltrSubscriptions'];
            $deleteitems = array();

            // Get items
            $query = "SELECT $subscriptionsTable.xar_uid,
                             $subscriptionsTable.xar_pid,
                             $rolesTable.xar_name,
                             $rolesTable.xar_state,
                             $subscriptionsTable.xar_htmlmail
                      FROM $subscriptionsTable, $rolesTable
                      WHERE $subscriptionsTable.xar_pid = ?
                      AND $subscriptionsTable.xar_uid = $rolesTable.xar_uid";

            $bindvars[] = (int) $pid;

            if(isset($uid)) {
                $query .= " AND $subscriptionsTable.xar_uid = ?";
                $bindvars[] = (int) $uid;
            }

            $query .= " ORDER by xar_uid, xar_pid";
            $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);

            // Check for an error
            if (!$result) return;

            // Put items into result array
            for (; !$result->EOF; $result->MoveNext()) {
                list($uid, 
                     $pid, 
                     $ownerName,
                     $state,
                     $htmlmail) = $result->fields;

                // Determine if a user state has been set to ROLES_STATE_DELETED (0)
                if ($state == 0) {
                    $deleteitems[] = $uid;
                } else {
                    $items[] = array('uid' => $uid,
                                      'pid' => $pid,
                                      'name' => $ownerName,
                                      'state' => $state,
                                      'htmlmail' => $htmlmail,
                                      'type' => 0); // xaraya subscription
                }
            }

            // Close result set
            $result->Close();

            // Delete any users that have been set to ROLES_STATE_DELETED (0)
            if (!empty($deleteitems)) {
                foreach ($deleteitems as $item) {
                    // Remove this subscription
                    $result = xarModAPIFunc('newsletter',
                                            'admin',
                                            'deletesubscription',
                                            array('uid' => $item));
                }
            }

            break;

        case 'altsubscription':
            // Name the table and column definitions
            $nwsltrTable = $xartable['nwsltrAltSubscriptions'];

            // Get items
            $query = "SELECT xar_id,
                             xar_name,
                             xar_email,
                             xar_pid,
                             xar_htmlmail
                      FROM $nwsltrTable
                      WHERE xar_pid = ?";
            
            $bindvars[] = (int) $pid;

            $query .= " ORDER by xar_email";
            $result = $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);

            // Check for an error
            if (!$result) return;

            // Put items into result array
            for (; !$result->EOF; $result->MoveNext()) {
                list($id, $name, $email, $pid, $htmlmail) = $result->fields;

                 $items[] = array('id' => $id,
                                  'name' => $name,
                                  'email' => $email,
                                  'pid' => $pid,
                                  'htmlmail' => $htmlmail,
                                  'state' => 3, // ROLES_STATE_ACTIVE
                                  'type' => 1); // alternative subscription
            }

            // Close result set
            $result->Close();
            
            break;

        default:
            break;
    }

    // Return the items
    return $items;
}

?>
