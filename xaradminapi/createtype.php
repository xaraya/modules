<?php

/**
 * create a new autolink type
 * TODO: arg list
 * @param $args['type_name'] name of the autolink type
 * @param $args['template_name'] name of the template to use
 * @param $args['dynamic_replace'] flag indicates dynamic replacement; default: 0
 * @param $args['type_desc'] description of the autolink type (optional)
 * @param $args['itemtype'] the item type used for hooks for items of this type; default: tid+10
 * @return int autolink type ID on success, false or NULL on failure
 */
function autolinks_adminapi_createtype($args)
{
    // TODO: checks for unique names and misisng values.

    // Get arguments from argument array
    extract($args);

    // Optional arguments
    if (!isset($type_desc)) {
        $type_desc = '';
    }

    if (!isset($dynamic_replace) || !is_numeric($dynamic_replace) || $dynamic_replace < 0 || $dynamic_replace > 1) {
        $dynamic_replace = 0;
    }

    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $itemtype = 0;
    }

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($type_name) || !isset($template_name)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Security check
    if(!xarSecurityCheck('AddAutolinks')) {return;}

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $autolinkstypestable = $xartable['autolinks_types'];

    // Check if that type name exists
    $query = 'SELECT xar_tid FROM ' . $autolinkstypestable
          . ' WHERE xar_type_name = ?';
    $result =& $dbconn->Execute($query, array($type_name));
    if (!$result) {return;}

    if ($result->RecordCount() > 0) {
        $msg = xarML('The given name already exists.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get next ID in table
    $nextID = $dbconn->GenId($autolinkstypestable);

    // If we have an ID, then we can set the link itemtype.
    if ($nextID > 0 && $itemtype === 0) {
        $itemtype = $nextID + 10;
    }

    // Add item
    $query = 'INSERT INTO ' . $autolinkstypestable . ' (
              xar_tid,
              xar_type_name,
              xar_template_name,
              xar_dynamic_replace,
              xar_link_itemtype,
              xar_type_desc)
            VALUES (?, ?, ?, ?, ?, ?)';

    $bind = array(
        $nextID, $type_name, $template_name, $dynamic_replace, $itemtype, $type_desc
    );

    $result =& $dbconn->Execute($query, $bind);
    if (!$result) {return;}

    // Get the ID of the item that we inserted
    $tid = $dbconn->PO_Insert_ID($autolinkstypestable, 'xar_tid');

    // If we couldn't set the link itemtype before, update it now.
    if ($tid > 0 && $itemtype == 0) {
        $itemtype = $tid + 10;

        $query = 'UPDATE ' . $autolinkstypestable
              . ' SET xar_link_itemtype = ?'
              . ' WHERE xar_tid = ?'
              . ' AND xar_link_itemtype <> ?';
        $result =& $dbconn->Execute($query, array($itemtype, $tid, $itemtype));
        if (!$result) {return;}
    }

    // Let any hooks know that we have created a new link type (an instance
    // of a new item of type 'link type'.
    // Note: there are no specific creation hooks for item types.
    xarModCallHooks(
        'item', 'create', $tid,
        array(
            'itemtype' => xarModGetVar('autolinks', 'typeitemtype'),
            'module' => 'autolinks',
            'urlparam' => 'tid'
        )
    );

    // Return the id of the newly created link to the calling process
    return $tid;
}

?>