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
 * Get an the storeis for an issue 
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] id of newsletter story to get
 * @returns story array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function newsletter_userapi_getissuestories($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($issueId) || !is_numeric($issueId)) {
        $invalid[] = 'issue id';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'userapi', 'getissuestories', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Optional arguments.
    if(!isset($startnum)) {
        $startnum = 1;
    }

    if (!isset($numitems)) {
        $numitems = -1;
    }

    if (!isset($display)) {
        $display = 'unpublished';
    }

    if (!isset($sortby)) {
        $sortby = 'title';
    }

    // Load categories API
    if (!xarModAPILoad('categories', 'user')) {
        $msg = xarML('Unable to load #(1) #(2) API','categories','user');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MODULE_DEPENDENCY', new SystemException($msg));
        return false;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $storiesTable = $xartable['nwsltrStories'];
    $topicsTable = $xartable['nwsltrTopics'];
    $rolesTable = $xartable['roles'];
    $categoriesTable = $xartable['categories'];

    // Initialize date arrays
    $storyDate = array();
    $datePublished = array();

    // Create query
    $query = "SELECT $storiesTable.xar_id,
                     $storiesTable.xar_ownerid,
                     $rolesTable.xar_name,
                     $storiesTable.xar_pid,
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
              FROM  $storiesTable, $rolesTable, $categoriesTable, $topicsTable
              WHERE $storiesTable.xar_ownerid = $rolesTable.xar_uid
              AND   $storiesTable.xar_cid = $categoriesTable.xar_cid
              AND   $topicsTable.xar_storyid = $storiesTable.xar_id
              AND   $topicsTable.xar_issueid = $issueId";

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

    // Put stories into result array
    $stories = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list($id, 
             $ownerId, 
             $ownerName, 
             $pid,
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
        
        // no article title by 
        $_article['title']=NULL;
        
        // if there is an article ID, get the article title
        if (!empty($articleid)){
            $_article  = current(xarModAPIFunc('articles','user','getAll',
            array('aids'=>array($articleid),
                  'extra'=>array('dynamicdata')
                  )
             ));

        }
        
        $stories[] = array('id' => $id,
                           'ownerId' => $ownerId,
                           'ownerName' => $ownerName,
                           'pid' => $cid,
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
                           'articleid' => $articleid,
                           'article' => array("title"=>$_article['title']));
                           

    }
    
    
    
    // Close result set
    $result->Close();

    // Return the story array
    return $stories;
}

?>
