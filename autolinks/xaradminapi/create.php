<?php

/**
 * create a new autolink
 * @param $args['keyword'] keyword of the item
 * @param $args['title'] title of the item
 * @param $args['url'] url of the item
 * @param $args['comment'] comment of the item
 * @param $args['tid'] type id for the item
 * @param $args['enabled'] integer, indicates whether item should be anabled; default: 0
 * @returns int
 * @return autolink ID on success, false on failure
 */
function autolinks_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);

    // Optional arguments
    if (!isset($comment)) {
        $comment = '';
    }

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($keyword)
        || !isset($name)
        || !isset($title)
        || !isset($tid)
        || !is_numeric($tid)
        || !isset($url)) {
        $msg = xarML(
            'Invalid Parameter Count',
            'admin', 'create', 'Autolinks'
        );
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (isset($enabled) && !empty($enabled)) {
        $enabled = 1;
    } else {
        $enabled = 0;
    }
    if (isset($match_re) && !empty($match_re)) {
        $match_re = 1;
    } else {
        $match_re = 0;
    }

    if (!isset($sample)) {
        $sample = '';
    }

    // Security check
    if(!xarSecurityCheck('AddAutolinks')) {return;}

    // Get database connection
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];

    // Check if that keyword or name exists
    $query = 'SELECT xar_lid FROM ' . $autolinkstable
          . ' WHERE xar_keyword = \'' . xarVarPrepForStore($keyword). '\''
          . ' OR xar_name = \'' . xarVarPrepForStore($name). '\'';
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if ($result->RecordCount() > 0) {
        $msg = xarML(
            'The link matching keyword "#(1)" or name "#(2)" already exists.',
            $keyword, $name
        );
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check the autolink type ID exists
    $type = xarModAPIfunc('autolinks', 'user', 'gettype', array('tid'=>$tid));
    if (!$type) {
        $msg = xarML('Autolink Type does not exist') . ' ('.$tid.')';
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Get next ID in table
    $nextId = $dbconn->GenId($autolinkstable);

    // Add item
    $query = "INSERT INTO $autolinkstable (
              xar_lid,
              xar_type_tid,
              xar_name,
              xar_keyword,
              xar_title,
              xar_url,
              xar_comment,
              xar_match_re,
              xar_sample,
              xar_enabled)
            VALUES (
              $nextId,
              " . xarVarPrepForStore($tid) . ",
              '" . xarVarPrepForStore($name) . "',
              '" . xarVarPrepForStore($keyword) . "',
              '" . xarVarPrepForStore($title) . "',
              '" . xarVarPrepForStore($url) . "',
              '" . xarVarPrepForStore($comment) . "',
              " . xarVarPrepForStore($match_re) . ",
              '" . xarVarPrepForStore($sample) . "',
              " . xarVarPrepForStore($enabled) . ")";
    
    $result =& $dbconn->Execute($query);
    if (!$result) {return;}

    // Get the ID of the item that we inserted
    $lid = $dbconn->PO_Insert_ID($autolinkstable, 'xar_lid');

    // Now compile the replacecache for the link.
    $result = xarModAPIfunc('autolinks', 'admin', 'updatecache', array('lid' => $lid));
    if (!$result) {return;}

    // Let any hooks know that we have created a new link
    xarModCallHooks(
        'item', 'create', $lid,
        array(
            'itemtype' => $type['itemtype'],
            'module' => 'autolinks',
            'urlparam' => 'lid'
        )
    );

    // Return the id of the newly created link to the calling process
    return $lid;
}

?>