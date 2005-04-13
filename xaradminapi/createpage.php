<?php
/*
 * Create a page.
 * It is added to the group hierarchy.
 * Mandatory columns:
 *  name: group name
 *  desc: group description
 *  insertpoint: ID of group inserting relative to
 *  offset: relationship to insertpoint ('after', 'before', 'firstchild', 'lastchild')
 * TODO: check the page type is valid
 * TODO: default most values and raise an error in missing mandatory values
 * @TODO: specifying pid is not supported by xarpages createpage yet ! (cfr. DD migrate)
 */

function xarpages_adminapi_createpage($args)
{
    extract($args);

    // Name is mandatory, but does not have to be unique.
    if (trim($name) == '') {
        $msg = xarML('Missing page name');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Get the itemtype.
    $pagetype = xarModAPIfunc(
        'xarpages', 'user', 'gettype',
        array('ptid' => $itemtype)
    );

    if (empty($pagetype)) {
        // Error - invalid page type.
        $msg = xarML('Invalid page type ID "#(1)"', $itemtype);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Security check - can we create pages of this type?
    if (!xarSecurityCheck('AddXarpagesPage', 1, 'Page', 'All:' . $pagetype['name'])) {
        return;
    }

    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $idname = 'xar_pid';
    $tablename = $xartable['xarpages_pages'];

    // Data for the query.
    $bind = array();
    $cols = array();

    // Include the optional parameters.
    foreach(array('desc', 'template', 'page_template', 'theme', 'encode_url', 'decode_url', 'function', 'status') as $colname) {
        if (isset($$colname) && is_string($$colname)) {
            $bind[] = $$colname;
            $cols[] = 'xar_' . $colname;
        }
    }

    // Open a space in the pages hierarchy.
    // Position in the hierarchy defined by args: insertpoint and offset
    // TODO: if insertpoint or offset are missing, then default them so that
    // the page is inserted as the first root page. That would help data
    // import, where a tree could be imported with no knowledge of existing
    // pages.
    if (!isset($insertpoint) || !isset($offset)) {
        $insertpoint = 0;
        $offset = 'before';
    }

    $gap = xarModAPIfunc(
        'xarpages', 'tree', 'insertprep',
        array_merge(
            $args,
            array('tablename' => $tablename, 'idname' => 'xar_pid')
        )
    );

    $bind[] = (int)$gap['parent'];
    $cols[] = 'xar_parent';

    $bind[] = (int)$gap['left'];
    $cols[] = 'xar_left';

    $bind[] = (int)$gap['right'];
    $cols[] = 'xar_right';

    $bind[] = (int)$itemtype;
    $cols[] = 'xar_itemtype';

    $bind[] = $name;
    $cols[] = 'xar_name';

    if (!empty($gap)) {
        // Insert the page
        $nextID = $dbconn->GenId($tablename);
        $bind[] = $nextID;
        $cols[] = 'xar_pid';

        $query = 'INSERT INTO ' . $tablename
            . '(' .implode(', ', $cols). ')'
            . ' VALUES(?' . str_repeat(',?', count($cols)-1) . ')';

        $result = $dbconn->execute($query, $bind);
        if (!$result) {return;}

        $pid = $dbconn->PO_Insert_ID($tablename, $idname);
    }

    // Create hooks - by passing the original $args list, any DD fields will also be passed
    $args['module'] = 'xarpages';
    $args['itemtype'] = $itemtype;
    $args['itemid'] = $pid;
    xarModCallHooks('item', 'create', $pid, $args);

    // Set this page as a module alias if necessary.
    if (!empty($alias)) {
        xarModSetAlias($name, 'xarpages');
    }

    return $pid;
}

?>
