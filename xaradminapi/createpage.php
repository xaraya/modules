<?php
/**
 * Create a page
 *
 * @package Xaraya
 * @copyright (C) 2005 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

/**
 * Create a page.
 *
 * It is added to the group hierarchy.
 *
 * Mandatory columns:
 * @param string name group name
 * @param string desc group description
 * @param int    insertpoint ID of group inserting relative to
 * @param string offset relationship to insertpoint ('after', 'before', 'firstchild', 'lastchild')
 * @param int    itemtype
 * @return int   pid
 *
 * @todo <jason>check the page type is valid
 * @todo <jason>default most values and raise an error in missing mandatory values
 * @todo <jason>specifying pid is not supported by xarpages createpage yet! (cfr. DD migrate)
 * @todo <jason>I would like to keep the DD fields separate from the standard fields (in a 'dd' element)
 */

function xarpages_adminapi_createpage($args)
{
    extract($args);

    // Name is mandatory, but does not have to be unique.
    if (trim($name) == '') {
        $msg = xarML('Missing page name');
        throw new BadParemeterException(null,$msg);
    }

    // Get the itemtype.
    $pagetype = xarModAPIfunc(
        'xarpages', 'user', 'get_type',
        array('ptid' => $itemtype)
    );

    if (empty($pagetype)) {
        // Error - invalid page type.
        $msg = xarML('Invalid page type ID "#(1)"', $itemtype);
        throw new BadParemeterException(null,$msg);
    }

    // Security check - can we create pages of this type?
    if (!xarSecurityCheck('AddXarpagesPage', 1, 'Page', 'All:' . $pagetype['name'])) {
        return;
    }

    $xartable = xarDB::getTables();
    $dbconn = xarDB::getConn();

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

    $bind[] = serialize($name);
    $cols[] = 'xar_name';

    $bind[] = $info;
    $cols[] = 'info';

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

    // Create hooks - by passing the original $args list, any DD fields will also be passed.
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