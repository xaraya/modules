<?php

/**
 * Create a new article
 * Usage : $aid = xarModAPIFunc('articles', 'admin', 'create', $article);
 *
 * @param $args['title'] name of the item (this is the only mandatory argument)
 * @param $args['summary'] summary for this item
 * @param $args['body'] body text for this item
 * @param $args['notes'] notes for the item
 * @param $args['status'] status of the item
 * @param $args['ptid'] publication type ID for the item
 * @param $args['pubdate'] publication date in unix time format (or default now)
 * @param $args['authorid'] ID of the author (default is current user)
 * @param $args['language'] language of the item
 * @param $args['cids'] category IDs this item belongs to
 * @returns int
 * @return articles item ID on success, false on failure
 */
function articles_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check (all the rest is optional, and set to defaults below)
    if (empty($title)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'title', 'admin', 'create', 'Articles');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

// Note : we use empty() here because we don't care whether it's set to ''
//        or if it's not set at all - defaults will apply in either case !

    // Default publication type is defined in the admin interface
    if (empty($ptid) || !is_numeric($ptid)) {
        $ptid = xarModGetVar('articles', 'defaultpubtype');
        // for security check below
        $args['ptid'] = $ptid;
    }

    // Default author ID is the current user, or Anonymous (1) otherwise
    if (empty($authorid) || !is_numeric($authorid)) {
        $authorid = xarUserGetVar('uid');
        if (empty($authorid)) {
            $authorid = 1;
        }
        // for security check below
        $args['authorid'] = $authorid;
    }

    // Default categories is none
    if (empty($cids) || !is_array($cids) ||
        // catch common mistake of using array('') instead of array()
        (count($cids) > 0 && empty($cids[0])) ) {
        $cids = array();
        // for security check below
        $args['cids'] = $cids;
    }

    // Security check
    if (!xarModAPILoad('articles', 'user')) return;

    $args['mask'] = 'SubmitArticles';
    if (!xarModAPIFunc('articles','user','checksecurity',$args)) {
        $msg = xarML('Not authorized to add #(1) items',
                    'Article');
        xarExceptionSet(XAR_USER_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return false;
    }

    // Default publication date is now
    if (empty($pubdate) || !is_numeric($pubdate)) {
        $pubdate = time();
    }

    // Default status is Submitted (0)
    if (empty($status) || !is_numeric($status)) {
        $status = 0;
    }

    // TODO: Default language is ... ???
    if (empty($language)) {
        $language = '';
    }

    // Default summary is empty
    if (empty($summary)) {
        $summary = '';
    }

    // Default notes is empty
    if (empty($notes)) {
        $notes = '';
    }

    // Default body text is empty
    if (empty($body) || !is_string($body)) {
        $body = '';
    }

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable =& xarDBGetTables();
    $articlestable = $xartable['articles'];

    // Get next ID in table
    if (empty($aid) || !is_numeric($aid) || $aid == 0) {
        $nextId = $dbconn->GenId($articlestable);
    } else {
        $nextId = $aid;
    }

    // Add item
    $query = "INSERT INTO $articlestable (
              xar_aid,
              xar_title,
              xar_summary,
              xar_body,
              xar_authorid,
              xar_pubdate,
              xar_pubtypeid,
              xar_notes,
              xar_status,
              xar_language)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($title) . "',
              '" . xarvarPrepForStore($summary) . "',
              '" . xarvarPrepForStore($body) . "',
              '" . xarvarPrepForStore($authorid) . "',
              '" . xarVarPrepForStore($pubdate) . "',
              '" . xarvarPrepForStore($ptid) . "',
              '" . xarvarPrepForStore($notes) . "',
              '" . xarvarPrepForStore($status) . "',
              '" . xarvarPrepForStore($language) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get aid to return
    if (empty($aid) || !is_numeric($aid) || $aid == 0) {
        $aid = $dbconn->PO_Insert_ID($articlestable, 'xar_aid');
    }

    if (empty($cids)) {
        $cids = array();
    }

    // Call create hooks for categories, hitcount etc.
    $args['aid'] = $aid;
// Specify the module, itemtype and itemid so that the right hooks are called
    $args['module'] = 'articles';
    $args['itemtype'] = $ptid;
    $args['itemid'] = $aid;
// TODO: get rid of this
    $args['cids'] = $cids;
    xarModCallHooks('item', 'create', $aid, $args);

    return $aid;
}

?>
