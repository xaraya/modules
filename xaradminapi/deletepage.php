<?php

/**
 * delete a page
 * @param $args['pid'] the ID of the page
 * @returns bool
 * @return true on success, false on failure
 */
function xarpages_adminapi_deletepage($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (empty($pid)) {
        $msg = xarML('Invalid page ID #(1)', $pid);
        throw new BadParameterException(null,$msg);
    }

    // Obtain current information on the page we are going to delete.
    $page = xarModAPIFunc(
        'xarpages', 'user', 'getpage',
        array('pid' => $pid, 'dd_flag' => false)
    );
    if (empty($page)) {
        // No need to raise an error, as the page may already have been deleted.
        //$msg = xarML('Page does not exist.');
        //throw new BadParameterException(null,$msg);
        return true;
    }

    // Security check
    if (!xarSecurityCheck('DeleteXarpagesPage', 1, 'Page', $page['name'] . ':' . $page['pagetype']['name'])) {
        return;
    }

    // Delete any module aliases for this page.
    xarModDelAlias($page['name'], 'xarpages');

    // These are set to be used later on
    $right = $page['right'];
    $left = $page['left'];
    $deslocation_inside = $right - $left + 1;

    // If the page was used as a special page anywhere, then reset that too,
    // so we don't have any special page orphans.
    foreach(array('default', 'error', 'notfound') as $special) {
        if (xarModVars::get('xarpages', $special . 'page') == $pid) {
            xarModVars::set('xarpages', $special . 'page', 0);
        }
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // Deleting a page

    //There are two possibilities when deleting a set:
    // 1 - Destroy every child inside it
    // 2 - Destroy the parent, and make the parent´s parent inherity the children
    // Option 1 is easiest, so we will go with it for now.

    // This part was mostly taken from Joe Celko´s article SQL for Smarties on DBMS, April 1996
    // So deleting all the subtree

    // Remove the page and its sub-tree
    $table = $xartable['xarpages_pages'];

    // Get a list of pages we are going to delete.
    $query = 'SELECT xar_pid FROM ' . $table . ' WHERE xar_left BETWEEN ? AND ?';
    $result = $dbconn->Execute($query, array($left, $right));
    if (!$result) return;

    $pids = array();
    while (!$result->EOF) {
        list($pid) = $result->fields;
        $pids[] = $pid;
        $result->MoveNext();
    }

    // Now the deletion query.
    $query = 'DELETE FROM ' . $table . ' WHERE xar_left BETWEEN ? AND ?';

    $result = $dbconn->Execute($query, array($left, $right));
    if (!$result) return;

    // Now close up the the gap
    $query = 'UPDATE ' . $table
        . ' SET xar_left ='
        . ' CASE WHEN xar_left > ?'
        . '    THEN xar_left - ?'
        . '    ELSE xar_left'
        . ' END,'
        . ' xar_right ='
        . ' CASE WHEN xar_right > ?'
        . '    THEN xar_right - ?'
        . '    ELSE xar_right'
        . ' END';

    $result = $dbconn->Execute(
        $query, array(
            (int)$left,
            (int)$deslocation_inside,
            (int)$left,
            (int)$deslocation_inside
        )
    );
    if (!$result) return;

    // Call hooks for every page being deleted, not just the main one.
    foreach($pids as $pid) {
        xarModCallHooks(
            'item', 'delete', $pid,
            array('module' => 'xarpages', 'itemtype' => $page['itemtype'])
        );
    }

    return true;
}

?>